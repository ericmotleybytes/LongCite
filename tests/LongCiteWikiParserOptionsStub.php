<?php
/// Source code file for LongCiteWikiParserOptionsStub class. This is a class
/// to mimic just a little of the MediaWiki ParserOutput class in order to facilitate
/// simple low level unit testing of MediaWiki targeted classes and functions.
/// MIT License. See <https://opensource.org/licenses/MIT>.
/// @file
### Note: This file uses Uses doxygen style annotation comments.
### Note: This file possibly includes some PHPUnit comment directives.

/// A stub class mimicking MediaWiki in order to facilitate low level unit testing.
class LongCiteWikiParserOptionsStub {

    protected $user        = null;      // user object
    protected $userLang    = null;      // language object
    protected $extLinkTgt  = "_blank";  // external link target
    protected $preSaveTran = true;      // presave transform

    public function __construct($user=null, $lang=null) {
        $this->user     = $user;
        $this->userLang = $lang;
    }

    public function getUser() {
        return $this->user;
    }

    public function getUserLangObj() {
        return $this->userLang;
    }

    public function getUserLang() {
        return $this->userLang->getCode();
    }

    public function stubGetModules() {
        return $this->addedModules;
    }

    public function getExternalLinkTarget() {
        return $this->extLinkTgt;
    }

    public function setExternalLinkTarget($tgt) {
        $result = $this->getExternalLinkTarget();
        $this->extLinkTgt = $tgt;
        return $result;
    }

    public function getPreSaveTransform() {
        return $this->preSaveTran;
    }

    public function setPreSaveTransform($flag) {
        $result = $this->preSaveTran;
        $this->preSaveTran = $flag;
        return $result;
    }

}
?>
