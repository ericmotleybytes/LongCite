<?php
/// Source code file for LongCiteWikiLanguageStub class. This is a class
/// to mimic just a little of the MediaWiki Language class in order to facilitate
/// simple low level unit testing of MediaWiki targeted classes and functions.
/// MIT License. See <https://opensource.org/licenses/MIT>.
/// @file
### Note: This file uses Uses doxygen style annotation comments.
### Note: This file possibly includes some PHPUnit comment directives.

/// A stub class mimicking MediaWiki in order to facilitate low level unit testing.
class LongCiteWikiLanguageStub {

    protected $code = "en";  ///< iso language code.

    public function __construct() {
        $this->code = "en";
    }

    public function getCode() {
        return $this->code;
    }

    public function setCode($code) {
        $this->code = $code;
    }

    public static function stubNew($code="en") {
        $lang = new LongCiteWikiLanguageStub();
        $lang->setCode($code);
        return $lang;
    }

}
?>
