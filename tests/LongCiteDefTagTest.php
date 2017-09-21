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

    public function testRender() {
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
        // check out defined parser hooks
        $stuff = $parser->stubGetHooks();
        $this->assertGreaterThan(0,strlen($stuff));
        // Simulate finding <longcitedef ...> by invoking parser callback.
        $tags = $master->getTagObjects();
        $this->assertEquals(0,count($tags));
        $args["lang"] = "de";
        $input  = "schlüssel=Russell, B. (1948)\n";
        $input .= "ding = Buch\n";
        $input .= "rensprache=de";
        $callbackParams = array($input,$args,$parser,$frame);
        $parser->stubCallHook("longcitedef",$input,$args,$frame);
        $tags = $master->getTagObjects();
        $this->assertEquals(1,count($tags));
        $tag = $tags[0];
        $tagMess = $tag->getMessenger();
        $tagMess->setEnableDebug(true);  // true just for debugging
        $tagMess->setDoTrigger(true);    // true just for debugging
        $tagMess->renderMessagesToTty(); // only for debugging
        $this->assertEquals(0,$tagMess->getMessageCount());
        $renOut = $tag->render();
        $expOut = "Russell, B. (1948). Buch.";
        $this->assertEquals($expOut,$renOut);
    }

}
?>
