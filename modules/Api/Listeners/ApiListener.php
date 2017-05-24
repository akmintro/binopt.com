<?php
namespace App\Api\Listeners;

use App\Api\Controllers\ErrorsController;
use App\Core\Models\User;


class ApiListener extends \Phalcon\Mvc\User\Plugin{
    public function beforeExecuteRoute($event, $dispatcher) {

        if (//false === $this->checkForValidApiKey()
            //|| false === $this->checkIpRateLimit()
            //||
            false === $this->resourceWithToken()
        ) {
            return false;
        }
    }

    private function checkForValidApiKey() {
        $apiKey = $this->request->getHeader('APIKEY');

        if (!in_array($apiKey, $this->config->apiKeys->toArray())) {
            $this->response->setStatusCode(403, 'Forbidden');
            $this->response->sendHeaders();
            $this->response->send();
            $this->view->disable();

            return false;
        }

        return true;
    }

    private function checkIpRateLimit() {
        $ip   = $this->request->getClientAddress();
        $time = time();
        $key  = $ip.':'.$time;

        $redis   = $this->getDI()->get('redis');
        $current = $redis->get($key);

        if ($current != null && $current > 5) {

            $this->response->setStatusCode(429, 'Too Many Requests');
            $this->response->sendHeaders();
            $this->response->send();
            $this->view->disable();

            return false;
        } else {
            $redis->multi();
            $redis->incr($key, 1);
            $redis->expire($key, 5);
            $redis->exec();
        }

        return true;
    }

    private function resourceWithToken()
    {

        $controller = $this->dispatcher->getControllerName();
        $action = $this->dispatcher->getActionName();
        $acl = $this->acl;

        if($controller == "Errors")
            return true;

        $role = "guest";

        $clientToken = $this->request->getHeader('APITOKEN');
        $clientSign = $this->request->getHeader('APISIGN');
        $clientTime = $this->request->getHeader('APITIME');

        if(file_get_contents($this->config->parameters->servertoken) == $clientToken)
            $role = "server";
        else if($clientToken && $clientSign && $clientTime) {
            $tokenData = $this->tokenParser->getData($clientToken);

            $manager = $this->getDI()->get('core_token_manager');
            $token = $manager->findToken($clientToken, $tokenData);

            if (!$token || $clientTime > $tokenData["exp"] || $clientTime < $tokenData["iat"]) {
                $this->dispatcher->forward([
                    'controller' => 'Errors',
                    'action' => 'show',
                    'params' => [403, 'Incorrect token' . time()]
                ]);
                return false;
            }


/*
            if($clientTime > time() || time() - $clientTime > 20){
                $this->dispatcher->forward([
                    'controller' => 'Errors',
                    'action'     => 'show',
                    'params'     => [403, 'Too old request or wrong time'.time()]
                ]);
                return false;
            }*/


            $data = $clientTime . $clientToken . $clientTime;
            $serverSign = hash_hmac('sha256', $data, $token->getSecret());

            //if($clientSign !== $serverSign){
            if(!$clientSign){
                $this->dispatcher->forward([
                    'controller' => 'Errors',
                    'action'     => 'show',
                    'params'     => [403, "Incorrect sign"]
                ]);
                return false;
            }

            switch ($token->getRole())
            {
                case 0:
                    $role = "admin";
                    break;
                case 1:
                    $role = "operator";
                    break;
                case 2:
                    $role = "user";
                    break;
            }
        }

        if (!$acl->isResource($controller)) {
            $this->dispatcher->forward([
                'controller' => 'Errors',
                'action'     => 'show',
                'params'     => [404, 'Resource not found']
            ]);
            return false;
        }

        $allowed = $acl->isAllowed($role, $controller, $action);
        if (!$allowed) {

            $this->dispatcher->forward(array(
                'controller' => 'Errors',
                'action'     => 'show',
                'params'     => [401, 'Access denied']
            ));
            //$this->session->destroy();
            return false;
        }

        return true;
    }
}
?>