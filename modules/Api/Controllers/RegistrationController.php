<?php
namespace App\Api\Controllers;

use Phalcon\Http\Response;

class RegistrationController extends BaseController {

    public function registerUserAction() {

        try {
            $manager = $this->getDI()->get('core_user_manager');

            $data = $this->request->getJsonRawBody(true);
            if (count($data) == 0) {
                throw new \Exception('Please provide data', 401);
            }

            $st_output = $manager->regiterUser($data);
            return $this->render($st_output);
        } catch (\Exception $e) {
            return $this->render(["meta" => [
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ]]);
        }
    }

    public function activateUserAction() {

        try {
            $manager = $this->getDI()->get('core_user_manager');

            $id = $this->request->getQuery('user');
            $code = $this->request->getQuery('code');

            $parameters = ["id = :id: and activation = :code:", "bind" => ['id' => $id, 'code' => $code]];

            $st_output = $manager->activateUser($parameters);

            $response = new Response();
            $response->redirect("https://binoption24.com", true, 200);
            $response->send();
        } catch (\Exception $e) {

            $response = new Response();
            $response->redirect("https://binoption24.com", true, $e->getCode());
            $response->send();
        }
    }
}
?>