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
        $input = "";
        $args  = array();
        $frame = false;
        $frameArgs = array();
        $frame  = new LongCiteWikiPPFrameStub($frameArgs);
        // instantiate tag
        $tag = new LongCiteTag($master,$input,$args,$parser,$frame);
        $messenger = $tag->getMessenger();
        $this->assertInstanceOf(LongCiteTag::class,$tag);
        $this->assertEquals($master,$tag->getMaster());
        $this->assertEquals($parser,$tag->getParser());
        $this->assertEquals($frame ,$tag->getFrame());
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
        // test preprocessInput
        $contchar = "\\";
        $inputLines  = "# Test lines...\n";
        $inputLines .= "one=1\n";
        $inputLines .= "     \n";
        $inputLines .= "two = $contchar\n";
        $inputLines .= "2\n";
        $inputLines .= "dummy\n";
        $inputLines .= "  three =  3\n";
        $expLines = array(
            "one=1",
            "two = 2",
            "dummy",
            "three =  3"
        );
        $actLines = $tag->preprocessInput($inputLines);
        $this->assertEquals($expLines,$actLines);
        $messenger->clearMessages();
        $actArr = $tag->preprocessSemiParsedLines($actLines);
        $expArr = array(
            array("one","1"),
            array("two","2"),
            array("three","3")
        );
        $this->assertEquals($expArr,$actArr);
        $this->assertEquals(1,$messenger->getMessageCount());
        $act = trim($messenger->renderMessagesText());
        $exp = "WARNING: Cannot parse (dummy).";
        $this->assertEquals($exp,$act);
        $messenger->clearMessages();
    }

}
?>
