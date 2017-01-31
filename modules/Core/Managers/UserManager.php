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

        $account = $this->getRealAccount($item);
        $item['deposits'] = $this->getDepostits($account);
        $item['withdrawals'] = $this->getWithdrawals($account);
        $item['startbalance'] = $account->getAmount();
        $wins = $loses = 0;
        $this->getWinsLoses($account, $wins, $loses);
        $item['currentbalance'] = $item['startbalance'] + $item['deposits'] - $item['withdrawals'] + $wins - $loses;

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

    private function getItems($items)
    {
        if(is_array($items))
            $new_items = $items;
        else
            $new_items = array($items->toArray());

        foreach ($new_items as &$item)
        {
            $item['username'] = User::findFirstById($item['id'])->getUsername();

            $account = $this->getRealAccount($item);
            $item['deposits'] = $this->getDepostits($account);
            $item['withdrawals'] = $this->getWithdrawals($account);
            $this->getWinsLoses($account, $item['wins'], $item['loses']);

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

    private function getRealAccount($item)
    {
        $user = $this->findFirstById($item['id']);
        $accounts = $user->account;

        foreach ($accounts as $account)
        {
            if($account->getRealdemo() == 1)
                return $account;
        }
    }


    private function getDepostits($account)
    {
        $value = 0;
        $deposits = $account->deposit;
        foreach ($deposits as $deposit)
        {
            $value += $deposit->getAmount();
        }
        return $value;
    }

    private function getWithdrawals($account)
    {
        $value = 0;
        $withdrawals = $account->withdrawal;
        foreach ($withdrawals as $withdrawal)
        {
            if($withdrawal->getState() == 2)
                $value += $withdrawal->getAmount();
        }
        return $value;
    }

    private function getWinsLoses($account, &$wins, &$loses)
    {
        $wins = $loses = 0;
        $bets = $account->bet;
        foreach ($bets as $bet)
        {
            $result = $bet->getResult();
            if($result == null || $result == 0)
                continue;
            if($result > 0)
                $wins += $result;
            else
                $loses += -$result;
        }
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