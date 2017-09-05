<?php
/// Source code file for LongCiteWikiMessageStub class. This is a class
/// to help mimic wfMessage() functionality.
/// MIT License. See <https://opensource.org/licenses/MIT>.
/// @file
### Note: This file uses Uses doxygen style annotation comments.
### Note: This file possibly includes some PHPUnit comment directives.

/// A stub class to help mimick wfMessage in order to facilitate low level unit testing.
class LongCiteWikiMessageStub {

    protected $msgKey = "";       ///< Message key specified at instantiation.
    protected $params = array();  ///< Message parameters.
    protected $lang   = null;     ///< Current messaging language.

    public function __construct($msgKey, $params=array()) {
        $this->msgKey  = $msgKey;
        $this->params($params);
        $this->lang = $GLOBALS["wgLang"];  // use a string code for stub
    }

    public function exists() {
        $trans = $this->stubLookupTranslation();
        if($trans===false) {
            return false;
        }
        return true;
    }

    public function getKey() {
        return $this->msgKey;
    }

    public function getLanguage() {
        return $this->lang;
    }

    public function getParams() {
        return $this->params;
    }

    public function inContentLanguage() {
        global $wgLanguageCode;
        $this->lang = $wgLanguageCode;
        return $this;
    }

    public function inLanguage($lang) {
        $this->lang = $lang;
        return $this;
    }

    public function params(...$params) {
        foreach($params as $param) {
            if(is_array($param)) {
                foreach($param as $par) {
                    $this->params[] = $par;
                }
            } else {
                $this->params[] = $param;
            }
        }
        return $this;
    }

    public function parse() {
        $trans = $this->toString();
        $html = htmlentities($trans);
        return $html;
    }

    public function plain() {
        $trans = $this->toString();
        return $trans;
    }

    public function text() {
        $trans = $this->toString();
        return $trans;
    }

    public function toString() {
        $trans = $this->stubLookupTranslation();
        if($trans===false) {
            return "?" . $this->msgKey . "?";
        }
        return $trans;
    }

    public function stubLookupTranslation() {
        # this is inefficient, but we don't care for unit testing.
        $jsonFile = __DIR__ . "/../i18n/" . $this->lang . ".json";
        if(!file_exists($jsonFile)) { return false; }  // no translation file
        $jsonStr = file_get_contents($jsonFile);
        $jsonArr = json_decode($jsonStr,true);
        if(is_null($jsonArr)) {
            trigger_error("Could not json_decode $jsonFile.",E_USER_WARNING);
            return false;
        }
        if(!array_key_exists($this->msgKey,$jsonArr)) { return false; }
        $result = $jsonArr[$this->msgKey];
        $n = 0;
        foreach($this->params as $param) {
            $n += 1;
            $result = str_replace('$'.$n,$param,$result);
        }
        return $result;
    }
}
?>
