<?php
require_once __DIR__."/../parse.php";
$parser_name='CSV Table import';//Label to show to user

class ModelExtensionModuleIssBulksyncParsersCsv extends ModelExtensionModuleIssBulksyncParse {

    public function parse( $sync ) {
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
}