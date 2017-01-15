<?php
namespace App\Core\Models;

class Deposit extends \Phalcon\Mvc\Model
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
    protected $deposittime;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=true)
     */
    protected $promo;

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
     * Method to set the value of field deposittime
     *
     * @param string $deposittime
     * @return $this
     */
    public function setDeposittime($deposittime)
    {
        $this->deposittime = $deposittime;

        return $this;
    }

    /**
     * Method to set the value of field promo
     *
     * @param integer $promo
     * @return $this
     */
    public function setPromo($promo)
    {
        $this->promo = $promo;

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
     * Returns the value of field deposittime
     *
     * @return string
     */
    public function getDeposittime()
    {
        return $this->deposittime;
    }

    /**
     * Returns the value of field promo
     *
     * @return integer
     */
    public function getPromo()
    {
        return $this->promo;
    }

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema("binopt");
        $this->belongsTo('account', '\Account', 'id', ['alias' => 'Account']);
        $this->belongsTo('promo', '\Promo', 'id', ['alias' => 'Promo']);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'deposit';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Deposit[]|Deposit
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Deposit
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
