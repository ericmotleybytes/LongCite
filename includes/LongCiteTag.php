<?php
/// Source code file for the LongCiteTag:: class.
/// @copyright Copyright (c) 2017, Eric Alan Christiansen.\n
/// MIT License. See <https://opensource.org/licenses/MIT>.
/// @file
### Note: This file uses Uses doxygen style annotation comments.
### Note: This file possibly includes some PHPUnit comment directives.

/// Parent Class for other LongCite tag classes.
class LongCiteTag {
    protected $master  = null;  ///< LongCiteMaster instance.
    protected $content = null;  ///< Stuff between open and close tags.
    protected $args    = null;  ///< Settings within opening tag.
    protected $frame   = null;  ///< MediaWiki template/recursive parsing structure.

    public function __construct($master) {
        $this->master = $master;
    }

    public function render($content, $args, $parser, $frame) {
        $this->content = $content;
        $this->args    = $args;
        $this->parser  = $parser;
        $this->frame   = $frame;
        return "<p>TBD</p>\n";
    }

    public function getContent() {
        return $this->content;
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

}
?>
