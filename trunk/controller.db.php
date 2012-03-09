<?PHP
/*
* Thesaurus Controller
*
* @package Thesaurus
* @author $Author: sheiko $
* @copyright (c) Dmitry Sheiko http://dsheiko.com
*/

define('LIBPATH', dirname(__FILE__) . "/thesaurus/");
try {
include LIBPATH . "controller.php";
include LIBPATH . "adapter/db.php";

$output = '';
$th = new Thesaurus_Controller(new Thesaurus_Adapter_Db(
   array(
        "host" => "127.0.0.1",
        "user" => "root",
        "password" => "",
        "dbname" => "thesaurus",
    )
), $output);
$th->dispatch($_REQUEST);

header("Cross-domain-access: true");
?>
$.callbackData = {
    status : 'ok',
    errorMsg : '',
    payload : <?PHP echo $output?>
};
<?PHP
} catch (Exception $e) {
?>
$.callbackData = {
    status : 'fail',
    errorMsg : '<?PHP echo $e->getMessage()?>',
    payload : null
};
<?PHP
}
