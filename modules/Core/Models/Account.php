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
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema("binopt");
        $this->hasMany('id', 'App\Core\Models\Bet', 'account', ['alias' => 'Bet']);
        $this->hasMany('id', 'App\Core\Models\Deposit', 'account', ['alias' => 'Deposit']);
        $this->hasMany('id', 'App\Core\Models\Withdrawal', 'account', ['alias' => 'Withdrawal']);
        $this->belongsTo('user', 'App\Core\Models\User', 'id', ['alias' => 'User']);
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



    public static function findAccount($user, $isreal)
    {
        return parent::findFirst(["user = :user: and realdemo = :rd:", 'bind' => ["user" => $user, "rd" => ($isreal ? 1 : 0)]]);
    }

    public function getDeposits($start = null, $end = null)
    {
        $value = 0;
        $deposits = $this->getRelated('deposit');
        foreach ($deposits as $deposit)
        {
            if($deposit->getState() == 1 && $deposit->getAdmin() == 0 && ($start == null || $deposit->getDeposittime() >= $start) && ($end == null || $deposit->getDeposittime() <= $end)) {

                $bonus = 1;
                if($deposit->promo != null)
                    $bonus += $deposit->promo->getBonus();

                $value += $deposit->getAmount() * $bonus;
            }
        }
        return $value;
    }

    public function getWithdrawals()
    {
        $value = 0;
        $withdrawals = $this->getRelated('withdrawal');
        foreach ($withdrawals as $withdrawal)
        {
            if($withdrawal->getState() == 2)
                $value += $withdrawal->getAmount();
        }
        return $value;
    }

    public function getBetStat(&$wins, &$loses, &$ingame)
    {
        $wins = $loses = $ingame = 0;
        $bets = $this->getRelated('bet');
        foreach ($bets as $bet)
        {
            $result = $bet->getResult();
            if($result == null || $result == 0)
                $ingame += $bet->getInvest();
                //$ingame += $bet->invest->getSize();
            elseif($result > 0)
                $wins += $result;
            else
                $loses += -$result;
        }
    }

    public function getBalance()
    {
        $wins = 0;
        $loses = 0;
        $ingame = 0;
        $this->getBetStat($wins, $loses, $ingame);
        return $this->getDeposits() - $this->getWithdrawals() + $wins - $loses - $ingame;
    }
}
