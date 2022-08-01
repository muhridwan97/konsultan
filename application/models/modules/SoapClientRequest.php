<?php

use Zend\Soap\Client;

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class SoapClientRequest
 * @property Client $client
 */
class SoapClientRequest
{
    private $client;
    private $version;
    private $wsdl;

    /**
     * Initialize library.
     *
     * @param $options
     * @return Client
     */
    public function init($options)
    {
        $wsdl = get_if_exist($options, 'wsdl');
        $version = get_if_exist($options, 'version', SOAP_1_1);
        if (is_null($this->client) || $version != $this->version || $wsdl != $this->wsdl) {
            $this->client = new Client($wsdl, [
                'soap_version' => $version
            ]);
        }
        return $this->client;
    }

    /**
     * Get available services.
     *
     * @param array $options
     * @return array
     * @throws Zend\Soap\Exception\UnexpectedValueException
     */
    public function getFunctions($options = [])
    {
        $this->init($options);

        return $this->client->getFunctions();
    }

    /**
     * Make client request.
     * $result = $client->sayGoodbye(['name' => 'Angga']);
     * var_dump($result->sayGoodbyeResult)
     *
     * @param array $options
     * @return mixed|null
     */
    public function request($options = [])
    {
        $this->init($options);

        $version = get_if_exist($options, 'version', SOAP_1_1);
        $service = get_if_exist($options, 'service');
        $params = get_if_exist($options, 'params');

        if (is_callable([$this->client, $service])) {
            if ($version == SOAP_1_1) {
                $result = call_user_func([$this->client, $service], $params);
                //$result = $this->client->__call($service, $params);
                if (property_exists($result, 'return')) {
                    return $result->return;
                } else {
                    return $result;
                }
            } else {
                $result = call_user_func([$this->client, $service], $params);
                return $result->{$service . 'Result'};
            }
        }
        return null;
    }

}
