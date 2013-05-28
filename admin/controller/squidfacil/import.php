<?php

class ControllerModuleSquidfacilImport extends Controller {

    private $error = array();

    public function index() {

        $this->document->setTitle("test");
        $this->getList();
    }

}

?>
