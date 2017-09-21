<?php
/// Source code file for the LongCiteParam:: class.
/// MIT License. See <https://opensource.org/licenses/MIT>.
/// @file
### Note: This file uses Uses doxygen style annotation comments.
### Note: This file possibly includes some PHPUnit comment directives.

/// Parent Class for other LongCite tag classes.
class LongCiteParam {

    const ParamClassPrefix  = "LongCiteParam";
    const ParamModeLong     = "long";
    const ParamModeShort    = "short";
    const ParamMsgKeyPrefix = "longcite-pn-";
    const CatLang = "language";
    const CatCtrl = "control";
    const CatCore = "core";
    const CatDesc = "description";
    const CatVerb = "verbose";

    protected static $paramClassMap = array(
        "longcite-pn-alwayslang" => array("LangCode",false,self::CatLang),
        "longcite-pn-author"     => array("PersonName",true,self::CatDesc),
        "longcite-pn-item"       => array("AlphaId",false,self::CatDesc),
        "longcite-pn-key"        => array("AlphaId",false,self::CatCore),
        "longcite-pn-note"       => array("Note",true,self::CatVerb),
        "longcite-pn-pubdate"    => array("Date",true,self::CatDesc),
        "longcite-pn-render"     => array("Boolean",false,self::CatCtrl),
        "longcite-pn-renlong"    => array("Boolean",false,self::CatCtrl),
        "longcite-pn-renlang"    => array("LangCode",false,self::CatCtrl),
        "longcite-pn-renctrl"    => array("Boolean",false,self::CatCtrl),
        "longcite-pn-rencore"    => array("Boolean",false,self::CatCtrl),
        "longcite-pn-rendesc"    => array("Boolean",false,self::CatCtrl),
        "longcite-pn-renverb"    => array("Boolean",false,self::CatCtrl),
        "longcite-pn-renskip"    => array("AlphaId",true,self::CatCtrl),
        "longcite-pn-renonly"    => array("AlphaId",true,self::CatCtrl)
    );

    public static function getAllCategories() {
        $validCats = array(self::CatLang, self::CatCtrl, self::CatCore,
            self::CatDesc, self::CatVerb);
        return $validCats;
    }

    /// Convert full or shortened param name msg keys to full msg keys.
    public static function getParamNameKeyLong($paramNameKey) {
        $prefix = self::ParamMsgKeyPrefix;
        $prefixLen = mb_strlen($prefix);
        if(mb_substr($paramNameKey,0,$prefixLen)==$prefix) {
            $result = $paramNameKey;
        } else {
            $result = $prefix . $paramNameKey;
        }
        return $result;
    }

    /// Convert full or shortened param name msg keys to full msg keys.
    public static function getParamNameKeyShort($paramNameKey) {
        $prefix = self::ParamMsgKeyPrefix;
        $prefixLen = mb_strlen($prefix);
        if(mb_substr($paramNameKey,0,$prefixLen)==$prefix) {
            $result = mb_substr($paramNameKey,$prefixLen);
        } else {
            $result = $paramNameKey;
        }
        return $result;
    }

    public static function getParamCategory($paramNameKey) {
        $paramNameKey = self::getParamNameKeyLong($paramNameKey);
        if(!array_key_exists($paramNameKey,self::$paramClassMap)) { return false; }
        $result = self::$paramClassMap[$paramNameKey][2];
        return $result;
    }

    public static function getParamClass($paramNameKey) {
        $paramNameKey = self::getParamNameKeyLong($paramNameKey);
        if(!array_key_exists($paramNameKey,self::$paramClassMap)) { return false; }
        $result = self::ParamClassPrefix . self::$paramClassMap[$paramNameKey][0];
        return $result;
    }

    public static function getParamMulti($paramNameKey) {
        $paramNameKey = self::getParamNameKeyLong($paramNameKey);
        if(!array_key_exists($paramNameKey,self::$paramClassMap)) { return null; }
        $result = self::ParamClassPrefix . self::$paramClassMap[$paramNameKey][1];
        return $result;
    }

    public static function getParamType($paramNameKey) {
        $paramNameKey = self::getParamNameKeyLong($paramNameKey);
        if(!array_key_exists($paramNameKey,self::$paramClassMap)) { return false; }
        $result = self::$paramClassMap[$paramNameKey][0];
        return $result;
    }

    public static function getParamDescKey($paramNameKey) {
        $paramNameKey = self::getParamNameKeyLong($paramNameKey);
        $result = str_replace("-pn-","-pd-",$paramNameKey);
        return $result;
    }

    public static function getParamDescription($paramNameKey,$langCode=null) {
        $paramNameKey = self::getParamNameKeyLong($paramNameKey);
        if(is_null($langCode)) {
            $master = LongCiteMaster::getActiveMaster();
            $langCode = $master->getOutputLangCode();
        }
        $descMsgKey = self::getParamDescKey($paramNameKey);
        $result = wfMessage($descMsgKey)->inLanguage($langCode)->plain();
        return $result;
    }

    public static function newParam($paramNameKey,$tag) {
        $paramNameKey = self::getParamNameKeyLong($paramNameKey);
        if(!array_key_exists($paramNameKey,self::$paramClassMap)) {
            return false;
        }
        $paramClass = self::ParamClassPrefix . self::$paramClassMap[$paramNameKey][0];
        $isMulti    = self::$paramClassMap[$paramNameKey][1];
        $category   = self::$paramClassMap[$paramNameKey][2];
        try {
            $result = new $paramClass($paramNameKey,$isMulti,$tag);
            $result->setCategory($category);
        } catch (Exception $e) {
            $result = false();
        }
        return $result;
    }

    protected $paramNameKey = "";      ///< i18n msg key.
    protected $tag        = null;      ///< LongCiteTag (or child) object.
    protected $isMulti    = false;     ///< Are multivalues allowed.
    protected $inputDelimMsgKey = "";  ///< Input delimiter msgKey (if needed).
    protected $outputDelimMsgKeys = array(); ///< Hash mode to msg key.
    protected $values     = array();   ///< Semi parsed values.
    protected $category   = null;      ///< Param category.
    protected $renderPrefixMsgKey = "longcite-pun-space";
    protected $renderSuffixMsgKey = "longcite-pun-period";

    public function __construct($paramNameKey, $isMulti, $tag) {
        $paramNameKey = self::getParamNameKeyLong($paramNameKey);
        if(!array_key_exists($paramNameKey,self::$paramClassMap)) {
            throw new LongCiteException("Unrecognized param msg key ($paramNameKey).");
        }
        $this->isMulti = $isMulti;
        if(!is_a($tag,"LongCiteTag")) {
            throw new LongCiteException("Tag is not a LongCiteTag.");
        }
        $this->tag = $tag;
        $this->paramNameKey = $paramNameKey;
        $this->setInputDelimMsgKey("longcite-delimi-semi");
        $longMode = LongCiteParam::ParamModeLong;
        $shortMode = LongCiteParam::ParamModeShort;
        $this->setOutputDelimMsgKey($longMode,"longcite-delimo-and");
        $this->setOutputDelimMsgKey($shortMode,"longcite-delimo-semi");
    }

    // Add values to the parameter.
    // @param $valuesStr - Scalar string, but if multi may have multiple delimited values.
    public function addValues($valuesStr) {
        $tag = $this->getTag();
        $parser = $tag->getParser();
        $frame  = $tag->getFrame();
        $valuesStr = trim($valuesStr);
        $valuesStr = $parser->recursiveTagParse($valuesStr,$frame);
        if($this->isMulti) {
            $inDelim = $this->getInputDelim();
        } else {
            $inDelim = null;
        }
        $vals = LongCiteUtil::parse($valuesStr,$inDelim);
        foreach($vals as $val) {
            if($this->isValueValid($val)) {
                if($this->isMulti()) {
                    $this->values[] = $val;
                } else {
                    $this->values = array($val);
                }
            } else {
                $mess = $this->getMessenger();
                $markupName = $this->getTag()->getTagMarkupName();
                $paramName = $this->getNames(true)[0];
                $msg = $this->wikiMessageIn(
                    "longcite-err-invalidval",$val,$paramName,$markupName
                );
                $msg = $msg->plain();
                $mess->registerMessageWarning($msg);
            }
        }
        return true;
    }


    public function getCategory() {
        return $this->category;
    }

    public function getFrame() {
        $frame = $this->getTag()->getFrame();
        return $frame;
    }


    public function getInputDelimMsgKey() {
        if($this->isMulti===false) { return false; }
        return $this->inputDelimMsgKey;
    }

    public function getInputDelim() {
        if($this->isMulti===false) { return false; }
        $msgKey = $this->getInputDelimMsgKey();
        if($msgKey===false) { return false; }
        $inLangCode = $this->getInputLangCode();
        $delim = wfMessage($msgKey)->inLanguage($inLangCode)->plain();
        return $delim;
    }

    public function getInputLangCode() {
        $code = $this->getTag()->getInputLangCode();
        return $code;
    }

    public function getMaster() {
        $master = $this->getTag()->getMaster();
        return $master;
    }

    public function getMessenger() {
        $mess = $this->getTag()->getMessenger();
        return $mess;
    }

    public function getNameKey() {
        return $this->paramNameKey;
    }

    public function getNames($usingInLang=true) {
        if($usingInLang) {
            $langCode = $this->tag->getInputLangCode();
        } else {
            $langCode = $this->tag->getOutputLangCode();
        }
        $msgKey = $this->paramNameKey;
        $namesStr = wfMessage($msgKey)->inLanguage($langCode)->plain();
        $names = mb_split('\;',$namesStr);
        return $names;
    }

    public function getOutputDelim($mode=LongCiteParam::ParamModeLong) {
        if($this->isMulti===false) { return false; }
        $outLangCode = $this->tag->getOutputLangCode();
        $msgKey = $this->getOutputDelimMsgKey($mode);
        if($msgKey===false) { return false; }
        $delim = wfMessage($msgKey)->inLanguage($outLangCode)->plain();
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

    public function getOutputLangCode() {
        $code = $this->getTag()->getOutputLangCode();
        return $code;
    }

    public function getParser() {
        $parser = $this->getTag()->getParser();
        return $parser;
    }

    public function getRenderPrefixMsgKey() {
        return $this->renderPrefixMsgKey;
    }

    public function getRenderSuffixMsgKey() {
        return $this->renderSuffixMsgKey;
    }

    public function getTag() {
        return $this->tag;
    }

    public function getType() {
        $result = substr(get_class($this),strlen(self::ParamClassPrefix));
        return $result;
    }

    public function getValues() {
        return $this->values;
    }

    public function isMulti() {
        return $this->isMulti;
    }

    public function isValueValid($value) {
        return is_string($value);
    }

    public function parseBooleanValue($valueStr,$default=null) {
        $valueStr = mb_strtolower(trim($valueStr));
        if($valueStr=="") { return $default; }
        $inLangCode = $this->getInputLangCode();
        $msgKeyMap = array(
            "longcite-pv-true"    => true,
            "longcite-pv-false"   => false,
            "longcite-pv-on"      => true,
            "longcite-pv-off"     => false,
            "longcite-pv-yes"     => true,
            "longcite-pv-no"      => false,
            "longcite-pv-numzero" => true,
            "longcite-pv-numone"  => false,
            "longcite-pv-null"    => $default,
            "longcite-pv-unknown" => $default,
            "longcite-pv-missing" => $default            
        );
        foreach($msgKeyMap as $msgKey => $val) {
            $trans = wfMessage($msgKey)->inLanguage($inLangCode)->plain();
            $trans = mb_strtolower(trim($trans));
            if($trans==$valueStr) {
                return $msgKeyMap[$msgKey];
            }
        }
        return null;
    }

    public function renderParam() {
        $values = $this->getValues();
        if(count($values)==0) { return false; }
        $tag = $this->getTag();
        $prefixMsgKey = $this->getRenderPrefixMsgKey();
        $prefix = $this->wikiMessageOut($prefixMsgKey)->plain();
        $tag->renderedOutputAdd($prefix,false);
        $delim  = $this->getOutputDelim();
        $stuff = implode($delim,$values);
        $tag->renderedOutputAdd($stuff,false);
        $suffixMsgKey = $this->getRenderSuffixMsgKey();
        $suffix = $this->wikiMessageOut($suffixMsgKey)->plain();
        $tag->renderedOutputAdd($suffix,false);
        return true;
    }

    public function setCategory($cat) {
        $validCats = self::getAllCategories();
        if(!in_array($cat,$validCats)) {
            trigger_error("Invalid param category ($cat).",E_USER_WARNING);
            return false;
        }
        $this->category = $cat;
    }

    public function setInputDelimMsgKey($delimMsgKey) {
        $this->inputDelimMsgKey = $delimMsgKey;
    }

    /// Set output delimiter message key for a mode.
    /// @mode - Should be long or short.
    /// @msgKey = The i18n message key.
    public function setOutputDelimMsgKey($mode,$msgKey) {
        $this->outputDelimMsgKeys[$mode] = $msgKey;
    }

    public function setRenderPrefixMsgKey($prefixMsgKey) {
        $this->renderPrefixMsgKey = $prefixMsgKey;
    }

    public function setRenderSuffix($suffixMsgKey) {
        $this->renderPrefixMsgKey = $suffixMsgKey;
    }

    private function wikiMessage($isInputLang=false,$msgKey, ...$params) {
        if($isInputLang) {
            $langCode = $this->getInputLangCode();
        } else {
            $langCode = $this->getOutputLangCode();
        }
        $msgObj = wfMessage($msgKey);
        $theParams = array();
        foreach($params as $param) {
            if(is_array($param)) {
                foreach($param as $par) {
                    $theParams[] = $par;
                }
            } else {
                $theParams[] = $param;
            }
        }
        if(count($theParams)>0) {
            $msgObj->params($theParams);
        }
        $msgObj = $msgObj->inLanguage($langCode);
        return $msgObj;
    }

    public Function wikiMessageIn($msgKey, ...$params) {
        return $this->wikiMessage(true,$msgKey,$params);
    }

    public Function wikiMessageOut($msgKey, ...$params) {
        return $this->wikiMessage(false,$msgKey,$params);
    }

}
?>
