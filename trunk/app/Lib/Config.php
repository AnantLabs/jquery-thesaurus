<?PHP
/*
* @package Thesaurus
* @author sheiko
* @copyright (c) Dmitry Sheiko http://dsheiko.com
*/

include_once dirname(__FILE__) . "/Config/Exception.php";

class Lib_Config
{
    /**
     * Config repository
     * @var array
     */
    private $_data = null;

    /**
     *
     * @param type $dataSource
     * @return Lib_Config
     * @throws Exception 
     */
    public function  __construct($dataSource)
    {
         if (is_null($dataSource)) {
            return $this;
        }
        if (is_string($dataSource)) {
            $dataSource = $this->_loadData($dataSource);
        }
        if (!is_array($dataSource)) {
            throw new Lib_Config_Exception('Invalid data source');
        }
        $this->_data = array();
        foreach ($dataSource as $key => $value) {
            if (is_array($value)) {
                $this->_data[$key] = new self($value);
            } else {
                $this->_data[$key] = $value;
            }
        }
    }

    /**
     * Loads data from source code
     *
     * @param string $path
     * @return array | string
     */
    private function _loadData($path)
    {
        static $registry = array();
        if (isset ($registry[$path])) {
            return $registry[$path];
        }
        $data = array();        
        if (file_exists($path)) {
            ob_start();
            $data = include($path);
            ob_end_clean();
        }
        $registry[$path] = $data;
        return $registry[$path];
    }

    /**
     * Retrieves a value
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name = null)
    {
         if (is_null($name) || is_null($this->_data)) {
            return $this->_data;
        }
        $result = null;
        if (array_key_exists($name, $this->_data)) {
            $result = $this->_data[$name];
        }
        return $result;
    }

}