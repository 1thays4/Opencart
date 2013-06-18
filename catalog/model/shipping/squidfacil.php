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
                $parametros['products'][] = array(
                    'sku' => $product['sku'],
                    'quantity' => $item['quantity']
                );
            }
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://www.squidfacil.com.br/webservice/freight/freight.php");
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
            foreach ($root->carrier as $externalCarrier) {
                foreach ($externalCarrier->service as $service) {
                    $quote_data[(string) $service->name] = array(
                        'code' => 'squidfacil.' . $service->name,
                        'title' => (string) $externalCarrier->name . ' - ' . (string) $service->name,
                        'cost' => (double) $service->value,
                        'tax_class_id' => 0,
                        'text' => $this->currency->format($this->currency->convert((double) $service->value, "BRL", $this->currency->getCode()), $this->currency->getCode(), 1.0000000)
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
