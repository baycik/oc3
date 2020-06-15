<?php

require_once __DIR__ . "/../parse.php";
$parser_name = 'CSV Table import'; //Label to show to user

class ModelExtensionModuleIssBulksyncParsersUploadCsv extends ModelExtensionModuleIssBulksyncParse {
    
    public function install(){
        $this->load->model('setting/setting');
        
        $ext=$this->config->get('config_file_ext_allowed');
        if( strpos($ext,'csv')===false ){
            $this->model_setting_setting->editSettingValue('config','config_file_ext_allowed',$ext."\ncsv");
        }
        
        $mime=$this->config->get('config_file_mime_allowed');
        if( strpos($mime,'application/vnd.ms-excel')===false ){
            $this->model_setting_setting->editSettingValue('config','config_file_mime_allowed',$mime."\napplication/vnd.ms-excel");
        }
        
        $mime=$this->config->get('config_file_mime_allowed');
        if( strpos($mime,'text/csv')===false ){
            $this->model_setting_setting->editSettingValue('config','config_file_mime_allowed',$mime."\ntext/csv");
        }
    }
    
    public function uninstall(){
        
    }

    public function parse($sync) {
        $this->sync = $sync;
        $sync_id = $sync['sync_id'];
        if(!empty($this->sync['sync_config']->source_file)){
            $source_file = $this->sync['sync_config']->source_file;
            $sync_name = $this->sync['sync_config']->sync_name;
            $filename = './'.$sync_name.rand(0,1000);
            if(!copy($source_file, $filename)){
                die("Downloading failed");
            };
            $csv_array = file($filename);
        } else {
            $this->load->model('tool/upload');
            $source_file = $this->model_tool_upload->getUploadByCode($_FILES[0]);
            $filename = DIR_UPLOAD . $source_file['filename'];
            $csv_array = file($filename);
            $this->model_tool_upload->deleteUpload($source_file['upload_id']);
        }
        unlink($filename);
        foreach ($csv_array as $csv_row) {
            if(strpos($csv_row, '<!') !== false){
                $csv_header_start = strpos($csv_row, '<!')+2;
                $csv_header_end = strpos($csv_row, '!>');
                $csv_header = substr($csv_row, $csv_header_start, $csv_header_end-$csv_header_start);
                $this->handleCsvHeader($csv_header, $this->sync);
                continue;
            }
            $row = str_getcsv($csv_row, ';');
            $set = "";
            foreach ($this->sync['sync_config']->sources as $field => $index) {
                if(isset($row[$index - 1])){
                    $set .= ", $field='{$row[$index - 1]}'";
                }
            }
            $sql = "
                INSERT INTO 
                    iss_tmp_current_sync
                SET
                    sync_id = '$sync_id'
                    $set
                ";
            try {
                $this->db->query($sql);
            } catch (Exception $e) {
                echo $e->getMessage();
                die($sql);
            }
        }
        return true;
    }
    private function handleCsvHeader($csv_header, $sync) {
        if(!empty($sync['sync_config']->source_file) && strpos($sync['sync_config']->source_file, '\\') !== false){
            $sync['sync_config']->source_file = str_replace('\\', '\\\\', $sync['sync_config']->source_file);
        }
        
        $sync['sync_config']->attributes = $this->verifyLocalConfig($sync['sync_config']->attributes, $csv_header);
        $this->db->query("UPDATE iss_sync_list SET sync_config = '".json_encode($sync['sync_config'], JSON_UNESCAPED_UNICODE )."' WHERE sync_id='{$sync['sync_id']}'");
        return;
    }
    
    private function verifyLocalConfig($db_config_attributes, $csv_header){
        $attribute_group_list = $this->prepareAttributeGroup($csv_header);
        if(empty($db_config_attributes)){
           $db_config_attributes = $attribute_group_list;
        } else {
            $db_attribute_names = $this->getDbAttributesNames($db_config_attributes);
            foreach($attribute_group_list as $attribute){
                if(strpos($db_attribute_names, $attribute['name']) !== false){
                    continue;
                }
                array_push($db_config_attributes,$attribute);
            }
        } 
        return $db_config_attributes;
    }
    
    private function prepareAttributeGroup($attribute_group_template){
        $attribute_group_list = [];
        $csv_attributes = explode(',', $attribute_group_template);
        foreach($csv_attributes as $index => $attribute){
            if(strpos($attribute, '|') === false){
                continue;
            }
            $attribute_object = [
                'field' => 'attribute_group',
                'name' => explode('|', $attribute)[0],
                'group_description' => explode('|', $attribute)[1],
                'index' => $index
            ];
            array_push($attribute_group_list,$attribute_object);
        }
        return $attribute_group_list;
    }

    private function getDbAttributesNames($db_config_attributes){
        $db_attribute_names = '';
        foreach($db_config_attributes as $attribute){
            $db_attribute_names .= $attribute->name;
        }
        return $db_attribute_names;
    }
}
