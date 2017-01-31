<?php
namespace App\Core\Models;

class Currency extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $currencytime;

    /**
     *
     * @var double
     * @Column(type="double", length=7, nullable=false)
     */
    protected $value;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $instrument;

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
     * Method to set the value of field value
     *
     * @param double $value
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = $value;

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
     * Returns the value of field currencytime
     *
     * @return string
     */
    public function getCurrencytime()
    {
        return $this->currencytime;
    }

    /**
     * Returns the value of field value
     *
     * @return double
     */
    public function getValue()
    {
        return $this->value;
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
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema("binopt");
        $this->belongsTo('instrument', '\Instrument', 'id', ['alias' => 'Instrument']);
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
