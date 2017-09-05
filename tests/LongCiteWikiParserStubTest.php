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
        // initialize stub wiki
        LongCiteWikiStub::initialize();
        // get a stub parser object.
        $parser = new LongCiteWikiParserStub();
        $this->assertInstanceOf(LongCiteWikiParserStub::class,$parser);
        // set a test parser hook
        $callable = array($this,"sampleHookRoutine");
        $parser->setHook('sample',$callable);
        $callableName = "";
        is_callable($callable,false,$callableName);
        // invoke the test parser hook
        $something = "bork";
        $results = $parser->stubHookCaller("sample",array($something));
        $this->assertNotFalse($results);
        $this->assertTrue(array_key_exists($callableName,$results));
        $this->assertEquals($something,$results[$callableName]);
    }

    public function sampleHookRoutine($something) {
        return $something;
    }
}
?>
