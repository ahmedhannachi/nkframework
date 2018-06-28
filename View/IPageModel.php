<? /** * Created by NKconcept. * Date: 04/08/2015 * Time: 18:11 */
abstract class IPageModel
{
    public abstract function printTitle();

    public abstract function printHead();

    public abstract function printBody();

    public abstract function printContent();

    public abstract function printCss();

    public abstract function printJs();

    public abstract function display();

    public static function isActive()
    {
        $page = strtolower($_GET[PagesManager::getPageIndex()]);
        if (empty($page) and strtolower(get_called_class()) == strtolower(PagesManager::getDefaultPageClassName())) return true; else if (strtolower(get_called_class()) == $page) return true; else return false;
    }

    public static function getUrl(Row $params = null)
    {
        $params = ($params ? $params : new Row());
        return "index.php?" . System::getPageIndex() . "=" . get_called_class() . ($params->length() ? "&" . http_build_query($params->toArray()) : "");
    }
}