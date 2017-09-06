<?php
/// Source code file for LongCiteMasterTest unit testing class.
/// MIT License. See <https://opensource.org/licenses/MIT>.
/// @file
### Note: This file uses Uses doxygen style annotation comments.
### Note: This file possibly includes some PHPUnit comment directives.

require_once __DIR__ . "/LongCiteWikiStub.php";

use PHPUnit\Framework\Testcase;

/// Some LongCite phpunit tests.
class LongCiteMasterTest extends Testcase {

    /// Test various functions.
    public function testFunctions() {
        // initialize
        LongCiteMaster::clearActiveMaster();
        LongCiteWikiStub::initialize();
        $master = LongCiteMaster::getActiveMaster();
        // stub wiki init should have created a LongCiteMaster object.
        $this->assertInstanceOf(LongCiteMaster::class,$master);
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
