<?php
namespace App\Api\Controllers;

use \Phalcon\Http\Response;

class BaseController extends \Phalcon\Mvc\Controller {
    protected $statusCode = 200;

    protected $headers    = [
        'Access-Control-Allow-Origin' => '*',
        'Access-Control-Allow-Headers' => 'X-Requested-With, content-type, access-control-allow-origin, accept, apikey',
        'Access-Control-Allow-Methods' => 'GET, PUT, POST, DELETE, OPTIONS','Access-Control-Allow-Credentials' => 'true'
    ];

    protected $payload = '';

    protected $format = 'json';

    protected function initResponse($status = 200) {
        $this->statusCode = $status;
        $this->headers    = array();
        $this->payload    = '';
    }

    protected function _getContent($payload) {
        return json_encode($payload);
    }

    protected function output() {
        $payload     = $this->getPayload();
        $status      = $this->getStatusCode();
        $description = $this->getHttpCodeDescription($status);
        $headers     = $this->getHeaders();

        $response = (new Response())
            ->setStatusCode($status, $description)
            ->setContentType('application/json', 'UTF-8')
            ->setContent(json_encode($payload, JSON_PRETTY_PRINT))
        ;

        foreach ($headers as $key => $value) {
            $response->setHeader($key, $value);
        }

        $this->view->disable();

        return $response;
    }

    protected function render($st_output, $statusCode = 200){
        $this->initResponse();

        $this->setStatusCode($statusCode);
        $this->setPayload($st_output);

        return $this->output();
    }

    protected function getPayload()
    {
        return $this->payload;
    }

    protected function setPayload($_payload)
    {
        $this->payload = $_payload;
    }

    protected function getStatusCode()
    {
        return $this->statusCode;
    }

    protected function setStatusCode($_statusCode)
    {
        $this->statusCode = $_statusCode;
    }

    protected function getHeaders()
    {
        return $this->headers;
    }

    protected function getHttpCodeDescription($_status)
    {
		return "Http Code Description";
    }
}
?>