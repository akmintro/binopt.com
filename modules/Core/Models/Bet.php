<?php
namespace App\Core\Models;

class Bet extends \Phalcon\Mvc\Model
{

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
    protected $instrument;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $starttime;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    protected $endtime;

    /**
     *
     * @var double
     * @Column(type="double", length=10, nullable=false)
     */
    protected $startval;

    /**
     *
     * @var double
     * @Column(type="double", length=10, nullable=true)
     */
    protected $endval;

    /**
     *
     * @var integer
     * @Column(type="integer", length=1, nullable=false)
     */
    protected $updown;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $invest;

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
     * Method to set the value of field instrument
     *
     * @param integer $instrument
     * @return $this
     */
    public function setInstrument($instrument)
    {
        $this->instrument = $instrument;

        return $this;
    }

    /**
     * Method to set the value of field starttime
     *
     * @param string $starttime
     * @return $this
     */
    public function setStarttime($starttime)
    {
        $this->starttime = $starttime;

        return $this;
    }

    /**
     * Method to set the value of field endtime
     *
     * @param string $endtime
     * @return $this
     */
    public function setEndtime($endtime)
    {
        $this->endtime = $endtime;

        return $this;
    }

    /**
     * Method to set the value of field startval
     *
     * @param double $startval
     * @return $this
     */
    public function setStartval($startval)
    {
        $this->startval = $startval;

        return $this;
    }

    /**
     * Method to set the value of field endval
     *
     * @param double $endval
     * @return $this
     */
    public function setEndval($endval)
    {
        $this->endval = $endval;

        return $this;
    }

    /**
     * Method to set the value of field updown
     *
     * @param integer $updown
     * @return $this
     */
    public function setUpdown($updown)
    {
        $this->updown = $updown;

        return $this;
    }

    /**
     * Method to set the value of field invest
     *
     * @param integer $invest
     * @return $this
     */
    public function setInvest($invest)
    {
        $this->invest = $invest;

        return $this;
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
     * Returns the value of field instrument
     *
     * @return integer
     */
    public function getInstrument()
    {
        return $this->instrument;
    }

    /**
     * Returns the value of field starttime
     *
     * @return string
     */
    public function getStarttime()
    {
        return $this->starttime;
    }

    /**
     * Returns the value of field endtime
     *
     * @return string
     */
    public function getEndtime()
    {
        return $this->endtime;
    }

    /**
     * Returns the value of field startval
     *
     * @return double
     */
    public function getStartval()
    {
        return $this->startval;
    }

    /**
     * Returns the value of field endval
     *
     * @return double
     */
    public function getEndval()
    {
        return $this->endval;
    }

    /**
     * Returns the value of field updown
     *
     * @return integer
     */
    public function getUpdown()
    {
        return $this->updown;
    }

    /**
     * Returns the value of field invest
     *
     * @return integer
     */
    public function getInvest()
    {
        return $this->invest;
    }

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema("binopt");
        $this->belongsTo('account', '\Account', 'id', ['alias' => 'Account']);
        $this->belongsTo('instrument', '\Instrument', 'id', ['alias' => 'Instrument']);
        $this->belongsTo('invest', '\Invest', 'id', ['alias' => 'Invest']);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'bet';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Bet[]|Bet
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Bet
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
