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

    public function restGet(array $parameters = null, $limit = 10, $offset = 0)
    {
        $items = $this->find($parameters);
        $data = $items->filter(function ($item) {
            return $item->toArray();
        });
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

    public function restUpdate($id, $data)
    {
        $item = $this->findFirstById($id);
        if (!$item) {
            throw new \Exception('Not found', 404);
        }

        if ((isset($data[0]['name']) && strlen($data[0]['name']) == 0) || (isset($data[0]['password']) && strlen($data[0]['password']) == 0) || (isset($data[0]['emailsuffix']) && strlen($data[0]['emailsuffix']) == 0))
            throw new \Exception("Incomplete Data", 402);

        $item2 = Operator::findFirst(["id <> :id: AND (name = :name: OR emailsuffix = :emailsuffix:)", "bind" => ["id" => $id, "name" => $data[0]['name'], "emailsuffix" => $data[0]['emailsuffix']]]);
        if($item2 != null)
        {
            if($item2->getName() == $data[0]['name'])
                throw new \Exception("Name Duplicate", 411);
            throw new \Exception("Emailsuffix Duplicate", 412);
        }

        $this->setFields($item, $data[0]);

        if (false === $item->update()) {
            foreach ($item->getMessages() as $message) {
                throw new \Exception($message->getMessage(), 400);
            }
        }

        return ["meta" => [
            "code" => 200,
            "message" => "OK"
        ]/*, "data" => $this->getItems($item)*/];
    }

    public function restDelete($id)
    {
        $item = $this->findFirstById($id);

        if (!$item) {
            throw new \Exception('Not found', 404);
        }

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

    public function restCreate($data)
    {

        if (strlen($data[0]['name']) == 0 || strlen($data[0]['password']) == 0 || strlen($data[0]['emailsuffix']) == 0)
            throw new \Exception("Incomplete Data", 406);

        $item = Operator::findFirst(["name = :name: OR emailsuffix = :emailsuffix:", "bind" => ["name" => $data[0]['name'], "emailsuffix" => $data[0]['emailsuffix']]]);
        if($item != null)
        {
            if($item->getName() == $data[0]['name'])
                throw new \Exception("Name Duplicate", 411);
            throw new \Exception("Emailsuffix Duplicate", 412);
        }

        $item = new Operator();
        $this->setFields($item, $data[0]);

        if (false === $item->create()) {
            foreach ($item->getMessages() as $message) {
                throw new \Exception($message->getMessage(), 400);
            }
        }

        return ["meta" => [
            "code" => 200,
            "message" => "OK"
        ]/*, "data" => $this->getItems($item)*/];
    }

    private function setFields($item, $data)
    {
        if (isset($data['name']))
            $item->setName($data['name']);

        if (isset($data['password']))
            $item->setPassword($this->getDI()->get('security')->hash($data['password']));

        if (isset($data['emailsuffix']))
            $item->setEmailsuffix($data['emailsuffix']);

        if (isset($data['ip']))
            $item->setIp($data['ip']);

        if (isset($data['regdate']))
            $item->setRegDate($data['regdate']);
    }

    private function getItems($items)
    {
        if(is_array($items))
            $new_items = $items;
        else
            $new_items = array($items->toArray());

        foreach ($new_items as &$item)
        {
            unset($item['password']);
        }

        return $new_items;
    }

    public function restLogin($data)
    {
        $opdata = $data[0];

        if(!isset($opdata['name']))
            throw new \Exception('name is not set', 402);

        if(!isset($opdata['password']))
            throw new \Exception('password is not set', 402);

        $parameters = [
            'name = :name:',
            'bind' => ['name' => $opdata['name']],
        ];

        $items = $this->find($parameters);
        $data = $items->filter(function($item){
            return $item->toArray();
        });

        if (count($data) == 0)
            throw new \Exception('no operator found', 404);

        if (count($data) > 1)
            throw new \Exception('many operators found', 404);

        if (!($this->security->checkHash($opdata['password'], $data[0]['password']))) {
            throw new \Exception('incorrect password', 413);
        }

        return $data[0];
    }
}