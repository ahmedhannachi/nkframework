<?
/**
 * Created by PhpStorm.
 * User: Directeur
 * Date: 13/05/2015
 * Time: 13:52
 */

class PreparedStatement{

    /**
     * @var PDOStatement
     */
    /**
     * @var PDOStatement
     */
    private $statement;

    /**
     * PreparedStatement constructor.
     * @param PDOStatement $PDOStatement
     */
    public function __construct(\PDOStatement $PDOStatement )
    {
        $this->statement = $PDOStatement;
    }

    /**
     * @param $index
     * @param $value
     * @return $this
     */
    public function setValue($index, $value )
    {
        if( is_integer($value) ) $param_type=PDO::PARAM_INT;
        else if( is_bool($value) ) $param_type=PDO::PARAM_BOOL;
        else $param_type=PDO::PARAM_STR;
        $this->statement->bindValue( $index,$value,$param_type );
        return $this;
    }

    /**
     * @return Table
     * @throws Exception
     */
    public function executeQuery()
    {
        try
        {
            $this->statement->execute();
            return new Table( $this->statement->fetchAll( PDO::FETCH_ASSOC ) );
        }
        catch( Exception $e )
        {
            throw new Exception( "Error : " . $e->getMessage() );
        }
    }

    /**
     * @return Row
     */
    public function executeUpdate()
    {
        $affectedRows = $this->statement->execute();
        return new Row( array( "rows"=>$affectedRows ) );
    }

}