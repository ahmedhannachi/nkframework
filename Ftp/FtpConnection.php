<?

/**
 * Created by NKconcept.
 * Date: 08/12/2015
 * Time: 20:36
 */
class FtpConnection
{
    private $user,
            $password;

    public function __construct( $user,$password )
    {
        $this->user=$user;
        $this->password=$password;
    }
    public function getUser()
    {
        return $this->user;
    }
    public function setUser($user)
    {
        $this->user = $user;
    }
    public function getPassword()
    {
        return $this->password;
    }
    public function setPassword($password)
    {
        $this->password = $password;
    }
    public function login()
    {
        if( empty( $this->user ) or empty( $this->password ) ) throw new Exception( "Please set user informations before connection!" );

    }


}