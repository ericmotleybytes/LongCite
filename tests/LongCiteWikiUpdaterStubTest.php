<?php
/// Source code file for LongCiteWikiUpdaterStubTest unit testing class.
/// MIT License. See <https://opensource.org/licenses/MIT>.
/// @file
### Note: This file uses Uses doxygen style annotation comments.
### Note: This file possibly includes some PHPUnit comment directives.

require_once __DIR__ . "/LongCiteWikiStub.php";

use PHPUnit\Framework\Testcase;

/// Some LongCite phpunit tests.
class LongCiteWikiUpdaterStubTest extends Testcase {

    /// Test various functions.
    public function testFunctions() {
        // initialize stub wiki
        LongCiteWikiStub::initialize();
        // get a stub updater object.
        $updater = new LongCiteWikiUpdaterStub();
        $this->assertInstanceOf(LongCiteWikiUpdaterStub::class,$updater);
        // get add and get
        $tbl = "dummy_tbl";
        $sqlFile = "dummyfile.sql";
        $updater->addExtensionTable($tbl,$sqlFile);
        $stuff = $updater->getExtensionUpdates();
        $this->assertEquals(1,count($stuff));
        $somestuff = $stuff[0];
        $this->assertEquals("addTable",$somestuff[0]);
        $this->assertEquals($tbl,$somestuff[1]);
        $this->assertEquals($sqlFile,$somestuff[2]);
        $this->assertEquals(true,$somestuff[3]);
    }

}
?>
