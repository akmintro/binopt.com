<?php
namespace App\Core\Managers;

use App\Core\Models\Token;
use App\Core\Models\User;

class TokenManager extends BaseManager
{
    protected $roles = ["admin" => 0, "operator" => 1, "user" => 2];

    public function find($parameters = null)
    {
        return Token::find($parameters);
    }

    public function restCreate($role, $id, $token, $secret, $exptime, $ip) {
        $item = new Token();
        $item->setRole($this->roles[$role]);
        $item->setId($id);
        $item->setTokenVal($token);
        $item->setSecret($secret);
        $item->setExptime($exptime);
        $item->setIp($ip);

        if (false === $item->create()) {
            foreach ($item->getMessages() as $message) {
                throw new \Exception($message->getMessage(), 400);
            }
        }

        return ["meta" => [
            "code" => 200,
            "message" => "OK"
        ]];
    }

    public function findToken($token, $tokenData)
    {
        $parameters = ["role = :role: and id = :id: and token_val = :token:", "bind" => ["role" => $this->roles[$tokenData["iss"]], "id" => $tokenData["sub"], "token" => $token]];
        $item = Token::findFirst($parameters);

        return $item;
    }

    public function restDelete()
    {
        $item = $this->find(["exptime < :time:", "bind" => ["time" => gmdate("Y-m-d H:i:s")]]);

        if (false === $item->delete()) {
            foreach ($item->getMessages() as $message) {
                throw new \Exception($message->getMessage(), 400);
            }
        }

        return ["meta" => [
            "code" => 200,
            "message" => "OK"
        ]];
    }
}
?>