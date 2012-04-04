<?PHP
/*
* @package Thesaurus
* @author sheiko
* @copyright (c) Dmitry Sheiko http://dsheiko.com
*/

include_once dirname(__FILE__) . "/Interface.php";

class Lib_Storage_Adapter_Csv implements Lib_Storage_Adapter_Interface
{
    private $_file;
    /**
     *
     * @param string $file CSV data file name
     */
    public function  __construct(Lib_Config $config) {
        $this->_file = $config->dataSource->file;
    }
    /**
     * Increments click stats for the term
     *
     * @param string $term
     * @return void
     */
    public function incrementClickStat($term) {
    }
    /**
     * Get glossary
     * 
     * @return array
     */
    public function getData() {
        $glossary = array();
        try {
        $data = file($this->_file);
        foreach ($data as $key => $line) {
            $parts = explode(";", $line);
            $glossary[$parts[0]] = $parts[1];
        }
        } catch (Exception $e) {
            throw new Exception('Cannot open file ' . $this->_file);
        }
        return $glossary;
    }
    /**
     *
     * @param string $term
     * @param boolean $caseSentitive
     * @return string
     */
    public function findDefinition($term, $caseSentitive) {
        $glossary = $this->getData();
        if (empty ($glossary)) {
            throw new Exception('Glossary is empty');
        }
        foreach ($glossary as $fTerm => $def) {
            if (true == $caseSentitive && 0 === strcmp($fTerm, $term)) {
                return $def;
            }
            if (false == $caseSentitive && 0 === strcasecmp($fTerm, $term)) {
                return $def;
            }
        }
        return null;
    }
}
