<?
/**
 * Created by Nkconcept.
 * Date: 14/05/2015
 * Time: 11:14
 */

class Column {

    private $index,
        $value,
        $type;

    public function __construct( $index,$value )
    {
        $this->index = $index;
        $this->value = $value;
        $this->type = gettype( $value );
    }
    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
    /**
     * @return mixed
     */
    public function getIndex()
    {
        return $this->index;
    }
    /**
     * @return mixed
     */
    public function type()
    {
        return $this->type;
    }
    /**
     * @param $value
     */
    public function setValue( $value )
    {
        $this->value = $value;
        $this->type = gettype( $value );
    }
}