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
        #
        $args["lang"] = "de";
        $input1   = "schlüssel=Russell, B. (1948)\n";
        $input2   = "ding = Buch\n";
        $input3   = "rensprache=de\n";
        $input    = $input1 . $input2 . $input3;
        $expMess  = "";
        $expOut1  = "Russell, B. (1948). ";
        $expOut2  = '<span class="mw-longcite-pv-recogitem">';
        $expOut3  = 'Ein Buch';
        $expOut4  = '</span>.';
        $expOut = $expOut1 . $expOut2 . $expOut3 . $expOut4;
        #
        $setup = array();
        $setup["master"]  = $master;
        $setup["args"]    = $args;
        $setup["input"]   = $input;
        $setup["expMess"] = $expMess;
        $setup["expOut"]  = $expOut;
        $this->helpRender($setup);
        #
        $input3   = "rensprache=en\n";
        $input    = $input1 . $input2 . $input3;
        $setup["input"]   = $input;
        $expOut3  = 'A book';
        $expOut = $expOut1 . $expOut2 . $expOut3 . $expOut4;
        $setup["expOut"]  = $expOut;
        $this->helpRender($setup);
    }

    private function helpRender($setup) {
        $master    = $setup["master"];
        $parser    = $master->getParser();
        $frameArgs = array();
        $frame     = new LongCiteWikiPPFrameStub($frameArgs);
        $args      = $setup["args"];
        $input     = $setup["input"];
        $expMess   = $setup["expMess"];
        $expOut    = $setup["expOut"];
        // check out defined parser hooks
        $stuff = $parser->stubGetHooks();
        $this->assertGreaterThan(0,strlen($stuff));
        // determing how many tag objests before testing new tag
        $tags = $master->getTagObjects();
        $tagCntBefore = count($tags);
        // Simulate finding <longcitedef ...> by invoking parser callback.
        $callbackParams = array($input,$args,$parser,$frame);
        $results = $parser->stubCallHook("longcitedef",$input,$args,$frame);
        #LongCiteUtil::writeToTty("DBG: results=".print_r($results,true)."\n");
        $renOut = $results["LongCiteMaster::tagLongCiteDef"];
        $tags = $master->getTagObjects();
        $tagCntAfter = count($tags);
        $this->assertEquals(1,$tagCntAfter-$tagCntBefore);
        #$tag = array_pop($tags);
        #$tagMess = $tag->getMessenger();
        #$renOut  = $tag->render();
        $this->assertEquals($expOut,$renOut);
        #$msgHtml = $tagMess->renderMessagesHtml(true);
        #$this->assertEquals($expMess,$msgHtml);
    }

}
?>
