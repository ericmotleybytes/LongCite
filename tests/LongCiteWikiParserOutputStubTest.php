<?php
/// Source code file for LongCiteWikiParserOutputStubTest unit testing class.
/// MIT License. See <https://opensource.org/licenses/MIT>.
/// @file
### Note: This file uses Uses doxygen style annotation comments.
### Note: This file possibly includes some PHPUnit comment directives.

require_once __DIR__ . "/LongCiteWikiStub.php";

use PHPUnit\Framework\Testcase;

/// Some LongCite phpunit tests.
class LongCiteWikiParserOutputStubTest extends Testcase {

    /// Test various functions.
    public function testFunctions() {
        // initialize
        LongCiteMaster::clearActiveMaster();
        LongCiteWikiStub::initialize();
        $master = LongCiteMaster::getActiveMaster();
        // get a stub output page object.
        $page = new LongCiteWikiParserOutputStub();
        $this->assertInstanceOf(LongCiteWikiParserOutputStub::class,$page);
        // add and get
        $module = "ext.longCite";
        $page->addModules($module);
        $exp = array($module);
        $modules = $page->stubGetModules();
        $this->assertEquals($exp,$modules);
    }

}
?>
