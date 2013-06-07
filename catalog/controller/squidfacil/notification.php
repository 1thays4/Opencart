<?php

class ControllerSquidfacilNotification extends Controller {

    public function index() {
        $sku = $this->request->get['sku'];
//        var_dump($sku);

        if (preg_match("/SQUID[0-9]+/", $sku)) {
            $this->load->model("squidfacil/product");
            $this->load->model("catalog/product");
            $externalProduct = $this->model_squidfacil_product->getProduct($sku);
            $id = $this->model_squidfacil_product->getProductIdBySKU($sku);
//            var_dump($id);
            $product = $this->model_catalog_product->getProduct($id);
//            var_dump($product);

            if ($id && $externalProduct && $product) {
                $data['sku'] = $product['sku'];
                $data['model'] = (string) $externalProduct->modelo;
                $data['upc'] = $product['upc'];
                $data['ean'] = $product['ean'];
                $data['jan'] = $product['jan'];
                $data['isbn'] = $product['isbn'];
                $data['mpn'] = $product['mpn'];
                $data['location'] = $product['location'];
                $data['quantity'] = $externalProduct->estoque;
                $data['minimum'] = $product['minimum'];
                $data['subtract'] = $product['subtract'];
                $data['stock_status_id'] = $product['stock_status'];
                $data['date_available'] = date("Y-m-d H:i:s", time());
                $data['manufacturer_id'] = $product['manufacturer_id'];
                $data['shipping'] = 0;
                $data['price'] = $externalProduct->preco_sugerido;
                $data['points'] = $product['points'];
                $data['weight'] = ($externalProduct->peso + $externalProduct->peso_embalagem) / 1000;
                $data['weight_class_id'] = $product['weight_class_id'];
                $data['length'] = $externalProduct->profundidade_embalagem;
                $data['width'] = $externalProduct->largura_embalagem;
                $data['height'] = $externalProduct->altura_embalagem;
                $data['length_class_id'] = $product['length_class_id'];
                $data['status'] = '1';
                $data['tax_class_id'] = $product['tax_class_id'];
                $data['sort_order'] = $product['sort_order'];
                
                $data['keyword'] = false;

                foreach ($this->model_squidfacil_product->getProductLanguages($id) as $language_code => $language) {
                    $data['product_description'][$language['language_id']]['name'] = (string) $externalProduct->nome;
                    $data['product_description'][$language['language_id']]['description'] = (string) $externalProduct->descricao;
                    $data['product_description'][$language['language_id']]['meta_keyword'] = '';
                    $data['product_description'][$language['language_id']]['meta_description'] = '';
                    $data['product_description'][$language['language_id']]['tag'] = '';
                }

                $data['image'] = $this->model_squidfacil_product->importProductImage($externalProduct->imagens->imagem);

                $data['product_store'] = $this->model_squidfacil_product->getProductStores($id);

                $data['product_category'] = $this->model_squidfacil_product->getProductCategories($id);
                
//                var_dump($data);

                $product = $this->model_squidfacil_product->updateProduct($id, $data);
            } else {
                $this->data['error_warning'] = "ignorando produto " . $sku;
            }
            if(!$externalProduct){
                $this->model_squidfacil_product->deleteProduct($id);
            }
        }
    }

}

?>
