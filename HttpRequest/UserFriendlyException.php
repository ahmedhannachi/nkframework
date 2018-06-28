<?

/**
 * Created by Nkconcept.
 * User: ahmed
 * Date: 21/02/2016
 * Time: 13:16
 */
class UserFriendlyException extends Exception
{

    private $title,
        $field,
        $callback,
        $callbackData;

    public function __construct($message = "", $title, $field = null, $callback = null, $callbackData = null, $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->field = $field;
        $this->callback = $callback;
        $this->callbackData = $callbackData;
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return null
     */
    public function getCallback()
    {
        return $this->callback;
    }

    /**
     * @return null
     */
    public function getCallbackData()
    {
        return $this->callbackData;
    }


}