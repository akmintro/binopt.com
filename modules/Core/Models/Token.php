<?php
namespace App\Core\Models;

class Token extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $role;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $id;

    /**
     *
     * @var string
     * @Primary
     * @Column(type="string", length=512, nullable=false)
     */
    protected $token_val;

    /**
     *
     * @var string
     * @Column(type="string", length=128, nullable=false)
     */
    protected $secret;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $exptime;

    /**
     * Method to set the value of field role
     *
     * @param integer $role
     * @return $this
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

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
     * Method to set the value of field token_val
     *
     * @param string $token_val
     * @return $this
     */
    public function setTokenVal($token_val)
    {
        $this->token_val = $token_val;

        return $this;
    }

    /**
     * Method to set the value of field secret
     *
     * @param string $secret
     * @return $this
     */
    public function setSecret($secret)
    {
        $this->secret = $secret;

        return $this;
    }

    /**
     * Method to set the value of field exptime
     *
     * @param string $exptime
     * @return $this
     */
    public function setExptime($exptime)
    {
        $this->exptime = $exptime;

        return $this;
    }

    /**
     * Returns the value of field role
     *
     * @return integer
     */
    public function getRole()
    {
        return $this->role;
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
     * Returns the value of field token_val
     *
     * @return string
     */
    public function getTokenVal()
    {
        return $this->token_val;
    }

    /**
     * Returns the value of field secret
     *
     * @return string
     */
    public function getSecret()
    {
        return $this->secret;
    }

    /**
     * Returns the value of field exptime
     *
     * @return string
     */
    public function getExptime()
    {
        return $this->exptime;
    }

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema("binopt");
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'token';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Token[]|Token
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Token
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
