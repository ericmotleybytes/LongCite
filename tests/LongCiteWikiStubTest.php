<?php
/// Source code file for LongCiteWikiStubTest unit testing class.
/// MIT License. See <https://opensource.org/licenses/MIT>.
/// @file
### Note: This file uses Uses doxygen style annotation comments.
### Note: This file possibly includes some PHPUnit comment directives.

require_once __DIR__ . "/LongCiteWikiStub.php";

use PHPUnit\Framework\Testcase;

/// Some LongCite phpunit tests.
//  @backupGlobals enabled
class LongCiteWikiStubTest extends Testcase {

    /// Test various functions.
    public function testFunctions() {
        // initialize
        LongCiteMaster::clearActiveMaster();
        LongCiteWikiStub::initialize();
        $master = LongCiteMaster::getActiveMaster();
        // check global wfMessage function.
        $exp = "NOTE";
        $act = wfMessage("longcite-msgtyp-note")->inLanguage("en")->plain();
        $this->assertEquals($exp,$act);
        // check global wfGetLangObj function.
        $langObj = wfGetLangObj("en");
        $this->assertInstanceOf(LongCiteWikiLanguageStub::class,$langObj);
        // check some globals
        $this->assertEquals("en",$GLOBALS["wgLanguageCode"]);
    }

}
?>
