<?PHP
/*
* @package Thesaurus
* @author sheiko
* @copyright (c) Dmitry Sheiko http://dsheiko.com
*/

class Lib_Config
{
    private $_data = null;

    public function  __construct($dataSource)
    {
         if (is_null($dataSource)) {
            return $this;
        }
        if (is_string($dataSource)) {
            $dataSource = $this->_loadData($dataSource);
        }
        if (!is_array($dataSource)) {
            throw new Exception('Invalid data source');
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
     * Load data from source code
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
     * Retrieve a value and return $default if there is no element set.
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