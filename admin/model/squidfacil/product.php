<?php

class ModelSquidfacilProduct extends Model {

    private $count;

    public function getCount() {
        return $this->count;
    }

    public function setCount($count) {
        $this->count = $count;
    }

    public function getProduct($product_id) {
        $obj = new stdClass();
        $obj->name = "test";
        return $obj;
    }

    public function getProducts($data = array()) {
        $parametros = array(
            'email' => 'fhcs@live.com',
            'token' => '67661255136719412136810710860704',
            'limite' => ($data['limit']<1)?20:$data['limit'],
            'pagina' => ($data['start']<0)?1:$data['start']+1
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://squidfacil/webservice/produtos/produtos.php");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($parametros));
        $response = curl_exec($ch);
        curl_close($ch);
        $xml = simplexml_load_string($response, 'SimpleXMLElement', LIBXML_NOCDATA);
        //$xml = new SimpleXMLElement($response, LIBXML_NOCDATA);
        $root = $xml->children();
        $produtos = $root[1];
        $this->setCount($root[2]->total);
        $arr = array();
        foreach ($produtos as $produto) {
            $arr[] = array(
                'sku' => $produto->sku,
                'name' => $produto->nome,
                'category' => $produto->categoria
            );
        }
        return $arr;
    }
    
    public function importProduct($sku, $request){
        var_dump($sku);
        var_dump($request);
        exit();
    }

}

?>
