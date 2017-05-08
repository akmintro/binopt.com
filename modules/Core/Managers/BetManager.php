<?php
namespace App\Core\Managers;

use App\Core\Models\Account;
use App\Core\Models\Bet;
use App\Core\Models\Instrument;
use App\Core\Models\Invest;
use App\Core\Models\Settings;
use App\Core\Models\Token;
use App\Core\Models\User;

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

        $data = $this->getFilteredData($data, $this->dispatcher->getParam("realdemo"), $this->request->getQuery('email'), $this->request->getQuery('status'));

        $meta = [
            "code" => 200,
            "message" => "OK",
            "limit" => (int)$limit,
            "offset" => (int)$offset,
            "total" => count($data)
        ];
/*
        if (count($data) > 0) {
            return ["meta" => $meta, "data" => array_slice($this->getItems($data), $offset, $limit)];
        }

        if (isset($parameters['bind']['id'])) {
            throw new \Exception('Not Found', 404);
        } else {
            throw new \Exception('No Content', 204);
        }*/
        return ["meta" => $meta, "data" => array_slice($this->getItems($data), $offset, $limit)];
    }

    public function restGetUser(array $parameters = null)
    {
        $items = $this->find($parameters);
        $data = $items->filter(function ($item) {
            return $item->toArray();
        });

        $account =  Account::findAccount($this->tokenParser->getUserid(), $this->request->getQuery("isreal") == "true");

        $data = $this->getAccountData($data, $account->getId());

        $meta = [
            "code" => 200,
            "message" => "OK",
            "total" => count($data)
        ];
/*
        if (count($data) > 0) {
            return ["meta" => $meta, "data" => $data];
        }

        if (isset($parameters['bind']['id'])) {
            throw new \Exception('Not Found', 404);
        } else {
            throw new \Exception('No Content', 204);
        }*/
        return ["meta" => $meta, "data" => ["balance" => $account->getBalance(), "history" => $data]];
    }

    private function getFilteredData($data, $realdemo, $email = null, $status = null)
    {
        $result = [];
        foreach ($data as $bet)
        {
            $account = Account::findFirstById($bet['account']);
            if(($account->getRealdemo() != $realdemo) || ($email != null && $account->user->getEmail() != $email))
                continue;

            if(($status == "active" && $bet['result'] != null)
                || ($status == "finished" && $bet['result'] == null)
                || ($status == "won" && $bet['result'] <= 0)
                || ($status == "lost" && $bet['result'] >= 0)
                || ($status == "draw" && $bet['result'] != 0))
                continue;

            $bet['email'] = $account->user->getEmail();
            unset($bet['account']);
            $result[] = $bet;
        }
        return $result;
    }

    private function getAccountData($data, $accountId)
    {
        $result = [];
        foreach ($data as $bet)
        {
            if($bet['account'] == $accountId) {
                $result[] = $bet;
            }
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
            $this->setFields($item,  ['endval' => $currency_data[$item->getInstrument()]['close']]);

            if (false === $item->update()) {
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

    public function restCreate($data) {
        $account =  Account::findAccount($this->tokenParser->getUserid(), $data[0]["isreal"]);/*
        $parameters = ["account = :account: and instrument = :instrument: and result is NULL",
            'bind' => ["account" => $account->getId(), "instrument" => $data[0]["instrument"]],
        ];*/

        $parameters = ["account = :account: and result is NULL",
            'bind' => ["account" => $account->getId()],
        ];
        if(count(Bet::find($parameters)) > 5)
            throw new \Exception("you can't have more than 5 bets at the same moment from this account", 500);
/*
        $invest = Invest::findFirstById($data[0]["invest"]);
        if($invest == null)
            throw new \Exception("invest not found", 404);

        if($account->user->getBalance() < $invest->getSize())
            throw new \Exception("balance is too small for this bet", 500);
*/

        $invest = (int)$data[0]['invest'];
        if($invest < 1)
            throw new \Exception("incorrect invest", 500);
        if($account->getBalance() < $invest)
            throw new \Exception("balance is too small for this bet", 500);


        $filename = $this->config->parameters->currencydata;
        if(!file_exists($filename))
            throw new \Exception('Currency file Not Found', 404);
        $currency_data = json_decode(file_get_contents($filename), true);

        if(!isset($currency_data[$data[0]['instrument']]))
            throw new \Exception('instrument not found', 404);


        $data[0]['account'] = $account->getId();
        $data[0]['startval'] = $currency_data[$data[0]['instrument']]['close'];
        $data[0]['starttime'] = $currency_data[$data[0]['instrument']]['currencytime'];

        $item = new Bet();
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
            unset($item['account']);
        }

        return $new_items;
    }

    private function setFields($item, $data)
    {
        if(isset($data['account']))
            $item->setAccount($data['account']);

        if(isset($data['instrument']))
            $item->setInstrument($data['instrument']);

        if(isset($data['starttime'])) {
            $item->setStarttime($data['starttime']);

            $endtime = strtotime($data['starttime']);
            $endtime = ceil(($endtime + 60) / 60) * 60;
            $item->setEndtime(date("Y-m-d H:i:s", $endtime));
        }

        if(isset($data['startval']))
            $item->setStartval($data['startval']);

        if(isset($data['endval'])) {
            $item->setEndval($data['endval']);
            $item->setResult($this->getResult($item));
        }

        if(isset($data['updown']) && ($data['updown'] == 0 || $data['updown'] == 1))
            $item->setUpdown($data['updown']);

        if(isset($data['invest']))
            $item->setInvest($data['invest']);
    }

    private function getResult($bet)
    {
        $winpercent = (float)(Settings::findFirstById(1)->getValue());
        //$invest = $bet->invest->getSize();
        $invest = $bet->getInvest();
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