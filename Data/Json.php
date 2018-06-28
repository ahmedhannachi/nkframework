<?
/**
 * Created by NKconcept.
 * Date: 13/08/2015
 * Time: 22:39
 */

class Json {

    private $data;

    public function __construct( $jsonString )
    {
        $this->data = json_decode($jsonString,true);
        if( (json_last_error() != JSON_ERROR_NONE) ) throw new Exception( "$jsonString is not a Json string" );
    }
    public function toArray()
    {
        return $this->data;
    }
    public function get( $index )
    {
        if( array_key_exists( $index,$this->data ) )
        {
            if( is_array( $this->data[ $index ] ) ) return new Json( json_encode( $this->data[ $index ] ) );
                else return $this->data[ $index ];
        }
        else return false;
    }
    public function getRowData()
    {
        return new Row( $this->data );
    }

}