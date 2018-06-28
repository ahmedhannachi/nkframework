<?

/**
 * Created by NKconcept.
 * Date: 08/12/2015
 * Time: 20:20
 */
abstract class Ftp
{
    private static $host,
                   $port=21,
                   $timeout=90,
                   $transfert_mode=FTP_ASCII; // or FTP_BINARY

    private static $user,
                   $password;

    private static $connection,
                   $login;

    public static function getHost()
    {
        return self::$host;
    }
    public static function setHost($host)
    {
        self::$host = $host;
    }
    public static function getPort()
    {
        return self::$port;
    }
    public static function setPort($port)
    {
        self::$port = $port;
    }
    public static function getUser()
    {
        return self::$user;
    }
    public static function setUser($user)
    {
        self::$user = $user;
    }
    public static function getTimeout()
    {
        return self::$timeout;
    }
    public static function setTimeout($timeout)
    {
        self::$timeout = $timeout;
    }
    public static function getPassword()
    {
        return self::$password;
    }
    public static function setPassword($password)
    {
        self::$password = $password;
    }
    public static function getConnection()
    {
        if( empty( Ftp::$host ) ) throw new Exception( "Please set host before connection!" );
        Ftp::$connection=ftp_connect(Ftp::$host,Ftp::$port,Ftp::$timeout);
    }
    public static function login()
    {
        if( empty( Ftp::$user ) or empty( Ftp::$password ) ) throw new Exception( "Please set username and password before connection!" );
        Ftp::$login=ftp_login(Ftp::$connection,Ftp::$user,Ftp::$password);
    }
    public static function put( $file,$desctination )
    {
        ftp_put(Ftp::$connection,$file,$desctination,Ftp::$transfert_mode);
    }
    public static function get( $file,$destination )
    {
        ftp_get(Ftp::$connection,$destination,$file,Ftp::$transfert_mode);
    }
}