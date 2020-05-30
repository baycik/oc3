<?php

require_once __DIR__ . "/../parse.php";
$parser_name = 'CSV Table import'; //Label to show to user

class ModelExtensionModuleIssBulksyncParsersUploadCsv extends ModelExtensionModuleIssBulksyncParse {

    public function parse($sync) {
        $this->sync = $sync;
        $this->load->model('tool/upload');
        $source_file = $this->model_tool_upload->getUploadByCode($_FILES[0]);
        $filename = DIR_UPLOAD . $source_file['filename'];
        $sync_id = $sync['sync_id'];
        $csv_array = file($filename);
        foreach ($csv_array as $csv_row) {
            $row = str_getcsv($csv_row, ';');
            $set = "";
            foreach ($this->sync['sync_config']->sources as $field => $index) {
                $set .= ", $field='{$row[$index - 1]}'";
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

}
