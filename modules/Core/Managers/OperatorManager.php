<?php
namespace App\Core\Managers;

use App\Core\Models\Operator;

class OperatorManager extends BaseManager
{
    public function find($parameters = null)
    {
        return Operator::find($parameters);
    }

    public function findFirstById($id)
    {
        return Operator::findFirstById((int)$id);
    }

    public function restGet(array $parameters = null, $limit = 10, $offset = 0) {
        $items = $this->find($parameters);
        $data = $items->filter(function($item){
            return $item->toArray();
        });
        $meta = [
            "code" => 200,
            "message" => "OK",
            "limit" => $limit,
            "offset" => $offset,
            "total" => count($data)
        ];

        if (count($data) > 0) {
            return ["meta" => $meta, "data" => $data];
        }

        if (isset($parameters['bind']['id'])) {
            throw new \Exception('Not Found', 404);
        } else {
            throw new \Exception('No Content', 204);
        }
    }

    public function restUpdate($id, $data) {
        $item = $this->findFirstById($id);
        if (!$item) {
            return ["meta" => [
                "code" => 404,
                "message" => "Not Found"
            ]];
        }
        $this->setFields($item, $data[0]);

        if (false === $item->update()) {
            foreach ($item->getMessages() as $message) {
                throw new \Exception($message->getMessage(), 500);
            }
        }

        return ["meta" => [
            "code" => 200,
            "message" => "OK"
        ], "data" => $item];
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
    }

    public function restCreate($data) {
        $item = new Operator();
        $this->setFields($item, $data[0]);

        if (false === $item->create()) {
            foreach ($item->getMessages() as $message) {
                throw new \Exception($message->getMessage(), 500);
            }
        }

        return ["meta" => [
            "code" => 200,
            "message" => "OK"
        ], "data" => $item];
    }

    private function setFields($item, $data)
    {
        if(isset($data['name']))
            $item->setName($data['name']);

        if(isset($data['emailsuffix']))
            $item->setEmailsuffix($data['emailsuffix']);

        if(isset($data['ip']))
            $item->setIp($data['ip']);

        if(isset($data['regdate']))
            $item->setRegDate($data['regdate']);
    }
}
?>