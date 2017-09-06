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
        "longcite-pn-alwayslang" => "LangCode",
        "longcite-pn-author"     => "PersonName",
        "longcite-pn-key"        => "AlphaId"
    );

    public static function getParamClass($paramNameKey) {
        if(!array_key_exists($paramNameKey,self::$paramClassMap)) { return false; }
        $result = self::ParamClassPrefix . self::$paramClassMap[$paramNameKey];
        return $result;
    }

    public static function getParamType($paramNameKey) {
        if(!array_key_exists($paramNameKey,self::$paramClassMap)) { return false; }
        $result = self::$paramClassMap[$paramNameKey];
        return $result;
    }

    public static function getParamDescKey($paramNameKey) {
        $result = str_replace("-pn-","-pd-",$paramNameKey);
        return $result;
    }

    public static function getParamDescription($paramNameKey) {
        $descMsgKey = self::getParamDescKey($paramNameKey);
        $result = wfMessage($descMsgKey)->plain();
        return $result;
    }

    public static function newParam($paramNameKey,$tag) {
        if(!array_key_exists($paramNameKey,self::$paramClassMap)) {
            return false;
        }
        $paramClass = self::ParamClassPrefix . self::$paramClassMap[$paramNameKey];
        try {
            $result = new $paramClass($paramNameKey,$tag);
        } catch (Exception $e) {
            $result = false();
        }
        return $result;
    }

    protected $paramNameKey = "";      ///< i18n msg key (except for "lang").
    protected $tag        = null;      ///< LongCiteTag (or child) object.
    protected $isMulti    = false;     ///< Are multivalues allowed.
    protected $inputDelim = ";";       ///< Multi value delimiter (if needed).
    protected $outputDelimMsgKeys = array(); ///< Hash mode to msg key.
    protected $rawValues  = array();   ///< Raw value(s) as set.

    public function __construct($paramNameKey, $tag) {
        if(!array_key_exists($paramNameKey,self::$paramClassMap)) {
            throw new LongCiteException("Unrecognized param msg key ($paramNameKey).");
        }
        if(!is_a($tag,"LongCiteTag")) {
            throw new LongCiteException("Tag is not a LongCiteTag.");
        }
        $this->tag = $tag;
        $this->paramNameKey = $paramNameKey;
        $longMode = LongCiteParam::ParamModeLong;
        $shortMode = LongCiteParam::ParamModeShort;
        $this->setOutputDelimMsgKey($longMode,"longcite-and-delim");
        $this->setOutputDelimMsgKey($shortMode,"longcite-semi-delim");
    }

    // Add values to the parameter.
    // @param $values - Scalar string, but if multi may have multiple delimited values.
    public function addRawValues($values) {
        $values = trim($values);
        if($this->isMulti()) {
            $delim = $this->getInputDelim();
            $valueList = explode($delim,$values);
            foreach($valueList as $value) {
                $this->rawValues[] = $value;
            }
        } else {
            # not a multi param, overwrite any old value.
            $this->rawValues = array($values);
        }
        return true;
    }

    public function getInputDelim() {
        if($this->isMulti===false) { return false; }
        return $this->inputDelim;
    }

    public function getNameKey() {
        return $this->paramNameKey;
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

    public function getRawValues() {
        return $this->rawValues;
    }

    public function getTag() {
        return $this->tag;
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
