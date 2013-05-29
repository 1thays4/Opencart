<?php

class ModelSquidfacilProduct extends Model {

    public function getProduct($product_id) {
        $obj = new stdClass();
        $obj->name = "test";
        return $obj;
    }

    public function getProducts($data = array()) {
        
    }

}

?>
