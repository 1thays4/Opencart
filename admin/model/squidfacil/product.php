<?php

class ModelSquidfacilProduct extends Model {

    private $count;
    private $url = "https://www.squidfacil.com.br/webservice/produtos/produtos.php";

    public function getUrl() {
        return $this->url;
    }

    public function setUrl($url) {
        $this->url = $url;
    }

    public function getCount() {
        return $this->count;
    }

    public function setCount($count) {
        $this->count = $count;
    }

    public function getProduct($sku) {
        $this->load->model('setting/setting');
        
        $setting = $this->model_setting_setting->getSetting('squidfacil');
        
        $parametros = array(
            'email' => $setting['squidfacil_email'],
            'token' => $setting['squidfacil_token'],
            'sku' => $sku
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->getUrl());
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($parametros));
        $response = curl_exec($ch);
        curl_close($ch);
        $xml = simplexml_load_string($response, 'SimpleXMLElement', LIBXML_NOCDATA);
        $root = $xml->children();
        $produtos = $root[1];
        $this->setCount(1);
        $data = array();
        return $produtos->produto;
    }

    public function checkDuplicateBySKU($sku) {
        $query = $this->db->query("SELECT DISTINCT count(*) AS total FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE p.sku = '" . $sku . "' AND pd.language_id = '" . (int) $this->config->get('config_language_id') . "'");
        return $query->row['total'];
    }
    
    public function getProductIdBySKU($sku){
        $query = $this->db->query("SELECT DISTINCT id FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE p.sku = '" . $sku . "' AND pd.language_id = '" . (int) $this->config->get('config_language_id') . "'");
        return $query->row['id'];
    }

    public function getProducts($data = array()) {
        $this->load->model('catalog/product');
        $this->load->model('setting/setting');
        
        $setting = $this->model_setting_setting->getSetting('squidfacil');

        $product_list = $this->model_catalog_product->getProducts();
        $ignorar = array();
        foreach ($product_list as $product) {
            if (preg_match("/SQUID[0-9]+/", $product['sku'])) {
                $ignorar[] = $product['sku'];
            }
        }

        $parametros = array(
            'email' => $setting['squidfacil_email'],
            'token' => $setting['squidfacil_token'],
            'limite' => ($data['limit'] < 1) ? 20 : $data['limit'],
            'pagina' => ($data['start'] < 0) ? 1 : $data['start'] + 1,
            'ignorar' => $ignorar
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->getUrl());
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($parametros));
        $response = curl_exec($ch);
        curl_close($ch);
        $xml = simplexml_load_string($response, 'SimpleXMLElement', LIBXML_NOCDATA);
        
        if($xml){
            $root = $xml->children();
            $data = array();
            if((int)$root[0]->codigo == 0){
                $produtos = $root[1];
                foreach ($produtos as $produto) {
                    //var_dump($this->checkDuplicateBySKU($produto->sku));
                    if ($this->checkDuplicateBySKU($produto->sku) == 0) {
                        $data[] = array(
                            'sku' => $produto->sku,
                            'name' => $produto->nome,
                            'category' => $produto->categoria
                        );
                    }
                }
                //var_dump($data);
                $this->setCount($root[2]->total);
            } else {
                $this->setCount(0);
                
            }
        }
        return $data;
    }

    public function importProduct($sku, $request) {
        $this->load->model('catalog/product');

        $produto = $this->getProduct($sku);

        $data['sku'] = $sku;
        $data['model'] = (string) $produto->modelo;
        $data['upc'] = '';
        $data['ean'] = '';
        $data['jan'] = '';
        $data['isbn'] = '';
        $data['mpn'] = '';
        $data['location'] = '';
        $data['quantity'] = $produto->estoque;
        $data['minimum'] = '';
        $data['subtract'] = '';
        $data['stock_status_id'] = '';
        $data['date_available'] = date("Y-m-d H:i:s", time());
        $data['manufacturer_id'] = '';
        $data['shipping'] = '';
        $data['price'] = $produto->preco_sugerido;
        $data['points'] = '';
        $data['weight'] = ($produto->peso + $produto->peso_embalagem) / 1000;
        $data['weight_class_id'] = '';
        $data['length'] = $produto->profundidade_embalagem;
        $data['width'] = $produto->largura_embalagem;
        $data['height'] = $produto->altura_embalagem;
        $data['length_class_id'] = '';
        $data['status'] = '1';
        $data['tax_class_id'] = '';
        $data['sort_order'] = '';

        $data['keyword'] = false;
        
        $this->load->model('localisation/language');

        if ($request['product_language_all'] == 1) {
            foreach ($this->model_localisation_language->getLanguages() as $language_code => $language) {
                $data['product_description'][$language['language_id']]['name'] = (string) $produto->nome;
                $data['product_description'][$language['language_id']]['description'] = (string) $produto->descricao;
                $data['product_description'][$language['language_id']]['meta_keyword'] = '';
                $data['product_description'][$language['language_id']]['meta_description'] = '';
                $data['product_description'][$language['language_id']]['tag'] = '';
            }
        } else {
            foreach ($request['product_language'] as $index => $language_id) {
                $data['product_description'][$language_id]['name'] = (string) $produto->nome;
                $data['product_description'][$language_id]['description'] = (string) $produto->descricao;
                $data['product_description'][$language_id]['meta_keyword'] = '';
                $data['product_description'][$language_id]['meta_description'] = '';
                $data['product_description'][$language_id]['tag'] = '';
            }
        }
        
        $data['image'] = $this->importProductImage($produto->imagem_principal);
        
        $data['product_store'] = $request['product_store'];

        $data['product_category'] = $request['product_category'];

        $this->model_catalog_product->addProduct($data);
    }
    
    public function importProductImage($image){
        $image_name = substr(strrchr($image, "/"), 1);
        $image_type = substr(strrchr($image_name, "."), 1);
        $subpath = 'data';
        $path = DIR_IMAGE . $subpath . DIRECTORY_SEPARATOR;
        if(!is_dir($path)){
            mkdir($path);
        }
        $fullpath =  $path . $image_name;

        $ch = curl_init ($image);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
        $raw=curl_exec($ch);
        curl_close ($ch);
        if(is_file($fullpath)){
            unlink($fullpath);
        }
        $fp = fopen($fullpath,'x');
        fwrite($fp, $raw);
        fclose($fp);
        
        return $subpath . DIRECTORY_SEPARATOR .$image_name;
    }

}

?>
