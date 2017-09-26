<?php
/// Source code file for LongCiteParamAuthorTest unit testing class.
/// MIT License. See <https://opensource.org/licenses/MIT>.
/// @file
### Note: This file uses Uses doxygen style annotation comments.
### Note: This file possibly includes some PHPUnit comment directives.

require_once __DIR__ . "/LongCiteWikiStub.php";

use PHPUnit\Framework\TestCase;

/// Some LongCite phpunit tests.
class LongCiteParamAuthorTest extends TestCase {

    /// Test functions.
    public function testFunctions() {
        // initialize
        LongCiteMaster::clearActiveMaster();
        LongCiteWikiStub::initialize();
        $master = LongCiteMaster::getActiveMaster();
        $parser = $master->getParser();
        $frame  = new LongCiteWikiPPFrameStub(array());
        // instantiate param
        $tag = new LongCiteTag($master,"",array(),$parser,$frame);
        $pnameKey = "longcite-pn-author";
        $param = new LongCiteParamAuthor($pnameKey,true,$tag);
        $this->assertInstanceOf(LongCiteParamAuthor::class,$param);
        $param = LongCiteParam::newParam($pnameKey,$tag);
        $this->assertInstanceOf(LongCiteParamAuthor::class,$param);
        $param = $tag->newParam($pnameKey);
        $this->assertInstanceOf(LongCiteParamAuthor::class,$param);
        $tag2 = $param->getTag();
        $this->assertInstanceOf(LongCiteTag::class,$tag2);
        # info
        $this->assertTrue($param->isMulti());
        $nameMsgKey = $param->getNameKey();
        $this->assertEquals($pnameKey,$nameMsgKey);
        $names = $param->getNames();
        $this->assertEquals("author",$names[0]);
        $type = $param->getType();
        $this->assertEquals("Author",$type);
        $master = $param->getMaster();
        $this->assertInstanceOf(LongCiteMaster::class,$master);
        $this->assertEquals("en",$param->getInputLangCode());
        $this->assertEquals("en",$param->getOutputLangCode());
        # delims
        $this->assertEquals("longcite-delimi-semi",$param->getInputDelimMsgKey());
        $this->assertEquals(";",$param->getInputDelim());
        $msgKey1 = $param->getOutputDelimMsgKey("long");
        $msgKey2 = $param->getOutputDelimMsgKey("short");
        #$this->assertEquals("longcite-delimo-and",$msgKey1);
        $this->assertEquals("longcite-delimo-semi",$msgKey1);
        $this->assertEquals("longcite-delimo-semi",$msgKey2);
        #$this->assertEquals(" and ",$param->getOutputDelim("long"));
        $this->assertEquals("; ",$param->getOutputDelim("long"));
        $this->assertEquals("; ",$param->getOutputDelim("short"));
        # values
        $name1 = "Bob Smith";
        $name2 = "Susan T. Jones";
        $param->addValues("$name1; $name2");
        $annValues = $param->getAnnotatedValues();
        $this->assertEquals(2,count($annValues),"Annotated values count.");
        $basName1 = $annValues[0][LongCiteParam::AnnValBasic];
        $this->assertEquals($name1,$basName1);
        $basName2 = $annValues[1][LongCiteParam::AnnValBasic];
        $this->assertEquals($name2,$basName2);
        $nameObj1 = $annValues[0][LongCiteParam::AnnValAsObj];
        $nameObj2 = $annValues[1][LongCiteParam::AnnValAsObj];
        $this->assertInstanceOf(LongCiteUtilPersonName::class,$nameObj1);
        $this->assertInstanceOf(LongCiteUtilPersonName::class,$nameObj2);
        $this->assertEquals($name1,$nameObj1->getRawName());
        $this->assertEquals($name2,$nameObj2->getRawName());
    }

}
?>
