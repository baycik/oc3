<?php

class ControllerExtensionModuleIssBulkSyncSetup extends Controller {
    
    private $error = array();

    public function index() {
        $data = [];
        $data['language'] = [];
        $data['language'] = array_merge($data['language'], $this->load->language('extension/module/iss_bulksync/setup'));
        $data['language'] = array_merge($data['language'], $this->load->language('extension/module/iss_bulksync/fields'));


	$this->document->setTitle($this->language->get('heading_title'));
        
        
	$data['back'] = $this->url->link('extension/module/iss_bulksync_setup', '', true);
               
	$url = '';
        
        $data['token_name'] = 'user_token';
        if(!empty($this->session->data['token'])){
            $data['token_name'] = 'token';
            $this->session->data['user_token'] = $this->session->data['token'];
        }
        
	$data['breadcrumbs'] = array();
        
	$data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_home'),
                'href' => $this->url->link('common/dashboard', $data['token_name'].'=' . $this->session->data['user_token'], true)
        );

	$data['breadcrumbs'][] = array(
	    'text' => $this->language->get('heading_title_parser'),
	    'href' => $this->url->link('extension/module/iss_bulksync_setup', $url, true)
	);
        $user_id = $this->customer->getId();
        $data['user_token'] = $this->session->data['user_token'];
	$data['heading_title'] = $this->language->get('heading_title');
	$data['column_left'] = $this->load->controller('common/column_left');
	$data['column_right'] = $this->load->controller('common/column_right');
	$data['content_top'] = $this->load->controller('common/content_top');
	$data['content_bottom'] = $this->load->controller('common/content_bottom');
	$data['footer'] = $this->load->controller('common/footer');
	$data['header'] = $this->load->controller('common/header');
	$data['user_id'] = $user_id;
        
        
        $this->load->model('localisation/language');
        $data['language_list']=$this->model_localisation_language->getLanguages();
        $this->load->model('localisation/tax_class');
        $data['tax_class_list']=$this->model_localisation_tax_class->getTaxClasses();
        
        
        
        
        $data['source_column_options']=$this->getColumnNumbers();
        
	$this->load->model('extension/module/iss_bulksync/setup');
        $this->model_extension_module_iss_bulksync_setup->updateDb();
        $data['sync_list'] = $this->model_extension_module_iss_bulksync_setup->getSyncList();
        $data['parser_list'] = $this->model_extension_module_iss_bulksync_setup->getParserList();
       
	$this->response->setOutput($this->load->view('extension/module/iss_bulksync/setup', $data));
    }
    
    private function getColumnNumbers(){
        $options="<option value=''>-</option>";
        $num=0;
        foreach( ['', 'A'] as $lett1){
            foreach( range('A', 'Z') as $lett2) {
                $num++;
                $options.="<option value='$num'>$num-{$lett1}{$lett2}</option>";
            }
        }
        return $options;
    }
       
    public function startParsing(){
        if( isset($this->request->post['code']) ){
            $_FILES[0] = $this->request->post['code'];
        }
        $sync_id = $this->request->post['sync_id'];
        if( !$sync_id ){
            echo "Source hasn't been selected";
            return false;
        }
        $this->load->model('extension/module/iss_bulksync/setup');
        $sync_list=$this->model_extension_module_iss_bulksync_setup->getSyncList($sync_id);
        if( $sync_list[0] ){
            $sync=$sync_list[0];
            $sync['sync_config']=json_decode($sync['sync_config']);
            $sync_parser_name=$sync['sync_parser_name'];
            $this->load->model("extension/module/iss_bulksync/parsers/$sync_parser_name");
            echo $this->{"model_extension_module_iss_bulksync_parsers_".$sync_parser_name}->initParser($sync_id);
        }
//        if( !isset($this->request->post['code']) ){
//            $this->load->model('extension/module/iss_bulksync/setup');
//            echo $this->model_extension_module_iss_bulksync_setup->updateParserConfig($sync_id);
//        }
        
    }
    
       
    public function addParser(){
        $sync_parser_name = $this->request->post['sync_parser_name'];
        $sync_name = $this->request->post['sync_name'];
        if( !$sync_parser_name ){
            echo "No parser selected";
            return;
        }
        $this->load->model('extension/module/iss_bulksync/setup');
        $this->model_extension_module_iss_bulksync_setup->addParser($sync_parser_name,$sync_name);
        
        $this->load->model("extension/module/iss_bulksync/parsers/$sync_parser_name");
        $this->{"model_extension_module_iss_bulksync_parsers_".$sync_parser_name}->install();
        $this->response->redirect($this->url->link('extension/module/iss_bulksync_setup', $data['token_name'].'=' . $this->session->data['user_token'], true));
    }
    
    public function deleteParser(){
        $sync_id = $this->request->post['sync_id'];
        if( !$sync_id ){
            echo "No sync selected";
            return;
        }
        $this->load->model('extension/module/iss_bulksync/setup');
        $this->model_extension_module_iss_bulksync_setup->deleteSync( $sync_id );
    }
    
    public function syncConfigGet(){
        $sync_id = $this->request->request['sync_id'];
        if( !$sync_id ){
            echo "No sync selected";
            return;
        }
        $this->load->model('extension/module/iss_bulksync/setup');
        $sync=$this->model_extension_module_iss_bulksync_setup->getSyncList( $sync_id );
        if( isset($sync[0]) && $sync[0]['sync_config'] ){
            echo "{\"sync_name\":\"{$sync[0]['sync_name']}\",\"sync_config\":{$sync[0]['sync_config']}}";
            exit;
        }
        echo "{\"sync_name\":\"\",\"sync_config\":\"\"}";
        //header("Content-type:text/json");
        //echo json_encode(json_decode("{\"sync_name\":\"{$sync[0]['sync_name']}\",\"sync_config\":{$sync[0]['sync_config']}}"),JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
        
    }
    public function syncConfigSave(){
        $config_json = $this->request->post['config'];
        $sync_id = $this->request->post['sync_id'];
        if( empty($config_json) ){
            echo "No config supplied";
            return;
        }
        $config= json_decode( html_entity_decode ($config_json) );
        $sync_name=$config->sync_name;
        $this->load->model('extension/module/iss_bulksync/setup');
        echo $this->model_extension_module_iss_bulksync_setup->updateSync( $sync_id, $sync_name, $config->sync_config );
    }
}
