<?php

class ControllerShippingSquidfacil extends Controller {

    public function index() {

        $this->language->load('shipping/squidfacil');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('squidfacil', $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->redirect($this->url->link('extension/shipping', 'token=' . $this->session->data['token'], 'SSL'));
        }

        $this->data['heading_title'] = $this->language->get('heading_title');

        $this->data['text_enabled'] = $this->language->get('text_enabled');
        $this->data['text_disabled'] = $this->language->get('text_disabled');
        
        $this->data['text_shipping_methods'] = $this->language->get('text_shipping_methods');

        $this->data['entry_email'] = $this->language->get('entry_email');
        $this->data['entry_token'] = $this->language->get('entry_token');
        $this->data['entry_status'] = $this->language->get('entry_status');

        $this->data['button_save'] = $this->language->get('button_save');
        $this->data['button_cancel'] = $this->language->get('button_cancel');

        if (isset($this->error['warning'])) {
            $this->data['error_warning'] = $this->error['warning'];
        } else {
            $this->data['error_warning'] = '';
        }

        if (isset($this->request->post['squidfacil_email'])) {
            $this->data['squidfacil_email'] = $this->request->post['squidfacil_email'];
        } else {
            $this->data['squidfacil_email'] = $this->config->get('squidfacil_email');
        }

        if (isset($this->request->post['squidfacil_token'])) {
            $this->data['squidfacil_token'] = $this->request->post['squidfacil_token'];
        } else {
            $this->data['squidfacil_token'] = $this->config->get('squidfacil_token');
        }

        if (isset($this->request->post['squidfacil_status'])) {
            $this->data['squidfacil_status'] = $this->request->post['squidfacil_status'];
        } else {
            $this->data['squidfacil_status'] = $this->config->get('squidfacil_status');
        }

        $this->data['breadcrumbs'] = array();

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => false
        );

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_shipping'),
            'href' => $this->url->link('extension/shipping', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('shipping/squidfacil', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );


        $this->data['action'] = $this->url->link('shipping/squidfacil', 'token=' . $this->session->data['token'], 'SSL');

        $this->data['cancel'] = $this->url->link('extension/shipping', 'token=' . $this->session->data['token'], 'SSL');


        $this->template = 'shipping/squidfacil.tpl';
        $this->children = array(
            'common/header',
            'common/footer'
        );

        $this->response->setOutput($this->render());
    }

    protected function validate() {

        if (!$this->user->hasPermission('modify', 'shipping/squidfacil')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->error) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

}

?>
