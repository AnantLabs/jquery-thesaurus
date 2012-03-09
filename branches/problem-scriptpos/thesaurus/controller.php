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
     * Converts mb. string to set of its UTF8 codes
     * @param string $line
     * @return string
     */
    private function _ordString($data)
    {
        $mb_hex = '';
        $bytes = 4;
        for ($i=0; $i<mb_strlen($data, 'UTF-8'); $i++) {
            $mb_hex .= sprintf("\\u%04x",
                $this->_ordUTF8(mb_substr($data, $i, 1, 'UTF-8'), 0, $bytes)
            );
        }
        return $mb_hex;
    }

    /**
     * Ord function extension taken from http://de2.php.net/manual/en/function.ord.php#78032
     * @param string $c
     * @param int $index
     * @param int $bytes
     * @return int
     */
    private function _ordUTF8($c, $index = 0, &$bytes = null)
    {
      $len = strlen($c);
      $bytes = 0;

      if ($index >= $len)
        return false;

      $h = ord($c{$index});

      if ($h <= 0x7F) {
        $bytes = 1;
        return $h;
      }
      else if ($h < 0xC2)
        return false;
      else if ($h <= 0xDF && $index < $len - 1) {
        $bytes = 2;
        return ($h & 0x1F) <<  6 | (ord($c{$index + 1}) & 0x3F);
      }
      else if ($h <= 0xEF && $index < $len - 2) {
        $bytes = 3;
        return ($h & 0x0F) << 12 | (ord($c{$index + 1}) & 0x3F) << 6
                                 | (ord($c{$index + 2}) & 0x3F);
      }
      else if ($h <= 0xF4 && $index < $len - 3) {
        $bytes = 4;
        return ($h & 0x0F) << 18 | (ord($c{$index + 1}) & 0x3F) << 12
                                 | (ord($c{$index + 2}) & 0x3F) << 6
                                 | (ord($c{$index + 3}) & 0x3F);
      }
      else
        return false;
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
            $out .= "'" . $this->_ordString($term) . "',\n";
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
        $this->_output = "'" . addslashes(
               $adapter->findDefinition($term, $caseSentitive)
        ) . "'";
        $this->_output = preg_replace("/[\n\r]/", " ", $this->_output);
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