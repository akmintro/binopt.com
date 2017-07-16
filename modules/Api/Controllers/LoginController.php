<?php
namespace App\Api\Controllers;

use App\Core\Models\Socnetwork;
use App\Core\Models\User;
use Phalcon\Http\Response;

class LoginController extends BaseController {

    protected $sessionDuration = 86400;

    public function authUserAction() {

        try {
            $manager = $this->getDI()->get('core_user_manager');
            $data = $this->request->getJsonRawBody(true);

            if (count($data) == 0) {
                throw new \Exception('Please provide data', 401);
            }
            $user = $manager->restLogin($data);

            $startTime = time();
            $endTime = $startTime + $this->sessionDuration;
            if($data[0]["remember"])
                $endTime = $startTime + $this->sessionDuration * 30;

            $clientTime = $startTime;
            if($data[0]["time"])
                $clientTime = $data[0]["time"];

            $token = $this->tokenParser->getToken("user", $user->getId(), $startTime, $endTime);
            $secret = hash("sha512", $user->getEmail().$token.$startTime.$user->getId());

            $clientIp = ip2long($this->request->getClientAddress(true));
            $user->setLastip($clientIp);
            if (false === $user->update()) {
                foreach ($user->getMessages() as $message) {
                    throw new \Exception($message->getMessage(), 400);
                }
            }

            $manager = $this->getDI()->get('core_token_manager');
            $manager->restCreate("user", $user->getId(), $token, $secret, gmdate("Y-m-d H:i:s", $endTime), $clientIp, $startTime - $clientTime);
            return $this->render(["token" => $token, "secret" => $secret]);

        } catch (\Exception $e) {
            return $this->render(["meta" => [
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ]]);
        }
    }

    public function uloginUserAction() {

        try {
            $data = $this->request->getJsonRawBody(true);
            $s = file_get_contents('http://ulogin.ru/token.php?token=' . $data[0]["token"] . '&host=' . $_SERVER['HTTP_HOST']);

            $userdata = json_decode($s, true);
            if($userdata['error']){
                throw new \Exception("Authorization error", 413);
            }


            $socnet = Socnetwork::findFirst(["name = :name:", "bind" => ["name" => $userdata['network']]]);
            if(!$socnet){
                throw new \Exception("Authorization error", 413);
            }

            $manager = $this->getDI()->get('core_user_manager');
            $user = $manager->findFirst(["email = :email: and socnet = :socnet:", 'bind' => ['email' => $userdata['email'], 'socnet' => $socnet->getId()]]);


            if($user == null)
            {
                $item['email'] = $userdata['email'];
                $item['socnet'] = $socnet->getId();
                if($userdata['first_name'])
                    $item['firstname'] = $userdata['first_name'];
                if($userdata['last_name'])
                    $item['lastname'] = $userdata['last_name'];
                if($userdata['bdate'])
                    $item['birthday'] = date_format(date_create_from_format('d.m.Y', $userdata['bdate']), 'Y-m-d');
                if($userdata['phone'])
                    $item['phone'] = $userdata['phone'];

                $item['password'] = substr(md5((microtime() - rand(3,1000000)) * rand(1,1000)),rand(0,20),20);

                $item['operator'] = 0;

                $manager->registerUser([0 => $item], true);

                $user = $manager->findFirst(["email = :email: and socnet = :socnet:", 'bind' => ['email' => $userdata['email'], 'socnet' => $socnet->getId()]]);
                if(!$user){
                    throw new \Exception("Authorization error", 413);
                }
            }

            $startTime = time();
            $endTime = $startTime + $this->sessionDuration;

            $clientTime = $startTime;
            if($data[0]["time"])
                $clientTime = $data[0]["time"];

            $token = $this->tokenParser->getToken("user", $user->getId(), $startTime, $endTime);
            $secret = hash("sha512", $user->getEmail().$token.$startTime.$user->getId());

            $clientIp = ip2long($this->request->getClientAddress(true));
            $user->setLastip($clientIp);
            if (false === $user->update()) {
                foreach ($user->getMessages() as $message) {
                    throw new \Exception($message->getMessage(), 400);
                }
            }

            $manager = $this->getDI()->get('core_token_manager');
            $manager->restCreate("user", $user->getId(), $token, $secret, gmdate("Y-m-d H:i:s", $endTime), $clientIp, $startTime - $clientTime);
            return $this->render(["email" => $userdata['email'].' ('.strtoupper($socnet->getCode()).')', "token" => $token, "secret" => $secret]);

        } catch (\Exception $e) {
            return $this->render(["meta" => [
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ]]);
        }
    }

    public function isauthUserAction() {
        return $this->render(["meta" => [
            "code" => 200,
            "message" => "OK"
        ]]);
    }

    public function unauthUserAction() {

        try {
            $clientToken = $this->request->getHeader('APITOKEN');
            $tokenData = $this->tokenParser->getData($clientToken);

            $manager = $this->getDI()->get('core_token_manager');
            $token = $manager->findToken($clientToken, $tokenData);

            if ($token && false === $token->delete()) {
                foreach ($token->getMessages() as $message) {
                    throw new \Exception($message->getMessage(), 400);
                }
            }

            return $this->render(["meta" => [
                "code" => 200,
                "message" => "OK"
            ]]);
        } catch (\Exception $e) {
            return $this->render(["meta" => [
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ]]);
        }
    }

    public function authOperAction() {

        try {
            $manager = $this->getDI()->get('core_operator_manager');
            $data = $this->request->getJsonRawBody(true);

            if (count($data) == 0) {
                throw new \Exception('Please provide data', 401);
            }
            $oper = $manager->restLogin($data);

            $startTime = time();

            $endTime = $startTime + $this->sessionDuration;
            if($data[0]["remember"])
                $endTime = $startTime + $this->sessionDuration * 30;

            $clientTime = $startTime;
            if($data[0]["time"])
                $clientTime = $data[0]["time"];

            $clientIp = ip2long($this->request->getClientAddress(true));
            if($oper['ip'] != null && $oper['ip'] != $clientIp){
                throw new \Exception('IP not allowed', 417);
            }

            $manager = $this->getDI()->get('core_token_manager');
            if($oper["id"] == 0) {
                $token = $this->tokenParser->getToken("admin", $oper["id"], $startTime, $endTime);
                $secret = hash("sha512", $oper["name"].$token.$startTime.$oper["id"]);
                $manager->restCreate("admin", $oper["id"], $token, $secret, gmdate("Y-m-d H:i:s", $endTime), $clientIp, $startTime - $clientTime);
            }
            else {
                $token = $this->tokenParser->getToken("operator", $oper["id"], $startTime, $endTime);
                $secret = hash("sha512", $oper["name"].$token.$startTime.$oper["id"]);
                $manager->restCreate("operator", $oper["id"], $token, $secret, gmdate("Y-m-d H:i:s", $endTime), $clientIp, $startTime - $clientTime);
            }

            return $this->render(["token" => $token, "secret" => $secret]);
        } catch (\Exception $e) {
            return $this->render(["meta" => [
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ]]);
        }
    }

    public function isauthOperAction() {
        return $this->render(["meta" => [
            "code" => 200,
            "message" => "OK"
        ]]);
    }

    public function unauthOperAction() {

        try {
            $clientToken = $this->request->getHeader('APITOKEN');
            $tokenData = $this->tokenParser->getData($clientToken);

            $manager = $this->getDI()->get('core_token_manager');
            $token = $manager->findToken($clientToken, $tokenData);

            if ($token && false === $token->delete()) {
                foreach ($token->getMessages() as $message) {
                    throw new \Exception($message->getMessage(), 400);
                }
            }

            return $this->render(["meta" => [
                "code" => 200,
                "message" => "OK"
            ]]);
        } catch (\Exception $e) {
            return $this->render(["meta" => [
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ]]);
        }
    }

    public function deleteAction() {
        try {
            $manager = $this->getDI()->get('core_token_manager');

            $st_output = $manager->restDelete();
            return $this->render($st_output);
        } catch (\Exception $e) {
            return $this->render(["meta" => [
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ]]);
        }
    }
}
?>