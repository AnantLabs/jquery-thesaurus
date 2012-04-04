<?PHP
/*
* @package Thesaurus
* @author sheiko 
* @copyright (c) Dmitry Sheiko http://dsheiko.com
*/

define('APPPATH', dirname(__FILE__) . "/app");
include APPPATH . "/Lib/Config.php";
include APPPATH . "/Lib/View.php";
include APPPATH . "/Lib/Mbstring.php";
include APPPATH . "/Lib/Storage.php";
include APPPATH . "/Lib/Dispatcher.php";

$storage = Lib_Storage::factory(new Lib_Config(APPPATH . '/Config/Config.php'));

$view = new Lib_View();
set_error_handler(array($view, "shutdownHandler"));

$dispatcher = new Lib_Dispatcher($storage, $view);
$dispatcher->dispatch($_REQUEST);
$view->render();