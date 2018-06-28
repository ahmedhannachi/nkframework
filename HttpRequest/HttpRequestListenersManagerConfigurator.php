<?

/**
 * Created by Nkconcept.
 * Date: 01/01/2017
 * Time: 20:46
 */
interface HttpRequestListenersManagerConfigurator
{
    /**
     * @param mixed $http_request_executer
     */
    public static function setHttpRequestExecuterSetterFunction($http_request_executer_setter_function);
    /**
     * @param string $http_request_listener_index
     */
    public static function setHttpRequestListenerIndex($http_request_listener_index);
}