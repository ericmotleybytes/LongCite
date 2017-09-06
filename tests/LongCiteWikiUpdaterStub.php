<?php
/// Source code file for LongCiteWikiUpdaterStub class. This is a class
/// to mimic just a little of the MediaWiki Updater class in order to facilitate
/// simple low level unit testing of MediaWiki targeted classes and functions.
/// MIT License. See <https://opensource.org/licenses/MIT>.
/// @file
### Note: This file uses Uses doxygen style annotation comments.
### Note: This file possibly includes some PHPUnit comment directives.

/// A stub class mimicking MediaWiki in order to facilitate low level unit testing.
class LongCiteWikiUpdaterStub {

    protected $extensionUpdates = array();  ///< Extension sql info.

    public function __construct() {
        $this->extensionUpdates = array();
    }

    public function addExtensionTable($tableName,$sqlFile) {
        #if(!file_exists($sqlFile)) {
        #    trigger_error("File $sqlFile does not exist.",E_USER_WARNING);
        #}
        $this->extTables[$tableName] = $sqlFile;
        $this->extensionUpdates[] = array("addTable",$tableName,$sqlFile,true);
    }

    public function getExtensionUpdates() {
        return $this->extensionUpdates;
    }

}
?>
