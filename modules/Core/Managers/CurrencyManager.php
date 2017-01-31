<?php
namespace App\Core\Managers;

use App\Core\Models\Currency;
use Phalcon\Mvc\Model;

class CurrencyManager extends BaseManager
{
    public function find($parameters = null)
    {
        return Currency::find($parameters);
    }

    public function restGetHistory(array $parameters = null, $limit = 10, $offset = 0)
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

    public function restCreate($data)
    {
        $time = $data['time'];
        foreach($data as $id => $obj)
        {
            if($id == 'time')
                continue;
            $item = new Currency();
            $this->setFields($item, $id, $obj['last'], $time);




            if (false === $item->create()) {
                foreach ($item->getMessages() as $message) {
                    throw new \Exception($message->getMessage(), 500);
                }
            }
        }

        return ["meta" => [
            "code" => 200,
            "message" => "OK"
        ], "data" => $this->getItems($item)];
    }

    private function setFields($item, $instrument, $value, $time)
    {
        if (isset($instrument))
            $item->setInstrument($instrument);

        if ($value)
            $item->setValue($value);

        if ($time)
            $item->setCurrencytime($time);
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