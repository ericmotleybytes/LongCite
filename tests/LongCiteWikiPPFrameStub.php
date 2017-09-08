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

    public function getArgument($name) {
        if(array_key_exists($name,$this->args)) {
            return $this->args[$name];
        } else {
            return null;
        }
    }

    public function getNamedArguments() {
        $result = array();
        foreach($this->args as $key => $val) {
            if(is_string($key)) {
                $result[$key] = $val;
            }
        }
        return $result;
    }

    public function getNumberedArguments() {
        $result = array();
        foreach($this->args as $key => $val) {
            if(is_int($key)) {
                $result[$key] = $val;
            }
        }
        return $result;
    }

    public function isEmpty() {
        if (count($this->args)==0) {
            return true;
        } else {
            return false;
        }
    }

}
?>
