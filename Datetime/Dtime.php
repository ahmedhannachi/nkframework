<?
/**
 * Created by NKconcept.
 * User: HANNACHI ahmed
 * Date: 22/07/2015
 * Time: 19:46
 */

class Dtime {

    private $timestamp;

    public function __construct( $timestamp )
    {
        $this->timestamp = $timestamp;
    }
    public function getTime()
    {
        return $this->timestamp;
    }
    public function getYear()
    {
        return intval( date( "Y",$this->timestamp ) );
    }
    public function getMonth()
    {
        return intval( date( "m",$this->timestamp ) );
    }
    public function getDay()
    {
        return intval( date( "d",$this->timestamp ) );
    }
    public function getHour()
    {
        return intval( date( "H",$this->timestamp ) );
    }
    public function getMinute()
    {
        return intval( date( "i",$this->timestamp ) );
    }
    public function getSecond()
    {
        return intval( date( "s",$this->timestamp ) );
    }
    public function isAt( $year=false,$month=false,$day=false,$hour=false,$minute=false,$second=false )
    {
        if( $this->getYear() != $year and $year ) return false;
        if( $this->getMonth() != $month and $month ) return false;
        if( $this->getDay() != $day and $day ) return false;
        if( $this->getHour() != $hour and $hour ) return false;
        if( $this->getMinute() != $minute and $minute ) return false;
        if( $this->getSecond() != $second and $second ) return false;
        return true;
    }
    public function format( $format )
    {
        return date($format,$this->timestamp);
    }
    public function isBigger( $second )
    {
        if( is_a( $second,"Dtime" ) ) $second = $second->getTime();
        $second=intval($second);
        if( $this->timestamp > $second ) return true;
        return false;
    }
    public function isSmaller( $second )
    {
        if( is_a( $second,"Dtime" ) ) $second = $second->getTime();
        $second=intval($second);
        if( $this->timestamp < $second ) return true;
        return false;
    }
    public function getRemainingTimestamp()
    {
        if( time()<$this->timestamp ) return $this->timestamp-time();
        return 0;
    }
    public function getDifferenceTime( Dtime $dtime )
    {
        return $this->getTime()-$dtime->getTime();
    }
    public function getElapsedString( Dtime $Dtime=null,$bigger_precision=false )
    {
        if( !$Dtime ) $Dtime=self::Now();
        if( $this->timestamp<$Dtime->getTime() ) $elapsed_str="Il y'a ";
        else $elapsed_str="Encore ";

        $diff= abs( $this->timestamp-$Dtime->getTime() );
        $precisions=array( "année(s)"=>31556926,
                           "mois"=>2629743,
                           "jour(s)"=>86400,
                           "heure(s)"=>3600,
                           "minute(s)"=>60,
                           "seconde(s)"=>1,
                        );
        $empty=true;
        foreach( $precisions As $precision=>$timestamp )
        {
            if( ($diff/$timestamp)>1 )
            {
                $empty=false;
                $nbr=floor($diff/$timestamp);
                $elapsed_str.=$nbr." $precision ";
                $diff-=$nbr*$timestamp;
                if( $bigger_precision ) break;
            }
        }

        if( $empty ) return "&aacute; l'instant";
        else return trim($elapsed_str);
    }
    public static function Now()
    {
        return new Dtime( time() );
    }
}