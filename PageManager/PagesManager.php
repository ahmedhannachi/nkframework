<?

/**
 * Created by Nkconcept.
 * Date: 01/01/2017
 * Time: 18:57
 */

abstract class PagesManager extends HttpRequestListenersManager implements PagesManagerConfigurator
{
    /**
     * @var
     */
    /**
     * @var
     */
    /**
     * @var
     */
    /**
     * @var
     */
    /**
     * @var
     */
    protected static $pages,
                     $pages_path,
                     $page_index,
                     $default_page_class_name,
                     $not_found_page_class_name;

    /**
     * @return Row
     * @throws Exception
     */
    public static function getPages()
    {
        if( empty( self::$pages_path ) ) throw new Exception( "Pages path needed." );
        if( !self::$pages ) self::$pages=new Row( Loader::load( self::$pages_path,true ) );
        return self::$pages;
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public static function getPageIndex()
    {
        if( empty( self::$page_index ) )  throw new Exception( "Page index needed." );
        return self::$page_index;
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public static function getDefaultPageClassName()
    {
        if( empty( self::$default_page_class_name ) ) throw new Exception( "Default page class name needed." );
        return self::$default_page_class_name;
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public static function getNotFoundPageClassName()
    {
        if( empty( self::$not_found_page_class_name ) ) throw new Exception( "Not found page class name needed." );
        return self::$not_found_page_class_name;
    }

    /**
     *
     */
    public static function display()
    {
        $requested_page=self::formatPageName($_GET[ self::getPageIndex() ]);
        if( empty( $_GET[ static::getPageIndex() ] ) )
            $page_class_name=static::getDefaultPageClassName();
        elseif( static::getPages()->indexExists( $requested_page ) )
            $page_class_name = $requested_page;
        else
            $page_class_name=static::getNotFoundPageClassName();

        /* @var $page IPageModel */
        $page=( new $page_class_name() );
        $page->display();
    }

    /**
     * @param $name
     * @return string
     */
    public static function formatPageName($name )
    {
        return strtoupper( substr($name,0,1) ). strtolower( substr($name,1) );
    }

    /**
     * @return IPageModel
     */
    public static function getActivePage()
    {
        $active_page=self::formatPageName($_GET[ self::getPageIndex() ]);
        if( empty($active_page) ) $active_page=self::getDefaultPageClassName();
        return new $active_page();
    }

    public static function getRefreshUrl( Row $parameters )
    {
        $ActivePage=self::getActivePage();
        $current_parameters=$_GET;
        unset($current_parameters[self::getPageIndex()]);
        return $ActivePage::getUrl(new Row(array_merge($current_parameters,$parameters->toArray())));
    }
}