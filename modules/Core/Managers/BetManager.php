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

    public function restUpdate($parameters, $data) {
        $item = $this->findFirst($parameters);
        if (!$item) {
            return ["meta" => [
                "code" => 404,
                "message" => "Not Found"
            ]];
        }

        if(!isset($data[0]['endtime']) || !isset($data[0]['endval']))
            throw new \Exception('endtime and endval are required', 400);

        $this->setFields($item, ['endtime' => $data[0]['endtime'], 'endval' => $data[0]['endval']] );

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



    public function restCreate($data) {
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
        if(is_array($items))
            $new_items = $items;
        else
            $new_items = array($items->toArray());

        foreach ($new_items as &$item)
        {
            $item['instrumentname'] = Instrument::findFirstById($item['instrument'])->getName();
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

        if(isset($data['endval']))
            $item->setEndval($data['endval']);

        if(isset($data['updown']))
            $item->setUpdown($data['updown']);

        if(isset($data['invest']))
            $item->setInvest($data['invest']);

        if($item->getEndtime() > $item->getStarttime())
            $item->setResult($this->getResult($item));
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