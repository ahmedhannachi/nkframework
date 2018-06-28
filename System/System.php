<?

/**
 * Created by Nkconcept.
 * Date: 01/01/2017
 * Time: 18:14
 */
class System extends Configuration
{
    static public function setPhpPath($php_path)
    {
        static::$php_path=$php_path;
    }
    static public function setDefaultDateFormat($default_date_format)
    {
        static::$default_date_format=$default_date_format;
    }
    static public function setDefaultDateTimeFormat($default_date_time_format)
    {
        static::$default_date_time_format=$default_date_time_format;
    }
    static function setPageIndex($page_index)
    {
        static::$page_index=$page_index;
    }
    static function setPagesPath($pages_path)
    {
        static::$pages_path=$pages_path;
    }
    static function setDefaultPageClassName($default_page_class_name)
    {
        static::$default_page_class_name=$default_page_class_name;
    }
    static function setNotFoundPageClassName($not_found_page_class_name)
    {
        static::$not_found_page_class_name=$not_found_page_class_name;
    }
    static function display( $display_exception=false )
    {
        static::executeHttpRequest( $display_exception );
    }
    public static function setHttpRequestExecuterSetterFunction($http_request_executer_setter_function)
    {
        if( !is_callable( $http_request_executer_setter_function ) ) throw new Exception( "System::setHttpRequestExecuterSetterFunction expecting function ".gettype($http_request_executer_setter_function)." given." );
        static::$http_request_executer_setter_function=$http_request_executer_setter_function;
    }
    public static function setHttpRequestListenerIndex($http_request_listener_index)
    {
        static::$http_request_listener_index=$http_request_listener_index;
    }

}