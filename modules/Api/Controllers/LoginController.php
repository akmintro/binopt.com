<?php
namespace App\Api\Controllers;

class LoginController extends BaseController {

    protected $sessionDuration = 86400;

    public function authUserAction() {

        try {
            $manager = $this->getDI()->get('core_user_manager');
            $data = $this->request->getJsonRawBody(true);

            if (count($data) == 0) {
                throw new \Exception('Please provide data', 400);
            }
            $user = $manager->restLogin($data);

            $startTime = time();

            $endTime = $startTime + $this->sessionDuration;
            if($data[0]["remember"])
                $endTime = $startTime + $this->sessionDuration * 30;

            $token = $this->tokenParser->getToken("user", $user["id"], $startTime, $endTime);

            $secret = hash("sha512", $user["username"].$token.$startTime.$user["id"].$user["phone"]);

            $manager = $this->getDI()->get('core_token_manager');
            $manager->restCreate("user", $user["id"], $token, $secret);

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
                    throw new \Exception($message->getMessage(), 500);
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
                throw new \Exception('Please provide data', 400);
            }
            $oper = $manager->restLogin($data);

            $startTime = time();

            $endTime = $startTime + $this->sessionDuration;
            if($data[0]["remember"])
                $endTime = $startTime + $this->sessionDuration * 30;

            $token = $this->tokenParser->getToken("operator", $oper["id"], $startTime, $endTime);

            $secret = hash("sha512", $oper["name"].$token.$startTime.$oper["id"]);

            $manager = $this->getDI()->get('core_token_manager');
            $manager->restCreate("operator", $oper["id"], $token, $secret);

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
                    throw new \Exception($message->getMessage(), 500);
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
}
?>