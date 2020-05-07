<?php
require_once __DIR__."/../parse.php";
$parser_name='Spreadsheet Table import';//Label to show to user

class ModelExtensionModuleIssBulksyncParsersSpreadsheet extends ModelExtensionModuleIssBulksyncParse {
    public function parse( $sync ) {
        return true;
    }      
}