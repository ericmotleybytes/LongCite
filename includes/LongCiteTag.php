<?php
/// Source code file for the LongCiteTag:: class.
/// @copyright Copyright (c) 2017, Eric Alan Christiansen.\n
/// MIT License. See <https://opensource.org/licenses/MIT>.
/// @file
### Note: This file uses Uses doxygen style annotation comments.
### Note: This file possibly includes some PHPUnit comment directives.

/// Parent Class for other LongCite tag classes.
class LongCiteTag {
    protected $master   = null;  ///< LongCiteMaster instance.
    protected $input    = null;  ///< Stuff between open and close tags.
    protected $args     = null;  ///< Settings within opening tag.
    protected $parser   = null;  ///< Mediawiki parser object.
    protected $frame    = null;  ///< MediaWiki template/recursive parsing structure.
    protected $inputLangCode  = "en";  ///< Language for understanding input source text.
    protected $outputLangCode = "en";  ///< Language for producing output text.
    protected $messenger = null;       ///< LongCiteMessenger object.
    protected $paramMsgKeys = array();  ///< Hash cat->arr of msg keys.
    protected $paramObjHash = array();  ///< Hash of paramNameMsgKey to param object.
    protected $renderedOutput = "";     ///< Copy of rendered output html.
    protected $argsToSkip = array();    ///< Gets 'lang' after language selection.

    public function __construct($master, $input, $args, $parser, $frame=false) {
        $this->master = $master;
        $this->input   = $input;
        $this->args    = $args;
        $this->parser  = $parser;
        $this->frame   = $frame;
        $this->inputLangCode  = $master->getInputLangCode();
        $this->outputLangCode = $master->getOutputLangCode();
        $this->messenger = new LongCiteMessenger($this->inputLangCode);
        $this->messenger->setEnableDebug(true);  # DBG DEBUG TBD!
        $this->argsToSkip = array();
        # set up param msg keys.
        $this->clearParamMsgKeys();
        $this->addParamMsgKeys(
            "alwayslang","key","author","note"
        );
        # Add css module if not already added.
        $parserOutput = $this->parser->getOutput();
        $this->master->loadCssModule($parserOutput);
    }

    public function addParamMsgKeys(...$msgKeys) {
        $mess = $this->getMessenger();
        foreach($msgKeys as $msgKey) {
            $msgKey   = LongCiteParam::getParamNameKeyLong($msgKey);
            $category = LongCiteParam::getParamCategory($msgKey);
            if($category===false) {
                trigger_error("Bad param name key ($msgKey).",E_USER_ERROR);
                return false;
            }
            $curMsgKeys = $this->getParamMsgKeys($category);
            if(!in_array($msgKey,$curMsgKeys)) {
                $this->paramMsgKeys[$category][] = $msgKey;
            }
        }
        return true;
    }

    public function clearParamMsgKeys() {
        $this->paramMsgKeys = array(
            LongCiteParam::CatLang => array(),
            LongCiteParam::CatCore => array(),
            LongCiteParam::CatCtrl => array(),
            LongCiteParam::CatDesc => array(),
            LongCiteParam::CatVerb => array()
        );
    }

    public function determineInputLanguage() {
        $this->argsToSkip = array();
        $parser   = $this->getParser();
        $frame    = $this->getFrame();
        $master   = $this->getMaster();
        $defLangCode = $master->getInputLangCode();
        $newLangCode = $defLangCode;
        $supportedLangCodes = $master->getSupportedLangCodes();
        // get list of msg keys for lang code param name, but there
        // should only be one entry on the list.
        $langMsgKeys = $this->getParamMsgKeys(LongCiteParam::CatLang);
        if(count($langMsgKeys)>1) {
            trigger_error("Too many language message keys.",E_USER_WARNING);
            return false;
        } elseif(count($langMsgKeys)<1) {
            trigger_error("Missing language message key.",E_USER_WARNING);
            return false;
        }
        $langMsgKey = $langMsgKeys[0];
        $langParamName = $this->wikiMessageIn($langMsgKey)->plain();
        if(array_key_exists($langParamName,$this->args)) {
            $langCode = LongCiteUtil::eregTrim($this->args[$langParamName]);
            $langCode = $parser->recursiveTagParse($langCode,$frame);
            $langCode = strtolower($langCode);
            if(!in_array($langCode,$supportedLangCodes)) {
                $mess = $this->getMessenger();
                $msg = $this->wikiMessageIn("longcite-err-badlang",$langCode);
                $mess->registerMessageWarning($msg);
            } else {
                $newLangCode = $langCode;
            }
            //unset($this->args[$langParamName]);
            $this->argsToSkip[] = "$langParamName";
        }
        $langParamObj = $this->getParamByMsgKey($langMsgKey);
        $langParamObj->addValues($newLangCode);
        $this->setInputLangCode($newLangCode);
        return $newLangCode;
    }

    public function getArgs() {
        return $this->args;
    }

    public function getFrame() {
        return $this->frame;
    }

    public function getInput() {
        return $this->input;
    }

    public function getInputLangCode() {
        return $this->inputLangCode;
    }

    public function getMaster() {
        return $this->master;
    }

    public function getMessenger() {
        return $this->messenger;
    }

    public function getOutputLangCode() {
        return $this->outputLangCode;
    }

    public function getParamByMsgKey($paramNameKey) {
        if(array_key_exists($paramNameKey,$this->paramObjHash)) {
            $result = $this->paramObjHash[$paramNameKey];
            $phpClass = get_class($result);
            return $result;
        }
        $validParamNameKeys = $this->getParamMsgKeys();
        if(!in_array($paramNameKey,$validParamNameKeys)) {
            $paramName  = $this->wikiMessageIn($paramNameKey)->plain();
            $markupName = $this->getTagMarkupName();
            $msg = $this->wikiMessageIn("longcite-err-badtagpar",$paramName,$markupName);
            $msg = $msg->plain();
            $this->getMessenger()->registerMessageWarning($msg);
            return false;
        }
        $param = LongCiteParam::newParam($paramNameKey,$this);
        $phpClass = get_class($param);
        $this->paramObjHash[$paramNameKey] = $param;
        $param->setParamOrder(count($this->paramObjHash));
        return $param;
    }

    public function getParamCategories() {
        return(array_keys($this->paramMsgKeys));
    }

    public function getParamMsgKeys($category=null) {
        if(is_null($category)) {
            $result = array();
            foreach($this->paramMsgKeys as $category => $msgKeys) {
                foreach($msgKeys as $msgKey) {
                    if(!in_array($msgKey,$result)) {
                        $result[] = $msgKey;
                    }
                }
            }
        } elseif(is_array($category)) {
            $result = array();
            foreach($category as $cat) {
                $catMsgKeys = $this->paramMsgKeys[$cat];
                foreach($catMsgKeys as $msgKey) {
                    if(!in_array($msgKey,$result)) {
                        $result[] = $msgKey;
                    }
                }
            }
        } else {
            $result = $this->paramMsgKeys[$category];
        }
        return $result;
    }

    public function getParamNameKey($paramName) {
        $paramName = LongCiteUtil::eregTrim(mb_strtolower($paramName));
        foreach($this->getParamMsgKeys() as $paramNameKey) {
            $paramNamesStr = $this->wikiMessageIn($paramNameKey)->plain();
            $paramNamesStr = LongCiteUtil::eregTrim(mb_strtolower($paramNamesStr));
            $paramNames = mb_split('\;',$paramNamesStr);
            foreach($paramNames as $parName) {
                $parName = LongCiteUtil::eregTrim(mb_strtolower($parName));
                if($paramName==$parName) {
                    return $paramNameKey;
                }
            }
        }
        return false;
    }

    public function getParamObjectHash() {
        return $this->paramObjHash;
    }

    public function getParser() {
        return $this->parser;
    }

    public function getTagMarkupName() {
        $markupName = get_class($this);
        $markupName = substr($markupName,0,strlen($markupName)-3);
        $markupName = strtolower($markupName);
        return $markupName;
    }

    public function getTagName() {
        return get_class($this);
    }

    public function newParam($paramNameKey) {
        $param = LongCiteParam::newParam($paramNameKey,$this);
        return $param;
    }

    public function preprocessInput($input) {
        $contchar = "\\";  # if at end of line it marks a continuation.
        // remove html comments from content
        $input = preg_replace( '/<!--(.|\s)*?-->/u' , '' , $input);
        // convert /r/n to /n (if any)
        $input = mb_ereg_replace('\r\n','\n',$input);
        // break up into rawlines
        $rawLines = mb_split('\n',$input);
        // recursive parse each raw line.
        $tempLines = array();
        $parser = $this->getParser();
        $frame = $this->getFrame();
        foreach($rawLines as $rawLine) {
            $tempLine = $parser->recursiveTagParse($rawLine,$frame);
            $tempLines[] = $tempLine;
        }
        // merge into parsable lines
        $parLines = array();
        $continuing = false;
        foreach($tempLines as $tempLine) {
            $tempLine = LongCiteUtil::eregTrim($tempLine);
            $lastchar = substr($tempLine,-1);
            if($lastchar==$contchar) {
                $tempLine = LongCiteUtil::eregTrim(substr($tempLine,0,-1));
            }
            if($continuing) {
                $lastidx = count($parLines) - 1;
                $parLines[$lastidx] = LongCiteUtil::eregTrim($parLines[$lastidx] . " " . $tempLine);
            } else {
                $parLines[] = $tempLine;
            }
            if($lastchar==$contchar) {
                $continuing = true;
            } else {
                $continuing = false;
            }
        }
        // weed out blank lines and comments
        $result = array();
        foreach($parLines as $parLine) {
            if(strlen($parLine)==0) { continue; }
            if(substr($parLine,0,1)=="#") { continue; }
            $result[] = $parLine;
        }
        return $result;
    }

    public function preprocessSemiParsedLines($parLines) {
        $arrOfArr = array(); // an array of arrays
        foreach($parLines as $parLine) {
            $parts = mb_split('\=',$parLine,2);
            if(count($parts)!=2) {
                $msg = $this->wikiMessageIn("longcite-err-cannotparse",$parLine)->plain();
                $this->getMessenger()->registerMessageWarning($msg);
                continue;
            }
            $parts[0] = LongCiteUtil::eregTrim($parts[0]);
            $parts[1] = LongCiteUtil::eregTrim($parts[1]);
            # remove surrounding doublequotes if needed
            if(substr($parts[1],0,1)=='"') {
                if(substr($parts[1],-1)=='"') {
                    $parts[1] = substr($parts[1],1,-1);
                }
            }
            # remove surrounding singlequotes if needed
            if(substr($parts[1],0,1)=="'") {
                if(substr($parts[1],-1)=="'") {
                    $parts[1] = substr($parts[1],1,-1);
                }
            }
            $arrOfArr[] = $parts;
        }
        return $arrOfArr;
    }

    public function render() {
        $this->renderPreperation();
        $this->renderedOutputAdd($this->getMessenger()->renderMessagesHtml(true),true);
        $result = LongCiteUtil::eregTrim($this->renderedOutputGet());
        return $result;
    }

    public function renderedOutputAdd($text,$isHtml=false) {
        if($isHtml) {
            $html = $text;
        } else {
            $html = htmlspecialchars($text);
        }
        $this->renderedOutput .= $html;
        return $this->renderedOutput;
    }

    public function renderedOutputAppend($text,$isHtml=false) {
        if($isHtml) {
            $html = $text;
        } else {
            $html = htmlspecialchars($text);
        }
        $this->renderedOutput = $this->renderedOutput . $html;
        return $this->renderedOutput;
    }

    public function renderedOutputGet() {
        return $this->renderedOutput;
    }

    public function renderedOutputPrepend($text,$isHtml=false) {
        if($isHtml) {
            $html = $text;
        } else {
            $html = htmlspecialchars($text);
        }
        $this->renderedOutput = $html . $this->renderedOutput;
        return $this->renderedOutput;
    }

    public function renderedOutputSet($text,$isHtml=false) {
        if($isHtml) {
            $html = $text;
        } else {
            $html = htmlspecialchars($text);
        }
        $this->renderedOutput = $html;
        return $this->renderedOutput;
    }

    public function renderedOutputTrim() {
        $this->renderedOutput = LongCiteUtil::eregTrim($this->renderedOutput);
    }

    public function renderPreperation() {
        // Determine the input language from the in tag always "lang" parameter.
        $inLangCode = $this->determineInputLanguage();
        $cats = array(
            LongCiteParam::CatCtrl,
            LongCiteParam::CatCore,
            LongCiteParam::CatDesc,
            LongCiteParam::CatVerb
        );
        $validParamMsgKeys = $this->getParamMsgKeys($cats);
        $mess = $this->getMessenger();
        $inLangCode = $this->getInputLangCode();
        $tagMarkupName = $this->getTagMarkupName();
        // Set the default output language to be the input language (can be
        // changed with the renlang param).
        $this->setOutputLangCode($inLangCode);
        // build map from lang-specific param name to param name msg key.
        $paramMap = array();
        foreach($validParamMsgKeys as $paramMsgKey) {
            $paramNamesStr = wfMessage($paramMsgKey)->inLanguage($inLangCode)->plain();
            $paramNamesStr = strtolower($paramNamesStr);
            $paramNames = mb_split('\;',$paramNamesStr);
            foreach($paramNames as $paramName) {
                $paramMap[$paramName] = $paramMsgKey;
            }
        }
        // save inputs from args from within opening tag
        foreach($this->args as $paramName => $paramVal) {
            if(in_array($paramName,$this->argsToSkip)) {
                continue;  // Skip the 'lang' key already processed.
            }
            if(!array_key_exists($paramName,$paramMap)) {
                $errKey = "longcite-err-badtagpar";
                $msg = $this->wikiMessageIn($errKey,$paramName,$tagMarkupName)->plain();
                $mess->registerMessageWarning($msg);
                continue;
            }
            $paramNameKey = $paramMap[$paramName];
            $paramObj = $this->getParamByMsgKey($paramNameKey);
            $paramObj->addValues($paramVal);
        }
        // process parameters set on lines between opening and closing tags.
        $semiParsedLines = $this->preprocessInput($this->input);
        $parsedArrOfArr  = $this->preprocessSemiParsedLines($semiParsedLines);
        foreach($parsedArrOfArr as $parts) {
            $paramName = strtolower(LongCiteUtil::eregTrim($parts[0]));
            $paramVal  = LongCiteUtil::eregTrim($parts[1]);
            if(!array_key_exists($paramName,$paramMap)) {
                $errKey = "longcite-err-badtagpar";
                $msg = $this->wikiMessageIn($errKey,$paramName,$tagMarkupName)->plain();
                $mess->registerMessageWarning($msg);
                continue;
            }
            $paramNameKey = $paramMap[$paramName];
            $paramObj = $this->getParamByMsgKey($paramNameKey);
            $paramObj->addValues($paramVal);
        }
    }

    public function setInputLangCode($code) {
        $supportedCodes = $this->master->getSupportedLangCodes();
        if(!in_array($code,$supportedCodes)) {
            $messenger = $this->messenger;
            $msg = $this->wikiMessageIn("longcite-err-badlang",$code)->plain();
            $messenger->registerMessage(LongCiteMessenger::WarningType,$msg);
            return $this->getInputLangCode();
        }
        $this->messenger->setLangCode($code);
        $this->inputLangCode = $code;
        return $code;
    }

    public function setOutputLangCode($code) {
        $supportedCodes = $this->master->getSupportedLangCodes();
        if(!in_array($code,$supportedCodes)) {
            $messenger = $this->messenger;
            $msg = $this->wikiMessageIn("longcite-err-badlang",$code)->plain();
            $messenger->registerMessage(LongCiteMessenger::WarningType,$msg);
            return $this->getOutputLangCode();
        }
        $this->outputLangCode = $code;
        return $code;
    }

    public function setRenderedOutput($html) {
        $this->renderedOutput = $html;
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
