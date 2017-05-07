<?php
namespace App\Core\Managers;

use App\Core\Models\Account;
use App\Core\Models\Deposit;
use App\Core\Models\Promo;

class DepositManager extends BaseManager
{
    public function find($parameters = null)
    {
        return Deposit::find($parameters);
    }

    public function findFirstById($id)
    {
        return Deposit::findFirstById((int)$id);
    }

    public function restGet(array $parameters = null, $limit = 10, $offset = 0)
    {
        $items = $this->find($parameters);
        $data = $items->filter(function ($item) {
            return $item->toArray();
        });

        $data = $this->getFilteredData($data, $this->request->getQuery('email'), $this->request->getQuery('operator'), $this->request->getQuery('status'));

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
        foreach ($data as $deposit)
        {
            $account = Account::findFirstById($deposit['account']);
            if(($account->getRealdemo() != 1) || ($email != null && $account->user->getEmail() != $email) || ($operator != null && $account->user->getOperator() != $operator))
                continue;

            if(($status == "try" && $deposit['state'] != 0)
                || ($status == "success" && $deposit['state'] != 1))
                continue;

            $deposit['balance'] = $account->user->getBalance();
            $deposit['email'] = $account->user->getEmail();
            $deposit['registration'] = $account->user->getRegistration();
            unset($deposit['account']);

            $result[] = $deposit;
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
        $item = new Deposit();
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

    private function setFields($item, &$data)
    {
        if(isset($data['account']))
            $item->setAccount($data['account']);

        if(isset($data['amount']))
            $item->setAmount($data['amount']);

        if(isset($data['wallet']))
            $item->setWallet($data['wallet']);

        if(isset($data['deposittime']))
            $item->setDeposittime($data['deposittime']);

        if(isset($data['promo'])) {
            $promo = Promo::findFirst(["code = :code:", "bind" => ["code" => $data['promo']]]);

            if($promo == null)
                throw new \Exception("promo not found", 404);

            $item->setPromo($promo->getId());
        }

        $admin = $this->dispatcher->getParam("admin");
        if($admin)
            $item->setAdmin($admin);
    }
}
?>