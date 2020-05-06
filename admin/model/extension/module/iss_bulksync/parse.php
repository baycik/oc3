<?php
class ModelExtensionModuleIssBulksyncParse extends Model {
    private $sync_config = '';
    public function __construct($registry) {
	parent::__construct($registry);
	$this->language_id = (int) $this->config->get('config_language_id');
	$this->store_id = (int) $this->config->get('config_store_id');
    }
    
    public function initParser($sync_id,$mode='detect_unchanged_entries'){
        //set_time_limit(300);
        $sync=$this->db->query("SELECT * FROM " . DB_PREFIX . "iss_sync_list WHERE sync_id='$sync_id'")->row;
        if( !$sync ){
            return false;
        }
        
        $this->sync_config = json_decode($sync['sync_config']);
	    $this->prepare_parsing($sync_id, $mode);
	
        

        $parser_method = 'parse_'.$sync['sync_parser_name'];
        if($this->$parser_method($sync)){
            
            $this->finish_parsing($sync_id,$mode);
	
            $this->db->query("UPDATE " . DB_PREFIX . "iss_sync_list SET sync_last_started=NOW() WHERE sync_id='{$sync['sync_id']}'");
            return true;
        } else {
            die('Error while parsing '.$parser_method.'!');
        };       
    }
    
    private function prepare_parsing($sync_id, $mode){
	    $this->db->query("DROP TEMPORARY TABLE IF EXISTS iss_tmp_previous_sync");#TEMPORARY
	    $this->db->query("CREATE TEMPORARY TABLE iss_tmp_previous_sync AS (SELECT * FROM " . DB_PREFIX . "iss_sync_entries WHERE sync_id='$sync_id')");

	    $this->db->query("DROP TEMPORARY TABLE IF EXISTS iss_tmp_current_sync");#TEMPORARY
	    $this->db->query("CREATE TEMPORARY TABLE iss_tmp_current_sync LIKE ".DB_PREFIX."iss_sync_entries");
        
        if($mode == 'partial_parse'){
            $this->db->query("INSERT INTO iss_tmp_current_sync SELECT * FROM ".DB_PREFIX."iss_sync_entries");
        }
    }
    private function finish_parsing($sync_id,$mode){
        /*
        if(isset($this->sync_config->csv_columns)){
            $changed_columns = $this->sync_config->csv_columns;
            $changed_columns[] = 'sync_entry_id';
            $sync_entries_structure = $this->db->query("SELECT * FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = '".DB_PREFIX."iss_sync_entries'")->rows;
            $sync_entries_columns = [];
            foreach($sync_entries_structure as $column){
                array_push($sync_entries_columns, $column['COLUMN_NAME']);
            }
            $unchanged_columns = array_diff($sync_entries_columns, $changed_columns);
            foreach($unchanged_columns as &$column){
                $column = 'cse.'.$column.' = bse.'.$column;
            }
            $set = implode(', ', $unchanged_columns);
            $sql = "
                UPDATE      
                    iss_tmp_current_sync cse
                                JOIN
                    ".DB_PREFIX."iss_sync_entries bse USING (`model`)
                SET $set
                WHERE bse.sync_id='$sync_id'"; 
            $this->db->query($sql);
        } */
	    $clear_previous_sync_sql = "DELETE FROM ".DB_PREFIX."iss_sync_entries WHERE sync_id = '$sync_id'";
	    $this->db->query($clear_previous_sync_sql);
        $fill_entries_table_sql = "
            INSERT INTO 
                ".DB_PREFIX."iss_sync_entries 
                    (`is_changed`,`sync_id` , `category_lvl1` , `category_lvl2` , `category_lvl3` , `product_name` , `model` , `ean` , `mpn`, `url` , `description` , `min_order_size` , `leftovers`,`stock_count` , `stock_status` , `manufacturer` , `origin_country` , `attribute1` , `attribute2` , `attribute3` , `attribute4` , `attribute5` ,  `attribute6` , `attribute7` , `attribute8` , `attribute9` , `attribute10` , `attribute11` , `attribute12` ,`attribute_group` ,  `image` , `image1` , `image2` , `image3` , `image4` , `image5` , `price1` , `price2` , `price3` , `price4` ,product_name1, product_name2, product_name3, `option_group1`,`price_group1`,`price`)
                SELECT          1,`sync_id` , `category_lvl1` , `category_lvl2` , `category_lvl3` , `product_name` , `model` , `ean` , `mpn`, `url` , `description` , `min_order_size` , `leftovers` , `stock_count` , `stock_status` , `manufacturer` , `origin_country` , `attribute1` , `attribute2` , `attribute3` , `attribute4` , `attribute5` ,  `attribute6` , `attribute7` , `attribute8` , `attribute9` , `attribute10` , `attribute11` , `attribute12` ,`attribute_group` , `image` , `image1` , `image2` , `image3` , `image4` , `image5` , `price1` , `price2` , `price3` , `price4` ,product_name1, product_name2, product_name3 ,
                    GROUP_CONCAT(option1 SEPARATOR '|') AS `option_group1`,
                    GROUP_CONCAT(price1 SEPARATOR '|') AS `price_group1`,
                    MIN(price1) AS `price`
                FROM 
                    iss_tmp_current_sync
                GROUP BY CONCAT(`category_lvl1`,'/',`category_lvl2`,'/',`category_lvl3`), model
                #HAVING price>0 AND price IS NOT NULL";
	    $this->db->query($fill_entries_table_sql);
        $this->groupEntriesByCategories($sync_id);
        if( $mode=='detect_unchanged_entries' ){
            $change_finder_sql="
                UPDATE
                    ".DB_PREFIX."iss_sync_entries bse
                        JOIN
                    iss_tmp_previous_sync bps USING (`sync_id` , `category_lvl1` , `category_lvl2` , `category_lvl3` , `product_name` , `model` , `ean` , `mpn` , `url` , `description` , `min_order_size` , `leftovers` , `stock_count` , `stock_status` , `manufacturer` , `origin_country` , `attribute1` , `attribute2` , `attribute3` , `attribute4` , `attribute5` , `attribute6` , `attribute7` , `attribute8` , `attribute9` , `attribute10`, `attribute11` ,`attribute_group`,`option1` , `option2` , `option3` , `image` , `image1` , `image2` , `image3` , `image4` , `image5` , `price1` , `price2` , `price3` , `price4`,product_name1, product_name2, product_name3)
                SET
                    bse.is_changed=0
                WHERE sync_id='$sync_id'";
            $this->db->query($change_finder_sql);
        }
    }
    
    public function parse_csv($sync) {
        $this->load->model('tool/upload');
        $source_file = $this->model_tool_upload->getUploadByCode($_FILES[0]);
        $filename = DIR_UPLOAD . $source_file['filename'];
        
        function fetch( $row, $key ){
            $fields=[
                'lvl1'=>0,
                'lvl2'=>1,
                'lvl3'=>2,
                'ean'=>3,
                'model'=>4,
                'name'=>5,
                'price'=>6,
                'stock_count'=>7,
                'manufacturer'=>8,
                'origin_country'=>9,
                'min_order_size'=>10,
                'description'=>11,
                'attribute1'=>11,
                'attribute2'=>12,
                'attribute3'=>13,
                'attribute4'=>14,
                'attribute5'=>15,
                'attribute6'=>16,
                'attribute7'=>17,
                'attribute8'=>18,
                'attribute9'=>19,
                'attribute10'=>20,
                'attribute11'=>null,
                'attribute12'=>null
            ];
            $value=isset($fields[$key]) && isset($row[$fields[$key]])?$row[$fields[$key]]:'';
            return addslashes($value);
        }
        

        
	    $sync_id = $sync['sync_id'];
        $csv_array = file($filename);
        foreach($csv_array as $csv_row){
            $row = str_getcsv($csv_row, ';');
            if( !fetch( $row, 'model' ) ){
                continue;
            }
            $sql = "
                INSERT INTO 
                    iss_tmp_current_sync
                SET
                    sync_id = '$sync_id',  
                    category_lvl1 = '".fetch( $row, 'lvl1' )."', 
                    category_lvl2 = '".fetch( $row, 'lvl2' )."',
                    category_lvl3 = '".fetch( $row, 'lvl3' )."',
                    product_name = '".$this->db->escape( fetch( $row, 'name' ) )."', 
                    model = '".fetch( $row, 'model' )."', 
                    ean = '".fetch( $row, 'ean' )."',
                    stock_count = '".fetch( $row, 'stock_count' )."',
                    manufacturer = '".fetch( $row, 'manufacturer' )."', 
                    attribute1 = '".fetch( $row, 'attribute1' )."',
                    attribute2 = '".fetch( $row, 'attribute2' )."',
                    attribute3 = '".fetch( $row, 'attribute3' )."',
                    attribute4 = '".fetch( $row, 'attribute4' )."',
                    attribute5 = '".fetch( $row, 'attribute5' )."',
                    attribute6 = '".fetch( $row, 'attribute6' )."',
                    attribute7 = '".fetch( $row, 'attribute7' )."',
                    attribute8 = '".fetch( $row, 'attribute8' )."',
                    attribute9 = '".fetch( $row, 'attribute9' )."',
                    attribute10 = '".fetch( $row, 'attribute10' )."',
                    price1 = REPLACE(REPLACE('".fetch( $row, 'price' )."', ' ', ''), ',', '.'),
                    origin_country = '".fetch( $row, 'origin_country' )."',
                    min_order_size = '".fetch( $row, 'min_order_size' )."', 
                    image = CONCAT('catalog/products/','".fetch( $row, 'model' )."','.jpg')
                ";
            try{
                $this->db->query($sql);
            }
            catch(Exception $e){
                echo $e->getMessage();
                die($sql);
            }
        }
        
                            
        return true;
    }  
    
    
    public function addSync($seller_id, $sync_source){
        $sql = "
            INSERT INTO " . DB_PREFIX . "iss_sync_list
                seller_id, sync_source,sync_comission,sync_last_improted
            ON DUPLICATE KEY UPDATE  
                seller_id = $seller_id,
                sync_source = $sync_source,
                sync_comission = '',
                sync_last_improted = ''
            ";
        $this->db->query($sql);
    }


    public function groupEntriesByCategories ($sync_id){
        if( !isset($sync_id) ){
            return;
        }
        $presql = "
            UPDATE " . DB_PREFIX . "iss_sync_groups
            SET total_products = 0 
            WHERE sync_id = '$sync_id'
            ";
        $this->db->query($presql);
        $sql = "
            INSERT INTO
                " . DB_PREFIX . "iss_sync_groups ( sync_id, category_lvl1, category_lvl2, category_lvl3, category_path, total_products )
            SELECT * FROM
                (SELECT 
                    sync_id, category_lvl1, category_lvl2, category_lvl3, CONCAT(category_lvl1,'/',category_lvl2 , '/' , category_lvl3), COUNT(model) AS tp
                FROM 	
                    " . DB_PREFIX . "iss_sync_entries AS bse    
                WHERE bse.sync_id = '$sync_id'
                GROUP BY bse.category_lvl1, bse.category_lvl2, bse.category_lvl3) hello_vasya
            ON DUPLICATE KEY UPDATE  total_products = tp
            ";
        $this->db->query($sql);
        /*$clear_empty="
            DELETE FROM 
                " . DB_PREFIX . "iss_sync_groups 
            WHERE sync_id='$sync_id' AND total_products=0;
            ";
        $this->db->query($clear_empty);*/
    }
    
    private function removeTempDir($tmp_prefix){
        $dir = './'.$tmp_prefix.'tmp_files';
        $files = array_diff(scandir($dir), array('.','..'));
        foreach ($files as $file) {
          (is_dir("$dir/$file")) ? delTree("$dir/$file") : unlink("$dir/$file");
        }
        return rmdir($dir); 
    }
}
