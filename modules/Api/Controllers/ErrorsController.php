<?php
namespace App\Api\Controllers;

class ErrorsController extends BaseController {
    public function indexAction()
    {
        return $this->render(["meta" => [
            'code' => 1,
            'message' => 2
        ]]);
    }

    public function showAction($code, $error)
    {
        return $this->render(["meta" => [
            'code' => $code,
            'message' => $error
        ]]);
    }

}
?>