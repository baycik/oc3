<?php

class ControllerExtensionModuleIssPriceUpdater extends Controller {
    
    function index(){
        $kur=0;
        $xml  = simplexml_load_file("http://www.tcmb.gov.tr/kurlar/today.xml");
        foreach($xml->Currency as $Currency){
            if( $Currency->CurrencyName=="US DOLLAR" ){
                $kur=$Currency->BanknoteSelling;
            }
        }
        if( $kur ){
            echo "www.tcmb.gov.tr deki kur: <b>$kur</b><br>";
            $this->db->query("UPDATE oc_product SET price=mpn*$kur WHERE mpn");
            echo $this->db->countAffected()." ürünün fiyatı güncellendi";
        } else {
            echo "KUR www.tcmb.gov.tr'den alınamadı !!!<br>";
        }
    }
}