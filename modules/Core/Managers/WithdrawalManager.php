<?php
namespace App\Core\Managers;

use App\Core\Models\Withdrawal;
use App\Core\Models\Account;

class WithdrawalManager extends BaseManager
{
    public function find($parameters = null)
    {
        return Withdrawal::find($parameters);
    }

    public function findFirstById($id)
    {
        return Withdrawal::findFirstById((int)$id);
    }

    public function restGet(array $parameters = null, $limit = 10, $offset = 0)
    {
        $items = $this->find($parameters);
        $data = $items->filter(function ($item) {
            return $item->toArray();
        });


        if($this->tokenParser->getOperid() == 0)
            $operator = $this->request->getQuery('operator');
        else
            $operator = $this->tokenParser->getOperid();

        $data = $this->getFilteredData($data, $this->request->getQuery('email'), $operator, $this->request->getQuery('status'));

        $meta = [
            "code" => 200,
            "message" => "OK",
            "limit" => (int)$limit,
            "offset" => (int)$offset,
            "total" => count($data)
        ];

        if (count($data) > 0) {
            return ["meta" => $meta, "data" => array_slice($data, $offset, $limit)];
        }

        if (isset($parameters['bind']['id'])) {
            throw new \Exception('Not Found', 404);
        } else {
            throw new \Exception('No Content', 204);
        }
    }

    private function getFilteredData($data, $email = null, $operator = null, $status = null)
    {
        $result = [];
        foreach ($data as $withdrawal)
        {
            $account = Account::findFirstById($withdrawal['account']);
            if(($account->getRealdemo() != 1) || ($email != null && $account->user->getEmail() != $email) || ($operator != null && $account->user->getOperator() != $operator))
                continue;

            if(($status == "canceled" && $withdrawal['state'] != 0)
                || ($status == "completed" && $withdrawal['state'] != 1)
                || ($status == "active" && $withdrawal['state'] != 2))
                continue;

            $withdrawal['balance'] = $account->user->getBalance();
            $withdrawal['email'] = $account->user->getEmail();
            $withdrawal['registration'] = $account->user->getRegistration();

            $result[] = $withdrawal;
        }
        return $result;
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
        $item = new Withdrawal();
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
        if(isset($data['account']))
            $item->setAccount($data['account']);

        if(isset($data['amount']))
            $item->setAmount($data['amount']);

        if(isset($data['wallet']))
            $item->setWallet($data['wallet']);

        if(isset($data['deposittime']))
            $item->setWithdrawaltime($data['deposittime']);

        if(isset($data['state']))
            $item->setState($data['state']);
    }
}
?>