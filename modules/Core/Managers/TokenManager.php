<?php
namespace App\Core\Managers;

use App\Core\Models\Token;
use App\Core\Models\User;

class TokenManager extends BaseManager
{
    protected $roles = ["admin" => 0, "operator" => 1, "user" => 2];
/*
    public function find($parameters = null)
    {
        return Token::find($parameters);
    }

    public function restGet(array $parameters = null, $limit = 10, $offset = 0)
    {
        $items = $this->find($parameters);
        $data = $items->filter(function ($item) {
            return $item->toArray();
        });

        $data = $this->getFilteredData($data, $this->request->getQuery('status'));

        $meta = [
            "code" => 200,
            "message" => "OK",
            "limit" => (int)$limit,
            "offset" => (int)$offset,
            "total" => count($data)
        ];

        if (count($data) > 0) {
            return ["meta" => $meta, "data" => array_slice($this->getItems($data), $offset, $limit)];
        }

        if (isset($parameters['bind']['id'])) {
            throw new \Exception('Not Found', 404);
        } else {
            throw new \Exception('No Content', 204);
        }
    }


    public function restDelete($id) {
        $item = $this->findFirstById($id);

        if (!$item) {
            throw new \Exception('Not found', 404);
        }

        if (false === $item->delete()) {
            foreach ($item->getMessages() as $message) {
                throw new \Exception($message->getMessage(), 500);
            }
        }

        return ["meta" => [
            "code" => 200,
            "message" => "OK"
        ]];
    }*/

    public function restCreate($role, $id, $token, $secret) {
        $item = new Token();
        $item->setRole($this->roles[$role]);
        $item->setId($id);
        $item->setTokenVal($token);
        $item->setSecret($secret);

        if (false === $item->create()) {
            foreach ($item->getMessages() as $message) {
                throw new \Exception($message->getMessage(), 500);
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
}
?>