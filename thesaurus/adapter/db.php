<?PHP
/*
* Thesaurus CSV adapter
*
* @package Thesaurus
* @author $Author: sheiko $
* @copyright (c) Dmitry Sheiko http://dsheiko.com
*/
include_once LIBPATH . "adapter/interface.php";

class Thesaurus_Adapter_Db implements Thesaurus_Adapter_Interface
{
    /**
     *
     * @param string $file CSV data file name
     */
    public function  __construct($dbConfig) {
        try {
            mysql_connect($dbConfig["host"], $dbConfig["user"], $dbConfig["password"]);
            mysql_select_db($dbConfig["dbname"]);
            mysql_query("set character_set_results=utf8");
            mysql_query("set character_set_client=utf8");
            mysql_query("set character_set_connection=utf8");
        } catch (Exception $e) {
            throw new Exception('Cannot connect DB ');
        }
    }

    /**
     * Increments click stats for the term
     * 
     * @param string $term
     * @return void
     */
    public function incrementClickStat($term) {
        mysql_query("UPDATE thesaurus SET clicked=clicked+1 WHERE term LIKE '{$term}'");
    }

    /**
     * Get glossary
     *
     * @return array
     */
    public function getData() {
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
    public function findDefinition($term, $caseSentitive) {
        if($caseSentitive) {
            $result = mysql_query("SELECT term, description FROM thesaurus WHERE term='{$term}'");
            mysql_query("UPDATE thesaurus SET visited=visited+1 WHERE term='{$term}'");
        } else {
            $result = mysql_query("SELECT term, description FROM thesaurus WHERE term LIKE '{$term}'");
            mysql_query("UPDATE thesaurus SET visited=visited+1 WHERE term LIKE '{$term}'");
        }
        $fetch = mysql_fetch_array($result, MYSQL_ASSOC);
	return $fetch['description'];
    }
}
