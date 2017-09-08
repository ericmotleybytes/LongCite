<?php
/// Source code file for LongCiteWikiPPFrameStubTest unit testing class.
/// MIT License. See <https://opensource.org/licenses/MIT>.
/// @file
### Note: This file uses Uses doxygen style annotation comments.
### Note: This file possibly includes some PHPUnit comment directives.

require_once __DIR__ . "/LongCiteWikiStub.php";

use PHPUnit\Framework\Testcase;

/// Some LongCite phpunit tests.
class LongCiteWikiPPFrameStubTest extends Testcase {

    /// Test various functions.
    public function testFunctions() {
        // initialize
        LongCiteMaster::clearActiveMaster();
        LongCiteWikiStub::initialize();
        $master = LongCiteMaster::getActiveMaster();
        // get a stub ppframe object.
        $args  = array("Hello" => "World", 0 => "Zero" );
        $frame = new LongCiteWikiPPFrameStub($args);
        $this->assertInstanceOf(LongCiteWikiPPFrameStub::class,$frame);
        // get
        $args2 = $frame->getArguments();
        $this->assertEquals($args,$args2);
        $this->assertEquals(array("Hello"=>"World"),$frame->getNamedArguments());
        $this->assertEquals(array("Zero"),$frame->getNumberedArguments());
        $this->assertFalse($frame->isEmpty());
    }

}
?>
