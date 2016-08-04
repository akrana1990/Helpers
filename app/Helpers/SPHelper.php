<?php

namespace App\Helpers;

use SparkPost\SparkPost;
use GuzzleHttp\Client;
use Http\Adapter\Guzzle6\Client as Guzzle6HttpAdapter;

/**
 * Class SPHelper
 * @package App\Helpers
 */
class SPHelper
{
    public $spark;
    public $options;

    protected $auth;
    protected $http_adapter;

    private $debug;

    /**
     * @param null $apiKey
     */

    public function __construct($apiKey = null)
    {
        $this->auth = $apiKey;
        $this->http_adapter = new Guzzle6HttpAdapter(new Client());
        $this->spark = new SparkPost($this->http_adapter, $this->auth);

        $this->debug = false;
        $this->setDefaultOptions();
    }

    /**
     *
     */

    protected function setDefaultOptions()
    {
        // Set default options

        $this->options = [
            'trackOpens'    => true,
            'trackClicks'   => false,
            'inlineCss'     => true,
        ];

        return;
    }

    /**
     *
     */

    public function debugMode()
    {
        $this->debug = true;
    }

    /**
     * @param $options
     */

    public function setOptions($options)
    {
        $this->options = array_merge($this->options, $options);
    }

    /**
     * @return bool
     */

    public function sendEmail()
    {
        $promise = $this->spark->transmissions->post($this->options);

        if ($this->debug) {
            try {
                $response = $promise->wait();
                echo $response->getStatusCode() . "\n";
                print_r($response->getBody()) . "\n";
                return $response;

            } catch (\Exception $e) {
                echo $e->getCode() . "\n";
                echo $e->getMessage() . "\n";
            }
        }
        return true;
    }
}

