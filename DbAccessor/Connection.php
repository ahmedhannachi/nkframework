<?
/**
 * Created by Nkconcept.
 * User: Ahmed hannachi
 * Date: 13/05/2015
 * Time: 00:55
 */

class Connection {

    private $server,
            $database,
            $username,
            $password,
            $PDOConnection;

    public function __construct( $server=null,$database=null,$username=null,$password=null )
    {
        $this->server=$server;
        $this->database=$database;
        $this->username=$username;
        $this->password=$password;
    }
    // Setters
    public function setServer( $server ){$this->server = $server;}
    public function setDatabase( $database ){$this->database = $database;}
    public function setUsername( $username ){$this->username = $username;}
    public function setPassword( $password ){$this->password = $password;}
    // Getters
    public function getServer(){ return $this->server; }
    public function getDatabase(){ return $this->database; }
    public function getUsername(){ return $this->username; }
    public function getPassword(){ return $this->password; }


    /**
     * @return mixed
     * @throws Exception
     */
    public function lastInsertedId()
    {
        if( $this->isOpen() )
        {
            return $this->PDOConnection->lastInsertId();
        }
        else
        {
            throw new Exception( "Open connection first." );
        }
    }


    /**
     * @return Connection
     * @throws Exception
     */
    public function open()
    {
        if( !$this->isOpen() )
        {
            try
            {
                $this->PDOConnection = new PDO("mysql:dbname={$this->database};host={$this->server};charset=utf8", $this->username, $this->password);
                $this->PDOConnection->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
                return $this;
            }
            catch( Exception $e )
            {
                throw new Exception( "Error while trying to open connection:".$e->getMessage() );
            }
        }else return $this;
    }

    /**
     * @return bool
     */
    public function isOpen()
    {
        if( is_a( $this->PDOConnection,"PDO" ) ) return true;
        return false;
    }

    /**
     * @return bool
     */
    public function close()
    {
        $this->PDOConnection = null;
        return true;
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function beginTransaction()
    {
        if( $this->isOpen() )
            $this->PDOConnection->beginTransaction();
        else
            throw new Exception( "Open connection first." );
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function inTransaction()
    {
        if( $this->isOpen() )
            return $this->PDOConnection->inTransaction();
        else
            throw new Exception( "Open connection first." );
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function commit()
    {
        if( $this->isOpen() )
        {
            if( $this->inTransaction() )
            {
                return $this->PDOConnection->commit();
            }
            else
            {
                throw new Exception( "Trying to commit while not in transaction." );
            }
        }
        else
        {
            throw new Exception( "Open connection first." );
        }
    }


    /**
     * @return mixed
     * @throws Exception
     */
    public function rollback()
    {
        if( $this->isOpen() )
        {
            if( $this->inTransaction() )
            {
                return $this->PDOConnection->rollback();
            }
            else
            {
                throw new Exception( "Trying to rollback while not in transaction." );
            }
        }
        else
        {
            throw new Exception( "Open connection first." );
        }
    }


    /**
     * @param $selectStatement
     * @return Table
     * @throws Exception
     */
    public function executeQuery( $selectStatement )
    {
        if( $this->isOpen() )
        {
            try
            {
                /* @var $PDOStatement PDOStatement */
                $PDOStatement = $this->PDOConnection->query( $selectStatement );
                $table = new Table( $PDOStatement->fetchAll( PDO::FETCH_ASSOC ) );
                return $table;
            }
            catch( Exception $e )
            {
                die( "Failed to execute Query ( $selectStatement )" );
            }
        }
        else
        {
            throw new Exception( "Open connection first." );
        }
    }


    /**
     * @param $updateStatement
     * @return Row
     * @throws Exception
     */
    public function executeUpdate( $updateStatement )
    {
        if( $this->isOpen() )
        {
            try
            {
                return $this->PDOConnection->exec( $updateStatement  );
            }
            catch( Exception $e )
            {
                die( "Failed to execute Update ( $updateStatement )" );
            }
        }
        else
        {
            throw new Exception( "Open connection first." );
        }
    }

    /**
     * @param $SqlString
     * @return PreparedStatement
     */
    public function prepare( $SqlString )
    {
        try
        {
            $PreparedStatement = $this->PDOConnection->prepare( $SqlString );
            return new PreparedStatement( $PreparedStatement );
        }
        catch( Exception $e )
        {
            die( "Error : " . $e->getMessage() );
        }
    }



}