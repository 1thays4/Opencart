<?php

class ControllerSquidfacilList extends Controller {

    public function index() {
        $this->load->language('squidfacil/list');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('squidfacil/product');

        $this->getList();
    }

    protected function getList() {
        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        $url = '';

        $this->data['breadcrumbs'] = array();

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => false
        );

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => false,
            'separator' => ' :: '
        );

        $this->data['import'] = $this->url->link('squidfacil/import/bulk', 'token=' . $this->session->data['token'] . $url, 'SSL');

        $this->data['categories'] = array();

        $data = array(
            'start' => ($page - 1),
            'limit' => $this->config->get('config_admin_limit')
        );

        $results = $this->model_squidfacil_product->getProducts($data);
        
        $product_total = $this->model_squidfacil_product->getCount($data);

        if (count($results)) {
            foreach ($results as $result) {
                $action = array();

                $action[] = array(
                    'text' => $this->language->get('text_import'),
                    'href' => $this->url->link('squidfacil/import', 'token=' . $this->session->data['token'] . '&sku=' . $result['sku'] . $url, 'SSL')
                );

                $this->data['products'][] = array(
                    'sku' => $result['sku'],
                    'name' => $result['name'],
                    'category' => $result['category'],
                    'selected' => isset($this->request->post['selected']) && in_array($result['sku'], $this->request->post['selected']),
                    'action' => $action
                );
            }
        } else {
            $this->data['products'] = array();
        }

        $this->data['heading_title'] = $this->language->get('heading_title');

        $this->data['text_no_results'] = $this->language->get('text_no_results');

        $this->data['column_name'] = $this->language->get('column_name');
        $this->data['column_category'] = $this->language->get('column_category');
        $this->data['column_action'] = $this->language->get('column_action');

        $this->data['button_import'] = $this->language->get('button_import');
        
        
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

        $pagination = new Pagination();
        $pagination->total = $product_total;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_admin_limit');
        $pagination->text = $this->language->get('text_pagination');
        $pagination->url = $this->url->link('squidfacil/list', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

        $this->data['pagination'] = $pagination->render();

        $this->template = 'squidfacil/list.tpl';
        $this->children = array(
            'common/header',
            'common/footer'
        );

        $this->response->setOutput($this->render());
    }

}

?>
