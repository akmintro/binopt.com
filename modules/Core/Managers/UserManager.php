<?php
namespace App\Core\Managers;

use App\Core\Models\Operator;
use App\Core\Models\User;

class UserManager extends BaseManager
{
    public function find($parameters = null)
    {
        return User::find($parameters);
    }

    public function findFirstById($id)
    {
        return User::findFirstById((int)$id);
    }

    public function restGet(array $parameters = null, $limit = 10, $offset = 0)
    {
        $items = $this->find($parameters);
        $data = $items->filter(function ($item) {
            return $item->toArray();
        });

        $data = $this->getFilteredData($data, $this->request->getQuery('status'));

        $meta = [
            "code" => 200,
            "message" => "OK",
            "limit" => (int)$limit,
            "offset" => (int)$offset,
            "total" => count($data)
        ];

        if (count($data) > 0) {
            return ["meta" => $meta, "data" => array_slice($this->getItems($data), $offset, $limit)];
        }

        if (isset($parameters['bind']['id'])) {
            throw new \Exception('Not Found', 404);
        } else {
            throw new \Exception('No Content', 204);
        }
    }

    private function getFilteredData($data, $status = null)
    {
        $result = [];
        foreach ($data as $user)
        {
            if(($status == "active" && $user['lastvisit'] == null)
                || ($status == "unactive" && $user['lastvisit'] != null))
                continue;

            $result[] = $user;
        }
        return $result;
    }

    private function getItems($items)
    {
        if(is_array($items))
            $new_items = $items;
        else
            $new_items = array($items->toArray());

        foreach ($new_items as &$item)
        {
            $user = User::findFirstById($item['id']);
            //$item['username'] = $user->getUsername();
            $user->getBalance($item);

            unset($item['firstname']);
            unset($item['lastname']);
            //unset($item['email']);
            unset($item['password']);
            unset($item['country']);
            unset($item['birthday']);
            unset($item['lastip']);
            unset($item['timezoneoffset']);
            unset($item['operator']);
        }

        return $new_items;
    }

    public function restGetById($id)
    {
        $data= $this->findFirstById($id);

        if (!$data)
            throw new \Exception('Not Found', 404);

        $meta = [
            "code" => 200,
            "message" => "OK"
        ];

        $item = $data->toArray();
        $item['operator'] = Operator::findFirstById($item['operator'])->toArray();
        $item['accounts'] = $data->account;

        unset($item['password']);
        unset($item['operator']['password']);
        unset($item['operator']['ip']);
        unset($item['operator']['regdate']);

        $data->getBalance($item);

        return ["meta" => $meta, "data" => $item];
    }

    public function restUpdate($id, $data) {
        $item = $this->findFirstById($id);
        if (!$item) {
            return ["meta" => [
                "code" => 404,
                "message" => "Not Found"
            ]];
        }
        $this->setFields($item, $data[0]);

        if (false === $item->update()) {
            foreach ($item->getMessages() as $message) {
                throw new \Exception($message->getMessage(), 500);
            }
        }

        return ["meta" => [
            "code" => 200,
            "message" => "OK"
        ], "data" => $this->getItems($item)];
    }

    public function restDelete($id) {
        $item = $this->findFirstById($id);

        if (!$item) {
            throw new \Exception('Not found', 404);
        }

        if (false === $item->delete()) {
            foreach ($item->getMessages() as $message) {
                throw new \Exception($message->getMessage(), 500);
            }
        }

        return ["meta" => [
            "code" => 200,
            "message" => "OK"
        ]];
    }

    public function restCreate($data) {
        $item = new User();
        $this->setFields($item, $data[0]);

        if (false === $item->create()) {
            foreach ($item->getMessages() as $message) {
                throw new \Exception($message->getMessage(), 500);
            }
        }

        return ["meta" => [
            "code" => 200,
            "message" => "OK"
        ], "data" => $this->getItems($item)];
    }

    private function setFields($item, $data)
    {
        if(isset($data['birthday']))
            $item->setBirthday($data['birthday']);

        if(isset($data['firstname']))
            $item->setFirstname($data['firstname']);

        if(isset($data['lastname']))
            $item->setLastname($data['lastname']);

        if(isset($data['country']))
            $item->setCountry($data['country']);

        if(isset($data['email']))
            $item->setEmail($data['email']);

        if(isset($data['operator']))
            $item->setOperator($data['operator']);

        if(isset($data['password']))
            $item->setPassword($this->getDI()->get('security')->hash($data['password']));

        if(isset($data['phone']))
            $item->setPhone($data['phone']);
    }

    public function restLogin($data)
    {
        $opdata = $data[0];

        if(!isset($opdata['name']))
            throw new \Exception('name is required', 500);

        if(!isset($opdata['password']))
            throw new \Exception('password is required', 500);

        $parameters = [
            "email = :name:",
            'bind' => ['name' => $opdata['name']],
        ];

        $items = $this->find($parameters);
        $data = $items->filter(function($item){
            return $item->toArray();
        });

        if (count($data) == 0)
            throw new \Exception('no user found', 500);

        if (count($data) > 1)
            throw new \Exception('many users found', 500);

        if (!($this->security->checkHash($opdata['password'], $data[0]['password']))) {
            throw new \Exception('incorrect password', 500);
        }

        return $data[0];
    }

    public function regiterUser($data) {

        if (User::findFirst(["email = :email:", 'bind' => ['email' => $data[0]['email']]])) {
            return ["meta" => [
                "code" => 500,
                "message" => "email is taken"
            ]];
        }

        $code = "";
        $code .= substr(md5((microtime() - rand(3,1000000)) * rand(1,1000)),rand(0,20),10);
        $code .= substr(md5((microtime() - rand(3,1000000)) * rand(1,1000)),rand(0,20),10);
        $code .= substr(md5((microtime() - rand(3,1000000)) * rand(1,1000)),rand(0,20),10);
        $code .= substr(md5((microtime() - rand(3,1000000)) * rand(1,1000)),rand(0,20),10);

        $item = new User();
        $this->setFields($item, $data[0]);

        $item->setActivation($code);

        if (false === $item->create()) {
            foreach ($item->getMessages() as $message) {
                throw new \Exception($message->getMessage(), 500);
            }
        }

        $to = $item->getEmail();

        $mailer = new \Phalcon\Ext\Mailer\Manager([

            'driver' 	 => 'smtp',
            'host'	 	 => 'smtp.gmail.com',
            'port'	 	 => 465,
            'encryption' => 'ssl',
            'username'   => $this->config->parameters->gmailusername,
            'password'	 => $this->config->parameters->gmailpassword,
            'from'		 => [
                'email' => 'example@gmail.com',
                'name'	=> 'YOUR FROM NAME'
            ]
        ]);

        $message = $mailer->createMessage()
            ->to($to)
            ->subject('Hello world!')
            ->content('<a href="https://binopt.com/api/v1/users/activate?email='.$to.'&code='.$code.'">Activate account</a>');

        // Set the Cc addresses of this message.
                //$message->cc('example_cc@gmail.com');

        // Set the Bcc addresses of this message.
                //$message->bcc('example_bcc@gmail.com');

        // Send message
        $message->send();

        return ["meta" => [
            "code" => 200,
            "message" => "OK"
        ]];
    }

    public function activateUser(array $parameters) {
        $user = User::findFirst($parameters);

        if($user == null)
            throw new \Exception('account not found', 404);

        $user->setActivation(null);
        if (false === $user->update()) {
            foreach ($user->getMessages() as $message) {
                throw new \Exception($message->getMessage(), 500);
            }
        }
    }

    public function changePassword($data)
    {
        $user = $this->findFirstById($this->tokenParser->getUserid());

        if ($user == null)
            throw new \Exception('no user found', 500);

        if(!isset($data[0]['oldpassword']))
            throw new \Exception('old password is required', 500);

        if (!($this->security->checkHash($data[0]['oldpassword'], $user->getPassword()))) {
            throw new \Exception('incorrect old password', 500);
        }

        if(!isset($data[0]['newpassword']))
            throw new \Exception('new password is required', 500);
        if(!isset($data[0]['newpassword2']))
            throw new \Exception('confirm password is required', 500);

        if($data[0]['newpassword'] != $data[0]['newpassword2'])
            throw new \Exception("passwords don't match", 500);


        $user->setPassword($this->getDI()->get('security')->hash($data[0]['newpassword']));

        if (false === $user->update()) {
            foreach ($user->getMessages() as $message) {
                throw new \Exception($message->getMessage(), 500);
            }
        }

        return ["meta" => [
            "code" => 200,
            "message" => "OK"
        ]];
    }
}
?>