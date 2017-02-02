<?php
namespace App\Core\Managers;

use App\Core\Models\Account;
use App\Core\Models\Bet;
use App\Core\Models\Instrument;

class BetManager extends BaseManager
{
    public function find($parameters = null)
    {
        return Bet::find($parameters);
    }

    public function findFirst($parameters = null)
    {
        return Bet::findFirst($parameters);
    }

    public function restGet(array $parameters = null, $limit = 10, $offset = 0)
    {
        $items = $this->find($parameters);
        $data = $items->filter(function ($item) {
            return $item->toArray();
        });

        $data = $this->getFilteredData($data, $this->dispatcher->getParam("realdemo"), $this->request->getQuery('username'), $this->request->getQuery('status'));

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

    private function getFilteredData($data, $realdemo, $username = null, $status = null)
    {
        $result = [];
        foreach ($data as $bet)
        {
            $account = Account::findFirstById($bet['account']);
            if(($account->getRealdemo() != $realdemo) || ($username != null && $account->user->getUsername() != $username))
                continue;

            if(($status == "active" && $bet['result'] != null)
                || ($status == "finished" && $bet['result'] == null)
                || ($status == "won" && $bet['result'] <= 0)
                || ($status == "lost" && $bet['result'] >= 0)
                || ($status == "draw" && $bet['result'] != 0))
                continue;

            $result[] = $bet;
        }
        return $result;
    }

    public function restUpdate($parameters) {
        $items = $this->find($parameters);

        $filename = $this->config->parameters->currencydata;
        if(!file_exists($filename))
            throw new \Exception('Currency file Not Found', 404);
        $currency_data = json_decode(file_get_contents($filename), true);

        foreach ($items as $item)
        {
            $this->setFields($item,  ['endtime' => $currency_data['time'], 'endval' => $currency_data[$item->getInstrument()]['last']]);

            if (false === $item->update()) {
                return "FALSE";
                foreach ($item->getMessages() as $message) {
                    throw new \Exception($message->getMessage(), 500);
                }
            }
        }
/*
        if (false === $items->update()) {
            return "FALSE";
            foreach ($items->getMessages() as $message) {
                throw new \Exception($message->getMessage(), 500);
            }
        }
*/
        return ["meta" => [
            "code" => 200,
            "message" => "OK"
        ], "data" => $this->getItems($item)];
    }

    public function restCreate($data) {

        $parameters = ["account = :account: and instrument = :instrument: and endtime is NULL",
            'bind' => ["account" => $data[0]["account"], "instrument" => $data[0]["instrument"]],
        ];

        if(count(Bet::find($parameters)) > 0)
            throw new \Exception("there are active bets on this instrument", 500);

        $filename = $this->config->parameters->currencydata;
        if(!file_exists($filename))
            throw new \Exception('Currency file Not Found', 404);
        $currency_data = json_decode(file_get_contents($filename), true);

        $item = new Bet();
        if(!isset($currency_data[$data[0]['instrument']]))
            throw new \Exception('Instrument Not Found', 404);
        if(!isset($currency_data['time']))
            throw new \Exception('Time Not Found', 404);

        $data[0]['startval'] = $currency_data[$data[0]['instrument']]['last'];
        $data[0]['starttime'] = $currency_data['time'];
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
        if($items == null)
            return null;

        if(is_array($items))
            $new_items = $items;
        else
            $new_items = array($items->toArray());

        foreach ($new_items as &$item)
        {
            $item['instrument'] = Instrument::findFirstById($item['instrument']);
        }

        return $new_items;
    }

    private function setFields($item, $data)
    {
        if(isset($data['account']))
            $item->setAccount($data['account']);

        if(isset($data['instrument']))
            $item->setInstrument($data['instrument']);

        if(isset($data['starttime']))
            $item->setStarttime($data['starttime']);

        if(isset($data['endtime']))
            $item->setEndtime($data['endtime']);

        if(isset($data['startval']))
            $item->setStartval($data['startval']);

        if(isset($data['endval'])) {
            $item->setEndval($data['endval']);
            $item->setResult($this->getResult($item));
        }

        if(isset($data['updown']))
            $item->setUpdown($data['updown']);

        if(isset($data['invest']))
            $item->setInvest($data['invest']);
    }

    private function getResult($bet)
    {
        $winpercent = $this->config->parameters->winpercent;
        $invest = $bet->invest->getSize();

        if($bet->getEndval() == $bet->getStartval())
            $result = 0;
        elseif(($bet->getUpdown() == 1 && $bet->getEndval() > $bet->getStartval()) || ($bet->getUpdown() == 0 && $bet->getEndval() < $bet->getStartval()))
            $result = $invest * $winpercent;
        else
            $result = -$invest;

        return $result;
    }
}
?>