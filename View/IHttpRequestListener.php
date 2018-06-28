<?

/**
 * Created by Nkconcept.
 * Date: 01/01/2017
 * Time: 18:23
 */

interface IHttpRequestListener
{
    /**
     * @return bool
     */
    static function isAjax();
    /**
     * @return Row
     */
    static function columns();
    /**
     * @return Response
     */
    static function execute( Row $data );
}