<?


/**
 * Created by NKconcept.
 * User: HANNACHI ahmed
 * Date: 21/07/2015
 * Time: 16:47
 */

abstract class Configuration extends PagesManager implements ConfigurationInitialiser {

    protected static $php_classes_paths,
                     $php_path,
                     $default_date_format,
                     $default_date_time_format;

    /**
     * @return string
     */
    public static function getDefaultDateFormat()
    {
        return self::$default_date_format;
    }
    /**
     * @return string
     */
    public static function getDefaultDateTimeFormat()
    {
        return self::$default_date_time_format;
    }
    /**
     * @return string
     * @throws Exception
     */
    public static function getPhpPath()
    {
        if( empty( self::$php_path ) )  throw new Exception( "Php path needed" );
        return self::$php_path;
    }
    private static function setPhpClassesPaths()
    {
        self::$php_classes_paths=new Table( Loader::load( self::getPhpPath() ) );
    }
    public static function getPhpClassesPaths()
    {
        if( !self::$php_classes_paths ) self::setPhpClassesPaths();
        return self::$php_classes_paths;
    }
}