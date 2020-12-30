<?php

class ControllerExtensionModuleIssLogin extends Controller {

    private $error = array();

    public function index() {

        $this->load->language('extension/module/iss_login');
        // Login override for admin users
        if (!empty($this->request->get['token'])) {
            $this->customer->logout();
            $this->cart->clear();

            unset($this->session->data['order_id']);
            unset($this->session->data['payment_address']);
            unset($this->session->data['payment_method']);
            unset($this->session->data['payment_methods']);
            unset($this->session->data['shipping_address']);
            unset($this->session->data['shipping_method']);
            unset($this->session->data['shipping_methods']);
            unset($this->session->data['comment']);
            unset($this->session->data['coupon']);
            unset($this->session->data['reward']);
            unset($this->session->data['voucher']);
            unset($this->session->data['vouchers']);

            $customer_info = $this->model_account_customer->getCustomerByToken($this->request->get['token']);

            if ($customer_info && $this->customer->login($customer_info['email'], '', true)) {
                // Default Addresses
                $this->load->model('account/address');

                if ($this->config->get('config_tax_customer') == 'payment') {
                    $this->session->data['payment_address'] = $this->model_account_address->getAddress($this->customer->getAddressId());
                }

                if ($this->config->get('config_tax_customer') == 'shipping') {
                    $this->session->data['shipping_address'] = $this->model_account_address->getAddress($this->customer->getAddressId());
                }

                $this->response->redirect($this->url->link('account/account', '', true));
            }
        }

        if ($this->customer->isLogged()) {
            $this->response->redirect($this->url->link('account/account', '', true));
        }

        $this->document->setTitle($this->language->get('heading_title'));


        // Added strpos check to pass McAfee PCI compliance test (http://forum.opencart.com/viewtopic.php?f=10&t=12043&p=151494#p151295)
        if (isset($this->request->post['redirect']) && (strpos($this->request->post['redirect'], $this->config->get('config_url')) !== false || strpos($this->request->post['redirect'], $this->config->get('config_ssl')) !== false)) {
            $data['redirect'] = $this->request->post['redirect'];
        } elseif (isset($this->session->data['redirect'])) {
            $data['redirect'] = $this->session->data['redirect'];

            unset($this->session->data['redirect']);
        } else {
            $data['redirect'] = '';
        }


        /* ========================= */
        /* ==========LOGIN========== */
        /* ========================= */

        if (isset($this->session->data['iss_login']['telephone'])) {
            $data['iss_login']['telephone'] = $this->session->data['iss_login']['telephone'];
        } else {
            $data['iss_login']['telephone'] = '';
        }

        if (isset($this->session->data['iss_login']['password'])) {
            $data['iss_login']['password'] = $this->session->data['iss_login']['password'];
        } else {
            $data['iss_login']['password'] = '';
        }
        
        
        if (isset($this->session->data['iss_login']['error_warning'])) {
            $data['iss_login']['error_warning'] = $this->session->data['iss_login']['error_warning'];
        } else {
            $data['iss_login']['error_warning'] = '';
        }
        $data['text_account_already'] = sprintf($this->language->get('text_account_already'), $this->url->link('account/login', '', true));

        /* ========================= */
        /* =========REGISTER======== */
        /* ========================= */
        if (isset($this->session->data['iss_register']['success'])) {
            $data['iss_register']['success'] = $this->session->data['iss_register']['success'];
            unset($this->session->data['iss_register']['success']);
        } else {
            $data['iss_register']['success'] = '';
        }
        /* =========ERROR======== */
        if (isset($this->session->data['iss_register']['error_warning'])) {
            $data['iss_register']['error_warning'] = $this->session->data['iss_register']['error_warning'];
        } else {
            $data['iss_register']['error_warning'] = '';
        }
        if (isset($this->session->data['iss_register']['error_firstname'])) {
            $data['iss_register']['error_firstname'] = $this->session->data['iss_register']['error_firstname'];
        } else {
            $data['iss_register']['error_firstname'] = '';
        }
        /*
          if (isset($this->session->data['iss_register']['error_storename'])) {
          $data['iss_register']['error_storename'] = $this->session->data['iss_register']['error_storename'];
          } else {
          $data['iss_register']['error_storename'] = '';
          }
          if (isset($this->session->data['iss_register']['error_email'])) {
          $data['iss_register']['error_email'] = $this->session->data['iss_register']['error_email'];
          } else {
          $data['iss_register']['error_email'] = '';
          } */
        if (isset($this->session->data['iss_register']['error_telephone'])) {
            $data['iss_register']['error_telephone'] = $this->session->data['iss_register']['error_telephone'];
        } else {
            $data['iss_register']['error_telephone'] = '';
        }
        if (isset($this->session->data['iss_register']['error_agree'])) {
            $data['iss_register']['error_agree'] = $this->session->data['iss_register']['error_agree'];
        } else {
            $data['iss_register']['error_agree'] = '';
        }
        if (isset($this->session->data['iss_register']['error_confirm'])) {
            $data['iss_register']['error_confirm'] = $this->session->data['iss_register']['error_confirm'];
        } else {
            $data['iss_register']['error_confirm'] = '';
        }

        /* =========DATA======== */

        if (isset($this->session->data['iss_register']['firstname'])) {
            $data['iss_register']['firstname'] = $this->session->data['iss_register']['firstname'];
        } else {
            $data['iss_register']['firstname'] = '';
        }
        if (isset($this->session->data['iss_register']['storename'])) {
            $data['iss_register']['storename'] = $this->session->data['iss_register']['storename'];
        } else {
            $data['iss_register']['storename'] = '';
        }
        if (isset($this->session->data['iss_register']['email'])) {
            $data['iss_register']['email'] = $this->session->data['iss_register']['email'];
        } else {
            $data['iss_register']['email'] = '';
        }
        if (isset($this->session->data['iss_register']['telephone'])) {
            $data['iss_register']['telephone'] = $this->session->data['iss_register']['telephone'];
        } else {
            $data['iss_register']['telephone'] = '';
        }
        if (isset($this->session->data['iss_register']['agree'])) {
            $data['iss_register']['agree'] = $this->session->data['iss_register']['agree'];
        } else {
            $data['iss_register']['agree'] = false;
        }
        if (isset($this->request->post['confirm'])) {
            $data['confirm'] = $this->request->post['confirm'];
        } else {
            $data['confirm'] = '';
        }
        // Captcha
        if ($this->config->get('captcha_' . $this->config->get('config_captcha') . '_status') && in_array('register', (array) $this->config->get('config_captcha_page'))) {
            $data['captcha'] = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha'), $this->error);
        } else {
            $data['captcha'] = '';
        }

        if ($this->config->get('config_account_id')) {
            $this->load->model('catalog/information');

            $information_info = $this->model_catalog_information->getInformation($this->config->get('config_account_id'));

            if ($information_info) {
                $data['text_agree'] = sprintf($this->language->get('text_agree'), $this->url->link('information/information/agree', 'information_id=' . $this->config->get('config_account_id'), true), $information_info['title'], $information_info['title']);
            } else {
                $data['text_agree'] = '';
            }
        } else {
            $data['text_agree'] = '';
        }


        $data['action_login'] = $this->url->link('extension/module/iss_login/login', '', true);

        $data['action_register'] = $this->url->link('extension/module/iss_login/register', '', true);


        $data['column_left'] = $this->load->controller('common/column_left');
        $data['column_right'] = $this->load->controller('common/column_right');
        $data['content_top'] = $this->load->controller('common/content_top');
        $data['content_bottom'] = $this->load->controller('common/content_bottom');
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');
        $this->response->setOutput($this->load->view('extension/module/iss_login', $data));
    }

    public function login() {
        $this->load->language('extension/module/iss_login');
        if ($this->validateLogın()) {
            unset($this->session->data['guest']);
            $this->load->model('account/address');
            if ($this->config->get('config_tax_customer') == 'payment') {
                $this->session->data['payment_address'] = $this->model_account_address->getAddress($this->customer->getAddressId());
            }
            if ($this->config->get('config_tax_customer') == 'shipping') {
                $this->session->data['shipping_address'] = $this->model_account_address->getAddress($this->customer->getAddressId());
            }
            // Wishlist
            if (isset($this->session->data['wishlist']) && is_array($this->session->data['wishlist'])) {
                $this->load->model('account/wishlist');
                foreach ($this->session->data['wishlist'] as $key => $product_id) {
                    $this->model_account_wishlist->addWishlist($product_id);
                    unset($this->session->data['wishlist'][$key]);
                }
            }
            $this->session->data['iss_register'] = [];
            $this->session->data['iss_login'] = [];
            // Added strpos check to pass McAfee PCI compliance test (http://forum.opencart.com/viewtopic.php?f=10&t=12043&p=151494#p151295)
            if (isset($this->request->post['redirect']) && $this->request->post['redirect'] != $this->url->link('account/logout', '', true) && (strpos($this->request->post['redirect'], $this->config->get('config_url')) !== false || strpos($this->request->post['redirect'], $this->config->get('config_ssl')) !== false)) {
                $this->response->redirect(str_replace('&amp;', '&', $this->request->post['redirect']));
            } else {
                $this->response->redirect($this->url->link('common/home', '', true));
            }
        }
        $this->response->redirect($this->url->link('extension/module/iss_login', '', true));
    }

    public function register() {
        if ($this->request->post['firstname']) {
            $this->session->data['iss_register']['firstname'] = $this->request->post['firstname'];
        }
        if ($this->request->post['storename']) {
            $this->session->data['iss_register']['storename'] = $this->request->post['storename'];
        }
        if ($this->request->post['telephone']) {
            $this->session->data['iss_register']['telephone'] = $this->request->post['telephone'];
        }
        if ($this->request->post['email']) {
            $this->session->data['iss_register']['email'] = $this->request->post['email'];
        }
        if ($this->request->post['agree']) {
            $this->session->data['iss_register']['agree'] = $this->request->post['agree'];
        }
        $this->load->language('extension/module/iss_login');
        $this->load->model('account/customer');
        if ($this->validateRegıster()) {
            $register_data = $this->session->data['iss_register'];
            $register_data['storename'] = '-----';
            $register_data['lastname'] = $register_data['storename'];
            $register_data['email'] = $this->config->get('config_email');
            $register_data['password'] = $this->generatePassword();
            $customer_id = $this->model_extension_module_iss_login->addCustomer($register_data);
            if ($customer_id) {
                $message = sprintf($this->language->get('text_sms_register'), $register_data['firstname'], $register_data['password']);
                $ok = $this->sendSms($register_data['telephone'], $message);
                $this->session->data['iss_register'] = [];
                $this->session->data['iss_login'] = [];
                $this->session->data['iss_register']['success'] = sprintf($this->language->get('text_register_success'), $register_data['firstname']);
            }
        }
        $this->response->redirect($this->url->link('extension/module/iss_login', '', true));
    }

    private function validateRegıster() {
        $this->load->model('account/customer');
        $this->load->model('extension/module/iss_login');
        $error = false;
        if ((utf8_strlen(trim($this->session->data['iss_register']['firstname'])) < 1) || (utf8_strlen(trim($this->session->data['iss_register']['firstname'])) > 32)) {
            $this->session->data['iss_register']['error_firstname'] = $this->language->get('error_firstname');
            $error = true;
        } else {
            unset($this->session->data['iss_register']['error_firstname']);
        }
        
        $validated_phone = $this->validateTelephone($this->session->data['iss_register']['telephone']);
        if (!$validated_phone) {
            $this->session->data['iss_register']['error_warning'] = $this->language->get('error_wrong_telephone');
            $error = true;
        } else {
            $this->session->data['iss_register']['telephone'] = $validated_phone;
        }
        
        if ($this->model_extension_module_iss_login->getTotalCustomers($this->session->data['iss_register']['telephone'])) {
            $this->session->data['iss_register']['error_telephone'] = $this->language->get('error_exists');
            $error = true;
        } else {
            unset($this->session->data['iss_register']['error_telephone']);
        }

        if (!$this->session->data['iss_register']['agree']) {
            $this->session->data['iss_register']['error_agree'] = $this->language->get('error_agree');
            $error = true;
        } else {
            unset($this->session->data['iss_register']['error_agree']);
        }
        if ($this->config->get('captcha_' . $this->config->get('config_captcha') . '_status') && in_array('register', (array) $this->config->get('config_captcha_page'))) {
            $captcha = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha') . '/validate');

            if ($captcha) {
                $this->session->data['iss_register']['captcha'] = $captcha;
            } else {
                $this->session->data['iss_register']['iss_error_captcha'] = $this->language->get('error_captcha');
                $error = true;
            }
        }
        $this->session->data['iss_login']['error_warning'] = [];
        return !$error;
    }

    protected function validateLogın() {
        // Check how many login attempts have been made.
        $this->load->model('account/customer');
        $this->load->model('extension/module/iss_login');
        $error = false;
        $contact_type = $this->defineContactType($this->request->post['telephone']);
        if($contact_type == 'email'){
            $validated_phone = $this->validateEmail($this->request->post['telephone']);
            if (!$validated_phone) {
                $this->session->data['iss_login']['error_warning'] = $this->language->get('error_wrong_telephone');
                $error = true;
            } else {
                $this->session->data['iss_login']['telephone'] = $validated_phone;
            }
        } else if($contact_type == 'telephone') {
            $validated_email = $this->validateTelephone($this->request->post['telephone']);
            if (!$validated_email) {
                $this->session->data['iss_login']['error_warning'] = $this->language->get('error_wrong_email');
                $error = true;
            } else {
                $this->session->data['iss_login']['telephone'] = $validated_email;
            }
        } else {
            $this->session->data['iss_login']['error_warning'] = $this->language->get('error_wrong_login');
            $error = true;
        }
        $login_info = $this->model_account_customer->getLoginAttempts($this->request->post['telephone']);
        if ($login_info && ($login_info['total'] >= $this->config->get('config_login_attempts')) && strtotime('-1 hour') < strtotime($login_info['date_modified'])) {
            $this->session->data['iss_login']['error_warning'] = $this->language->get('error_attempts');
            $error = true;
        }
        // Check if customer has been approved.
        $customer_info = $this->model_extension_module_iss_login->getCustomer($this->request->post['telephone']);

        if (!$customer_info) {
            $this->session->data['iss_login']['error_warning'] = $this->language->get('error_login');
            $error = true;
        }
        if ($customer_info && !$customer_info['status']) {
            $this->session->data['iss_login']['error_warning'] = $this->language->get('error_approved');
            $error = true;
        } 
        if (!$error) {
            if (!$this->customer->login($this->request->post['telephone'], $this->request->post['password'])) {
                $this->session->data['iss_login']['error_warning'] = $this->language->get('error_data_login');
                $error = true;
                $this->model_account_customer->addLoginAttempt($this->request->post['telephone']);
            } else {
                $this->model_account_customer->deleteLoginAttempts($this->request->post['telephone']);
            }
        }
        $this->session->data['iss_register']['error_warning'] = [];
        return !$error;
    }

    public function send_password() {
        $this->load->language('extension/module/iss_login');
        $this->load->model('extension/module/iss_login');
        $this->load->model('account/customer');
        $result = [
            'success' => false, 
            'error' => '' 
        ];
        if (!$this->request->get['telephone'] || $this->request->get['telephone'] == '') {
            $result['error'] = $this->language->get('error_empty_telephone');
            echo json_encode($result);
            exit();
        }
        $contact_type = $this->defineContactType($this->request->get['telephone']);
        if($contact_type == 'email'){
            $validated_email = $this->validateEmail($this->request->get['telephone']);
            if (!$validated_email) {
                $result['error'] = $this->language->get('error_wrong_telephone');
                echo json_encode($result);
                exit();
            } else {
                $this->session->data['iss_login']['telephone'] = $validated_email;
            }
        } else if($contact_type == 'telephone') {
            $validated_phone = $this->validateTelephone($this->request->get['telephone']);
            if (!$validated_phone) {
                $result['error'] = $this->language->get('error_wrong_email');
                echo json_encode($result);
                exit();
            } else {
                $this->session->data['iss_login']['telephone'] = $validated_phone;
            }
        } else {
            $result['error'] = $this->language->get('error_wrong_login');
            echo json_encode($result);
            exit();
        }
        $customer_info = $this->model_extension_module_iss_login->getCustomer($this->session->data['iss_login']['telephone']);
        if (empty($customer_info)) {
            $result['error'] = $this->language->get('error_login_not_found');
            echo json_encode($result);
            exit();
        }
        if ($customer_info && !$customer_info['status']) {
            $result['error'] = $this->language->get('error_approved');
            echo json_encode($result);
            exit();
        }
        if($customer_info && !$this->model_extension_module_iss_login->getLastAttempt($this->session->data['iss_login']['telephone'])){
            $this->model_account_customer->addLoginAttempt($this->session->data['iss_login']['telephone'].'|password_reset');
            $password = $this->generatePassword();
            $this->model_extension_module_iss_login->editPassword($this->session->data['iss_login']['telephone'], $password);
            $message = sprintf($this->language->get('text_sms_pssword_send'), $customer_info['firstname'], $password);
            if($contact_type == 'email' || !empty($customer_info['email'])){
                //$result['success'] = $this->sendEmail($this->session->data['iss_login']['telephone'], $message);
                $result['error'] = $this->language->get('success_email_send');
            } 
            if($contact_type == 'telephone' || !empty($customer_info['telephone'])){
                $result['success'] = $this->sendSms($this->session->data['iss_login']['telephone'], $message);
                if($result['success']){
                    $result['error'] = $this->language->get('success_sms_send'); 
                } else {
                    $result['error'] = $this->language->get('success_sms_send_error'); 
                }
            }
        } else {
            $result['error'] = $this->language->get('error_not_expired');
        }
        echo json_encode($result);
    }
    
    private function defineContactType($contact){
        $contact_type = '';
        if (filter_var($contact, FILTER_VALIDATE_EMAIL)) {
            $contact_type = 'email';
        }
        if (preg_match("/^((8|\+7)[\- ]?)?(\(?\d{3}\)?[\- ]?)?[\d\- ]{7,10}$/", $contact)) {
            $contact_type = 'telephone';
        }
        return $contact_type;
    }
    
    private function validateEmail($contacts_string){
        if(!empty($contacts_string)){
            if(filter_var($contacts_string, FILTER_VALIDATE_EMAIL)){
                return $contacts_string;
            }
        }
        return false;
    }

    private function validateTelephone($contacts_string){
        if(!empty($contacts_string)){
            preg_match_all("/[8\+7][\- ]?\(?\d{3}\)?[\- ]??[\d\- ]{7,10}/", $contacts_string, $matches);
            if(!empty($matches[0])){
                foreach($matches[0] as &$telephone){
                    $mobile = str_replace([' ',';',',','(',')','-'], "", $telephone);
                }
                $telephone = $matches[0][0];
                return $telephone;
            }
        }
        return false;
    }

    private function generatePassword(){
        $alphabet = 'abcdefghijklmnopqrstuvwxyz1234567890';//ABCDEFGHIJKLMNOPQRSTUVWXYZ
        $password = array(); 
        $alpha_length = strlen($alphabet) - 1; 
        for ($i = 0; $i < 4; $i++){
            $n = rand(0, $alpha_length);
            $password[] = $alphabet[$n];
        }
        return implode($password);
    }

    public function sendSms($number = null, $body = null) {
        $sms_sender = 'NILSON';
        $sms_user = 'wwwnilson';
        $sms_password = 'Makhablinch99431';
        $this->session->data['iss_sms'] = [
            'session_id' => '',
            'session_time' => time()
        ];
        if (!$sms_sender || !$sms_user || !$sms_password) {
            echo "Настройки для отправки смс не установленны";
            return false;
        }
        if (!in_array('https', stream_get_wrappers())) {
            echo "Sms can not be sent. https is not available";
            return false;
        }
        $sid = json_decode(file_get_contents("https://integrationapi.net/rest/user/sessionId?login=" . $sms_user . "&password=" . $sms_password));
        if (!$sid) {
            echo 'Authorization to SMS service failed';
            return false;
        }
        $this->session->data['iss_sms']['session_id'] = $sid;
        $this->session->data['iss_sms']['session_time'] = time();

        $post_vars = array(
            'SessionID' => $this->session->data['iss_sms']['session_id'],
            'SourceAddress' => $sms_sender,
            'DestinationAddresses' => $number,
            'Data' => $body
        );
        $opts = array(
            'http' => [
                'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
                'method' => "POST",
                'content' => http_build_query($post_vars)
            ]
        );
        $response = file_get_contents('https://integrationapi.net/rest/Sms/SendBulk/', false, stream_context_create($opts));
        $msg_ids = json_decode($response);
        if (!$msg_ids[0]) {
            $this->session->data['iss_sms']['session_time'] = 0;
            return false;
        }
        return true;
    }
    
    
	public function sendEmail($email, $body) {
            $this->load->language('extension/module/iss_login');
            $store_name = html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8');
            $subject = $this->language->get('entry_password') . ' ' . $store_name;
            $data['body'] = $body;
            $mail = new Mail($this->config->get('config_mail_engine'));
            $mail->parameter = $this->config->get('config_mail_parameter');
            $mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
            $mail->smtp_username = $this->config->get('config_mail_smtp_username');
            $mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
            $mail->smtp_port = $this->config->get('config_mail_smtp_port');
            $mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

            $mail->setTo($email);
            $mail->setFrom($this->config->get('config_email'));
            $mail->setSender($store_name);
            $mail->setSubject($subject);
            $mail->setText($this->load->view('mail/iss_login', $data));
            $mail->send(); 
	}
}
