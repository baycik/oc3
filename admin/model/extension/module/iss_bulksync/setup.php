<?php

class ModelExtensionModuleIssBulkSyncSetup extends Model {

    public function __construct($registry) {
	parent::__construct($registry);
	$this->language_id = (int) $this->config->get('config_language_id');
	$this->store_id = (int) $this->config->get('config_store_id');
    }

    private $parser_registry=[
        'csv'=>[
            'csv_columns' => [],
            'name'=>'Import table',
            'language_id'=>1,
            'options'=>[],
            'attributes'=>[
                [
                    'field'=>'attribute1',
                    'name'=>'Topraklama',
                    'group_description'=>'Ürün Özellikleri'
                ],
                [
                    'field'=>'attribute2',
                    'name'=>'Çocuk Koruması',
                    'group_description'=>'Ürün Özellikleri'
                ],
                [
                    'field'=>'attribute3',
                    'name'=>'Kapak',
                    'group_description'=>'Ürün Özellikleri'
                ],
                [
                    'field'=>'attribute4',
                    'name'=>'Vidalı Klemens',
                    'group_description'=>'Ürün Özellikleri'
                ],
                [
                    'field'=>'attribute5',
                    'name'=>'Işıklandırma',
                    'group_description'=>'Ürün Özellikleri'
                ],
                [
                    'field'=>'attribute6',
                    'name'=>'UPS',
                    'group_description'=>'Ürün Özellikleri'
                ],
                [
                    'field'=>'attribute7',
                    'name'=>'Tuş sayısı',
                    'group_description'=>'Ürün Özellikleri'
                ],
                [
                    'field'=>'attribute8',
                    'name'=>'Gerilim',
                    'group_description'=>'Ürün Özellikleri'
                ],
                [
                    'field'=>'attribute9',
                    'name'=>'Amperaj',
                    'group_description'=>'Ürün Özellikleri'
                ],
                [
                    'field'=>'attribute10',
                    'name'=>'Plastik',
                    'group_description'=>'Ürün Özellikleri'
                ]
            ],
            'filters'=>[
                [
                    'field'=>'attribute1',
                    'name'=>'Topraklama'
                ],
                [
                    'field'=>'attribute2',
                    'name'=>'Çocuk Koruma'
                ],
                [
                    'field'=>'attribute3',
                    'name'=>'Kapak'
                ],
                [
                    'field'=>'attribute4',
                    'name'=>'Vidalı Klemens'
                ],
                [
                    'field'=>'attribute5',
                    'name'=>'Işıklandırma'
                ],
                [
                    'field'=>'attribute6',
                    'name'=>'UPS'
                ],
                [
                    'field'=>'attribute7',
                    'name'=>'Tuş sayısı'
                ],
                [
                    'field'=>'attribute8',
                    'name'=>'Gerilim'
                ],
                [
                    'field'=>'attribute9',
                    'name'=>'Amperaj'
                ],
                [
                    'field'=>'attribute10',
                    'name'=>'Plastik'
                ]
            ],
            'manufacturer'=>'manufacturer'
        ]
    ];

    public function addParser($user_id,$parser_id){
        $parser_object=$this->parser_registry[$parser_id];
        $parser_config= json_encode($parser_object, JSON_UNESCAPED_UNICODE);
        $this->db->query("INSERT INTO " . DB_PREFIX . "iss_sync_list SET user_id='$user_id', sync_parser_name='{$parser_id}', sync_name='{$parser_object['name']}',sync_config='$parser_config'");
    }
    
    public function getSyncList ( $sync_id=0 ){
        $where="";
        if( $sync_id ){
            $where=" WHERE sync_id='$sync_id'";
        }
        $sql="SELECT * FROM " . DB_PREFIX . "iss_sync_list $where";
        return $this->db->query($sql)->rows;
    }

    public function deleteSync($sync_id){
        $this->db->query("DELETE FROM " . DB_PREFIX . "iss_sync_list WHERE sync_id=".(int) $sync_id );
        $this->db->query("DELETE FROM " . DB_PREFIX . "iss_sync_groups WHERE sync_id=".(int) $sync_id );
        $this->db->query("DELETE FROM " . DB_PREFIX . "iss_sync_entries WHERE sync_id=".(int) $sync_id );
    }
    
    public function updateSync( $sync_id, $sync_name, $parser_config ){
    	if( $sync_id ){
    	    $this->db->query("UPDATE " . DB_PREFIX . "iss_sync_list SET sync_name='$sync_name',sync_config='$parser_config' WHERE sync_id='$sync_id'");
    	}
    }
    
    public function getParserList(){
        $directory = str_replace("\\", "/", __DIR__.'/parsers/');
	$parsers = array_diff(scandir($directory), array('..', '.'));
        
        $allowed_parsers=[];
	foreach ($parsers as $parser_file_name) {
            include __DIR__.'/parsers/'.$parser_file_name;
            $allowed_parsers[$parser_file_name]=$parser_name;
        }
        return $allowed_parsers;
    }
    public function getCategoryList($filter_data) {

	$where = "WHERE sync_id = '{$filter_data['sync_id']}'";
	$order = '';
	$limit = '';

	if (isset($filter_data['filter_name'])) {
	    $where .= " AND category_path LIKE '%{$filter_data['filter_name']}%'";
	}


	if (isset($filter_data['sort'])) {
	    $order = "ORDER BY {$filter_data['sort']} {$filter_data['order']}";
	}

	if (isset($filter_data['start'])) {
	    $limit = "LIMIT {$filter_data['start']} , {$filter_data['limit']}";
	}
	/*
	  if(isset($filter_data['user_id'])){
	  $where .= " AND user_id =  '{$filter_data['user_id']}'";
	  } */
	$sql = "
                SELECT * FROM 
                    " . DB_PREFIX . "iss_sync_groups
                $where
                $order
                $limit
                ";
	$rows = $this->db->query($sql);
	return $rows->rows;
    }

    public function getCategoriesTotal($filter_data) {
	$where = "WHERE sync_id = '{$filter_data['sync_id']}'";
	if ( isset($filter_data['filter_name']) ) {
	    $where .= " AND category_path LIKE '%{$filter_data['filter_name']}%'";
	}
	$sql = "SELECT 
		COUNT(*) AS num 
	    FROM 
               " . DB_PREFIX . "iss_sync_groups  
            $where
            ORDER BY category_lvl1,category_lvl2,category_lvl3";
	$row = $this->db->query($sql);
	return $row->row['num'];
    }
    
    public function saveCategoryPrefs ($data){
        $this->db->query("
            UPDATE " . DB_PREFIX . "iss_sync_entries se
                JOIN " . DB_PREFIX . "iss_sync_groups sg ON 
                    se.category_lvl1 = sg.category_lvl1 
                    AND se.category_lvl2 = sg.category_lvl2 
                    AND se.category_lvl3 = sg.category_lvl3 
            SET 
                se.is_changed = 1
            WHERE
                sg.group_id = ". (int) $data['group_id']);
        $sql = "
            UPDATE 
             " . DB_PREFIX . "iss_sync_groups
            SET
                comission = ". (int) $data['category_comission']. ",
                destination_categories = '". $data['destination_categories']. "'
            WHERE group_id = ". (int) $data['group_id'];
        return $this->db->query($sql);
    }
        
    public function updateDb(){
	$result=$this->db->query("SELECT value FROM ".DB_PREFIX."setting WHERE `key`='iss_bulksync_db_applied_patches'");
	$db_applied_patches=$result->row?$result->row['value']:'';
        $directory = str_replace("\\", "/", __DIR__.'/db_update/');
	$patches = array_diff(scandir($directory), array('..', '.'));
	foreach ($patches as $patch) {
	    $patch_version = str_replace('.sql', '', $patch);
	    if( strpos($db_applied_patches, $patch_version) === false ) {
                $patch_sql=file_get_contents($directory . $patch);
                $this->db->query($patch_sql);
		$db_applied_patches.="|" . $patch_version;
                $this->db->query("DELETE FROM ".DB_PREFIX."setting WHERE `key`='iss_bulksync_db_applied_patches'");
		$this->db->query("INSERT INTO ".DB_PREFIX."setting SET value='$db_applied_patches',`key`='iss_bulksync_db_applied_patches'");
	    }
	}
    }
}
