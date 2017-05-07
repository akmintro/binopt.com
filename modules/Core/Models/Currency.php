<?php
namespace App\Core\Models;

class Currency extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var string
     * @Primary
     * @Column(type="string", nullable=false)
     */
    protected $currencytime;

    /**
     *
     * @var integer
     * @Primary
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $instrument;

    /**
     *
     * @var double
     * @Column(type="double", length=10, nullable=false)
     */
    protected $open;

    /**
     *
     * @var double
     * @Column(type="double", length=10, nullable=false)
     */
    protected $close;

    /**
     *
     * @var double
     * @Column(type="double", length=10, nullable=false)
     */
    protected $min;

    /**
     *
     * @var double
     * @Column(type="double", length=10, nullable=false)
     */
    protected $max;

    /**
     * Method to set the value of field currencytime
     *
     * @param string $currencytime
     * @return $this
     */
    public function setCurrencytime($currencytime)
    {
        $this->currencytime = $currencytime;

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
     * Method to set the value of field open
     *
     * @param double $open
     * @return $this
     */
    public function setOpen($open)
    {
        $this->open = $open;

        return $this;
    }

    /**
     * Method to set the value of field close
     *
     * @param double $close
     * @return $this
     */
    public function setClose($close)
    {
        $this->close = $close;

        return $this;
    }

    /**
     * Method to set the value of field min
     *
     * @param double $min
     * @return $this
     */
    public function setMin($min)
    {
        $this->min = $min;

        return $this;
    }

    /**
     * Method to set the value of field max
     *
     * @param double $max
     * @return $this
     */
    public function setMax($max)
    {
        $this->max = $max;

        return $this;
    }

    /**
     * Returns the value of field currencytime
     *
     * @return string
     */
    public function getCurrencytime()
    {
        return $this->currencytime;
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
     * Returns the value of field open
     *
     * @return double
     */
    public function getOpen()
    {
        return (double)$this->open;
    }

    /**
     * Returns the value of field close
     *
     * @return double
     */
    public function getClose()
    {
        return (double)$this->close;
    }

    /**
     * Returns the value of field min
     *
     * @return double
     */
    public function getMin()
    {
        return (double)$this->min;
    }

    /**
     * Returns the value of field max
     *
     * @return double
     */
    public function getMax()
    {
        return (double)$this->max;
    }

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema("binopt");
        $this->belongsTo('instrument', 'App\Core\Models\Instrument', 'id', ['alias' => 'Instrument']);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'currency';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Currency[]|Currency
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Currency
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
