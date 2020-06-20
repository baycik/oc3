<?php

class ControllerExtensionModuleIssPriceUpdaterMonitor extends Controller {
    
    private $error = array();

    public function index() {
	$this->document->setTitle("FIYAT GUNCELLEYICISI");
        
        
	$data['back'] = $this->url->link('common/dashboard', '', true);
               
	$url = '';
        
	$data['breadcrumbs'] = array();

	$data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_home'),
                'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );
        $data['user_token'] = $this->session->data['user_token'];
	$data['column_left'] = $this->load->controller('common/column_left');
	$data['column_right'] = $this->load->controller('common/column_right');
	$data['content_top'] = $this->load->controller('common/content_top');
	$data['content_bottom'] = $this->load->controller('common/content_bottom');
	$data['footer'] = $this->load->controller('common/footer');
	$data['header'] = $this->load->controller('common/header');
        
	$this->response->setOutput($this->load->view('extension/module/iss_priceupdater/updater', $data));
    }
}
