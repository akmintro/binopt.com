<?php
namespace App\Core\Models;

class Operator extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $id;

    /**
     *
     * @var string
     * @Column(type="string", length=30, nullable=false)
     */
    protected $name;

    /**
     *
     * @var string
     * @Column(type="string", length=200, nullable=false)
     */
    protected $password;

    /**
     *
     * @var string
     * @Column(type="string", length=20, nullable=false)
     */
    protected $emailsuffix;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=true)
     */
    protected $ip;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    protected $regdate;

    /**
     * Method to set the value of field id
     *
     * @param integer $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Method to set the value of field name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Method to set the value of field password
     *
     * @param string $password
     * @return $this
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Method to set the value of field emailsuffix
     *
     * @param string $emailsuffix
     * @return $this
     */
    public function setEmailsuffix($emailsuffix)
    {
        $this->emailsuffix = $emailsuffix;

        return $this;
    }

    /**
     * Method to set the value of field ip
     *
     * @param integer $ip
     * @return $this
     */
    public function setIp($ip)
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * Method to set the value of field regdate
     *
     * @param string $regdate
     * @return $this
     */
    public function setRegdate($regdate)
    {
        $this->regdate = $regdate;

        return $this;
    }

    /**
     * Returns the value of field id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns the value of field name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the value of field password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Returns the value of field emailsuffix
     *
     * @return string
     */
    public function getEmailsuffix()
    {
        return $this->emailsuffix;
    }

    /**
     * Returns the value of field ip
     *
     * @return integer
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Returns the value of field regdate
     *
     * @return string
     */
    public function getRegdate()
    {
        return $this->regdate;
    }

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema("binopt");
        $this->hasMany('id', 'Robotcode', 'operator', ['alias' => 'Robotcode']);
        $this->hasMany('id', 'User', 'operator', ['alias' => 'User']);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'operator';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Operator[]|Operator
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Operator
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
