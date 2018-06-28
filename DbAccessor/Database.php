<?
/**
 * Created by Nkconcept.
 * User: Directeur
 * Date: 14/05/2015
 * Time: 11:06
 */

class Database {

    /**
     * @var array
     */
    private $tables;

    /**
     * Database constructor.
     */
    public function __construct()
    {
        $this->tables = array();
    }

    /**
     * @param $index
     * @param Table $table
     * @throws Exception
     */
    public function add($index, Table $table )
    {
        if( !array_key_exists( $index, $this->tables ) )
        {
            $this->tables[ $index ] = $table;
        }
        else
        {
            throw new Exception( "Table already exists" );
        }
    }

    /**
     * @param $index
     * @return mixed
     * @throws Exception
     */
    public function tables($index )
    {
        if( array_key_exists( $index,$this->tables ) )
        {
            return $this->tables[ $index ];
        }
        else
        {
            throw new Exception( "Table does not exists" );
        }
    }

    /**
     * @return array
     */
    public function tablesName()
    {
        return array_keys( $this->tables );
    }
}