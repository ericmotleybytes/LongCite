<?php
/// Source code file for LongCiteWikiOutputPageStub class. This is a class
/// to mimic just a little of the MediaWiki OutputPage class in order to facilitate
/// simple low level unit testing of MediaWiki targeted classes and functions.
/// MIT License. See <https://opensource.org/licenses/MIT>.
/// @file
### Note: This file uses Uses doxygen style annotation comments.
### Note: This file possibly includes some PHPUnit comment directives.

/// A stub class mimicking MediaWiki in order to facilitate low level unit testing.
class LongCiteWikiOutputPageStub {

    protected $addedModules = array();  ///< Modules added.
    protected $context = null;

    public function __construct($context=null) {
        $this->context = $context;
        $this->addedModules = array();
    }

    public function addModules($modules) {
        if(is_array($modules)) {
            foreach($modules as $module) {
                $this->addedModules[] = $module;
            }
        } else {
            $this->addedModules[] = $modules;
        }
    }

    public function stubGetModules() {
        return $this->addedModules;
    }

}
?>
