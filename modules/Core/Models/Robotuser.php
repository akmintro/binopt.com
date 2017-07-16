<?php
namespace App\Core\Models;

use Phalcon\Validation;
use Phalcon\Mvc\Model\Validator\Email as EmailValidator;

class Robotuser extends \Phalcon\Mvc\Model
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
     * @Column(type="string", length=50, nullable=false)
     */
    protected $email;

    /**
     *
     * @var string
     * @Column(type="string", length=200, nullable=false)
     */
    protected $password;

    /**
     *
     * @var string
     * @Column(type="string", length=30, nullable=true)
     */
    protected $firstname;

    /**
     *
     * @var string
     * @Column(type="string", length=30, nullable=true)
     */
    protected $lastname;

    /**
     *
     * @var string
     * @Column(type="string", length=30, nullable=true)
     */
    protected $middlename;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    protected $birthday;

    /**
     *
     * @var string
     * @Column(type="string", length=10, nullable=true)
     */
    protected $passport;

    /**
     *
     * @var string
     * @Column(type="string", length=100, nullable=true)
     */
    protected $passportauth;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    protected $passportdate;

    /**
     *
     * @var string
     * @Column(type="string", length=50, nullable=true)
     */
    protected $passportfile;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=true)
     */
    protected $country;

    /**
     *
     * @var string
     * @Column(type="string", length=50, nullable=true)
     */
    protected $city;

    /**
     *
     * @var string
     * @Column(type="string", length=50, nullable=true)
     */
    protected $address;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=true)
     */
    protected $postindex;

    /**
     *
     * @var string
     * @Column(type="string", length=50, nullable=true)
     */
    protected $addressfile;

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
     * Method to set the value of field middlename
     *
     * @param string $middlename
     * @return $this
     */
    public function setMiddlename($middlename)
    {
        $this->middlename = $middlename;

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
     * Method to set the value of field passport
     *
     * @param string $passport
     * @return $this
     */
    public function setPassport($passport)
    {
        $this->passport = $passport;

        return $this;
    }

    /**
     * Method to set the value of field passportauth
     *
     * @param string $passportauth
     * @return $this
     */
    public function setPassportauth($passportauth)
    {
        $this->passportauth = $passportauth;

        return $this;
    }

    /**
     * Method to set the value of field passportdate
     *
     * @param string $passportdate
     * @return $this
     */
    public function setPassportdate($passportdate)
    {
        $this->passportdate = $passportdate;

        return $this;
    }

    /**
     * Method to set the value of field passportfile
     *
     * @param string $passportfile
     * @return $this
     */
    public function setPassportfile($passportfile)
    {
        $this->passportfile = $passportfile;

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
     * Method to set the value of field city
     *
     * @param string $city
     * @return $this
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Method to set the value of field address
     *
     * @param string $address
     * @return $this
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Method to set the value of field postindex
     *
     * @param integer $postindex
     * @return $this
     */
    public function setPostindex($postindex)
    {
        $this->postindex = $postindex;

        return $this;
    }

    /**
     * Method to set the value of field addressfile
     *
     * @param string $addressfile
     * @return $this
     */
    public function setAddressfile($addressfile)
    {
        $this->addressfile = $addressfile;

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
     * Returns the value of field email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
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
     * Returns the value of field middlename
     *
     * @return string
     */
    public function getMiddlename()
    {
        return $this->middlename;
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
     * Returns the value of field passport
     *
     * @return string
     */
    public function getPassport()
    {
        return $this->passport;
    }

    /**
     * Returns the value of field passportauth
     *
     * @return string
     */
    public function getPassportauth()
    {
        return $this->passportauth;
    }

    /**
     * Returns the value of field passportdate
     *
     * @return string
     */
    public function getPassportdate()
    {
        return $this->passportdate;
    }

    /**
     * Returns the value of field passportfile
     *
     * @return string
     */
    public function getPassportfile()
    {
        return $this->passportfile;
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
     * Returns the value of field city
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Returns the value of field address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Returns the value of field postindex
     *
     * @return integer
     */
    public function getPostindex()
    {
        return $this->postindex;
    }

    /**
     * Returns the value of field addressfile
     *
     * @return string
     */
    public function getAddressfile()
    {
        return $this->addressfile;
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
        $this->hasMany('id', 'Robotlink', 'robot_id', ['alias' => 'Robotlink']);
        $this->belongsTo('country', '\Country', 'id', ['alias' => 'Country']);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'robotuser';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Robotuser[]|Robotuser
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Robotuser
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
