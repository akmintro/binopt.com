<?php
namespace App\Api\Controllers;

use Phalcon\Cli\Console;

class BetsController extends BaseController {

    public function readAction() {
        try {
            $manager = $this->getDI()->get('core_bet_manager');

            $conditions = array();
            $binds = array();

            $instrument = $this->request->getQuery('instrument');
            if($instrument != null) {
                $conditions[] = 'instrument = :instrument:';
                $binds['instrument'] = $instrument;
            }

            $updown = $this->request->getQuery('updown');
            if($updown != null) {
                $conditions[] = 'updown = :updown:';
                $binds['updown'] = $updown;
            }

            $start = $this->request->getQuery('start');
            if($start != null) {
                $conditions[] = 'starttime >= STR_TO_DATE(:start:, \'%Y-%m-%d %h:%i:%s\')';
                $binds['start'] = $start;
            }

            $end = $this->request->getQuery('end');
            if($end != null) {
                $conditions[] = 'starttime <= STR_TO_DATE(:end:, \'%Y-%m-%d %h:%i:%s\')';
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
/*
            $sort = $this->request->getQuery('sort');
            if($sort != null) {
                $parameters['order'] = $sort;
            }*/
            $parameters['order'] = 'starttime desc';

            $offset = $this->request->getQuery('offset');
            if($offset == null)
                $offset = 0;

            $limit = $this->request->getQuery('limit');
            if($limit == null)
                $limit = 10;

            $st_output = $manager->restGet($parameters, $limit, $offset);

            return $this->render($st_output);
        } catch (\Exception $e) {
            return $this->render(["meta" => [
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ]]);
        }
    }

    public function readUserAction() {
        try {
            $manager = $this->getDI()->get('core_bet_manager');

            $parameters = ['order' => 'starttime desc'];
/*
            $sort = $this->request->getQuery('sort');
            if($sort != null) {
                $parameters['order'] = $sort;
            }*/

            $st_output = $manager->restGetUser($parameters);

            return $this->render($st_output);
        } catch (\Exception $e) {
            return $this->render(["meta" => [
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ]]);
        }
    }

    public function updateAction() {
        try {
            $manager = $this->getDI()->get('core_bet_manager');

            $conditions = array();
            $binds = array();

            $time = $this->request->getQuery('time');

            if($time != null) {
                $conditions[] = "starttime <= :time:";
                $binds['time'] = $time;
            }
            else
                throw new \Exception('Time is not set', 402);

            $conditions[] = "result is NULL";

            $parameters = [];
            if(count($conditions) > 0)
            {
                $parameters = [
                    implode(" and ", $conditions),
                    'bind' => $binds,
                ];
            }

            $result = $manager->restUpdate($parameters);

            return $this->render($result);
        } catch (\Exception $e) {
            return $this->render(["meta" => [
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ]]);
        }
    }

    public function createAction() {

        try {
            $manager = $this->getDI()->get('core_bet_manager');

            $data = $this->request->getJsonRawBody(true);
            if (count($data) == 0) {
                throw new \Exception('Please provide data', 401);
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
}
?>