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
    protected $paramMap = array(); ///< Hash array of valid param names to types.

    public function __construct($master) {
        $this->master = $master;
        $this->paramMap = array();
    }

    public function render($input, $args, $parser, $frame) {
        $this->input   = $input;
        $this->args    = $args;
        $this->parser  = $parser;
        $this->frame   = $frame;
        # Add css module if not already added.
        $parserOutput = $parser->getOutput();
        $this->master->loadCssModule($parserOutput);
        return "";
    }

    public function getMaster() {
        return $this->master;
    }

    public function getInput() {
        return $this->input;
    }

    public function getArgs() {
        return $this->args;
    }

    public function getParser() {
        return $this->parser;
    }

    public function getFrame() {
        return $this->frame;
    }

    public function getTagName() {
        return get_class($this);
    }

    public function getParamMap() {
        return $this->paramMap;
    }
}
?>
