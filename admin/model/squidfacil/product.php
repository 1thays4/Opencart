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

        var_dump($parametros);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->getUrl());
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($parametros));
        $response = curl_exec($ch);
        var_dump($response);
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

        $this->load->model('localisation/language');

        if ($request['product_language_all'] == 1) {
            foreach ($this->model_localisation_language->getLanguages() as $language_code => $language) {
                $data['product_description'][$language['language_id']]['name'] = (string) $produto->nome;
                $data['product_description'][$language['language_id']]['description'] = (string) $produto->descricao;
            }
        } else {
            foreach ($request['product_language'] as $index => $language_id) {
                $data['product_description'][$language_id]['name'] = (string) $produto->nome;
                $data['product_description'][$language_id]['description'] = (string) $produto->descricao;
            }
        }
        
        $data['product_image'][] = $this->importProductImage($item->image);
        //var_dump($data);
        //exit();

        $data['product_store'] = $request['product_store'];

        $data['product_category'] = $request['product_category'];

        $this->model_catalog_product->addProduct($data);
        /*
          'product_id' => string '48' (length=2)
          'model' => string 'product 20' (length=10)
          'sku' => string 'test 1' (length=6)
          'upc' => string '' (length=0)
          'ean' => string '' (length=0)
          'jan' => string '' (length=0)
          'isbn' => string '' (length=0)
          'mpn' => string '' (length=0)
          'location' => string 'test 2' (length=6)
          'quantity' => string '995' (length=3)
          'stock_status_id' => string '5' (length=1)
          'image' => string 'data/demo/ipod_classic_1.jpg' (length=28)
          'manufacturer_id' => string '8' (length=1)
          'shipping' => string '1' (length=1)
          'price' => string '100.0000' (length=8)
          'points' => string '0' (length=1)
          'tax_class_id' => string '9' (length=1)
          'date_available' => string '2009-02-08' (length=10)
          'weight' => string '1.00000000' (length=10)
          'weight_class_id' => string '1' (length=1)
          'length' => string '0.00000000' (length=10)
          'width' => string '0.00000000' (length=10)
          'height' => string '0.00000000' (length=10)
          'length_class_id' => string '2' (length=1)
          'subtract' => string '1' (length=1)
          'minimum' => string '1' (length=1)
          'sort_order' => string '0' (length=1)
          'status' => string '1' (length=1)
          'date_added' => string '2009-02-08 17:21:51' (length=19)
          'date_modified' => string '2011-09-30 01:07:06' (length=19)
          'viewed' => string '0' (length=1)
          'language_id' => string '1' (length=1)
          'name' => string 'iPod Classic' (length=12)
          'description' => string '&lt;div class=&quot;cpt_product_description &quot;&gt;
          &lt;div&gt;
          &lt;p&gt;
          &lt;strong&gt;More room to move.&lt;/strong&gt;&lt;/p&gt;
          &lt;p&gt;
          With 80GB or 160GB of storage and up to 40 hours of battery life, the new iPod classic lets you enjoy up to 40,000 songs or up to 200 hours of video or any combination wherever you go.&lt;/p&gt;
          &lt;p&gt;
          &lt;strong&gt;Cover Flow.&lt;/strong&gt;&lt;/p&gt;
          &lt;p&gt;
          Browse through your music collection by flipping through album art. Sel'... (length=1056)
          'meta_description' => string '' (length=0)
          'meta_keyword' => string '' (length=0)
          'tag' => string '' (length=0)
         * 
         */
    }
    
    public function importProductImage($image){
        $image_type = substr(strrchr($image, "."), 1);
        $filename = "tmp." . $image_type;
        $path = Mage::getBaseDir('media') . DS . 'import' . DS;
        if(!is_dir($path)){
            mkdir($path);
        }
        $fullpath =  $path . $filename;

        $ch = curl_init ($item->image);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
        $raw=curl_exec($ch);
        curl_close ($ch);
        if(file_exists($fullpath)){
            unlink($fullpath);
        }
        $fp = fopen($fullpath,'x');
        fwrite($fp, $raw);
        fclose($fp);

        $product = Mage::getModel('catalog/product')->load($new_product_id);
        $product->setMediaGallery(array('images' => array(), 'values' => array()));
        $product->addImageToMediaGallery($fullpath, array('image', 'small_image', 'thumbnail'), false, false);
    }

}

?>
