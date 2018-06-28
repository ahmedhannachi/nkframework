<?
/**
 * Created by NKconcept.
 * Date: 21/02/2016
 * Time: 14:59
 */

abstract class Referenced{
    /**
     * @return String
     */
    function getReference()
    {
        return "#".preg_replace('/[a-z]/', '', get_called_class()).$this->getAddedAt()->format("ym").$this->getId();
    }
    /**
     * @return Dtime
     */
    abstract function getAddedAt();
    /**
     * @return int
     */
    abstract function getId();
}