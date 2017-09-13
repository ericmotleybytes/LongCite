<?php
/// Source code file for LongCiteDefTagTest unit testing class.
/// MIT License. See <https://opensource.org/licenses/MIT>.
/// @file
### Note: This file uses Uses doxygen style annotation comments.
### Note: This file possibly includes some PHPUnit comment directives.

require_once __DIR__ . "/LongCiteWikiStub.php";

use PHPUnit\Framework\TestCase;

/// Some LongCite phpunit tests.
class LongCiteDefTagTest extends TestCase {

    /// Test functions.
    public function testFunctions() {
        // initialize
        LongCiteMaster::clearActiveMaster();
        LongCiteWikiStub::initialize();
        $master = LongCiteMaster::getActiveMaster();
        $parser = $master->getParser();
        // instantiate tag
        $tag = new LongCiteDefTag($master,"",array(),$parser,false);
        $this->assertInstanceOf(LongCiteDefTag::class,$tag);
        $this->assertEquals("LongCiteDefTag",$tag->getTagName());
    }

}
?>
