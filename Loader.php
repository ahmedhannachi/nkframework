<?

/**
 * Created by Nkconcept.
 * Date: 01/01/2017
 * Time: 19:05
 */
abstract class Loader
{
    /* @var $classes_paths array */
    public static $classes_paths;

    /**
     * @param $path
     * @param bool|false $initiate
     * @return array
     */
    public static function load($path,$initiate=false){

        static $classes=array();
        if( $initiate ) $classes=array();

        $dir_handle = @opendir($path) or die("Unable to open $path");

        while (false !== ($file = readdir($dir_handle)))
        {
            if($file!="." && $file!="..")
            {
                if (is_dir($path."/".$file))
                {
                    self::load($path."/".$file);
                }
                else
                {
                    $index=basename($file,".php");
                    $file_path=$path . "/" . $file;

                    if( array_key_exists( $index,$classes ) )
                    {
                        if( is_array( $classes[ $index ] ) )
                        {
                            $classes[ $index ][] = $file_path;
                        }
                        else
                        {
                            $s_path=$classes[ $index ];
                            $classes[ $index ]=array( $s_path, $file_path );
                        }
                    }
                    else
                    {
                        $classes[ $index ]=$file_path;
                    }
                }
            }
        }
        closedir($dir_handle);
        return $classes;
    }
    public static function autoLoaderFunctionDescription() {
        return function( $class_name ){
            $class_name=substr( $class_name,( strrpos( $class_name,"\\" )?strrpos( $class_name,"\\" )+1:0 ) );
            if( is_array( Loader::$classes_paths[ $class_name ] ) )
            {
                foreach( Loader::$classes_paths[ $class_name ] As $file )
                {
                    if( file_exists( $file ) ) include $file;
                }
            }
            else
            {
                if( file_exists( Loader::$classes_paths[ $class_name ] ) ) include Loader::$classes_paths[ $class_name ];
            }
        };
    }
    public static function buildClassesFiles( $destination,$tables=null )
    {
        if( !$tables ) $tables=DatabaseAccessor::connection()->open()->executeQuery("SHOW TABLES FROM ".DatabaseAccessor::getDbname());
        while( $tables->hasNext() )
        {
            $content="<? \r\n";
            $table_name=$tables->next()->next()->getValue();
            $class_name=implode(array_map("ucfirst",explode("_",$table_name)));
            $content.="class $class_name extends WritablePersistence{ \r\n";
            $content.=" protected ";
            $columns=DatabaseAccessor::connection()->open()->executeQuery("SHOW COLUMNS FROM `$table_name`");
            $content.=" ".implode(",",$columns->toArrayByFunction( function( $row ){ return "$".$row->getColumn( "Field" )->getValue(); } ))."; \r\n";
            $content.=" public function __construct( ".implode(",",$columns->toArrayByFunction( function( $row ){ return "$".$row->getColumn( "Field" )->getValue()."=null"; } ))." ){  \r\n";
            $content.=" ".implode("; \r\n",$columns->toArrayByFunction( function( $row ){ return '$this->'.$row->getColumn( "Field" )->getValue().'=$'.$row->getColumn( "Field" )->getValue(); } ))."; \r\n } \r\n";
            $content.=" ".implode(" ",$columns->toArrayByFunction( function( $row ){ return " public function get".implode(array_map("ucfirst",explode("_",$row->getColumn( "Field" )->getValue()))).'(){ return $this->'.$row->getColumn( "Field" )->getValue().'; }'; } ))." ";
            $content.=" ".implode(" ",$columns->toArrayByFunction( function( $row ){ return " public function set".implode(array_map("ucfirst",explode("_",$row->getColumn( "Field" )->getValue()))).'( $'.$row->getColumn( "Field" )->getValue().' ){ $this->'.$row->getColumn( "Field" )->getValue().'=$'.$row->getColumn( "Field" )->getValue().'; }'; } ))." ";
            $content.="}";
            $file=fopen("$destination/$class_name.php","w+");
            fwrite($file,$content);
            fclose($file);
        }
    }
}