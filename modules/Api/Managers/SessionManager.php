<?php

namespace App\Api\Managers;

use App\Api\Managers\Session;
//use App\Api\Managers\Session;
//use App\Api\SessionComponent\AccountType;



use Phalcon\Cache\Backend\Redis;
use Phalcon\Mvc\User\Plugin;


class SessionManager extends Plugin
{
    const LOGIN_DATA_USERNAME = "username";
    const LOGIN_DATA_PASSWORD = "password";

    /**
     * @var AccountType[] Account types
     */
    protected $accountTypes;

    /**
     * @var Session Currenty active session
     */
    protected $session;

    /**
     * @var int Expiration time of created sessions
     */
    protected $sessionDuration;

    /**
     * @var Redis $redis redis adapter
     */
    protected $redis;


    public function __construct($sessionDuration = 86400, Redis $redis )
    {
        $this->sessionDuration = $sessionDuration;

        $this->accountTypes = [];
        $this->session = null;
        $this->redis = $redis;
    }


    /**
     * @param $key
     * @return bool
     */
    public function exists($key)
    {
        $this->key = $key;
        return $this->redis->exists($key);
    }


    /**
     * @param $key
     * @param $data
     * @return bool
     */
    public function save($key, $data)
    {
        return $this->redis->save($key, $data, $this->sessionDuration);
    }


    /**
     * @param $key
     * @return mixed|null
     */
    public function get($key)
    {
        return $this->redis->get($key);
    }


    /**
     * @param $name
     * @param AccountType $account
     * @return $this
     */
    public function registerAccountType($name, AccountType $account)
    {
        $this->accountTypes[$name] = $account;

        return $this;
    }


    /**
     * @return array|\Components\Session\AccountType[]
     */
    public function getAccountTypes()
    {
        return $this->accountTypes;
    }


    /**
     * @return int
     */
    public function getSessionDuration()
    {
        return $this->sessionDuration;
    }


    /**
     * @param $time
     */
    public function setSessionDuration($time)
    {
        $this->sessionDuration = $time;
    }


    /**
     * @return Session|null
     */
    public function getSession()
    {
        return $this->session;
    }


    /**
     * @param Session $session
     */
    public function setSession(Session $session)
    {
        $this->session = $session;
    }


    /**
     * @return bool
     *
     * Check if a user is currently logged in
     */
    public function loggedIn()
    {
        return !!$this->session;
    }


    /**
     * @param string $accountTypeName
     * @param string $username
     * @param string $password
     *
     * @return Session Created session
     *
     * Helper to login with username & password
     */
    public function loginWithUsernamePassword($accountTypeName, $username, $password)
    {
        return $this->login($accountTypeName, [

            self::LOGIN_DATA_USERNAME => $username,
            self::LOGIN_DATA_PASSWORD => $password
        ]);
    }


    /**
     * @param array $data
     * @return bool|Session
     * @internal param string $accountTypeName
     */
    public function login(array $data = [])
    {

        $identity = $this->generateIdentity();

        if (!$identity) {
            return false;
        }

        $startTime = time();
        $expirationTime = ['startTime' => $startTime, 'endTime' => $startTime + $this->sessionDuration];
        $data = array_merge($data, $expirationTime);

//        $session = new App\Api\SessionComponent\Session($identity, $startTime, $startTime + $this->sessionDuration);

        $session = new Session($identity, $startTime, $startTime + $this->sessionDuration);
        $token = $this->tokenParser->getToken($session);
        $data = array_merge($data, ['token' => $token]);
        $session->setToken($token);
        $this->save($identity, json_encode($data), $this->sessionDuration);
        $this->session = $session;

        return $this->session;
    }

    /**
     * @return string
     */
    public function generateIdentity()
    {
        /*TODO better token generate*/
        return md5(random_int(100, 200));
    }


    /**
     * @param $name
     *
     * @return \Components\Session\AccountType Account-type
     */
    public function getAccountType($name)
    {
        if (array_key_exists($name, $this->accountTypes)) {

            return $this->accountTypes[$name];
        }

        return false;
    }


    /**
     * @param string $token Token to authenticate with
     *
     * @return bool
     * @throws SessionException
     */
    public function authenticateToken($token)
    {
        try {

            $session = $this->tokenParser->getSession($token);

        } catch (\Exception $e) {

            return false;

        }

        if (!$session) {
            return false;
        }

        if ($session->getExpirationTime() < time()) {

            return false;

        }


        $session->setToken($token);

        if (!$this->exists($session->getIdentity())) {
            return false;
        }
        $this->session = $session;

        return true;
    }


    /**
     * @param $token
     * @param array $data
     * @return bool|Session|int|null
     */
    public function addData($token, $data)
    {
        if ($this->authenticateToken($token)) {
            try {
                // get session object and token by JWT
                $session = $this->tokenParser->getSession($token);
                $token = $this->tokenParser->getToken($session);

                // decode data from body request
                $decodeData = json_decode($data);

                // old data from session presented like array
                $oldData = $this->get($session->getIdentity());
                $oldData = (array)(json_decode($oldData));

                // new data from session presented like array
                $newData = (array)($decodeData);

                // entity name of deleted object
                $entity = array_keys($newData)[0];

                // form new session object
                if (is_array($decodeData->{$entity})) {
                    if (!isset($oldData[$entity])) {
                        $oldData[$entity] = [];
                    }
                    if (!is_array($oldData[$entity])) {
                        $oldData[$entity] = [$oldData[$entity]];
                    }
                    foreach ($decodeData->{$entity} as $dataItem) {
                        if (array_search($dataItem, $oldData[$entity]) === false) {
                            $oldData[$entity][] = $dataItem;
                        }
                    }
                } else {
                    $oldData[$entity] = $decodeData->{$entity};
                }

                // save new data to session
                $this->save($session->getIdentity(), json_encode($oldData));

                //set new JWT token
                $session->setToken($token);
                $this->session = $session;

                return $this->getData($token);

            } catch (\Exception $e) {
                return false;
            }
        }
        return false;

    }


    /**
     * @param $token
     * @param $good
     * @return array|bool|mixed|null
     */
    public function deleteItem($token, $good) {

        if ($this->authenticateToken($token)) {
            try {
                // get session object and token by JWT
                $session = $this->tokenParser->getSession($token);
                $token = $this->tokenParser->getToken($session);

                // decode data from body request
                $decodeData = json_decode($good);

                // old data from session presented like array
                $oldData = $this->get($session->getIdentity());
                $oldData = (array)(json_decode($oldData));

                // new data from session presented like array
                $newData = (array)($decodeData);

                // entity name of deleted object
                $entity = array_keys($newData)[0];

                // check old and new data is array
                $oldIsArray = is_array($oldData[$entity]);
                $newIsArray = is_array($decodeData->{$entity});

                // form new session object
                if (isset($oldData[$entity])) {
                    switch (true) {
                        case ($oldIsArray && $newIsArray): {
                            foreach ($decodeData->{$entity} as $item) {
                                $deletedIndex = array_search($item, $oldData[$entity]);
                                if ($deletedIndex !== false) {
                                    unset($oldData[$entity][$deletedIndex]);
                                    $oldData[$entity] = array_values($oldData[$entity]);
                                }
                            }
                            break;
                        }
                        case (!$oldIsArray && !$newIsArray): {
                            if ($oldData[$entity] === $decodeData->{$entity}) {
                                $oldData[$entity] = null;
                            }
                            break;
                        }
                        case (!$oldIsArray && $newIsArray): {
                            if (array_search($oldData[$entity], $decodeData->{$entity}) !== false) {
                                $oldData[$entity] = null;
                            }
                            break;
                        }
                        case ($oldIsArray && !$newIsArray): {
                            $deletedIndex = array_search($decodeData->{$entity}, $oldData[$entity]);
                            if ($deletedIndex !== false) {
                                unset($oldData[$entity][$deletedIndex]);
                                $oldData[$entity] = array_values($oldData[$entity]);
                            }
                            break;
                        }
                    }
                }

                // save new data to session
                $this->save($session->getIdentity(), json_encode($oldData));

                //set new JWT token
                $session->setToken($token);
                $this->session = $session;

                return $this->getData($token);
            } catch (\Exception $exception) {
                return false;
            }
        }
        return false;

    }


    /**
     * @param $token
     * @return array|mixed|null
     */
    public function logout($token) {

        if ($this->authenticateToken($token)) {
            $session = $this->tokenParser->getSession($token);
            $oldData = $this->get($session->getIdentity());
            $oldData = get_object_vars(json_decode($oldData));
            unset($oldData['user']);
            $this->save($session->getIdentity(), json_encode($oldData));
            return $oldData;
        }
    }


    /**
     * @param $token
     * @return array|bool|mixed|null
     */
    public function getData($token)
    {
        if ($this->authenticateToken($token)) {
            try {
                $session = $this->tokenParser->getSession($token);
                $oldData = $this->get($session->getIdentity());
                $oldData = get_object_vars(json_decode($oldData));

                return $oldData;
            } catch (\Exception $e) {
                return false;

            }
        }
        return false;

    }

}
