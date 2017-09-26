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
        $inputArr = array();
        $inputArr[0]   = "schlüssel=DeMarco & Lister (1987)\n";
        $inputArr[1]   = "ding = Buch\n";
        $inputArr[2]   = "rensprache=de\n";
        $inputArr[3]   = "autoren=Tom DeMarco;Timothy Lister\n";
        $inputArr[4]   = "titel=Peopleware\n";
        $input         = implode("",$inputArr);
        $expMess  = "";
        $expOutArr = array();
        $expOutArr[0] = "DeMarco &amp; Lister (1987). ";
        $expOutArr[1] = '<span class="mw-longcite-pv-recogitem">';
        $expOutArr[2] = 'Ein Buch';
        $expOutArr[3] = '</span>.';
        $expOutArr[4] = ' Mit dem Titel ';
        $expOutArr[5] = '<i>&quot;Peopleware&quot;</i>.';
        $expOutArr[6] = ' Geschrieben von ';
        $expOutArr[7] = 'Tom _DeMarco_';
        $expOutArr[8] = '; ';
        $expOutArr[9] = 'Timothy _Lister_.';
        $expOut = implode("",$expOutArr);
        #
        $setup = array();
        $setup["master"]  = $master;
        $setup["args"]    = $args;
        $setup["input"]   = $input;
        $setup["expMess"] = $expMess;
        $setup["expOut"]  = $expOut;
        $this->helpRender($setup);
        # de to en
        $inputArr[2]    = "rensprache=en\n";
        $input          = implode("",$inputArr);
        $setup["input"] = $input;
        $expOutArr[2]   = 'A book';
        $expOutArr[4] = ' Entitled ';
        $expOutArr[6]   = ' Written by ';
        $expOutArr[8]   = '; ';
        $expOut = implode("",$expOutArr);
        $setup["expOut"] = $expOut;
        $this->helpRender($setup);
        # de to es
        $inputArr[2]    = "rensprache=es\n";
        $input          = implode("",$inputArr);
        $setup["input"] = $input;
        $expOutArr[2]   = 'Un libro';
        $expOutArr[4] = ' Titulado ';
        $expOutArr[6]   = ' Escrito por ';
        $expOutArr[8]   = '; ';
        $expOut = implode("",$expOutArr);
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
