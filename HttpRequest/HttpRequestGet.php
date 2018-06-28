<?

/**
 * Created by Nkconcept.
 * Date: 17/06/2017
 * Time: 20:50
 */
class HttpRequestGet
{
    /**
     * @param $index
     * @return Column
     */
    public static function get($index )
    {
        return new Column( $index,$_GET[ $index ] );
    }
    /**
     * @param $index
     * @param $value
     */
    public static function set($index, $value )
    {
        $_GET[ $index ]=$value;
    }
}