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
        $args["inputlang"]  = "de";
        $args["outputlang"] = "de";
        $inputArr = array();
        $inputArr[0]   = "#schlüssel=DeMarco & Lister (1987/2013)\n";
        $inputArr[1]   = "ding = Buch\n";
        $inputArr[2]   = "#rensprache=de\n";
        $inputArr[3]   = "autoren=Tom DeMarco;Timothy Lister\n";
        $inputArr[4]   = "titel=Peopleware\n";
        $inputArr[5]   = "veröffdatum=1987;2013\n";
        $inputArr[6]   = "untertitel=Productive Projects and Teams\n";
        $inputArr[7]   = "verlag=Addison-Wesley\n";
        $inputArr[8]   = "verlagsort=Upper Saddle River, New Jersey, USA\n";
        $inputArr[9]   = "edition=Anniversary\n";
        $inputArr[10]  = "url=http://www.google.com/|google.com|Search engine.|2017\n";
        $input         = implode("",$inputArr);
        $expMess  = "";
        $expOutArr     = array();
        $expOutArr[0]  = '<p class="mw-longcite-refdef-hang">';
        $expOutArr[0] .= '<b>DeMarco_T; Lister_T (1987/2013)</b>. ';
        $expOutArr[1]  = '<span class="mw-longcite-pv-recogitem">';
        $expOutArr[2]  = 'Ein Buch';
        $expOutArr[3]  = '</span>.';
        $expOutArr[4]  = ' Mit dem Titel ';
        $expOutArr[5]  = '<i>&quot;Peopleware&quot;</i>.';
        $expOutArr[6]  = ' Mit dem Untertitel ';
        $expOutArr[7]  = '<i>&quot;Productive Projects and Teams&quot;</i>.';
        $expOutArr[8]  = ' Geschrieben von ';
        $expOutArr[9]  = 'Tom DeMarco';
        $expOutArr[10] = '; ';
        $expOutArr[11] = 'Timothy Lister.';
        $expOutArr[12] = ' Datum der Veröffentlichung ';
        $expOutArr[13] = '1987; 2013.';
        $expOutArr[14] = ' Anniversary';
        $expOutArr[15] = ' Edition.';
        $expOutArr[16] = ' Veröffentlicht von ';
        $expOutArr[17] = 'Addison-Wesley.';
        $expOutArr[18] = ' Veröffentlicht am ';
        $expOutArr[19] = 'Upper Saddle River, New Jersey, USA.';
        $expOutArr[20] = ' Online bei ';
        $expOutArr[21] = '<a href="http://www.google.com/">google.com</a> (Search engine.';
        $expOutArr[22] = ' Abgerufen ';
        $expOutArr[23] = '2017.).';
        $expOutArr[24] = '</p>';
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
        $master->existingCitationKeysReset();
        $args["outputlang"] = "en";
        $inputArr[2]    = "#rensprache=en\n";
        $input          = implode("",$inputArr);
        $setup["input"] = $input;
        $expOutArr[2]   = 'A book';
        $expOutArr[4]   = ' Entitled ';
        $expOutArr[6]   = ' Subtitled ';
        $expOutArr[8]   = ' Written by ';
        $expOutArr[12]  = ' Publication date ';
        $expOutArr[15] = ' edition.';
        $expOutArr[16]  = ' Published by ';
        $expOutArr[18]  = ' Publisher location ';
        $expOutArr[20]  = ' Online at ';
        $expOutArr[22] = ' Retrieved ';
        $expOut = implode("",$expOutArr);
        $setup["expOut"] = $expOut;
        $setup["args"]    = $args;
        $this->helpRender($setup);
        # de to es
        $master->existingCitationKeysReset();
        $args["outputlang"] = "es";
        $inputArr[2]    = "#rensprache=es\n";
        $input          = implode("",$inputArr);
        $setup["input"] = $input;
        $expOutArr[2]   = 'Un libro';
        $expOutArr[4]   = ' Titulado ';
        $expOutArr[6]   = ' Subtitulado ';
        $expOutArr[8]   = ' Escrito por ';
        $expOutArr[12]  = ' Fecha de publicación ';
        $expOutArr[15] = ' edición.';
        $expOutArr[16]  = ' Publicado por ';
        $expOutArr[18]  = ' Publicado en ';
        $expOutArr[20]  = ' En línea en ';
        $expOutArr[22] = ' Descargado en ';
        $expOut = implode("",$expOutArr);
        $setup["expOut"]  = $expOut;
        $setup["args"]    = $args;
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
