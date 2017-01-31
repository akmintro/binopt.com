<?php
namespace App\Core\Managers;

use App\Core\Models\Operator;

class SummaryManager extends BaseManager
{
    public function find($parameters = null)
    {
        return Operator::find($parameters);
    }

    public function findFirstById($id)
    {
        return Operator::findFirstById((int)$id);
    }

    public function restGetDeposits(array $parameters = null, $limit = 10, $offset = 0)
    {
        $items = $this->find($parameters);
        $data = $items->filter(function ($item) {
            return $item->toArray();
        });

        $data = $this->getDepositData($data, $this->request->getQuery('start'), $this->request->getQuery('end'));

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

    private function getDepositData($data, $start, $end)
    {
        $result = [];
        foreach ($data as $operator)
        {
            $users = Operator::findFirstById($operator['id'])->user;
            $deposits = 0;
            foreach ($users as $user) {
                $account = $user->getRealAccount();
                if($account == null)
                    continue;
                $deposits += $account->getDeposits($start, $end);
            }
            $operator['deposits'] = $deposits;
            $result[] = $operator;
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
            unset($item['password']);
        }

        return $new_items;
    }
}