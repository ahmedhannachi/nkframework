<?
/**
 * Created by NKconcept.
 * User: Nkconcept
 * Date: 04/07/2015
 * Time: 17:01
 */

class Response {
    private $parameters;
    public function __construct( array $parameters=array() )
    {
        $this->parameters = $this->buildData( $parameters );
    }
    private function buildData( array $parameters )
    {
        $data=array();
        foreach( $parameters As $index=>$value )
        {
            if( is_array($value) )
                $data[ $index ] = $this->buildData( $value );
            else
                $data[ $index ] = $value;
        }
        return $data;
    }
    /** GETTERS **/
    public function getParameters()
    {
        return $this->parameters;
    }
    private function getDataUtf8Encoded( $data )
    {
        $data_utf8=array();
        foreach( $data As $index=>$value )
        {
            if( is_array($value) )
                $data_utf8[ $index ]=$this->getDataUtf8Encoded( $value );
            else
                $data_utf8[ $index ]=utf8_encode( $value );
        }
        return $data_utf8;
    }
    public function toJson()
    {
        return json_encode( $this->getDataUtf8Encoded( $this->getParameters() ) );
    }
    public function toArray()
    {
        return $this->parameters;
    }
    public function count()
    {
        return count( $this->getParameters() );
    }
    public function getIndexes()
    {
        return array_keys( $this->getParameters() );
    }
    public function getValues()
    {
        return array_values( $this->parameters );
    }
    public function get( $index )
    {
        if( $this->indexExists($index) ) return $this->parameters[ $index ];
        else throw new Exception( "parameter($index) does not exists!" );
    }
    public function indexExists($index)
    {
        return array_key_exists($index,$this->parameters);
    }
    /** GETTERS **/

    /** SETTERS **/
    public function push($index,$value)
    {
        $this->parameters[$index]=$value;
    }
    public function put( $value )
    {
        $this->parameters[]=$value;
    }
    public function remove($index)
    {
        unset( $this->parameters[ $index ] );
    }
    /** SETTERS **/
}