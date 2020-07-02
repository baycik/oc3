<?php

class ModelExtensionModuleIssBulksyncParse extends Model {

    private $sync_config = '';

    public function __construct($registry) {
        parent::__construct($registry);
        $this->language_id = (int) $this->config->get('config_language_id');
        $this->store_id = (int) $this->config->get('config_store_id');
    }

    public function initParser($sync_id) {
        @set_time_limit(300);
        $sync_list_entry = $this->db->query("SELECT * FROM iss_sync_list WHERE sync_id='$sync_id'")->row;
        if (!$sync_list_entry) {
            return false;
        }
        $sync_list_entry['sync_config'] = $this->sync_config = json_decode($sync_list_entry['sync_config']);
        $this->prepare_parsing($sync_id, $this->sync_config->parsing_mode);
        if ($this->parse($sync_list_entry)) {
            $this->finish_parsing($sync_id, $this->sync_config->parsing_mode);
            $this->db->query("UPDATE iss_sync_list SET sync_last_started=NOW() WHERE sync_id='{$sync_list_entry['sync_id']}'");
            return true;
        } else {
            die('Error while parsing !');
        };
    }

    public function parse($sync_list_entry) {
        //Must be overrided by parser class
        return false;
    }
    
    public function install(){
        //May be overrided by parser class if needed
        return true;
    }

    private function prepare_parsing($sync_id, $mode) {
        $this->db->query("DROP TEMPORARY TABLE IF EXISTS iss_tmp_previous_sync"); #TEMPORARY
        $this->db->query("CREATE TEMPORARY TABLE iss_tmp_previous_sync AS (SELECT * FROM iss_sync_entries WHERE sync_id='$sync_id')");

        $this->db->query("DROP TEMPORARY TABLE IF EXISTS iss_tmp_current_sync"); #TEMPORARY
        $this->db->query("CREATE TEMPORARY TABLE iss_tmp_current_sync LIKE iss_sync_entries");
    }

    private function finish_parsing($sync_id, $mode) {
        $clear_previous_sync_sql = "DELETE FROM iss_sync_entries WHERE sync_id = '$sync_id'";
        $this->db->query($clear_previous_sync_sql);
        
        $fields=$this->db->query("SHOW COLUMNS FROM iss_sync_entries");
        $insert_fields='';
        $select_fields='';
        $delimiter='';
        foreach( $fields->rows as $field ){
            if( $field['Field']=='sync_entry_id' ){
                continue;
            }
            $insert_fields.="$delimiter`{$field['Field']}`";
            if($field['Field']=='option_group1'){
                $field['Field']="GROUP_CONCAT(option1 SEPARATOR '|') AS `option_group1`";
            }
            if($field['Field']=='price_group1'){
                $field['Field']="GROUP_CONCAT(price1 SEPARATOR '|') AS `price_group1`";
            }
            if($field['Field']=='is_changed'){
                $field['Field']="1";
            }
            if($field['Field']=='price'){
                $field['Field']="MIN(price1) AS `price`";
            }
            $select_fields.="$delimiter{$field['Field']}";
            $delimiter=',';
        }
        $fill_entries_table_sql = "
            INSERT INTO 
                iss_sync_entries 
                    ($insert_fields)
                SELECT 
                    $select_fields
                FROM 
                    iss_tmp_current_sync
                GROUP BY CONCAT(`category_lvl1`,'/',`category_lvl2`,'/',`category_lvl3`), model";
        $this->db->query($fill_entries_table_sql);
        $this->groupEntriesByCategories($sync_id);
        if ($mode == 'detect_unchanged_entries') {
            $change_finder_sql = "
                UPDATE
                    iss_sync_entries bse
                        JOIN
                    iss_tmp_previous_sync bps USING ($insert_fields)
                SET
                    bse.is_changed=0
                WHERE sync_id='$sync_id'";
            $this->db->query($change_finder_sql);
        }
    }

    public function addSync($seller_id, $sync_source) {
        $sql = "
            INSERT INTO iss_sync_list
                seller_id, sync_source,sync_comission,sync_last_improted
            ON DUPLICATE KEY UPDATE  
                seller_id = $seller_id,
                sync_source = $sync_source,
                sync_comission = '',
                sync_last_improted = ''
            ";
        $this->db->query($sql);
    }

    public function groupEntriesByCategories($sync_id) {
        if (!isset($sync_id)) {
            return;
        }
        $presql = "
            UPDATE 
                iss_sync_groups
            SET 
                total_products = 0 
            WHERE 
                sync_id = '$sync_id'
            ";
        $this->db->query($presql);
        $sql = "
            INSERT INTO
                iss_sync_groups ( sync_id, category_lvl1, category_lvl2, category_lvl3, category_path, total_products )
            SELECT * FROM
                (SELECT 
                    sync_id, category_lvl1, category_lvl2, category_lvl3, CONCAT(category_lvl1,'/',category_lvl2 , '/' , category_lvl3), COUNT(model) AS tp
                FROM 	
                    iss_sync_entries AS bse    
                WHERE bse.sync_id = '$sync_id'
                GROUP BY bse.category_lvl1, bse.category_lvl2, bse.category_lvl3) h
            ON DUPLICATE KEY UPDATE  total_products = tp
            ";
        $this->db->query($sql);
        $clear_empty = "
          DELETE FROM
              iss_sync_groups
          WHERE 
              sync_id='$sync_id' AND total_products=0 
              AND ( destination_categories IS NULL OR NOT destination_categories );
          ";
        $this->db->query($clear_empty);
    }
}
