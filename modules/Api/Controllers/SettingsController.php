<?php
namespace App\Api\Controllers;

use Phalcon\Cli\Console;

class SettingsController extends BaseController {

    public function readAction() {
        try {
            $manager = $this->getDI()->get('core_settings_manager');

            $st_output = $manager->restGet();

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
            $manager = $this->getDI()->get('core_settings_manager');

            $data = $this->request->getJsonRawBody(true);
            if (count($data[0]) == 0) {
                throw new \Exception('Please provide data', 400);
            }
            $result = $manager->restUpdate($data);

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