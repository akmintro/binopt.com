<?php
namespace App\Core\Managers;

use App\Core\Models\Account;
use App\Core\Models\Bet;
use App\Core\Models\Instrument;
use App\Core\Models\Invest;
use App\Core\Models\Settings;
use App\Core\Models\Token;
use App\Core\Models\User;

class SettingsManager extends BaseManager
{
    public function find($parameters = null)
    {
        return Settings::find($parameters);
    }

    public function findFirst($parameters = null)
    {
        return Settings::findFirst($parameters);
    }

    public function restGet()
    {
        $items = $this->find();
        $data = $items->filter(function ($item) {
            return $item->toArray();
        });

        $meta = [
            "code" => 200,
            "message" => "OK",
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
        return ["meta" => $meta, "data" => $data];
    }

    public function restUpdate($data) {

        foreach ($data as $item)
        {
            if(!isset($item["id"]))
                throw new \Exception("id is required", 500);
            if(!isset($item["value"]))
                throw new \Exception("value is required", 500);

            $setting = Settings::findFirstById($item["id"]);
            if (!$setting) {
                return ["meta" => [
                    "code" => 404,
                    "message" => "Setting Not Found"
                ]];
            }

            $setting->setValue($item["value"]);
            if (false === $setting->update()) {
                foreach ($item->getMessages() as $message) {
                    throw new \Exception($message->getMessage(), 500);
                }
            }
        }

        return ["meta" => [
            "code" => 200,
            "message" => "OK"
        ]];
    }
}
?>