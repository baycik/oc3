<?php

class ModelExtensionModuleIssLogin extends Model {

    public function addCustomer($data) {
        if (isset($data['customer_group_id']) && is_array($this->config->get('config_customer_group_display')) && in_array($data['customer_group_id'], $this->config->get('config_customer_group_display'))) {
            $customer_group_id = $data['customer_group_id'];
        } else {
            $customer_group_id = $this->config->get('config_customer_group_id');
        }

        $this->load->model('account/customer_group');

        $customer_group_info = $this->model_account_customer_group->getCustomerGroup($customer_group_id);
        $this->db->query("INSERT INTO " . DB_PREFIX . "customer SET customer_group_id = '" . (int) $customer_group_id . "', store_id = '" . (int) $this->config->get('config_store_id') . "', language_id = '" . (int) $this->config->get('config_language_id') . "', firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', custom_field = '" . $this->db->escape(isset($data['custom_field']['account']) ? json_encode($data['custom_field']['account']) : '') . "', salt = '" . $this->db->escape($salt = token(9)) . "', password = '" . $this->db->escape(sha1($salt . sha1($salt . sha1($data['password'])))) . "', newsletter = '" . (isset($data['newsletter']) ? (int) $data['newsletter'] : 0) . "', ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "', status = '" . (int) !$customer_group_info['approval'] . "', date_added = NOW()");

        $customer_id = $this->db->getLastId();

        if ($customer_group_info['approval']) {
            $this->db->query("INSERT INTO `" . DB_PREFIX . "customer_approval` SET customer_id = '" . (int) $customer_id . "', type = 'customer', date_added = NOW()");
        }

        return $customer_id;
    }

    public function getTotalCustomers($login) {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "customer WHERE LOWER(email) = '" . $this->db->escape(utf8_strtolower($login)) . "' OR LOWER(telephone) = '" . $this->db->escape(utf8_strtolower($login)) . "'");

        return $query->row['total'];
    }

    public function getCustomer($login) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE LOWER(email) = '" . $this->db->escape(utf8_strtolower($login)) . "' OR LOWER(telephone) = '" . $this->db->escape(utf8_strtolower($login)) . "'");

        return $query->row;
    }

    public function getLastAttempt($login) {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "customer_login` WHERE `email` = '" . $this->db->escape($login.'|password_reset') . "' AND date_modified > DATE_SUB('" . $this->db->escape(date('Y-m-d H:i:s')) . "', INTERVAL 3 HOUR)");

        return $query->num_rows;
    }

    public function editPassword($telephone, $password) {
        $this->db->query("UPDATE " . DB_PREFIX . "customer SET salt = '" . $this->db->escape($salt = token(9)) . "', password = '" . $this->db->escape(sha1($salt . sha1($salt . sha1($password)))) . "', code = '' WHERE LOWER(telephone) = '" . $this->db->escape(utf8_strtolower($telephone)) . "'");
    }

}
