<?php
namespace App\Api\Controllers;

class InstrumentsController extends BaseController {

    public function readAction() {
        try {
            $manager = $this->getDI()->get('core_instrument_manager');

            $conditions = array();
            $binds = array();

            $id = $this->request->getQuery('id');
            if($id != null) {
                $conditions[] = 'id = :id:';
                $binds['id'] = $id;
            }

            $name = $this->request->getQuery('name');
            if($name != null) {
                $conditions[] = 'name = :name:';
                $binds['name'] = $name;
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
            $manager = $this->getDI()->get('core_instrument_manager');

            $data = $this->request->getJsonRawBody(true);
            if (count($data[0]) == 0) {
                throw new \Exception('Please provide data', 401);
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
            $manager = $this->getDI()->get('core_instrument_manager');
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
            $manager = $this->getDI()->get('core_instrument_manager');

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