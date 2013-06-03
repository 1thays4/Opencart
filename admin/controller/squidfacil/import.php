<?php

class ControllerSquidfacilImport extends Controller {

    public function index() {
        $this->language->load('squidfacil/import');

        $this->document->setTitle($this->language->get('heading_title'));
        
        $this->load->model('squidfacil/product');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_squidfacil_product->importProduct($this->request->get['sku'], $this->request->post);
			
            $this->session->data['success'] = $this->language->get('text_success');
            $this->session->data['success_param'] = $this->request->get['sku'];

            $this->redirect($this->url->link('squidfacil/list', 'token=' . $this->session->data['token'], 'SSL'));
        }
        
        $this->data['token'] = $this->session->data['token'];

        $this->data['heading_title'] = $this->language->get('heading_title');

        $this->data['text_enabled'] = $this->language->get('text_enabled');
        $this->data['text_disabled'] = $this->language->get('text_disabled');
        $this->data['text_all_zones'] = $this->language->get('text_all_zones');
        $this->data['text_none'] = $this->language->get('text_none');

        $this->data['entry_category'] = $this->language->get('entry_category');

        $this->data['button_import'] = $this->language->get('button_import');

        if (isset($this->error['warning'])) {
            $this->data['error_warning'] = $this->error['warning'];
        } else {
            $this->data['error_warning'] = '';
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

        $this->data['action'] = $this->url->link('squidfacil/import', 'token=' . $this->session->data['token'] . '&sku=' . $this->request->get['sku'] , 'SSL');

        $this->getForm();

        $this->template = 'squidfacil/import.tpl';
        $this->children = array(
            'common/header',
            'common/footer'
        );

        $this->response->setOutput($this->render());
    }

    public function getForm() {
        $this->data['entry_category'] = $this->language->get('entry_category');

        // Categories
        $this->load->model('catalog/category');

        if (isset($this->request->post['product_category'])) {
            $categories = $this->request->post['product_category'];
        } elseif (isset($this->request->get['product_id'])) {
            $categories = $this->model_catalog_product->getProductCategories($this->request->get['product_id']);
        } else {
            $categories = array();
        }

        $this->data['product_categories'] = array();

        foreach ($categories as $category_id) {
            $category_info = $this->model_catalog_category->getCategory($category_id);

            if ($category_info) {
                $this->data['product_categories'][] = array(
                    'category_id' => $category_info['category_id'],
                    'name' => ($category_info['path'] ? $category_info['path'] . ' &gt; ' : '') . $category_info['name']
                );
            }
        }
    }

    protected function validateForm() {
        if (!$this->user->hasPermission('modify', 'catalog/product')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if ($this->error && !isset($this->error['warning'])) {
            $this->error['warning'] = $this->language->get('error_warning');
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

}

?>
