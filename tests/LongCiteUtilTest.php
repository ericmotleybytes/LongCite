<?php
/// Source code file for LongCiteUtilTest unit testing class.
/// MIT License. See <https://opensource.org/licenses/MIT>.
/// @file
### Note: This file uses Uses doxygen style annotation comments.
### Note: This file possibly includes some PHPUnit comment directives.

require_once __DIR__ . "/../includes/LongCiteUtil.php";

use PHPUnit\Framework\TestCase;

/// Some LongCite phpunit tests.
class LongCiteUtilTest extends TestCase {

    /// Test date functions.
    public function testGuid() {
        if (function_exists('openssl_random_pseudo_bytes')) {
            $guid = LongCiteUtil::generateOpensslGuid();
            $this->assertNotFalse($guid);
            $this->assertRegExp('/^[0-9A-F]{32}$/',$guid);            
        }
        $guid = LongCiteUtil::generateGenericGuid();
        $this->assertNotFalse($guid);
        $this->assertRegExp('/^[0-9A-F]{32}$/',$guid);            
    }

    /// Test a_or_an functions.
    public function testA_or_An() {
        $this->assertEquals("An",LongCiteUtil::a_or_an("elephant"));
        $this->assertEquals("A",LongCiteUtil::a_or_an("monkey"));
        $this->assertEquals("An",LongCiteUtil::a_or_an("elephant",true));
        $this->assertEquals("A",LongCiteUtil::a_or_an("monkey",true));
        $this->assertEquals("an",LongCiteUtil::a_or_an("elephant",false));
        $this->assertEquals("a",LongCiteUtil::a_or_an("monkey",false));
    }

    /// Test hex encode/decode.
    public function testHexEncodeDecode() {
        $this->assistHexEncode("b","%62");
        $this->assistHexDecode("%62","b");
        $this->assistHexDecode("x%62y%6262","xbyb62");
    }

    public function assistHexEncode($raw,$exp) {
        $act = LongCiteUtil::percentHexEncode($raw);
        $this->assertEquals($exp,$act);
    }

    public function assistHexDecode($raw,$exp) {
        $act = LongCiteUtil::percentHexDecode($raw);
        $this->assertEquals($exp,$act);
    }

    /// Test backslashes2urlencode.
    public function testBackslashes2hex() {
        $qq = '"';
        $this->assistBack2hex("","");
        $this->assistBack2hex("abc","abc");
        $this->assistBack2hex("a\\b\\$qq"."c","a%62%22c");
    }

    public function assistBack2hex($raw,$exp) {
        $act = LongCiteUtil::backslashes2percenthex($raw);
        $this->assertEquals($exp,$act);
    }

    /// Test parse.
    public function testParse() {
        $qq = '"';
        $q  = "'";
        $b  = "\\";
        $this->assistParse("abc",array("abc"));
        $this->assistParse("  abc  xyz  ",array("abc  xyz"));
        $this->assistParse("  $qq abc $qq  ",array(' abc '));
        $this->assistParse("  $b \\ abc  ",array('  abc'));
        $this->assistParse("abc|xyz",array("abc","xyz"),"|");
        $this->assistParse(" abc | xyz ",array("abc","xyz"),"|");
        $this->assistParse(" abc $b| xyz ",array("abc | xyz"),"|");
        $this->assistParse(" abc | xyz\\  ",array("abc","xyz "),"|");
    }

    public function assistParse($raw,$exp,$delim=null) {
        $act = LongCiteUtil::parseValuesStr($raw,$delim);
        $this->assertEquals(count($exp),count($act));
        $this->assertEquals($exp,$act);
    }

    public function testI18nTranslateWord() {
        $e = "en";
        $d = "de";
        $m = LongCiteUtil::GenderMale;
        $f = LongCiteUtil::GenderFemale;
        $n = LongCiteUtil::GenderNeutral;
        $u = LongCiteUtil::GenderUnknown;
        $this->helpTran("mr","Mr.",$e,$e,$u,$m);
        $this->helpTran("mister","Hr.",$e,$d,$u,$m);
        $this->helpTran("Herr","Mr.",$d,$e,$u,$m);
        $this->helpTran("doktorin","Dr.",$d,$d,$u,$f);
    }

    public function helpTran($word,$exp,$fLang,$tLang,
        $gend=LongCiteUtil::GenderUnknown,$igend=LongCiteUtil::GenderUnknown) {
        $pat = '^longcite\-nst\-.*$';
        $expIndGend = $igend;
        $indGend = null;
        $act = LongCiteUtil::i18nTranslateWord($word,$fLang,$tLang,$pat,$gend,$indGend);
        $this->assertEquals($exp,$act);
        $this->assertEquals($expIndGend,$indGend);
    }

    public function testEregStuff() {
        $cls = "LongCiteUtil";
        $str = "  \t ABC \t \n";
        $actStr = $cls::eregTrim($str);
        $expStr = "ABC";
        $this->assertEquals($expStr,$actStr);
        $delim = ":X:";
        $expDelim = "\\:X\\:";
        $actDelim = $cls::eregQuote($delim);
        $this->assertEquals($expDelim,$actDelim);
    }

    public function testVariantStuff() {
        $util = "LongCiteUtil";
        $str = "An Elephant";
        $expArr = array($str);
        $actArr = $util::i18nVariants($str);
        $this->assertEquals($expArr,$actArr);
        $str = "[An/The] Elephant";
        $expArr = array("An Elephant","The Elephant","Elephant");
        $actArr = $util::i18nVariants($str);
        $this->assertEquals($expArr,$actArr);
        $str = "[An/The] Elephant [flies/walks]";
        $expArr = array("An Elephant flies","An Elephant walks","An Elephant",
            "The Elephant flies","The Elephant walks","The Elephant",
            "Elephant flies","Elephant walks","Elephant");
        $actArr = $util::i18nVariants($str);
        $this->assertEquals($expArr,$actArr);
    }

    public function testTranslateItem() {
        $this->helpTransItem("wiki","en","en","A wiki");
        $this->helpTransItem("the BOOK","en","en","A book");
        $this->helpTransItem("the BOOK","en","de","Ein Buch");
        $this->helpTransItem("the BOOK","en","es","Un libro");
        $this->helpTransItem("Der Brief","de","en","A letter");
        $this->helpTransItem("Der Brief","de","es","Una carta");
        $this->helpTransItem("Bork Bork","de","es",false);
    }

    public function helpTransItem($item,$fromLang,$toLang,$expItem) {
        $util = "LongCiteUtil";
        $keyPat = '^longcite\-itm\-.+$';
        $actItem = $util::i18nTranslateItem($item,$fromLang,$toLang,$keyPat);
        $this->assertEquals($expItem,$actItem);
    }

}
?>
