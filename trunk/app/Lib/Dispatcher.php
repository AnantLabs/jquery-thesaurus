<?PHP
/*
* @package Thesaurus
* @author sheiko
* @copyright (c) Dmitry Sheiko http://dsheiko.com
*/

class Lib_Dispatcher
{
    private $_storage;
    private $_view;

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

        if (isset ($request['term']) && isset ($request['onclick'])) {
           return $controller->onclickAction($this->_storage);
        } elseif (isset ($request['term'])) {
           return $controller->termDefAction($this->_storage);
        } else {
            return $controller->termListAction($this->_storage);
        }
    }

}
