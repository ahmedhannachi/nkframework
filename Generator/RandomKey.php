<?
/**
 * Created by Nkconcept.
 * User: HANNACHI ahmed
 * Date: 28/07/2015
 * Time: 15:39
 */

class RandomKey {
    private $key="";
    public function __construct( $length,$disableSpecialCharacters=false,$disableNumbers=false,$disableLetters=false )
    {
        for( $i=0;$i<$length;$i++ )
        {
            $this->key.=RandomKey::randomChar($disableSpecialCharacters,$disableNumbers,$disableLetters);
        }
    }
    public static function randomChar( $disableSpecialCharacters=false,$disableNumbers=false,$disableLetters=false )
    {
        $chars="";
        if( !$disableSpecialCharacters ) $chars .= "-_*?!^&";
        if( !$disableNumbers ) $chars .= "0123456789";
        if( !$disableLetters ) $chars .= "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        return $chars[ rand( 0,( strlen($chars)-1 ) ) ];
    }
    public function getKey()
    {
        return $this->key;
    }
    public function getKeyCapitale()
    {
        return strtoupper( $this->key );
    }
    public function getKeySeparated( $partLength,$separator )
    {
        if( $this->getLength() < $partLength ) throw new Exception( "Part length can't be bigger than key length !" );
        $currentIndex=0;
        $separatedKeyInArray=array();
        while( $currentIndex<$this->getLength() )
        {
            $separatedKeyInArray .= substr( $currentIndex,$partLength );
        }
        return implode($separator,$separatedKeyInArray);
    }
    public function getLength()
    {
        return strlen($this->key);
    }
}