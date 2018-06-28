<?
/**
 * Created by NKconcept.
 * User: Directeur
 * Date: 15/05/2015
 * Time: 15:51
 */

abstract class DatabaseAccessor {

    private static  $con,
                    $server,
                    $dbname,
                    $username,
                    $password;

    /**
     * @return Connection
     */
    public static function connection()
    {
        if( !is_a( DatabaseAccessor::$con,"Connection" ) )
        {
            DatabaseAccessor::$con = new Connection(
                    DatabaseAccessor::$server,
                    DatabaseAccessor::$dbname,
                    DatabaseAccessor::$username,
                    DatabaseAccessor::$password
            );
        }
        return DatabaseAccessor::$con;
    }

    public static function close()
    {
        DatabaseAccessor::$con=null;
    }

    /**
     * @param mixed $server
     */
    public static function setServer($server)
    {
        self::$server = $server;
    }

    /**
     * @param mixed $dbname
     */
    public static function setDbname($dbname)
    {
        self::$dbname = $dbname;
    }

    /**
     * @param mixed $username
     */
    public static function setUsername($username)
    {
        self::$username = $username;
    }

    /**
     * @param mixed $password
     */
    public static function setPassword($password)
    {
        self::$password = $password;
    }

    /**
     * @return mixed
     */
    public static function getServer()
    {
        return self::$server;
    }

    /**
     * @return mixed
     */
    public static function getDbname()
    {
        return self::$dbname;
    }

    /**
     * @return mixed
     */
    public static function getUsername()
    {
        return self::$username;
    }

    /**
     * @return mixed
     */
    public static function getPassword()
    {
        return self::$password;
    }






}