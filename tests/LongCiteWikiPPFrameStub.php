<?php
/// Source code file for LongCiteWikiPPFrameStub class. This is a class
/// to mimic just a little of the MediaWiki ParserOutput class in order to facilitate
/// simple low level unit testing of MediaWiki targeted classes and functions.
/// MIT License. See <https://opensource.org/licenses/MIT>.
/// @file
### Note: This file uses Uses doxygen style annotation comments.
### Note: This file possibly includes some PHPUnit comment directives.

/// A stub class mimicking MediaWiki in order to facilitate low level unit testing.
class LongCiteWikiPPFrameStub {

    protected $args = array();  ///< Has array or arguments.

    public function __construct($args=array()) {
        $this->args = $args;
    }

    public function getArguments() {
        return $this->args;
    }

}
?>
