<?php
namespace App\Api\Controllers;

class CurrencyController extends BaseController {

    public function readHistoryAction() {
        try {
            $manager = $this->getDI()->get('core_currency_manager');

            $conditions = array();
            $binds = array();

            $instrument = $this->request->getQuery('instrument');
            if($instrument != null) {
                $conditions[] = 'instrument = :instrument:';
                $binds['instrument'] = $instrument;
            }

            $start = $this->request->getQuery('start');
            if($start != null) {
                $conditions[] = 'currencytime >= :start:';
                $binds['start'] = $start;
            }

            $end = $this->request->getQuery('end');
            if($end != null) {
                $conditions[] = 'currencytime <= :end:';
                $binds['end'] = $end;
            }

            $parameters = [];
            if(count($conditions) > 0)
            {
                $parameters = [
                    implode(" and ", $conditions),
                    'bind' => $binds,
                ];
            }

            $parameters['order'] = "currencytime";

            $st_output = $manager->restGetHistory($parameters);

            return $this->render($st_output);
        } catch (\Exception $e) {
            return $this->render(["meta" => [
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ]]);
        }
    }

    public function readLastAction() {
        try {
            $filename = $this->config->parameters->currencydata;
            if(!file_exists($filename))
                throw new \Exception('Currency file Not Found', 404);

            $currency_data = json_decode(file_get_contents($filename), true);

            return $this->render(["meta" => [
                "code" => 200,
                "message" => "OK"
            ], "data" => $currency_data]);

        } catch (\Exception $e) {
            return $this->render(["meta" => [
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ]]);
        }
    }

    public function createAction() {

        try {
            $manager = $this->getDI()->get('core_currency_manager');

            $data = $this->request->getJsonRawBody(true);

            if (count($data) == 0) {
                throw new \Exception('Please provide data', 400);
            }
            $st_output = $manager->restCreate($data);
            return $this->render($st_output);
        } catch (\Exception $e) {
            return $this->render(["meta" => [
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ]]);
        }
    }


    public function deleteAction() {
        try {
            $manager = $this->getDI()->get('core_currency_manager');

            $before = $this->request->getQuery('before');
            if($before == null) {
                throw new \Exception('Before is not set', 404);
            }

            $st_output = $manager->restDelete($before);
            return $this->render($st_output);
        } catch (\Exception $e) {
            return $this->render(["meta" => [
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ]]);
        }
    }
}
?>