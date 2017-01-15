<?php
namespace App\Core\Models;

class Robotcode extends \Phalcon\Mvc\Model
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
    protected $code;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $type;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $startdate;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $enddate;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $operator;

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
     * Method to set the value of field code
     *
     * @param string $code
     * @return $this
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Method to set the value of field type
     *
     * @param integer $type
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Method to set the value of field startdate
     *
     * @param string $startdate
     * @return $this
     */
    public function setStartdate($startdate)
    {
        $this->startdate = $startdate;

        return $this;
    }

    /**
     * Method to set the value of field enddate
     *
     * @param string $enddate
     * @return $this
     */
    public function setEnddate($enddate)
    {
        $this->enddate = $enddate;

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
     * Returns the value of field id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns the value of field code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Returns the value of field type
     *
     * @return integer
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Returns the value of field startdate
     *
     * @return string
     */
    public function getStartdate()
    {
        return $this->startdate;
    }

    /**
     * Returns the value of field enddate
     *
     * @return string
     */
    public function getEnddate()
    {
        return $this->enddate;
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
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema("binopt");
        $this->belongsTo('operator', '\Operator', 'id', ['alias' => 'Operator']);
        $this->belongsTo('type', '\Robotcodetype', 'id', ['alias' => 'Robotcodetype']);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'robotcode';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Robotcode[]|Robotcode
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Robotcode
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
