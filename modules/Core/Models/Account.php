<?php
namespace App\Core\Models;

class Account extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     * @Primary
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $id;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $user;

    /**
     *
     * @var integer
     * @Column(type="integer", length=1, nullable=false)
     */
    protected $realdemo;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $amount;

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
     * Method to set the value of field user
     *
     * @param integer $user
     * @return $this
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Method to set the value of field realdemo
     *
     * @param integer $realdemo
     * @return $this
     */
    public function setRealdemo($realdemo)
    {
        $this->realdemo = $realdemo;

        return $this;
    }

    /**
     * Method to set the value of field amount
     *
     * @param integer $amount
     * @return $this
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

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
     * Returns the value of field user
     *
     * @return integer
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Returns the value of field realdemo
     *
     * @return integer
     */
    public function getRealdemo()
    {
        return $this->realdemo;
    }

    /**
     * Returns the value of field amount
     *
     * @return integer
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema("binopt");
        $this->hasMany('id', 'Bet', 'account', ['alias' => 'Bet']);
        $this->hasMany('id', 'Deposit', 'account', ['alias' => 'Deposit']);
        $this->hasMany('id', 'Withdrawal', 'account', ['alias' => 'Withdrawal']);
        $this->belongsTo('user', '\User', 'id', ['alias' => 'User']);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'account';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Account[]|Account
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Account
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
