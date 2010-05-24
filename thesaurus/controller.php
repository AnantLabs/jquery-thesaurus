<?PHP
/*
* Thesaurus Lib
*
* @package Thesaurus
* @author $Author: sheiko $
* @copyright (c) Dmitry Sheiko http://dsheiko.com
*/

class Thesaurus_Controller {

    private $_adapter = null;
    private $_output;

    /**
     *
     * @param Thesaurus_Adapter_Interface $adapter data access adapter
     * @param reference $output
     */
    public function  __construct(Thesaurus_Adapter_Interface $adapter, &$output)
    {
        $this->_adapter = $adapter;
        $this->_output = &$output;
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
            $out .= "'" . $term . "',\n";
        }
        $this->_output = "[" . rtrim($out, ",\n") . "\n]";
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
        $this->_output = "'" . addslashes($adapter->findDefinition($term, $caseSentitive)) . "'";
    }
    /**
     * Dispathes request and returns output of the requested action
     *
     * @param array $request
     * @return string
     */
    public function dispatch(array $request)
    {
        if (isset ($request['term']) && isset ($request['onclick'])) {
           return $this->onclickAction($this->_adapter);
        } elseif (isset ($request['term'])) {
           return $this->termDefAction($this->_adapter);
        } else {
            return $this->termListAction($this->_adapter);
        }
    }
    
}