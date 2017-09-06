<?php
/// Source code file for the LongCiteParam:: class.
/// MIT License. See <https://opensource.org/licenses/MIT>.
/// @file
### Note: This file uses Uses doxygen style annotation comments.
### Note: This file possibly includes some PHPUnit comment directives.

/// Parent Class for other LongCite tag classes.
class LongCiteParam {

    const ParamClassPrefix = "LongCiteParam";
    const ParamModeLong    = "long";
    const ParamModeShort   = "short";

    protected static $paramClassMap = array(
        "author" => "PersonName",
        "key"    => "AlphaIdentifier"
    );

    protected static $paramDescMap = array(
        "author" => "The name of whoever wrote it.",
        "key"    => "The alphanumeric citation reference identifier."
    );

    public static function getParamClass($paramName) {
        if(!array_key_exists($paramName,self::$paramClassMap)) { return false; }
        $result = self::ParamClassPrefix . self::$paramClassMap[$paramName];
        return $result;
    }

    public static function getParamType($paramName) {
        if(!array_key_exists($paramName,self::$paramClassMap)) { return false; }
        $result = self::$paramClassMap[$paramName];
        return $result;
    }

    public static function getParamDescription($paramName) {
        if(!array_key_exists($paramName,self::$paramDescMap)) { return false; }
        $result = self::$paramDescMap[$paramName];
        return $result;
    }

    public static function newParam($paramName) {
        if(!array_key_exists($paramName,self::$paramClassMap)) {
            return false;
        }
        $paramClass = self::ParamClassPrefix . self::$paramClassMap[$paramName];
        $paramObj = new $paramClass($paramName);
        return $paramObj;
    }

    protected $paramName  = "";        ///< Name of the parameter in mark up.
    protected $isMulti    = false;     ///< Are multivalues allowed.
    protected $inputDelim = ";";       ///< Multi value delimiter (if needed).
    protected $outputDelimMsgKeys = array(); ///< Hash mode to msg key.
    protected $valuesRaw  = array();   ///< Raw value(s) as set.

    public function __construct($paramName) {
        $this->paramName = $paramName;
        $longMode = LongCiteParam::ParamModeLong;
        $shortMode = LongCiteParam::ParamModeShort;
        $this->setOutputDelimMsgKey($longMode,"longcite-and-delim");
        $this->setOutputDelimMsgKey($shortMode,"longcite-semi-delim");
    }

    // Add values to the parameter.
    // @param $values - Scalar string, but if multi may have multiple delimited values.
    public function addValues($values) {
        $values = trim($values);
        if($this->isMulti()) {
            $delim = $this->getInputDelim();
            $valueList = explode($delim,$values);
            foreach($valueList as $value) {
                $this->valuesRaw[] = $value;
            }
        } else {
            # not a multi param, overwrite any old value.
            $this->valuesRaw = array($values);
        }
    }

    public function getInputDelim() {
        if($this->isMulti===false) { return false; }
        return $this->inputDelim;
    }

    public function getName() {
        return $this->paramName;
    }

    public function getOutputDelim($mode=LongCiteParam::ParamModeLong) {
        $msgKey = $this->getOutputDelimMsgKey($mode);
        if($msgKey===false) { return false; }
        $delim = wfMessage($msgKey);
        return $delim;
    }

    public function getOutputDelimMsgKey($mode=LongCiteParam::ParamModeLong) {
        if($this->isMulti===false) { return false; }
        if(!array_key_exists($mode,$this->outputDelimMsgKeys)) {
            return false;
        }
        $msgKey = $this->outputDelimMsgKeys[$mode];
        return $msgKey;
    }

    public function getType() {
        $result = substr(get_class($this),strlen(self::ParamClassPrefix));
        return $result;
    }

    public function isMulti() {
        return $this->isMulti;
    }

    /// Set output delimiter message key for a mode.
    /// @mode - Should be long or short.
    /// @msgKey = The i18n message key.
    public function setOutputDelimMsgKey($mode,$msgKey) {
        $this->outputDelimMsgKeys[$mode] = $msgKey;
    }

}
?>
