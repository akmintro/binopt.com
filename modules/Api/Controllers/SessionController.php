<?php
namespace App\Api\Controllers;

/*
 *
 * NOT USED ANYWHERE
 *
 */

class SessionController extends BaseController {

    /**
     * @return mixed
     */
    public function readAction() {


        try {
            $session = $this->rest_session;
            if (!$token = $this->request->getQuery('token')) {
                throw new \Exception('Unauthorized', 401);
            }
            $sessionData = $session->getData($token);
            $meta = [
                "code" => 200,
                "message" => "OK",
            ];

            $st_output['meta'] = $meta;
            $st_output['data'] = $sessionData;
            return $this->render($st_output);
        } catch (\Exception $e) {
            return $this->render(["meta" => [
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ]]);
        }
    }

    /**
     * @return mixed
     */
    public function updateAction() {
        try {
            $body = $this->request->getJsonRawBody(true);
            $session = $this->rest_session;
            $bodyAvailability = (isset($body) && $body != "");
            if ($token = $this->request->getQuery('token')) {
                if ($bodyAvailability) {
                    $sessionData['data'] = $session->addData($token, json_encode($body));
                    $meta = [
                        "code" => 200,
                        "message" => "OK",
                    ];

                    $st_output['meta'] = $meta;
                    $st_output['data'] = $sessionData;
                    return $this->render($st_output);

                }
                throw new \Exception('BODY_IS_EMPTY', 401);
            }
            throw new \Exception('Unauthorized', 401);

        } catch (\Exception $e) {
            return $this->render(["meta" => [
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ]]);
        }
    }

    /**
     * @return mixed
     */
    public function deleteAction() {
        try {
            $session = $this->rest_session;
            $body = $this->request->getJsonRawBody(true);
            $bodyAvailability = (isset($body) && $body != "");
            if ($token = $this->request->getQuery('token')) {
                if ($bodyAvailability) {
                    $sessionData['data'] = $session->deleteItem($token, json_encode($body));
                    $meta = [
                        "code" => 200,
                        "message" => "OK",
                    ];

                    $st_output['meta'] = $meta;
                    $st_output['data'] = $sessionData;
                    return $this->render($st_output);

                }
                throw new \Exception('BODY_IS_EMPTY', 401);
            }
            throw new \Exception('Unauthorized', 401);
        } catch (\Exception $e) {
            return $this->render(["meta" => [
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ]]);
        }
    }

    /**
     * @return mixed
     */
    public function createAction() {
        try {
            $session = $this->getDI()->get('rest_session');
            $sessionData = $session->login([]);
            $data = $session->getData($sessionData->getToken());

            $meta = [
                "code" => 200,
                "message" => "OK",
            ];

            $st_output['meta'] = $meta;
            $st_output['data'] = $data;
            return $this->render($st_output);
        } catch (\Exception $e) {
            return $this->render(["meta" => [
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ]]);
        }
    }


    /* @TODO make login */
    public function loginAction() {

        try {
            $manager = $this->getDI()->get('core_operator_manager');
            $data = $this->request->getJsonRawBody(true);

            if (count($data) == 0) {
                throw new \Exception('Please provide data', 400);
            }
            $st_output = $manager->restLogin($data);
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
