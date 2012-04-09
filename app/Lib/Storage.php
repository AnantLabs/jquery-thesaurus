<?PHP
/*
* Thesaurus Lib
*
* @package Thesaurus
* @author $Author: sheiko $
* @copyright (c) Dmitry Sheiko http://dsheiko.com
*/

include_once dirname(__FILE__) . "/Storage/Exception.php";

/**
 * Factory to obtain adapter instance
 */
class Lib_Storage 
{
    /**
     *
     * @param Lib_Config $config
     * @return Lib_Storage_Adapter_Interface
     * @throws Lib_Storage_Exception 
     */
    public static function factory(Lib_Config $config)
    {
        $adapter = $config->dataSource->driver;
        $classLoc = sprintf("%s/Lib/Storage/Adapter/%s.php", APPPATH, ucfirst($adapter));
        $className = sprintf("Lib_Storage_Adapter_%s", ucfirst($adapter));

        if (!file_exists($classLoc)) {
            throw new Lib_Storage_Exception(sprintf("Cannot find '%s' adapter file", $adapter));
        }
        include_once $classLoc;
        if (!class_exists($className)) {
            throw new Lib_Storage_Exception(sprintf("Cannot find '%s' adapter class", $adapter));
        }
        return new $className($config);
    }
    
}