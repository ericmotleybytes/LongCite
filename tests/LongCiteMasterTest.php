<?php
/// Source code file for LongCiteMasterTest unit testing class.
/// MIT License. See <https://opensource.org/licenses/MIT>.
/// @file
### Note: This file uses Uses doxygen style annotation comments.
### Note: This file possibly includes some PHPUnit comment directives.

require_once __DIR__ . "/LongCiteWikiStub.php";

use PHPUnit\Framework\TestCase;

/// Some LongCite phpunit tests.
class LongCiteMasterTest extends TestCase {

    /// Test various functions.
    public function testFunctions() {
        // initialize
        LongCiteMaster::clearActiveMaster();
        LongCiteWikiStub::initialize();
        $master = LongCiteMaster::getActiveMaster();
        // stub wiki init should have created a LongCiteMaster object.
        $this->assertInstanceOf(LongCiteMaster::class,$master);
        // check language
        $langCodes = $master->getSupportedLangCodes();
        $this->assertTrue(in_array("en",$langCodes));
        $this->assertTrue(in_array("de",$langCodes));
        $this->assertFalse(in_array("zz",$langCodes));
        $this->assertEquals("en",$master->getOutputLangCode());
        $this->assertEquals("en",$master->getInputLangCode());
        $outCode = $master->setOutputLangCode("de");
        $this->assertEquals("de",$outCode);
        $this->assertEquals("de",$master->getOutputLangCode());
        $outCode = $master->setOutputLangCode("en");
        // css should not initially be loaded
        $this->assertFalse($master->isCssLoaded());
        $master->setCssLoaded(true);
        $this->assertTrue($master->isCssLoaded());
        $master->setCssLoaded(false);
        $this->assertFalse($master->isCssLoaded());
        $parserOutput = new LongCiteWikiParserOutputStub();
        $master->loadCssModule($parserOutput);
        $this->assertTrue($master->isCssLoaded());
    }

}
?>
