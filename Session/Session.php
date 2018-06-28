<?
/**
 * Created by NKconcept.
 * Date: 07/08/2015
 * Time: 20:54
 */

abstract class Session {
    public static function push( $ar_index,$index,$value )
    {
        $_SESSION[ $ar_index ][ $index ] = $value;
    }

    public static function put( $index,$value )
    {
        $_SESSION[ $index ] = $value;
    }

    public static function get( $index )
    {
        return $_SESSION[ $index ];
    }
    public static function remove( $index )
    {
        $_SESSION[ $index ]=null;
    }
    public static function destroy()
    {
        unset($_SESSION);
        session_destroy();
    }
}