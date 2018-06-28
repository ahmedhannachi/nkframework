<?

/**
 * Created by Nkconcept.
 * Date: 01/01/2017
 * Time: 20:48
 */
interface PagesManagerConfigurator
{
    static function setPageIndex( $page_index );
    static function setPagesPath( $pages_path );
    static function setDefaultPageClassName( $default_page_class_name );
    static function setNotFoundPageClassName( $not_found_page_class_name );
}