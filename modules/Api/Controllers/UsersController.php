<?php
namespace App\Api\Controllers;


class UsersController extends BaseController {

    public function readAction() {
        try {
            $manager = $this->getDI()->get('core_user_manager');

            $conditions = array();
            $binds = array();

            $id = $this->request->getQuery('id');
            if($id != null) {
                $conditions[] = 'id = :id:';
                $binds['id'] = $id;
            }

            $fname = $this->request->getQuery('firstname');
            if($fname != null) {
                $conditions[] = 'firstname = :firstname:';
                $binds['firstname'] = $fname;
            }

            $lname = $this->request->getQuery('lastname');
            if($lname != null) {
                $conditions[] = 'lastname = :lastname:';
                $binds['lastname'] = $lname;
            }

            $email = $this->request->getQuery('email');
            if($email != null) {
                $conditions[] = 'email = :email:';
                $binds['email'] = $email;
            }

            $start = $this->request->getQuery('start');
            if($start != null) {
                $conditions[] = 'registration >= date(:start:)';
                $binds['start'] = $start;
            }

            $end = $this->request->getQuery('end');
            if($end != null) {
                $conditions[] = 'registration <= date(:end:)';
                $binds['end'] = $end;
            }

            $phone = $this->request->getQuery('phone');
            if($phone != null) {
                $conditions[] = 'phone = :phone:';
                $binds['phone'] = $phone;
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

    public function getAction($id) {
        try {
            $manager = $this->getDI()->get('core_user_manager');

            $st_output = $manager->restGetById($id);

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
            $manager = $this->getDI()->get('core_user_manager');
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
            $manager = $this->getDI()->get('core_user_manager');
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
            $manager = $this->getDI()->get('core_user_manager');
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

    public function changePasswordAction() {
        try {
            $manager = $this->getDI()->get('core_user_manager');

            $data = $this->request->getJsonRawBody(true);
            if (count($data[0]) == 0) {
                throw new \Exception('Please provide data', 400);
            }
            $result = $manager->changePassword($data);

            return $this->render($result);
        } catch (\Exception $e) {
            return $this->render(["meta" => [
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ]]);
        }
    }

}
?>