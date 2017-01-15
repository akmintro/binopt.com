<?php
namespace App\Core\Managers;

use App\Core\Models\Bet;

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

    public function restGet(array $parameters = null, $limit = 10, $offset = 0) {
        $items = $this->find($parameters);
        $data = $items->filter(function($item){
            return $item->toArray();
        });
        $meta = [
            "code" => 200,
            "message" => "OK",
            "limit" => $limit,
            "offset" => $offset,
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
        ], "data" => $item];
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
        ], "data" => $item];
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
    }
}
?>