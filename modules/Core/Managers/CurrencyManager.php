<?php
namespace App\Core\Managers;

use App\Core\Models\Currency;

class CurrencyManager extends BaseManager
{
    public function find($parameters = null)
    {
        return Currency::find($parameters);
    }

    public function restGetHistory(array $parameters = null)
    {
        $items = $this->find($parameters);

        $data = $items->filter(function ($item) {
            return $item->toArray();
        });
        $meta = [
            "code" => 200,
            "message" => "OK",
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

    public function restCreate($data)
    {
        $time = $data['time'];
        $items = [];
        foreach($data as $id => $obj)
        {
            if($id == 'time')
                continue;
            $item = new Currency();
            $this->setFields($item, $id, $obj, $time);

            if (false === $item->create()) {
                foreach ($item->getMessages() as $message) {
                    throw new \Exception($message->getMessage(), 500);
                }
            }
            $items[] = $item;
        }

        return ["meta" => [
            "code" => 200,
            "message" => "OK"
        ], "data" => $items];
    }

    private function setFields($item, $instrument, $obj, $time)
    {
        if (isset($instrument))
            $item->setInstrument($instrument);

        if ($obj['open'])
            $item->setOpen($obj['open']);

        if ($obj['close'])
            $item->setClose($obj['close']);

        if ($obj['min'])
            $item->setMin($obj['min']);

        if ($obj['max'])
            $item->setMax($obj['max']);

        if ($time)
            $item->setCurrencytime($time);
    }

    public function restDelete($before)
    {
        $item = $this->find(["currencytime < :time:", "bind" => ["time" => $before]]);

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
}