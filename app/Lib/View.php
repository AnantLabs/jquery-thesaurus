<?PHP
/*
* @package Thesaurus
* @author sheiko
* @copyright (c) Dmitry Sheiko http://dsheiko.com
*/

class Lib_View
{
    const OUTPUT_TPL = '
    $.callbackData = {
        status : \'%s\',
        errorMsg : \'%s\',
        payload : %s
    };';
    const FAIL = "fail";
    const OK = "ok";
    
    public $data = null;
    /**
     * Applied headers
     */
    public function setHeaders()
    {
        header("Content-type: text/html; charset=UTF-8");
        header("Cross-domain-access: true");
    }
    /**
     * Outputs view 
     */
    public function render()
    {
        $this->setHeaders();
        printf(self::OUTPUT_TPL, self::OK, null, $this->data ? $this->data : 0);
    }
    /**
     * Wraps error message into JSON when fatal error  happens
     */
    function shutdownHandler()
    {
        $this->setHeaders();
        $err = error_get_last();
        if ($err && $err['type'] != E_DEPRECATED && $err['type'] != E_WARNING && $err['type'] != E_NOTICE) {
            printf(self::OUTPUT_TPL, self::FAIL, $err['message'], null);
        }

    }
}