<?php

class ControllerExtensionModuleIssBulkSyncSetup extends Controller {
    
    private $error = array();

    public function index() {
        $this->load->language('extension/module/iss_bulksync/setup');

	$this->document->setTitle($this->language->get('heading_title'));
        
        
	$data['back'] = $this->url->link('extension/module/iss_bulksync_setup', '', true);
               
	$url = '';
        
	$data['breadcrumbs'] = array();

	$data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_home'),
                'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

	$data['breadcrumbs'][] = array(
	    'text' => $this->language->get('heading_title'),
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
        $data['source_column_options']=$this->getColumnNumbers();
        
	$this->load->model('extension/module/iss_bulksync/setup');
        $this->model_extension_module_iss_bulksync_setup->updateDb();
        $data['sync_list'] = $this->model_extension_module_iss_bulksync_setup->getSyncList($user_id);
        $data['parser_list'] = $this->model_extension_module_iss_bulksync_setup->getParserList($user_id);
       
	$this->response->setOutput($this->load->view('extension/module/iss_bulksync/setup', $data));
    }
    
    private function getColumnNumbers(){
        $options="<option value='-'>-</option>";
        foreach( range('A', 'Z') as $i=>$lett) {
            $num=$i+1;
            $options.="<option value='$num-$lett'>$num-$lett</option>";
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
            return;
        }
        
        if( !isset($this->request->post['code']) ){
            $this->load->model('extension/module/iss_bulksync/setup');
            echo $this->model_extension_module_iss_bulksync_setup->updateParserConfig($sync_id);
        }
        
        $this->load->model('extension/module/iss_bulksync/parse');
        echo $this->model_extension_module_iss_bulksync_parse->initParser($sync_id,'update_all_entries');
    }
    
       
    public function addParser(){
        $parser_id = $this->request->post['parser_id'];
        if( !$parser_id ){
            echo "No parser selected";
            return;
        }
        $seller_id = $this->customer->getId();
        $this->load->model('extension/module/iss_bulksync/setup');
        $this->model_extension_module_iss_bulksync_setup->addParser($seller_id,$parser_id);
        $this->response->redirect($this->url->link('extension/module/iss_bulksync_setup', 'user_token=' . $this->session->data['user_token'], true));
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
        echo "{\"sync_name\":\"{$sync[0]['sync_name']}\",\"sync_config\":{$sync[0]['sync_config']}}";
    }
}
