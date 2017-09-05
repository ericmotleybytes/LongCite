<?php
/// Source code file for LongCiteWikiStubTest unit testing class.
/// MIT License. See <https://opensource.org/licenses/MIT>.
/// @file
### Note: This file uses Uses doxygen style annotation comments.
### Note: This file possibly includes some PHPUnit comment directives.

require_once __DIR__ . "/LongCiteWikiStub.php";

use PHPUnit\Framework\Testcase;

/// Some LongCite phpunit tests.
class LongCiteWikiStubTest extends Testcase {

    /// Test various checker functions.
    public function testInitialization() {
        // initialize stub wiki
        LongCiteWikiStub::initialize();
        // check some globals
        $this->assertEquals("en",$GLOBALS["wgLanguageCode"]);
    }

}
?>
