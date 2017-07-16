<?php
namespace App\Core\Models;

class Robotlink extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     * @Primary
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $code_id;

    /**
     *
     * @var integer
     * @Primary
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $user_id;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $robot_id;

    /**
     *
     * @var string
     * @Column(type="string", length=64, nullable=false)
     */
    protected $instruments;

    /**
     *
     * @var integer
     * @Column(type="integer", length=1, nullable=false)
     */
    protected $turned;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    protected $offtime;

    /**
     * Method to set the value of field code_id
     *
     * @param integer $code_id
     * @return $this
     */
    public function setCodeId($code_id)
    {
        $this->code_id = $code_id;

        return $this;
    }

    /**
     * Method to set the value of field user_id
     *
     * @param integer $user_id
     * @return $this
     */
    public function setUserId($user_id)
    {
        $this->user_id = $user_id;

        return $this;
    }

    /**
     * Method to set the value of field robot_id
     *
     * @param integer $robot_id
     * @return $this
     */
    public function setRobotId($robot_id)
    {
        $this->robot_id = $robot_id;

        return $this;
    }

    /**
     * Method to set the value of field instruments
     *
     * @param string $instruments
     * @return $this
     */
    public function setInstruments($instruments)
    {
        $this->instruments = $instruments;

        return $this;
    }

    /**
     * Method to set the value of field turned
     *
     * @param integer $turned
     * @return $this
     */
    public function setTurned($turned)
    {
        $this->turned = $turned;

        return $this;
    }

    /**
     * Method to set the value of field offtime
     *
     * @param string $offtime
     * @return $this
     */
    public function setOfftime($offtime)
    {
        $this->offtime = $offtime;

        return $this;
    }

    /**
     * Returns the value of field code_id
     *
     * @return integer
     */
    public function getCodeId()
    {
        return $this->code_id;
    }

    /**
     * Returns the value of field user_id
     *
     * @return integer
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * Returns the value of field robot_id
     *
     * @return integer
     */
    public function getRobotId()
    {
        return $this->robot_id;
    }

    /**
     * Returns the value of field instruments
     *
     * @return string
     */
    public function getInstruments()
    {
        return $this->instruments;
    }

    /**
     * Returns the value of field turned
     *
     * @return integer
     */
    public function getTurned()
    {
        return $this->turned;
    }

    /**
     * Returns the value of field offtime
     *
     * @return string
     */
    public function getOfftime()
    {
        return $this->offtime;
    }

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema("binopt");
        $this->belongsTo('code_id', '\Robotcode', 'id', ['alias' => 'Robotcode']);
        $this->belongsTo('robot_id', '\Robotuser', 'id', ['alias' => 'Robotuser']);
        $this->belongsTo('user_id', '\User', 'id', ['alias' => 'User']);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'robotlink';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Robotlink[]|Robotlink
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Robotlink
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
