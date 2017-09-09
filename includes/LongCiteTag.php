<?php
/// Source code file for the LongCiteTag:: class.
/// @copyright Copyright (c) 2017, Eric Alan Christiansen.\n
/// MIT License. See <https://opensource.org/licenses/MIT>.
/// @file
### Note: This file uses Uses doxygen style annotation comments.
### Note: This file possibly includes some PHPUnit comment directives.

/// Parent Class for other LongCite tag classes.
class LongCiteTag {
    const ParamCategoryLanguage    = "lang";
    const ParamCategoryControl     = "ctrl";
    const ParamCategoryCore        = "core";
    const ParamCategoryDescription = "desc";
    const ParamCategoryVerbose     = "verb";
    protected $master   = null;  ///< LongCiteMaster instance.
    protected $input    = null;  ///< Stuff between open and close tags.
    protected $args     = null;  ///< Settings within opening tag.
    protected $parser   = null;  ///< Mediawiki parser object.
    protected $frame    = null;  ///< MediaWiki template/recursive parsing structure.
    protected $inputLangCode  = "en";  ///< Language for understanding input source text.
    protected $outputLangCode = "en";  ///< Language for producing output text.
    protected $paramObjs   = array();  ///< Hash of paramNameMsgKey to param object.
    protected $messenger = null;       ///< LongCiteMessenger object.
    protected $paramMsgKeys = array();  ///< Hash cat->arr of msg keys.
    protected $renderedOutput = "";     ///< Copy of rendered output html.

    public function __construct($master, $input, $args, $parser, $frame=false) {
        $this->master = $master;
        $this->input   = $input;
        $this->args    = $args;
        $this->parser  = $parser;
        $this->frame   = $frame;
        $this->inputLangCode  = $master->getInputLangCode();
        $this->outputLangCode = $master->getOutputLangCode();
        $this->messenger = new LongCiteMessenger($this->outputLangCode);
        # set up param msg keys.
        $this->paramMsgKeys = array(
            self::ParamCategoryLanguage    => array(),
            self::ParamCategoryCore        => array(),
            self::ParamCategoryControl     => array(),
            self::ParamCategoryDescription => array(),
            self::ParamCategoryVerbose     => array()
        );
        $this->addParamMsgKeys(self::ParamCategoryLanguage,
            "longcite-pn-alwayslang");
        $this->addParamMsgKeys(self::ParamCategoryCore,
            array("longcite-pn-key"));
        $this->addParamMsgKeys(LongCiteTag::ParamCategoryDescription,
            array("longcite-pn-author"));
        $this->addParamMsgKeys(LongCiteTag::ParamCategoryDescription,
            array("longcite-pn-note"));
        # Add css module if not already added.
        $parserOutput = $this->parser->getOutput();
        $this->master->loadCssModule($parserOutput);
    }

    public function addParamMsgKeys($category,$msgKeys) {
        if(!is_array($msgKeys)) { $msgKeys = array($msgKeys); }
        $theMsgKeys = $this->paramMsgKeys[$category];
        foreach($msgKeys as $msgKey) {
            if(!in_array($msgKey,$theMsgKeys)) {
                $theMsgKeys[] = $msgKey;
            }
        }
        $this->paramMsgKeys[$category] = $theMsgKeys;
    }

    public function determineInputLanguage() {
        $parser   = $this->getParser();
        $frame    = $this->getFrame();
        $master   = $this->getMaster();
        $defLangCode = $master->getInputLangCode();
        $newLangCode = $defLangCode;
        $supportedLangCodes = $master->getSupportedLangCodes();
        // get list of msg keys for lang code param name, but there
        // should only be one entry on the list.
        $langMsgKeys = $this->getParamMsgKeys(self::ParamCategoryLanguage);
        if(count($langMsgKeys)>1) {
            trigger_error("Too many language message keys.",E_USER_WARNING);
            return false;
        } elseif(count($langMsgKeys)<1) {
            trigger_error("Missing language message key.",E_USER_WARNING);
            return false;
        }
        $langMsgKey = $langMsgKeys[0];
        $langParamName = $this->wikiMessage($langMsgKey)->plain();
        if(array_key_exists($langParamName,$this->args)) {
            $langCode = trim($this->args[$langParamName]);
            $langCode = $parser->recursiveTagParse($langCode,$frame);
            $langCode = strtolower($langCode);
            if(!in_array($langCode,$supportedLangCodes)) {
                $mess = $this->getMessenger();
                $msg = $this->wikiMessage("longcite-err-badlang",$langCode);
                $mess->registerMessageWarning($msg);
            } else {
                $newLangCode = $langCode;
            }
            unset($this->args[$langParamName]);
        }
        $langParamObj = $this->getParamByMsgKey($langMsgKey);
        $langParamObj->addValues($newLangCode);
        return $newLangCode;
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
        if(array_key_exists($paramNameKey,$this->paramObjs)) {
            return $this->paramObjs[$paramNameKey];
        }
        $validParamNameKeys = $this->getParamMsgKeys();
        if(!in_array($paramNameKey,$validParamNameKeys)) {
            $paramName  = $this->wikiMessage($paramNameKey)->plain();
            $markupName = $this->getTagMarkupName();
            $msg = $this->wikiMessage(longcite-err-badtagpar,$paramName,$markupName);
            $this->getMessenger()->registerMessageWarning($msg);
            return false;
        }
        $param = LongCiteParam::newParam($paramNameKey,$this);
        $this->paramObjs[$paramNameKey] = $param;
        return $param;
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
        $input = preg_replace( '/<!--(.|\s)*?-->/' , '' , $input);
        // convert /r/n to /n (if any)
        $input = str_replace("\r\n","\n",$input);
        // break up into rawlines
        $rawLines = explode("\n",$input);
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
            $tempLine = trim($tempLine);
            $lastchar = substr($tempLine,-1);
            if($lastchar==$contchar) {
                $tempLine = trim(substr($tempLine,0,-1));
            }
            if($continuing) {
                $lastidx = count($parLines) - 1;
                $parLines[$lastidx] = trim($parLines[$lastidx] . " " . $tempLine);
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
            $parts = explode("=",$parLine,2);
            if(count($parts)!=2) {
                $msg = $this->wikiMessage("longcite-err-cannotparse",$parLine)->plain();
                $this->getMessenger()->registerMessageWarning($msg);
                continue;
            }
            $parts[0] = trim($parts[0]);
            $parts[1] = trim($parts[1]);
            $arrOfArr[] = $parts;
        }
        return $arrOfArr;
    }

    public function render() {
        $this->renderPreperation();
        return $this->setRenderedOutput("");
    }

    public function renderedOutputAdd($html) {
        $this->renderedOutput .= $html;
        return $this->renderedOutput;
    }

    public function renderedOutputGet() {
        return $this->renderedOutput;
    }

    public function renderedOutputSet($html) {
        $this->renderedOutput = $html;
        return $this->renderedOutput;
    }

    public function renderPreperation() {
        $inLangCode = $this->determineInputLanguage();
        $cats = array(
            self::ParamCategoryControl,
            self::ParamCategoryCore,
            self::ParamCategoryDescription,
            self::ParamCategoryVerbose
        );
        $validParamMsgKeys = $this->getParamMsgKeys($cats);
        $inLangCode = $this->getInputLangCode();
        $tagMarkupName = $this->getTagMarkupName();
        $mess = $this->getMessenger();
        // build map from lang-specific param name to param name msg key.
        $paramMap = array();
        foreach($validParamMsgKeys as $paramMsgKey) {
            $paramName = wfMessage($paramMsgKey)->inLanguage($inLangCode)->plain();
            $paramName = strtolower($paramName);
            $paramMap[$paramName] = $paramMsgKey;
        }
        // process args from within opening tag
        foreach($this->args as $paramName => $paramVal) {
            if(!array_key_exists($paramName,$paramMap)) {
                $errKey = "longcite-err-badtagpar";
                $msg = $this->wikiMessage($errKey,$paramName,$tagMarkupName);
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
            $paramName = strtolower(trim($parts[0]));
            $paramVal  = trim($parts[1]);
            if(!array_key_exists($paramName,$paramMap)) {
                $errKey = "longcite-err-badtagpar";
                $msg = $this->wikiMessage($errKey,$paramName,$tagMarkupName);
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
            $msg = $this->wikiMessage("longcite-err-badlang",$code)->plain();
            $messenger->registerMessage(LongCiteMessenger::WarningType,$msg);
            return $this->getInputLangCode();
        }
        $this->inputLangCode = $code;
        return $code;
    }

    public function setOutputLangCode($code) {
        $supportedCodes = $this->master->getSupportedLangCodes();
        if(!in_array($code,$supportedCodes)) {
            $messenger = $this->messenger;
            $msg = $this->wikiMessage("longcite-err-badlang",$code)->plain();
            $messenger->registerMessage(LongCiteMessenger::WarningType,$msg);
            return $this->getOutputLangCode();
        }
        $this->messenger->setLangCode($code);
        $this->outputLangCode = $code;
        return $code;
    }

    public function setRenderedOutput($html) {
        $this->renderedOutput = $html;
    }

    public function wikiMessage($msgKey, ...$params) {
        $langCode = $this->getOutputLangCode();
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

}
?>
