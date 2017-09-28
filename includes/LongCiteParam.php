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
    const AnnValBasic   = "basic";
    const AnnValIsValid = "isValid";
    const AnnValIsRecog = "isRecognized";
    const AnnValAsHtml  = "asHtml";
    const AnnValAsObj   = "asObject";

    protected static $paramClassMap = array(
        "longcite-pn-alwayslang" => array("LangCode",false,self::CatLang),
        "longcite-pn-author"     => array("Author",true,self::CatDesc),
        "longcite-pn-edition"    => array("Edition",true,self::CatDesc),
        "longcite-pn-item"       => array("Item",false,self::CatDesc),
        "longcite-pn-key"        => array("Key",false,self::CatCore),
        "longcite-pn-note"       => array("Note",true,self::CatVerb),
        "longcite-pn-pubdate"    => array("PubDate",true,self::CatDesc),
        "longcite-pn-publisher"  => array("Publisher",true,self::CatDesc),
        "longcite-pn-publoc"     => array("PubLoc",true,self::CatDesc),
        "longcite-pn-render"     => array("Boolean",false,self::CatCtrl),
        "longcite-pn-renlong"    => array("Boolean",false,self::CatCtrl),
        "longcite-pn-renlang"    => array("LangCode",false,self::CatCtrl),
        "longcite-pn-renctrl"    => array("Boolean",false,self::CatCtrl),
        "longcite-pn-rencore"    => array("Boolean",false,self::CatCtrl),
        "longcite-pn-rendesc"    => array("Boolean",false,self::CatCtrl),
        "longcite-pn-renverb"    => array("Boolean",false,self::CatCtrl),
        "longcite-pn-renskip"    => array("AlphaId",true,self::CatCtrl),
        "longcite-pn-renonly"    => array("AlphaId",true,self::CatCtrl),
        "longcite-pn-subtitle"   => array("Subtitle",false,self::CatDesc),
        "longcite-pn-title"      => array("Title",false,self::CatDesc)
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
    protected $annValues  = array();   ///< Annotated values, array of hash arrays.
    protected $category   = null;      ///< Param category.
    protected $renderPrefixMsgKey = "longcite-pun-space";
    protected $renderSuffixMsgKey = "longcite-pun-period";
    protected $paramOrder = 0;  ///< Param order within containing tag.

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
        #$this->setOutputDelimMsgKey($longMode,"longcite-delimo-and");
        $this->setOutputDelimMsgKey($longMode,"longcite-delimo-semi");
        $this->setOutputDelimMsgKey($shortMode,"longcite-delimo-semi");
        $this->category = self::getParamCategory($paramNameKey);
    }

    // Add values to the parameter.
    // @param $valuesStr - Scalar string, but if multi may have multiple delimited values.
    public function addValues($valuesStr,$okHtml=null) {
        $tag = $this->getTag();
        $parser = $tag->getParser();
        $frame  = $tag->getFrame();
        $result = true;
        $valuesStr = LongCiteUtil::eregTrim($valuesStr);
        $valuesStr = $parser->recursiveTagParse($valuesStr,$frame);
        if($okHtml===null) {
            $valuesStr = strip_tags($valuesStr);
        } else {
            $valuesStr = strip_tags($valuesStr,$okHtml);
        }
        if($this->isMulti) {
            $inDelim = $this->getInputDelim();
        } else {
            $inDelim = null;
        }
        $vals = LongCiteUtil::parseValuesStr($valuesStr,$inDelim);
        foreach($vals as $val) {
            $annValue = array();
            $annValue[self::AnnValBasic]   = $val;
            $annValue[self::AnnValIsRecog] = null;
            $annValue[self::AnnValAsHtml]  = null;
            $annValue[self::AnnValAsObj]   = null;
            $annValue[self::AnnValIsValid] = null;
            if($this->isValueValid($val)) {
                $annValue[self::AnnValIsValid] = true;
            } else {
                $annValue[self::AnnValIsValid] = false;
                $mess = $this->getMessenger();
                $markupName = $this->getTag()->getTagMarkupName();
                $paramName = $this->getNames(true)[0];
                $msg = $this->wikiMessageIn(
                    "longcite-err-invalidval",$val,$paramName,$markupName
                );
                $msg = $msg->plain();
                $mess->registerMessageWarning($msg);
                $result = false;
            }
            if($this->isMulti()) {
                $this->annValues[] = $annValue;
            } else {
                $this->annValues = array($annValue);
            }
        }
        return true;
    }

    public function getAnnotatedValues($evenInvalid=false) {
        $results = array();
        foreach($this->annValues as $annValue) {
            if($evenInvalid or $annValue[self::AnnValIsValid]) {
                $results[] = $annValue;
            }
        }
        return $results;
    }

    public function getBasicValues($evenInvalid=false) {
        $results = array();
        foreach($this->getAnnotatedValues($evenInvalid) as $annValue) {
            $results[] = $annValue[self::AnnValBasic];
        }
        return $results;
    }

    public function getValueObjects() {
        $results = array();
        foreach($this->annValues as $annValue) {
            $obj = $annValue[self::AnnValAsObj];
            if(is_object($obj)) {
                $results[] = $obj;
            }
        }
        return $results;
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

    public function getParamOrder() {
        return $this->paramOrder;
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

    public function isMulti() {
        return $this->isMulti;
    }

    public function isValueValid($value) {
        return is_string($value);
    }

    public function parseBooleanValue($valueStr,$default=null) {
        $valueStr = mb_strtolower(LongCiteUtil::eregTrim($valueStr));
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
            $trans = mb_strtolower(LongCiteUtil::eregTrim($trans));
            if($trans==$valueStr) {
                return $msgKeyMap[$msgKey];
            }
        }
        return null;
    }

    public function renderParam() {
        $tag = $this->getTag();
        $values = $this->getBasicValues();
        if(count($values)==0) { return false; }
        $this->renderParamPrefix();
        $this->renderParamValues();
        $this->renderParamSuffix();
        return true;
    }

    public function renderParamPrefix() {
        $tag = $this->getTag();
        $prefixMsgKey = $this->getRenderPrefixMsgKey();
        if(!is_array($prefixMsgKey)) {
            $prefixMsgKey = array($prefixMsgKey);
        }
        foreach($prefixMsgKey as $msgKey) {
            $prefix = $this->wikiMessageOut($msgKey)->plain();
            $tag->renderedOutputAdd($prefix,false);
        }
    }

    public function renderParamSuffix() {
        $tag = $this->getTag();
        $suffixMsgKey = $this->getRenderSuffixMsgKey();
        if(!is_array($suffixMsgKey)) {
            $suffixMsgKey = array($suffixMsgKey);
        }
        foreach($suffixMsgKey as $msgKey) {
            $suffix = $this->wikiMessageOut($msgKey)->plain();
            $tag->renderedOutputAdd($suffix,false);
        }
    }

    public function renderParamValues() {
        $tag = $this->getTag();
        $delim  = $this->getOutputDelim();
        $values = $this->getBasicValues();
        $stuff = implode($delim,$values);
        $tag->renderedOutputAdd($stuff,false);
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

    public function setParamOrder($order) {
        $this->paramOrder = $order;
    }

    public function setRenderPrefixMsgKey($prefixMsgKey) {
        $this->renderPrefixMsgKey = $prefixMsgKey;
    }

    public function setRenderSuffixMsgKey($suffixMsgKey) {
        $this->renderSuffixMsgKey = $suffixMsgKey;
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
