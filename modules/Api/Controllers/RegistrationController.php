<?php
namespace App\Api\Controllers;

use Phalcon\Http\Response;

class RegistrationController extends BaseController {

    protected $sessionDuration = 86400;

    public function registerUserAction() {

        try {
            $manager = $this->getDI()->get('core_user_manager');

            $data = $this->request->getJsonRawBody(true);
            if (count($data) == 0) {
                throw new \Exception('Please provide data', 400);
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

            $email = $this->request->getQuery('email');
            $code = $this->request->getQuery('code');

            $parameters = ["email = :email: and activation = :code:", "bind" => ['email' => $email, 'code' => $code]];

            $st_output = $manager->activateUser($parameters);

            $response = new Response();
            $response->redirect("http://en.wikipedia.org", true, 200);
            $response->send();
        } catch (\Exception $e) {

            $response = new Response();
            $response->redirect("http://en.wikipedia.org", true, $e->getCode());
            $response->send();
            /*
            return $this->render(["meta" => [
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ]]);*/
        }
    }


}
?>