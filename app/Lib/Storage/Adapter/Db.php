<?PHP
/*
* @package Thesaurus
* @author sheiko
* @copyright (c) Dmitry Sheiko http://dsheiko.com
*/

include_once dirname(__FILE__) . "/Interface.php";

class Lib_Storage_Adapter_Db implements Lib_Storage_Adapter_Interface
{
    /**
     *
     * @param string $file CSV data file name
     */
    public function  __construct(Lib_Config $config) 
    {        
        try {
            mysql_connect($config->dataSource->host, $config->dataSource->user, $config->dataSource->password);
            mysql_select_db($config->dataSource->dbname);
            mysql_query("set character_set_results=utf8");
            mysql_query("set character_set_client=utf8");
            mysql_query("set character_set_connection=utf8");
        } catch (Exception $e) {
            throw new Lib_Storage_Exception('Cannot connect DB');
        }
    }

    /**
     * Increments click stats for the term
     * 
     * @param string $term
     * @return void
     */
    public function incrementClickStat($term) 
    {
        mysql_query(sprintf("UPDATE thesaurus SET clicked = clicked + 1 WHERE term LIKE '%s'", 
            mysql_real_escape_string($term)));
    }
    /**
     * Increment view stats of each term of given associative array
     * @param array $stats 
     */
    public function commitViewStat(array $stats) 
    {
        foreach ($stats as $term => $views) {
            mysql_query(sprintf("UPDATE thesaurus SET visited = visited + %d " 
                . " WHERE term LIKE '%s'", $views, mysql_real_escape_string($term)));
        }
    }    

    /**
     * Get glossary
     *
     * @return array
     */
    public function getData() 
    {
        $glossary = array();
        $result = mysql_query("SELECT term FROM thesaurus");
        while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $glossary[$row['term']] = 1;
        }
        return $glossary;
    }
    /**
     *
     * @param string $term
     * @param boolean $caseSentitive
     * @return string
     */
    public function findDefinition($term, $caseSentitive) 
    {
        if (empty ($term)) {
            throw new Lib_Storage_Exception('Term must not be empty');
        }
        $condition = $caseSentitive ? "term = '%s'" : "term LIKE '%s'";
        $result = mysql_query(sprintf("SELECT term, description FROM thesaurus WHERE " . $condition, 
            mysql_real_escape_string($term)));
        $fetch = mysql_fetch_array($result, MYSQL_ASSOC);
	return $fetch['description'];
    }
}
