<?php
/// Source code file for LongCiteWikiLanguageStubTest unit testing class.
/// MIT License. See <https://opensource.org/licenses/MIT>.
/// @file
### Note: This file uses Uses doxygen style annotation comments.
### Note: This file possibly includes some PHPUnit comment directives.

require_once __DIR__ . "/LongCiteWikiStub.php";

use PHPUnit\Framework\TestCase;

/// Some LongCite phpunit tests.
class LongCiteWikiLanguageStubTest extends TestCase {

    /// Test various functions.
    public function testFunctions() {
        // initialize
        LongCiteMaster::clearActiveMaster();
        LongCiteWikiStub::initialize();
        $master = LongCiteMaster::getActiveMaster();
        // check global wfGetLangObj function.
        $langObj = wfGetLangObj("en");
        $this->assertInstanceOf(LongCiteWikiLanguageStub::class,$langObj);
        $this->assertEquals("en",$langObj->getCode());
        // check stubnew
        $langObj = LongCiteWikiLanguageStub::stubNew("en");
        $this->assertInstanceOf(LongCiteWikiLanguageStub::class,$langObj);
        $this->assertEquals("en",$langObj->getCode());
        // check direct instantiation
        $langObj = new LongCiteWikiLanguageStub();
        $this->assertInstanceOf(LongCiteWikiLanguageStub::class,$langObj);
        $this->assertEquals("en",$langObj->getCode());
        // check some functions
        $langObj->setCode("de");
        $this->assertEquals("de",$langObj->getCode());
    }

}
?>
