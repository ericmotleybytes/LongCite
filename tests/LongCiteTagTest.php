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
        // instantiate tag
        $tag = new LongCiteTag($master);
        $this->assertInstanceOf(LongCiteTag::class,$tag);
        $master = $tag->getMaster();
        $this->assertInstanceOf(LongCiteMaster::class,$master);
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
        $frameArgs = array();
        $tagArgs   = array();
        $tagInput  = "";
        $parser = new LongCiteWikiParserStub();
        $frame  = new LongCiteWikiPPFrameStub($frameArgs);
        $this->assertFalse($master->isCssLoaded());
        $html = $tag->render($tagInput,$tagArgs,$parser,$frame);
        $this->assertTrue($master->isCssLoaded());
        $this->assertEquals("",$html);
        $this->assertEquals($tagInput,$tag->getInput());
        $this->assertEquals($tagArgs,$tag->getArgs());
        $this->assertEquals($parser,$tag->getParser());
        $this->assertEquals($frame,$tag->getFrame());
        $this->assertEquals("LongCiteTag",$tag->getTagName());
    }

}
?>
