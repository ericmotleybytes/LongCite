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
        // initialize stub wiki
        LongCiteWikiStub::initialize();
        // stub wiki init should have created a LongCiteMaster object.
        $this->assertTrue(array_key_exists("wgLongCiteMasterInstance",$GLOBALS));
        $master = $GLOBALS["wgLongCiteMasterInstance"];
        $this->assertInstanceOf(LongCiteMaster::class,$master);
    }

}
?>
