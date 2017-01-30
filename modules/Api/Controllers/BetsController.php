<?php
namespace App\Api\Controllers;

use Phalcon\Cli\Console;

class BetsController extends BaseController {

    public function readAction() {
        try {
            $manager = $this->getDI()->get('core_bet_manager');

            $conditions = array();
            $binds = array();

            $account = $this->request->getQuery('account');
            if($account != null) {
                $conditions[] = 'account = :account:';
                $binds['account'] = $account;
            }

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

            $invest = $this->request->getQuery('invest');
            if($invest != null) {
                $conditions[] = 'invest = :invest:';
                $binds['invest'] = $invest;
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

            $sort = $this->request->getQuery('sort');
            if($sort != null) {
                $parameters['order'] = $sort;
            }

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

    public function updateAction() {
        try {
            $manager = $this->getDI()->get('core_bet_manager');
            /*
            if ($this->request->getHeader('CONTENT_TYPE') ==
                'application/json') {
                $data = $this->request->getJsonRawBody(true);
            } else {
                $data = $this->request->getPost();
            }
            */
            $data = $this->request->getJsonRawBody(true);
            if (count($data[0]) == 0) {
                throw new \Exception('Please provide data', 400);
            }

            $conditions = array();
            $binds = array();

            $account = $this->request->getQuery('account');
            if($account != null) {
                $conditions[] = 'account = :account:';
                $binds['account'] = $account;
            }

            $instrument = $this->request->getQuery('instrument');
            if($instrument != null) {
                $conditions[] = 'instrument = :instrument:';
                $binds['instrument'] = $instrument;
            }

            $parameters = [];
            if(count($conditions) > 0)
            {
                $parameters = [
                    implode(" and ", $conditions),
                    'bind' => $binds,
                ];
            }

            $parameters['order'] = 'starttime desc';

            $result = $manager->restUpdate($parameters, $data);

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
            /*
            if ($this->request->getHeader('CONTENT_TYPE') ==
                'application/json') {
                $data = $this->request->getJsonRawBody(true);
            } else {
                $data = $this->request->getPost();
            }
            */
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
}
?>