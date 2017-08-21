<?php
class ControllerPaymentVoypay extends Controller{

    private $error = array();
    public function index(){

        $this->load->language ( 'payment/voypay' );
        $this->document->setTitle ( $this->language->get ( 'heading_title' ) );
        $this->load->model ( 'setting/setting' );
        if (($this->request->server ['REQUEST_METHOD'] == 'POST') && ($this->validate ())) {
            $this->load->model ( 'setting/setting' );
            $this->model_setting_setting->editSetting ( 'voypay', $this->request->post );
            $this->session->data ['success'] = $this->language->get ( 'text_success' );
            $this->response->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
        }
        $data ['heading_title'] = $this->language->get ( 'heading_title' );
        $data ['text_enabled'] = $this->language->get ( 'text_enabled' );
        $data ['text_disabled'] = $this->language->get ( 'text_disabled' );
        $data ['text_all_zones'] = $this->language->get ( 'text_all_zones' );
        $data ['entry_mer_no'] = $this->language->get ('entry_mer_no');
        $data ['entry_sign'] = $this->language->get ('entry_sign');
        $data ['entry_currency'] = $this->language->get ('entry_currency');
        // $data ['entry_transactionurl'] = $this->language->get ('entry_transactionurl');
        $data ['entry_order_status'] = $this->language->get ('entry_order_status' );
        $data ['entry_voypay_success_order_status'] = $this->language->get ('entry_voypay_success_order_status');
        $data ['entry_voypay_fail_order_status'] = $this->language->get ('entry_voypay_fail_order_status');
        $data ['entry_geo_zone'] = $this->language->get ('entry_geo_zone' );
        $data ['entry_status'] = $this->language->get ('entry_status' );
        $data ['entry_sort_order'] = $this->language->get ('entry_sort_order' );
        $data ['button_save'] = $this->language->get ('button_save' );
        $data ['button_cancel'] = $this->language->get ('button_cancel' );
        $data ['tab_general'] = $this->language->get ('tab_general' );

        if (isset ( $this->error ['warning'] )) {
            $data ['error_warning'] = $this->error ['warning'];
        } else {
            $data ['error_warning'] = '';
        }

        if (isset ( $this->error ['mer_no'] )) {
            $data ['error_mer_no'] = $this->error ['mer_no'];
        } else {
            $data ['error_mer_no'] = '';
        }

        if (isset ( $this->error ['sign'] )) {
            $data ['error_sign'] = $this->error ['sign'];
        } else {
            $data ['error_sign'] = '';
        }

        $this->document->breadcrumbs = array ();

        $this->document->breadcrumbs [] = array (
            'href' => HTTPS_SERVER . 'index.php?route=common/home&token=' . $this->session->data ['token'],
            'text' => $this->language->get ( 'text_home' ),
            'separator' => FALSE
        );

        $this->document->breadcrumbs [] = array (
            'href' => HTTPS_SERVER . 'index.php?route=extension/payment&token=' . $this->session->data ['token'],
            'text' => $this->language->get ( 'text_payment' ),
            'separator' => ' :: '
        );

        $this->document->breadcrumbs [] = array (
            'href' => HTTPS_SERVER . 'index.php?route=payment/voypay&token=' . $this->session->data ['token'],
            'text' => $this->language->get ( 'heading_title' ),
            'separator' => ' :: '
        );

        $data ['action'] = HTTPS_SERVER . 'index.php?route=payment/voypay&token=' . $this->session->data ['token'];
        $data ['cancel'] = HTTPS_SERVER . 'index.php?route=extension/payment&token=' . $this->session->data ['token'];

        if (isset ( $this->request->post ['voypay_mer_no'] )) {
            $data ['voypay_mer_no'] = $this->request->post ['voypay_mer_no'];
        } else {
            $data ['voypay_mer_no'] = $this->config->get ( 'voypay_mer_no' );
        }

        if (isset ( $this->request->post ['voypay_sign'] )) {
            $data ['voypay_sign'] = $this->request->post ['voypay_sign'];
        } else {
            $data ['voypay_sign'] = $this->config->get ( 'voypay_sign' );
        }

        if (isset ( $this->request->post ['voypay_currency'] )) {
            $data ['voypay_currency'] = $this->request->post ['voypay_currency'];
        } else {
            $data ['voypay_currency'] = $this->config->get ( 'voypay_currency' );
        }

        if (isset($this->request->post['voypay_transactionurl'])) {
            $data['voypay_transactionurl'] = $this->request->post['voypay_transactionurl'];
        } else {
            $data['voypay_transactionurl'] = $this->config->get('voypay_transactionurl');
        }

        if (isset ( $this->request->post ['voypay_order_status_id'] )) {
            $data ['voypay_order_status_id'] = $this->request->post ['voypay_order_status_id'];
        } else {
            $data ['voypay_order_status_id'] = $this->config->get ( 'voypay_order_status_id' );
        }

        if (isset ( $this->request->post ['voypay_new_success_order_status_id'] )) {
            $data ['voypay_new_success_order_status_id'] = $this->request->post ['voypay_new_success_order_status_id'];
        } else {
            $data ['voypay_new_success_order_status_id'] = $this->config->get ( 'voypay_new_success_order_status_id' );
        }

        if (isset ( $this->request->post ['voypay_new_fail_order_status_id'] )) {
            $data ['voypay_new_fail_order_status_id'] = $this->request->post ['voypay_new_fail_order_status_id'];
        } else {
            $data ['voypay_new_fail_order_status_id'] = $this->config->get ( 'voypay_new_fail_order_status_id' );
        }

        $this->load->model ( 'localisation/order_status' );

        $data ['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses ();

        if (isset ( $this->request->post ['voypay_geo_zone_id'] )) {
            $data ['voypay_geo_zone_id'] = $this->request->post ['voypay_geo_zone_id'];
        } else {
            $data ['voypay_geo_zone_id'] = $this->config->get ( 'voypay_geo_zone_id' );
        }

        $this->load->model ( 'localisation/geo_zone' );

        $data ['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones ();

        if (isset ( $this->request->post ['voypay_status'] )) {
            $data ['voypay_status'] = $this->request->post ['voypay_status'];
        } else {
            $data ['voypay_status'] = $this->config->get ( 'voypay_status' );
        }

        if (isset ( $this->request->post ['voypay_mode'] )) {
            $data ['voypay_mode'] = $this->request->post ['voypay_mode'];
        } else {
            $data ['voypay_mode'] = $this->config->get ( 'voypay_mode' );
        }

        if (isset ( $this->request->post ['voypay_sort_order'] )) {
            $data ['voypay_sort_order'] = $this->request->post ['voypay_sort_order'];
        } else {
            $data ['voypay_sort_order'] = $this->config->get ( 'voypay_sort_order' );
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $this->response->setOutput($this->load->view('payment/voypay.tpl', $data));
    }

    private function validate() {

        if (! $this->request->post ['voypay_mer_no']) {
            $this->error ['mer_no'] = $this->language->get ( 'error_mer_no' );
        }

        if (! $this->request->post ['voypay_sign']) {
            $this->error ['sign'] = $this->language->get ( 'error_sign' );
        }
        return !$this->error;
    }

}