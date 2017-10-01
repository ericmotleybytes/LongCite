<?php
/// Source code file for the LongCiteTag:: class.
/// @copyright Copyright (c) 2017, Eric Alan Christiansen.\n
/// MIT License. See <https://opensource.org/licenses/MIT>.
/// @file
### Note: This file uses Uses doxygen style annotation comments.
### Note: This file possibly includes some PHPUnit comment directives.

/// Parent Class for other LongCite tag classes.
class LongCiteTag {
    const InputLangArgNames =
        "lang;" .
        "inlang;inputlang;inlanguage;inputlanguage;" .
        "insprache;einsprache;eingabesprache;" .
        "enlengua;entradalengua";
    const OutputLangArgNames =
        "outlang;outputlang;outlanguage;outputlanguage;" .
        "aussprache;ausgabesprache;" .
        "fueralengua;salidalengua";

    protected $master   = null;  ///< LongCiteMaster instance.
    protected $input    = null;  ///< Stuff between open and close tags.
    protected $adjInput = null;  ///< Adjusted and partially recursively parsed input lines.
    protected $args     = null;  ///< Settings within opening tag.
    protected $parser   = null;  ///< Mediawiki parser object.
    protected $frame    = null;  ///< MediaWiki template/recursive parsing structure.
    protected $inputLangCode  = "en";  ///< Language for understanding input source text.
    protected $outputLangCode = "en";  ///< Language for producing output text.
    protected $messenger = null;       ///< LongCiteMessenger object.
    protected $paramMsgKeys = array();  ///< Hash cat->arr of msg keys.
    protected $paramObjHash = array();  ///< Hash of paramNameMsgKey to param object.
    protected $renderedOutput = "";     ///< Copy of rendered output html.
    protected $guid;

    public function __construct($master, $input, $args, $parser, $frame=false) {
        $this->master = $master;
        $this->input   = $input;
        $this->args    = $args;
        $this->parser  = $parser;
        $this->frame   = $frame;
        // set internal guid
        $this->guid = LongCiteUtil::generateOpensslGuid();
        // more
        $this->inputLangCode  = $master->getInputLangCode();
        $this->outputLangCode = $master->getOutputLangCode();
        $this->messenger = new LongCiteMessenger($this->inputLangCode);
        $this->messenger->setEnableDebug(true);  # DBG DEBUG TBD!
        # set up param msg keys.
        $this->clearParamMsgKeys();
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

    /// Get adjusted input.
    public function adjustedInputGet() {
        return $this->adjInput;
    }

    /// Save adjusted input.
    public function adjustedInputSet($adjInput) {
        $this->adjInput = $adjInput;
    }

    /// Do tag preproessing before rendering.
    public function doPreprocessing() {
        # Add css module if not already added.
        $parserOutput = $this->parser->getOutput();
        $this->master->loadCssModule($parserOutput);
        # process the in tag arguments
        $this->doArgProcessing();
    }

    /// Process arguments withing the source tag.
    public function doArgProcessing() {
        $iLangNameArr = mb_split('\;',self::InputLangArgNames);
        $oLangNameArr = mb_split('\;',self::OutputLangArgNames);
        $supportedLangCodes = $this->getMaster()->getSupportedLangCodes();
        $result = true;
        foreach($this->args as $argName => $argVal) {
            $argName = mb_ereg_replace('[\_\ ]',"",$argName);
            $argName = mb_strtolower($argName);
            if(in_array($argName,$iLangNameArr)) {
                // found inlang arg
                $argVal = LongCiteUtil::eregTrim($argVal);
                $argVal = mb_strtolower($argVal);
                $stat = $this->setInputLangCode($argVal);
                if($stat===false) { $result = false; }
            } elseif(in_array($argName,$oLangNameArr)) {
                // found outlang arg
                $argVal = LongCiteUtil::eregTrim($argVal);
                $argVal = mb_strtolower($argVal);
                if($argVal=='*') {
                    $tgtLangCode = $this->parser->getTargetLanguage()->getCode();
                    if(in_array($tgtLangCode,$supportedLangCodes)) {
                        $stat = $this->setOutputLangCode($tgtLangCode);
                        if($stat===false) { $result = false; }
                    } else {
                        $result = false;
                    }
                } else {
                    $stat = $this->setOutputLangCode($argVal);
                    if($stat===false) { $result = false; }
                }
            } else {
                // unrecognized argument
                $mess = $this->messenger;
                $tagName = $this->getTagName();
                $msgObj = $this->wikiMessageIn("longcite-err-badtagarg",$argVal,$tagName);
                $msg = $msgObj->plain();
                $mess->registerMessageWarning($msg);
                $result = false;
            }
        }
        return $result;
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

    public function getArgs() {
        return $this->args;
    }

    public function getFrame() {
        return $this->frame;
    }

    public function getGuid() {
        return $this->guid;
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
        if(!is_object($param)) {
            $msg = "Could not instantiate param object for $paramNameKey.";
            trigger_error($msg,E_USER_WARNING);
            return false;
        }
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
        ##$savePreSaveTran = $this->parser->getOptions()->getPreSaveTransform();
        ##$this->parser->getOptions()->setPreSaveTransform(false);
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
            #$tempLine = $parser->recursiveTagParse($rawLine,$frame);
            $tempLine = $parser->recursivePreprocess($rawLine,$frame);
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
        // $arrOfArr is an array of arrays. There is one top level array entry for each
        // unfiltered input line. Each of these entries is an array where the [0] entry
        // is what is before the "=" character (the param name alias) and the [1] entry is
        // the values string (the stuff after the "=" character).
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
        // define param categories of interest
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
        // process parameters set on lines between opening and closing tags.
        // expand macros, weed out comments, merge continuation lines, etc.
        $semiParsedLines = $this->preprocessInput($this->input);
        $this->adjustedInputSet($semiParsedLines);
        // debug messages...
        ##trigger_error("try trigger error",E_USER_NOTICE);
        ##$raw = $GLOBALS["wgRawHtml"];
        ##if($raw===true) { $rawStr="t"; }
        ##elseif($raw===false) { $rawStr="f"; }
        ##else { $rawStr = "$raw"; }
        ##$dbgMsg  = "wgRawHtml=$rawStr.";
        ##$mess->registerMessageDebug($dbgMsg);
        ##$dbgMsg  = "input=";
        ##$dbgMsg .= $this->input;
        ##$mess->registerMessageDebug($dbgMsg);
        ##$dbgMsg  = "adjIn=";
        ##$dbgMsg .= implode("\n",$semiParsedLines);
        ##$mess->registerMessageDebug($dbgMsg);
        // break up into arrary of array of name and values string.
        $parsedArrOfArr  = $this->preprocessSemiParsedLines($semiParsedLines);
        // process each parameter, creating parameter objects and setting basic values.
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
            return false;
        }
        $this->messenger->setLangCode($code);
        $this->inputLangCode = $code;
        return true;
    }

    public function setOutputLangCode($code) {
        $supportedCodes = $this->master->getSupportedLangCodes();
        if(!in_array($code,$supportedCodes)) {
            $messenger = $this->messenger;
            $msg = $this->wikiMessageIn("longcite-err-badlang",$code)->plain();
            $messenger->registerMessage(LongCiteMessenger::WarningType,$msg);
            return false;
        }
        $this->outputLangCode = $code;
        return true;
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
