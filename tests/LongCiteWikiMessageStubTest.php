<?php
/// Source code file for LongCiteWikiMessageStubTest unit testing class.
/// MIT License. See <https://opensource.org/licenses/MIT>.
/// @file
### Note: This file uses Uses doxygen style annotation comments.
### Note: This file possibly includes some PHPUnit comment directives.

require_once __DIR__ . "/LongCiteWikiStub.php";

use PHPUnit\Framework\Testcase;

/// Some LongCite phpunit tests.
class LongCiteWikiMessageStubTest extends Testcase {

    /// Test various functions.
    public function testFunctions() {
        // initialize
        LongCiteMaster::clearActiveMaster();
        LongCiteWikiStub::initialize();
        $master = LongCiteMaster::getActiveMaster();
        // Get an existing key message object
        $msgKey = "longcite-note";
        $msgObj = new LongCiteWikiMessageStub($msgKey);
        $this->assertInstanceOf(LongCiteWikiMessageStub::class,$msgObj);
        $this->assertEquals($msgKey,$msgObj->getKey());
        $this->assertTrue($msgObj->exists());
        $this->assertEquals("en",$msgObj->getLanguage()->getCode());
        $this->assertEquals("NOTE",$msgObj->parse());
        $this->assertEquals("NOTE",$msgObj->plain());
        $this->assertEquals("NOTE",$msgObj->text());
        $this->assertEquals("NOTE",$msgObj->toString());
        $this->assertEquals("NOTE",$msgObj->stubLookupTranslation());
        $msgObj->inLanguage("de");
        $this->assertEquals("de",$msgObj->getLanguage()->getCode());
        $this->assertEquals("HINWEIS",$msgObj->parse());
        $this->assertEquals("HINWEIS",$msgObj->plain());
        $this->assertEquals("HINWEIS",$msgObj->text());
        $this->assertEquals("HINWEIS",$msgObj->toString());
        $this->assertEquals("HINWEIS",$msgObj->stubLookupTranslation());
        // Get a non-existing key message object
        $msgKey = "a-non-existing-message-key";
        $msgObj = new LongCiteWikiMessageStub($msgKey);
        $this->assertInstanceOf(LongCiteWikiMessageStub::class,$msgObj);
        $this->assertEquals($msgKey,$msgObj->getKey());
        $this->assertFalse($msgObj->exists());
        $this->assertEquals("?".$msgKey."?",$msgObj->plain());
        // Get a timestamp key with one param.
        $msgKey = "longcite-timestamp";
        $msgObj = new LongCiteWikiMessageStub($msgKey);
        $param = "2017-04-22 22:39:45";
        $msgObj->params($param);
        $params = $msgObj->getParams();
        $this->assertEquals(array($param),$params);
        $this->assertEquals("[TIMESTAMP=$param]",$msgObj->plain());
        // Try a compound
        $msgKey = "longcite-timestamp";
        $param = "2017-04-22 22:39:45";
        $exp = "[ZEITSTEMPEL=$param]";
        $str = $msgObj->inLanguage("de")->plain();
        $this->assertEquals($exp,$str);
        // try the global function
        $msgKey = "longcite-timestamp";
        $param = "2017-04-22 22:39:45";
        $exp = "[TIMESTAMP=$param]";
        $str = wfMessage($msgKey,$param)->plain();
        $this->assertEquals($exp,$str);
        $exp = "[ZEITSTEMPEL=$param]";
        $str = wfMessage($msgKey,$param)->inLanguage("de")->plain();
        $this->assertEquals($exp,$str);
    }

}
?>
