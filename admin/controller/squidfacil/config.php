<?php

class ControllerSquidfacilConfig extends Controller {

    public function index() {
        $this->language->load('squidfacil/config');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('squidfacil', $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->redirect($this->url->link('squidfacil/config', 'token=' . $this->session->data['token'], 'SSL'));
        }

        $this->data['heading_title'] = $this->language->get('heading_title');

        $this->data['text_enabled'] = $this->language->get('text_enabled');
        $this->data['text_disabled'] = $this->language->get('text_disabled');
        $this->data['text_all_zones'] = $this->language->get('text_all_zones');
        $this->data['text_none'] = $this->language->get('text_none');

        $this->data['entry_email'] = $this->language->get('entry_email');
        $this->data['entry_token'] = $this->language->get('entry_token');

        $this->data['button_save'] = $this->language->get('button_save');
        $this->data['button_cancel'] = $this->language->get('button_cancel');


        if (isset($this->error['warning'])) {
            $this->data['error_warning'] = $this->error['warning'];
        } else {
            $this->data['error_warning'] = '';
        }

        if (isset($this->session->data['success'])) {
            $this->data['success'] = $this->session->data['success'];
            if (isset($this->session->data['success_param'])) {
                $this->data['success_param'] = $this->session->data['success_param'];
                unset($this->session->data['success_param']);
            } else {
                $this->data['success_param'] = '';
            }
            unset($this->session->data['success']);
        } else {
            $this->data['success'] = '';
        }
        
        $this->data['breadcrumbs'] = array();

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => false
        );

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('squidfacil/list', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('page_title'),
            'href' => false,
            'separator' => ' :: '
        );

        $this->data['action'] = $this->url->link('squidfacil/config', 'token=' . $this->session->data['token'], 'SSL');

        $this->data['cancel'] = $this->url->link('squidfacil/config', 'token=' . $this->session->data['token'], 'SSL');

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

        $this->template = 'squidfacil/config.tpl';
        $this->children = array(
            'common/header',
            'common/footer'
        );

        $this->response->setOutput($this->render());
    }

    protected function validate() {
        if (!$this->user->hasPermission('modify', 'squidfacil/config')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

}

?>
