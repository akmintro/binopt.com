<?php
namespace App\Api\Listeners;

use App\Api\Controllers\ErrorsController;
use App\Core\Models\Operator;
use App\Core\Models\User;


class ApiListener extends \Phalcon\Mvc\User\Plugin{
    public function beforeExecuteRoute($event, $dispatcher) {

        if (//false === $this->checkForValidApiKey() ||
            //false === $this->checkIpRateLimit() ||
            false === $this->resourceWithToken() ||
            false
        ) {
            return false;
        }
    }
/*
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
    }*/
/*
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
*/
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
        $clientIp = ip2long($this->request->getClientAddress(true));

        if(file_get_contents($this->config->parameters->servertoken) == $clientToken)
            $role = "server";
        else if($clientToken && $clientSign && $clientTime) {
            $tokenData = $this->tokenParser->getData($clientToken);

            $manager = $this->getDI()->get('core_token_manager');
            $token = $manager->findToken($clientToken, $tokenData);

            if (!$token || time() > (int)$tokenData["exp"] || time() < (int)$tokenData["iat"]) {
                $this->dispatcher->forward([
                    'controller' => 'Errors',
                    'action' => 'show',
                    'params' => [405, 'Incorrect token' . time() . "-" . $tokenData["iat"].((!$token ) ? $tokenData : "false")]
                ]);
                return false;
            }

            $timeshift = $token->getTimeshift();
            if($clientTime + $timeshift - 15 > time() || $clientTime + $timeshift + 15 < time()) {
                $this->dispatcher->forward([
                    'controller' => 'Errors',
                    'action' => 'show',
                    'params' => [416, 'Too old request or incorrect time' . (time() - $clientTime)]
                ]);
                return false;
            }

            $tokenIp = $token->getIp();
            if($tokenIp != null && $tokenIp != $clientIp){
                $this->dispatcher->forward([
                    'controller' => 'Errors',
                    'action' => 'show',
                    'params' => [417, 'Incorrect IP']
                ]);
                return false;
            }

            $data = $clientTime . $clientToken . $clientTime;
            $serverSign = hash_hmac('sha256', $data, $token->getSecret());

            if($clientSign !== $serverSign){
            //if(!$clientSign){
                $this->dispatcher->forward([
                    'controller' => 'Errors',
                    'action'     => 'show',
                    'params'     => [406, "Incorrect sign"]
                ]);
                return false;
            }

            switch ($token->getRole())
            {
                case 0:
                    $role = "admin";
                    $adm = Operator::findFirstById($tokenData["sub"]);
                    if($adm == null) {
                        $this->dispatcher->forward([
                            'controller' => 'Errors',
                            'action' => 'show',
                            'params' => [405, 'Incorrect token']
                        ]);
                        return false;
                    }
                    $serverIp = $adm->getIp();
                    if($serverIp != null && $serverIp != $clientIp){
                        $this->dispatcher->forward([
                            'controller' => 'Errors',
                            'action' => 'show',
                            'params' => [417, 'Incorrect IP']
                        ]);
                        return false;
                    }

                    break;
                case 1:
                    $role = "operator";
                    $oper = Operator::findFirstById($tokenData["sub"]);
                    if($oper == null) {
                        $this->dispatcher->forward([
                            'controller' => 'Errors',
                            'action' => 'show',
                            'params' => [405, 'Incorrect token']
                        ]);
                        return false;
                    }
                    $serverIp = $oper->getIp();
                    if($serverIp != null && $serverIp != $clientIp){
                        $this->dispatcher->forward([
                            'controller' => 'Errors',
                            'action' => 'show',
                            'params' => [417, 'Incorrect IP']
                        ]);
                        return false;
                    }
                    break;
                case 2:
                    $role = "user";
                    $user = User::findFirstById($tokenData["sub"]);
                    if($user == null) {
                        $this->dispatcher->forward([
                            'controller' => 'Errors',
                            'action' => 'show',
                            'params' => [405, 'Incorrect token null']
                        ]);
                        return false;
                    }
                    $user->setLastvisit(gmdate("Y-m-d H:i:s", time()));
                    if (false === $user->update()) {
                        foreach ($user->getMessages() as $message) {
                            throw new \Exception($message->getMessage(), 400);
                        }
                    }
                    break;
            }
        }

        if (!$acl->isResource($controller)) {
            $this->dispatcher->forward([
                'controller' => 'Errors',
                'action'     => 'show',
                'params'     => [403, 'Resource not found']
            ]);
            return false;
        }

        $allowed = $acl->isAllowed($role, $controller, $action);
        if (!$allowed) {

            $this->dispatcher->forward(array(
                'controller' => 'Errors',
                'action'     => 'show',
                'params'     => [407, 'Access denied']
            ));
            //$this->session->destroy();
            return false;
        }

        return true;
    }
}
?>