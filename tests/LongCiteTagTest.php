<?php
/// Source code file for LongCiteTagTest unit testing class.
/// MIT License. See <https://opensource.org/licenses/MIT>.
/// @file
### Note: This file uses Uses doxygen style annotation comments.
### Note: This file possibly includes some PHPUnit comment directives.

require_once __DIR__ . "/LongCiteWikiStub.php";

use PHPUnit\Framework\Testcase;

/// Some LongCite phpunit tests.
class LongCiteTagTest extends Testcase {

    /// Test functions.
    public function testFunctions() {
        // initialize
        LongCiteMaster::clearActiveMaster();
        LongCiteWikiStub::initialize();
        $master = LongCiteMaster::getActiveMaster();
        $parser = $master->getParser();
        $messenger = $master->getMessenger();
        $input = "";
        $args  = array();
        $frame = false;
        $frameArgs = array();
        $frame  = new LongCiteWikiPPFrameStub($frameArgs);
        // instantiate tag
        $tag = new LongCiteTag($master,$input,$args,$parser,$frame);
        $this->assertInstanceOf(LongCiteTag::class,$tag);
        $this->assertEquals($master,$tag->getMaster());
        $this->assertEquals($parser,$tag->getParser());
        $this->assertEquals($frame ,$tag->getFrame());
        $this->assertEquals($messenger,$tag->getMessenger());
        // test language control
        $tag->setInputLangCode("de");
        $this->assertEquals("de",$tag->getInputLangCode());
        $tag->setOutputLangCode("de");
        $this->assertEquals("de",$tag->getOutputLangCode());
        $tag->setInputLangCode("en");
        $this->assertEquals("en",$tag->getInputLangCode());
        $tag->setOutputLangCode("en");
        $this->assertEquals("en",$tag->getOutputLangCode());
        // test render
        $this->assertTrue($master->isCssLoaded());
        $html = $tag->render();
        $this->assertTrue($master->isCssLoaded());
        $this->assertEquals("",$html);
        $this->assertEquals($input,$tag->getInput());
        $this->assertEquals($args,$tag->getArgs());
        $this->assertEquals($parser,$tag->getParser());
        $this->assertEquals($frame,$tag->getFrame());
        $this->assertEquals("LongCiteTag",$tag->getTagName());
    }

}
?>
