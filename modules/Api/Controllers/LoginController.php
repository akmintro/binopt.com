<?php
namespace App\Api\Controllers;

use App\Core\Models\User;

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

            $token = $this->tokenParser->getToken("user", $user->getId(), $startTime, $endTime);
            $secret = hash("sha512", $user->getEmail().$token.$startTime.$user->getId());

            $clientIp = ip2long($this->request->getClientAddress(true));
            $user->setLastip($clientIp);
            $user->setLastvisit(gmdate("Y-m-d H:i:s", time()));
            if (false === $user->update()) {
                foreach ($user->getMessages() as $message) {
                    throw new \Exception($message->getMessage(), 400);
                }
            }

            $manager = $this->getDI()->get('core_token_manager');
            $manager->restCreate("user", $user->getId(), $token, $secret, gmdate("Y-m-d H:i:s", $endTime), $clientIp);
            return $this->render(["token" => $token, "secret" => $secret]);

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

            $clientIp = ip2long($this->request->getClientAddress(true));
            $manager = $this->getDI()->get('core_token_manager');
            if($oper["id"] == 0) {
                $token = $this->tokenParser->getToken("admin", $oper["id"], $startTime, $endTime);
                $secret = hash("sha512", $oper["name"].$token.$startTime.$oper["id"]);
                $manager->restCreate("admin", $oper["id"], $token, $secret, gmdate("Y-m-d H:i:s", $endTime), $clientIp);
            }
            else {
                $token = $this->tokenParser->getToken("operator", $oper["id"], $startTime, $endTime);
                $secret = hash("sha512", $oper["name"].$token.$startTime.$oper["id"]);
                $manager->restCreate("operator", $oper["id"], $token, $secret, gmdate("Y-m-d H:i:s", $endTime), $clientIp);
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