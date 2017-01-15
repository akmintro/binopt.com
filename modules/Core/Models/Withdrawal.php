<?php
namespace App\Core\Models;

class Withdrawal extends \Phalcon\Mvc\Model
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
    protected $account;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $amount;

    /**
     *
     * @var integer
     * @Column(type="integer", length=20, nullable=false)
     */
    protected $wallet;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $withdrawaltime;

    /**
     *
     * @var integer
     * @Column(type="integer", length=6, nullable=false)
     */
    protected $state;

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
     * Method to set the value of field account
     *
     * @param integer $account
     * @return $this
     */
    public function setAccount($account)
    {
        $this->account = $account;

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
     * Method to set the value of field wallet
     *
     * @param integer $wallet
     * @return $this
     */
    public function setWallet($wallet)
    {
        $this->wallet = $wallet;

        return $this;
    }

    /**
     * Method to set the value of field withdrawaltime
     *
     * @param string $withdrawaltime
     * @return $this
     */
    public function setWithdrawaltime($withdrawaltime)
    {
        $this->withdrawaltime = $withdrawaltime;

        return $this;
    }

    /**
     * Method to set the value of field state
     *
     * @param integer $state
     * @return $this
     */
    public function setState($state)
    {
        $this->state = $state;

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
     * Returns the value of field account
     *
     * @return integer
     */
    public function getAccount()
    {
        return $this->account;
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
     * Returns the value of field wallet
     *
     * @return integer
     */
    public function getWallet()
    {
        return $this->wallet;
    }

    /**
     * Returns the value of field withdrawaltime
     *
     * @return string
     */
    public function getWithdrawaltime()
    {
        return $this->withdrawaltime;
    }

    /**
     * Returns the value of field state
     *
     * @return integer
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema("binopt");
        $this->belongsTo('account', '\Account', 'id', ['alias' => 'Account']);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'withdrawal';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Withdrawal[]|Withdrawal
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Withdrawal
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
