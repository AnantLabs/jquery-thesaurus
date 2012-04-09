<?PHP
/*
* @package Thesaurus
* @author sheiko
* @copyright (c) Dmitry Sheiko http://dsheiko.com
*/

class Lib_Dispatcher
{
    /**
     *
     * @var Lib_Storage_Adapter_Interface
     */
    private $_storage;
    /**
     *
     * @var Lib_View 
     */
    private $_view;
    /**
     *
     * @param Lib_Storage_Adapter_Interface $storage
     * @param Lib_View $view 
     */
    public function  __construct(Lib_Storage_Adapter_Interface $storage, Lib_View &$view)
    {
        $this->_storage = $storage;
        $this->_view = &$view;
    }
    /**
     * Dispathes request and returns output of the requested action
     *
     * @param array $request
     * @return string
     */
    public function dispatch(array $request)
    {
        include_once APPPATH . "/Controller/Index.php";
        $controller = new Controller_Index($this->_storage, $this->_view);
        $methodName = $_GET["action"] . "Action";
        if (method_exists($controller, $methodName)){            
            return $controller->$methodName();
        }
        return false;
    }

}
