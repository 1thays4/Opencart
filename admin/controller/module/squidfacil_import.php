<?php

class ControllerModuleSquidfacilImport extends Controller {

    private $error = array();

    public function index() {
        $this->load->language('module/squidfacil_import'); 
        $this->document->setTitle("test");
        $this->template = 'module/squidfacil_import.tpl';
        $this->children = array(
            'common/header',
            'common/footer'
        );
        //$this->getList();
        $this->response->setOutput($this->render());
    }

}

?>
