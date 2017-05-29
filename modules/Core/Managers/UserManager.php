<?php
namespace App\Core\Managers;

use App\Core\Models\Account;
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
            unset($item['activation']);
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
            throw new \Exception("Not Found", 404);
        }

        if (User::findFirst(["id <> :id: AND email = :email:", 'bind' => ['id' => $id, 'email' => $data[0]['email']]])) {
            throw new \Exception("Email Duplicate", 414);
        }

        $this->setFields($item, $data[0]);

        if($this->tokenParser->isOper()) {
            $amount = (int)$data[0]["amount"];
            if ($amount > 0)
            {
                $account = Account::findFirst(["user = :user: and realdemo = 1", "bind" => ["user" => $item->getId()]]);
                $this->getDI()->get('core_deposit_manager')->restCreate([["account" => $account->getId(), "amount" => $amount, "wallet" => $this->config->parameters->adminwallet, "admin" => 1, "state" => 1]]);
            }
        }

        if (false === $item->update()) {
            foreach ($item->getMessages() as $message) {
                throw new \Exception($message->getMessage(), 400);
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
                throw new \Exception($message->getMessage(), 400);
            }
        }

        return ["meta" => [
            "code" => 200,
            "message" => "OK"
        ]];
    }

    private function createUser($item, $amount = 0)
    {
        if (strlen($item->getFirstname()) == 0 || strlen($item->getLastname()) == 0 || strlen($item->getPassword()) == 0 || strlen($item->getEmail()) == 0)
            throw new \Exception("Incomplete Data", 402);

        if (false === $item->create()) {
            foreach ($item->getMessages() as $message) {
                throw new \Exception($message->getMessage(), 400);
            }
        }

        $accmanager = $this->getDI()->get('core_account_manager');
        $depmanager = $this->getDI()->get('core_deposit_manager');

        $accmanager->restCreate([["user" => $item->getId(), "realdemo" => 0]]);
        $account = Account::findFirst(["user = :user: and realdemo = 0", "bind" => ["user" => $item->getId()]]);
        $depmanager->restCreate([["account" => $account->getId(), "amount" => 1000, "wallet" => $this->config->parameters->adminwallet, "state" => 1]]);

        $accmanager->restCreate([["user" => $item->getId(), "realdemo" => 1]]);
        if($amount > 0)
        {
            $account = Account::findFirst(["user = :user: and realdemo = 1", "bind" => ["user" => $item->getId()]]);
            $depmanager->restCreate([["account" => $account->getId(), "amount" => $amount, "wallet" => $this->config->parameters->adminwallet, "admin" => 1, "state" => 1]]);
        }
    }

    public function restCreate($data) {

        if (User::findFirst(["email = :email:", 'bind' => ['email' => $data[0]['email']]])) {
            throw new \Exception("Email Duplicate", 414);
        }

        if(!isset($data[0]['operator']))
            $data[0]['operator'] = $this->tokenParser->getOperid();

        if(!isset($data[0]['email']))
            $data[0]['email'] = $data[0]['lastname'] . "." . $data[0]['firstname'] . Operator::findFirstById($data[0]['operator'])->getEmailsuffix();

        $item = new User();
        $this->setFields($item, $data[0]);

        $amount = (int)$data[0]["amount"];
        if($amount < 0)
            $amount = 0;

        $this->createUser($item, $amount);

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
            throw new \Exception('name is not set', 402);

        if(!isset($opdata['password']))
            throw new \Exception('password is not set', 402);


        $items = $this->find([ "email = :name:", 'bind' => ['name' => $opdata['name']]]);

        $data = $items->filter(function($item){
            return $item->toArray();
        });

        if (count($data) == 0)
            throw new \Exception('no user found', 404);

        if (count($data) > 1)
            throw new \Exception('many users found', 404);

        if (!($this->security->checkHash($opdata['password'], $data[0]['password']))) {
            throw new \Exception('incorrect password', 413);
        }

        return User::findFirstById($data[0]["id"]);
    }

    public function regiterUser($data) {

        if (User::findFirst(["email = :email:", 'bind' => ['email' => $data[0]['email']]])) {
            throw new \Exception("Email Duplicate", 414);
        }

        $code = "";
        $code .= substr(md5((microtime() - rand(3,1000000)) * rand(1,1000)),rand(0,20),10);
        $code .= substr(md5((microtime() - rand(3,1000000)) * rand(1,1000)),rand(0,20),10);
        $code .= substr(md5((microtime() - rand(3,1000000)) * rand(1,1000)),rand(0,20),10);
        $code .= substr(md5((microtime() - rand(3,1000000)) * rand(1,1000)),rand(0,20),10);

        $item = new User();
        unset($data[0]['operator']);
        $this->setFields($item, $data[0]);

        $item->setActivation($code);


        $this->createUser($item);

        $to = $item->getEmail();

        $mailer = new \Phalcon\Ext\Mailer\Manager([

            'driver' 	 => 'smtp',
            'host'	 	 => 'smtp.gmail.com',
            'port'	 	 => 465,
            'encryption' => 'ssl',
            'username'   => $this->config->parameters->gmailusername,
            'password'	 => $this->config->parameters->gmailpassword,
            'from'		 => [
                'email' => $this->config->parameters->gmailusername,
                'name'	=> $this->config->parameters->gmailtopic
            ]
        ]);

        $message = $mailer->createMessage()
            ->to($to)
            ->subject('Hello world!')
            ->content('<a href="https://binoption24.com/api/users/activate?user='.$item->getEmail().'&code='.$code.'">Activate account</a>');

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
                throw new \Exception($message->getMessage(), 404);
            }
        }
    }

    public function changePassword($data)
    {
        $user = $this->findFirstById($this->tokenParser->getUserid());

        if ($user == null)
            throw new \Exception('no user found', 404);

        if(!isset($data[0]['oldpassword']))
            throw new \Exception('old password is not set', 402);

        if (!($this->security->checkHash($data[0]['oldpassword'], $user->getPassword()))) {
            throw new \Exception('incorrect old password', 413);
        }

        if(!isset($data[0]['newpassword']))
            throw new \Exception('new password is not set', 402);
        if(!isset($data[0]['newpassword2']))
            throw new \Exception('confirm password is not set', 402);

        if($data[0]['newpassword'] != $data[0]['newpassword2'])
            throw new \Exception("passwords don't match", 415);


        $user->setPassword($this->getDI()->get('security')->hash($data[0]['newpassword']));

        if (false === $user->update()) {
            foreach ($user->getMessages() as $message) {
                throw new \Exception($message->getMessage(), 400);
            }
        }

        return ["meta" => [
            "code" => 200,
            "message" => "OK"
        ]];
    }
}
?>