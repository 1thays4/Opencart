<?php

class ModelShippingSquidfacil extends Model {

    function getQuote($address) {
        $this->load->Model('catalog/product');
        $this->load->model('setting/setting');

        $setting = $this->model_setting_setting->getSetting('squidfacil');

        $parametros = array(
            'email' => $setting['squidfacil_email'],
            'token' => $setting['squidfacil_token'],
            'cep' => $address['postcode'],
        );

        foreach ($this->cart->getProducts() as $item) {
            $product = $this->model_catalog_product->getProduct($item['key']);
            if (preg_match("/SQUID([0-9]+)/", $product['sku'])) {
                $parametros['produtos'][] = array(
                    'sku' => $product['sku'],
                    'quantidade' => $item['quantity']
                );
            }
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://www.squidfacil.com.br/webservice/frete/frete.php");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($parametros));
        $response = curl_exec($ch);
        curl_close($ch);

        $xml = simplexml_load_string($response, 'SimpleXMLElement', LIBXML_NOCDATA);
        $error = null;

        if ($xml) {
            $root = $xml->children();
            $transportadoras = $root[1];
            foreach ($transportadoras as $transportadora) {
                foreach ($transportadora->servicos->children() as $service) {
                    //print_r($service);
                    $quote_data[(string) $service->nome] = array(
                        'code' => 'squidfacil.' . $service->nome,
                        'title' => (string) $transportadora->nome . ' - ' . (string) $service->nome,
                        'cost' => (double) $service->valor,
                        'tax_class_id' => 0,
                        'text' => $this->currency->format($this->currency->convert((double) $service->valor, "BRL", $this->currency->getCode()), $this->currency->getCode(), 1.0000000)
                    );
                }
            }
            $method_data = array(
                'code' => 'squidfacil',
                'title' => '',
                'quote' => $quote_data,
                'sort_order' => 1,
                'error' => $error
            );
        }
        //var_dump($parametros);
        return $method_data;
    }

}
?>
