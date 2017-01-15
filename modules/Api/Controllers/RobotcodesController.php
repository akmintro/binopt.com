<?php
namespace App\Api\Controllers;

class RobotcodesController extends BaseController {

    public function readAction() {
        try {
            $manager = $this->getDI()->get('core_robotcode_manager');

            $conditions = array();
            $binds = array();

            $id = $this->request->getQuery('id');
            if($id != null) {
                $conditions[] = 'id = :id:';
                $binds['id'] = $id;
            }

            $code = $this->request->getQuery('code');
            if($code != null) {
                $conditions[] = 'code = :code:';
                $binds['code'] = $code;
            }

            $type = $this->request->getQuery('type');
            if($type != null) {
                $conditions[] = 'type = :type:';
                $binds['type'] = $type;
            }

            $start = $this->request->getQuery('start');
            if($start != null) {
                $conditions[] = 'enddate >= date(:start:)';
                $binds['start'] = $start;
            }

            $end = $this->request->getQuery('end');
            if($end != null) {
                $conditions[] = 'startdate <= date(:end:)';
                $binds['end'] = $end;
            }

            $operator = $this->request->getQuery('operator');
            if($operator != null) {
                $conditions[] = 'operator = :operator:';
                $binds['operator'] = $operator;
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
            if($offset != null) {
                $parameters['offset'] = $offset;
            }

            $limit = $this->request->getQuery('limit');
            if($limit != null) {
                $parameters['limit'] = $limit;
            }

            $st_output = $manager->restGet($parameters);

            return $this->render($st_output);
        } catch (\Exception $e) {
            return $this->render(["meta" => [
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ]]);
        }
    }

    public function updateAction($id) {
        try {
            $manager = $this->getDI()->get('core_robotcode_manager');
            if ($this->request->getHeader('CONTENT_TYPE') ==
                'application/json') {
                $data = $this->request->getJsonRawBody(true);
            } else {
                $data = [$this->request->getPut()];
            }
            if (count($data[0]) == 0) {
                throw new \Exception('Please provide data', 400);
            }
            $result = $manager->restUpdate($id, $data);

            return $this->render($result);
        } catch (\Exception $e) {
            return $this->render(["meta" => [
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ]]);
        }
    }

    public function deleteAction($id) {
        try {
            $manager = $this->getDI()->get('core_robotcode_manager');
            $st_output = $manager->restDelete($id);
            return $this->render($st_output);
        } catch (\Exception $e) {
            return $this->render(["meta" => [
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ]]);
        }
    }

    public function createAction() {

        try {
            $manager = $this->getDI()->get('core_robotcode_manager');
            if ($this->request->getHeader('CONTENT_TYPE') ==
                'application/json') {
                $data = $this->request->getJsonRawBody(true);
            } else {
                $data = $this->request->getPost();
            }
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