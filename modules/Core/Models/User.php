<?php
namespace App\Core\Models;

use Phalcon\Validation;
use Phalcon\Validation\Validator\Email as EmailValidator;

class User extends \Phalcon\Mvc\Model
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
    protected $firstname;

    /**
     *
     * @var string
     * @Column(type="string", length=50, nullable=false)
     */
    protected $lastname;

    /**
     *
     * @var string
     * @Column(type="string", length=50, nullable=false)
     */
    protected $email;

    /**
     *
     * @var string
     * @Column(type="string", length=15, nullable=false)
     */
    protected $phone;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $lastvisit;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    protected $registration;

    /**
     *
     * @var string
     * @Column(type="string", length=200, nullable=false)
     */
    protected $password;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $country;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $birthday;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=true)
     */
    protected $lastip;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $operator;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=true)
     */
    protected $timezoneoffset;

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
     * Method to set the value of field firstname
     *
     * @param string $firstname
     * @return $this
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Method to set the value of field lastname
     *
     * @param string $lastname
     * @return $this
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Method to set the value of field email
     *
     * @param string $email
     * @return $this
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Method to set the value of field phone
     *
     * @param string $phone
     * @return $this
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Method to set the value of field lastvisit
     *
     * @param string $lastvisit
     * @return $this
     */
    public function setLastvisit($lastvisit)
    {
        $this->lastvisit = $lastvisit;

        return $this;
    }

    /**
     * Method to set the value of field registration
     *
     * @param string $registration
     * @return $this
     */
    public function setRegistration($registration)
    {
        $this->registration = $registration;

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
     * Method to set the value of field country
     *
     * @param integer $country
     * @return $this
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Method to set the value of field birthday
     *
     * @param string $birthday
     * @return $this
     */
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;

        return $this;
    }

    /**
     * Method to set the value of field lastip
     *
     * @param integer $lastip
     * @return $this
     */
    public function setLastip($lastip)
    {
        $this->lastip = $lastip;

        return $this;
    }

    /**
     * Method to set the value of field operator
     *
     * @param integer $operator
     * @return $this
     */
    public function setOperator($operator)
    {
        $this->operator = $operator;

        return $this;
    }

    /**
     * Method to set the value of field timezoneoffset
     *
     * @param integer $timezoneoffset
     * @return $this
     */
    public function setTimezoneoffset($timezoneoffset)
    {
        $this->timezoneoffset = $timezoneoffset;

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
     * Returns the value of field firstname
     *
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Returns the value of field lastname
     *
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Returns the value of field email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Returns the value of field phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Returns the value of field lastvisit
     *
     * @return string
     */
    public function getLastvisit()
    {
        return $this->lastvisit;
    }

    /**
     * Returns the value of field registration
     *
     * @return string
     */
    public function getRegistration()
    {
        return $this->registration;
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
     * Returns the value of field country
     *
     * @return integer
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Returns the value of field birthday
     *
     * @return string
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * Returns the value of field lastip
     *
     * @return integer
     */
    public function getLastip()
    {
        return $this->lastip;
    }

    /**
     * Returns the value of field operator
     *
     * @return integer
     */
    public function getOperator()
    {
        return $this->operator;
    }

    /**
     * Returns the value of field timezoneoffset
     *
     * @return integer
     */
    public function getTimezoneoffset()
    {
        return $this->timezoneoffset;
    }

    /**
     * Validations and business logic
     *
     * @return boolean
     */
    public function validation()
    {
        $validator = new Validation();

        $validator->add(
            'email',
            new EmailValidator(
                [
                    'model'   => $this,
                    'message' => 'Please enter a correct email address',
                ]
            )
        );

        return $this->validate($validator);
    }

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema("binopt");
        $this->hasMany('id', 'Account', 'user', ['alias' => 'Account']);
        $this->belongsTo('country', '\Country', 'id', ['alias' => 'Country']);
        $this->belongsTo('operator', '\Operator', 'id', ['alias' => 'Operator']);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'user';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return User[]|User
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return User
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
