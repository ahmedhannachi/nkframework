<?
/**
 * Created by NKconcept.
 * Date: 09/08/2015
 * Time: 13:20
 */

abstract class IComponents implements IComponentManager {
    public static function getHtml( Row $data=null )
    {
        static::getContent( $data );
        static::getJs( $data );
    }
}