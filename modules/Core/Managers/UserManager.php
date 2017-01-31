<?php
namespace App\Core\Managers;

use App\Core\Models\Operator;
use App\Core\Models\User;

class UserManager extends BaseManager
{
    public function find($parameters = null)
    {
        return User::find($parameters);
    }

    public function findFirstById($id)
    {
        return User::findFirstById((int)$id);
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

    private function getFilteredData($data, $status = null)
    {
        $result = [];
        foreach ($data as $user)
        {
            if(($status == "active" && $user['lastvisit'] == null)
                || ($status == "unactive" && $user['lastvisit'] != null))
                continue;

            $result[] = $user;
        }
        return $result;
    }

    private function getItems($items)
    {
        if(is_array($items))
            $new_items = $items;
        else
            $new_items = array($items->toArray());

        foreach ($new_items as &$item)
        {
            $user = User::findFirstById($item['id']);
            $item['username'] = $user->getUsername();

            $account = $user->getRealAccount();
            if($account == null)
                $item['startbalance'] = $item['deposits'] = $item['withdrawals'] = $item['wins'] = $item['loses'] = null;
            else {
                $item['startbalance'] = $account->getAmount();
                $item['deposits'] = $account->getDeposits();
                $item['withdrawals'] = $account->getWithdrawals();
                $account->getWinsLoses($item['wins'], $item['loses']);
                $item['currentbalance'] = $item['startbalance'] + $item['deposits'] - $item['withdrawals'] + $wins - $loses;
            }

            unset($item['firstname']);
            unset($item['lastname']);
            unset($item['email']);
            unset($item['password']);
            unset($item['country']);
            unset($item['birthday']);
            unset($item['lastip']);
            unset($item['timezoneoffset']);
            unset($item['operator']);
        }

        return $new_items;
    }

    public function restGetById($id)
    {
        $data= $this->findFirstById($id);

        if (!$data)
            throw new \Exception('Not Found', 404);

        $meta = [
            "code" => 200,
            "message" => "OK"
        ];

        $item = $data->toArray();
        $item['operator'] = Operator::findFirstById($item['operator'])->toArray();

        unset($item['password']);
        unset($item['operator']['password']);
        unset($item['operator']['ip']);
        unset($item['operator']['regdate']);

        $account = $data->getRealAccount();
        if($account == null)
            $item['startbalance'] = $item['deposits'] = $item['withdrawals'] = $item['wins'] = $item['loses'] = null;
        else {
            $item['deposits'] = $account->getDeposits();
            $item['withdrawals'] = $account->getWithdrawals();
            $item['startbalance'] = $account->getAmount();
            $wins = $loses = 0;
            $account->getWinsLoses($wins, $loses);
            $item['currentbalance'] = $item['startbalance'] + $item['deposits'] - $item['withdrawals'] + $wins - $loses;
        }

        return ["meta" => $meta, "data" => $item];
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
        ], "data" => $this->getItems($item)];
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
        $item = new User();
        $this->setFields($item, $data[0]);

        if (false === $item->create()) {
            foreach ($item->getMessages() as $message) {
                throw new \Exception($message->getMessage(), 500);
            }
        }

        return ["meta" => [
            "code" => 200,
            "message" => "OK"
        ], "data" => $this->getItems($item)];
    }

    private function setFields($item, $data)
    {
        if(isset($data['birthday']))
            $item->setBirthday($data['birthday']);

        if(isset($data['firstname']))
            $item->setFirstname($data['firstname']);

        if(isset($data['lastname']))
            $item->setLastname($data['lastname']);

        if(isset($data['country']))
            $item->setCountry($data['country']);

        if(isset($data['email']))
            $item->setEmail($data['email']);

        if(isset($data['operator']))
            $item->setOperator($data['operator']);

        if(isset($data['password']))
            $item->setPassword($this->getDI()->get('security')->hash($data['password']));

        if(isset($data['phone']))
            $item->setPhone($data['phone']);
    }

    public function restLogin($data)
    {
        $opdata = $data[0];

        if(!isset($opdata['name']))
            throw new \Exception('name is required', 500);

        if(!isset($opdata['password']))
            throw new \Exception('password is required', 500);

        $parameters = [
            "CONCAT(lastname,'.',firstname) = :name:",
            'bind' => ['name' => $opdata['name']],
        ];

        $items = $this->find($parameters);
        $data = $items->filter(function($item){
            return $item->toArray();
        });

        if (count($data) == 0)
            throw new \Exception('no user found', 500);

        if (count($data) > 1)
            throw new \Exception('many users found', 500);

        if (!($this->security->checkHash($opdata['password'], $data[0]['password']))) {
            throw new \Exception('incorrect password', 500);
        }

        return ["meta" => [
            "code" => 200,
            "message" => "OK"
        ], "data" => []];
    }
}
?>