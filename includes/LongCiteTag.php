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
    protected $frame    = null;  ///< MediaWiki template/recursive parsing structure.
    protected $inputLangCode  = "en";
    protected $outputLangCode = "en";

    public function __construct($master, $input, $args, $parser, $frame=false) {
        $this->master = $master;
        $this->input   = $input;
        $this->args    = $args;
        $this->parser  = $parser;
        $this->frame   = $frame;
        $this->paramMap = array();
        $this->inputLangCode  = $master->getInputLangCode();
        $this->outputLangCode = $master->getOutputLangCode();
        # Add css module if not already added.
        $parserOutput = $this->parser->getOutput();
        $this->master->loadCssModule($parserOutput);
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
        return $this->getMaster()->getMessenger();
    }

    public function getOutputLangCode() {
        return $this->outputLangCode;
    }

    public function getParser() {
        return $this->parser;
    }

    public function getTagName() {
        return get_class($this);
    }

    public function newParam($paramNameKey) {
        $param = LongCiteParam::newParam($paramNameKey,$this);
        return $param;
    }

    public function render() {
        $this->getMaster()->renderTrace();
        return "";
    }

    public function setInputLangCode($code) {
        $supportedCodes = $this->master->getSupportedLangCodes();
        if(!in_array($code,$supportedCodes)) {
            $outCode = $this->getOutputLangCode();
            $messenger = $this->master->getMessenger();
            $msg = wfMessage("longcite-err-badlang",$code)->
                $msg->inLanguage($outCode)->plain();
            $messenger->registerMessage(LongCiteMessenger::WarningType,$msg);
            return $this->getInputLangCode();
        }
        $this->inputLangCode = $code;
        return $code;
    }

    public function setOutputLangCode($code) {
        $supportedCodes = $this->master->getSupportedLangCodes();
        if(!in_array($code,$supportedCodes)) {
            $outCode = $this->getOutputLangCode();
            $messenger = $this->master->getMessenger();
            $msg = wfMessage("longcite-err-badlang",$code)->
                $msg->inLanguage($outCode)->plain();
            $messenger->registerMessage(LongCiteMessenger::WarningType,$msg);
            return $this->getOutputLangCode();
        }
        $this->outputLangCode = $code;
        return $code;
    }

}
?>
