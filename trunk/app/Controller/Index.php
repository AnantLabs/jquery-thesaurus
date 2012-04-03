<?PHP
/*
* @package Thesaurus
* @author sheiko
* @copyright (c) Dmitry Sheiko http://dsheiko.com
*/

class Controller_Index {

    private $_storage;
    private $_view;

    /**
     *
     * @param Thesaurus_Adapter_Interface $storage data access adapter
     * @param reference $output
     */
    public function  __construct(Lib_Storage_Adapter_Interface $storage, Lib_View &$view)
    {
        $this->_storage = $storage;
        $this->_view = &$view;
    }


    /**
     * Increments click stats for the term
     *
     * $_REQUEST {
     *    term :string
     *    onclick: boolean
     * }
     *
     * @param array $glossary
     * @return void
     */
    public function onclickAction(Thesaurus_Adapter_Interface $adapter)
    {
        $term = $_REQUEST['term'];
        $glossary = $adapter->incrementClickStat($term);
    }
    /**
     * Get JSON-like output with term list
     *
     * $_REQUEST {}
     *
     * @param array $glossary
     * @return void
     */
    public function termListAction(Thesaurus_Adapter_Interface $adapter)
    {
        $out = "";
        $glossary = $adapter->getData();
        foreach ($glossary as $term => $def) {
            $out .= "'" . Lib_Mbstring::ordString($term) . "',\n";
        }
        $this->_view->data = "[" . rtrim($out, ",\n") . "\n]";
    }
    /**
     * Get JSON-like output with term definition
     *
     * $_REQUEST {
     *    term :string
     *    caseSentitive :boolean
     * }
     *
     * @param Thesaurus_Adapter_Interface $adapter
     * @return void
     */
    public function termDefAction(Thesaurus_Adapter_Interface $adapter)
    {
        $term = $_REQUEST['term'];
        $caseSentitive = isset($_REQUEST['caseSentitive']) ? $_REQUEST['caseSentitive'] : false;
        if (!trim($term)) {
            throw new Exception('Invalid term given (' . $term . ')');
        }
        $this->_view->data = "'" . addslashes(
               $adapter->findDefinition($term, $caseSentitive)
        ) . "'";
        $this->_view->data = preg_replace("/[\n\r]/", " ", $this->_view->data);
    }
   

}