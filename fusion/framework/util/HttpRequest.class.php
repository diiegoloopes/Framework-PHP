<?php
class HttpRequest {
    private $_scriptUrl=null;
    private $_baseUrl=null;

    /**
     * Returns the relative URL for the application.
     * This is similar to {@link getScriptUrl scriptUrl} except that
     * it does not have the script file name, and the ending slashes are stripped off.
     * @param boolean $absolute whether to return an absolute URL. Defaults to false, meaning returning a relative one.
     * @return string the relative URL for the application
     * @see setScriptUrl
     * @copyright Yii's Framework
     */
    public function getBaseUrl($absolute=false)
    {
            if($this->_baseUrl==null)
                    $this->_baseUrl=rtrim(dirname($this->getScriptUrl()),'\\/');
            
            var_dump($this->_baseUrl);
            return $absolute ? $this->getHostInfo() . $this->_baseUrl : $this->_baseUrl;
    }

    
    /**
     * Returns the relative URL of the entry script.
     * The implementation of this method referenced Zend_Controller_Request_Http in Zend Framework.
     * @return string the relative URL of the entry script.
     * @copyright Yii's Framework
     */
    private function getScriptUrl(){
            if($this->_scriptUrl==null){
                    $scriptName=basename($_SERVER['SCRIPT_FILENAME']);
                    if(basename($_SERVER['SCRIPT_NAME'])===$scriptName)
                            $this->_scriptUrl=$_SERVER['SCRIPT_NAME'];
                    else if(basename($_SERVER['PHP_SELF'])===$scriptName)
                            $this->_scriptUrl=$_SERVER['PHP_SELF'];
                    else if(isset($_SERVER['ORIG_SCRIPT_NAME']) && basename($_SERVER['ORIG_SCRIPT_NAME'])===$scriptName)
                            $this->_scriptUrl=$_SERVER['ORIG_SCRIPT_NAME'];
                    else if(($pos=strpos($_SERVER['PHP_SELF'],'/'.$scriptName))!==false)
                            $this->_scriptUrl=substr($_SERVER['SCRIPT_NAME'],0,$pos).'/'.$scriptName;
                    else if(isset($_SERVER['DOCUMENT_ROOT']) && strpos($_SERVER['SCRIPT_FILENAME'],$_SERVER['DOCUMENT_ROOT'])===0)
                            $this->_scriptUrl=str_replace('\\','/',str_replace($_SERVER['DOCUMENT_ROOT'],'',$_SERVER['SCRIPT_FILENAME']));
            }
            return $this->_scriptUrl;
    }
}
?>