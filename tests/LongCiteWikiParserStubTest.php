<?php
/// Source code file for LongCiteWikiParserStubTest unit testing class.
/// MIT License. See <https://opensource.org/licenses/MIT>.
/// @file
### Note: This file uses Uses doxygen style annotation comments.
### Note: This file possibly includes some PHPUnit comment directives.

require_once __DIR__ . "/LongCiteWikiStub.php";

use PHPUnit\Framework\Testcase;

/// Some LongCite phpunit tests.
class LongCiteWikiParserStubTest extends Testcase {

    /// Test various functions.
    public function testFunctions() {
        // initialize
        LongCiteMaster::clearActiveMaster();
        LongCiteWikiStub::initialize();
        $master = LongCiteMaster::getActiveMaster();
        // get a stub parser object.
        $parser = new LongCiteWikiParserStub();
        $this->assertInstanceOf(LongCiteWikiParserStub::class,$parser);
        // check parse substitutions
        $subs = array("Hello"=>"World",1=>"One");
        $frame = new LongCiteWikiPPFrameStub($subs);
        $raw = 'I like the {{{Hello}}} of {{{1}}}.';
        $exp = "I like the World of One.";
        $act1 = $parser->recursiveTagParse($raw);
        $act2 = $parser->recursiveTagParse($raw,$frame);
        $this->assertEquals($raw,$act1);
        $this->assertEquals($exp,$act2);
        // ask for its ParserOutput object.
        $parserOutput = $parser->getOutput();
        $this->assertInstanceOf(LongCiteWikiParserOutputStub::class,$parserOutput);
        // set a test parser hook
        $callable = array($this,"sampleHookRoutine");
        $parser->setHook('sample',$callable);
        $callableName = "";
        is_callable($callable,false,$callableName);
        // invoke the test parser hook
        $something = "bork";
        $results = $parser->stubCallHook("sample","",array(),$parser,false);
        $this->assertNotFalse($results);
        $this->assertTrue(array_key_exists($callableName,$results));
        $this->assertEquals($something,$results[$callableName]);
    }

    public function sampleHookRoutine($input,$args,$parser,$frame) {
        return "bork";
    }
}
?>
