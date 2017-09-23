<?php
/// Source code file for LongCiteUtilDateTest unit testing class.
/// MIT License. See <https://opensource.org/licenses/MIT>.
/// @file
### Note: This file uses Uses doxygen style annotation comments.
### Note: This file possibly includes some PHPUnit comment directives.

require_once __DIR__ . "/../includes/LongCiteUtilDate.php";
require_once __DIR__ . "/../includes/LongCiteUtil.php";

use PHPUnit\Framework\TestCase;

/// Some LongCite phpunit tests.
class LongCiteUtilDateTest extends TestCase {

    /// Test date functions.
    public function testDates() {
        $this->helpDt("en","2001-03-30","2001-03-30",2001,3,30,false,false);
        $this->helpDt("en","280 BC","280 B.C.E.",280,null,null,true,false);
        $this->helpDt("en","c. 280 BC","c. 280 B.C.E.",280,null,null,true,true);
        $this->helpDt("en","ca. 280 BCE","c. 280 B.C.E.",280,null,null,true,true);
        $this->helpDt("en","circa 280 B.C.E.","c. 280 B.C.E.",280,null,null,true,true);
        $this->helpDt("en","c. 280 b.c.","c. 280 B.C.E.",280,null,null,true,true);
        $this->helpDt("en/de","c. 280 BCE","c. 280 v.u.Z.",280,null,null,true,true);
        $this->helpDt("en","-150","151 B.C.E.",151,null,null,true,false);
        $this->helpDt("en","-150 bc","151 B.C.E.",151,null,null,true,false);
        $this->helpDt("en","150","150 C.E.",150,null,null,false,false);
        $this->helpDt("en","1500","1500",1500,null,null,false,false);
        $this->helpDt("en","bork","?bork?",null,null,null,false,false);
        $this->helpDt("en","1957-04","1957-04",1957,4,null,false,false);
        $this->helpDt("en","1957/04","1957-04",1957,4,null,false,false);
        $this->helpDt("en","1957.04","1957-04",1957,4,null,false,false);
        $this->helpDt("en","1957-04-25","1957-04-25",1957,4,25,false,false);
        $this->helpDt("en","1957/04/25","1957-04-25",1957,4,25,false,false);
        $this->helpDt("en/de","c. 280 BCE","c. 280 v.u.Z.",280,null,null,true,true);
        $this->helpDt("en","October 1957","1957-10",1957,10,null,false,false);
        $this->helpDt("en","1957 october","1957-10",1957,10,null,false,false);
        $this->helpDt("en","OCT 1957","1957-10",1957,10,null,false,false);
        $this->helpDt("en","1957 Oct","1957-10",1957,10,null,false,false);
        $this->helpDt("en","October 14, 1957","1957-10-14",1957,10,14,false,false);
        $this->helpDt("en","14-Oct-1957","1957-10-14",1957,10,14,false,false);
        $this->helpDt("de/en","ca. 280 vuZ","c. 280 B.C.E.",280,null,null,true,true);
        $this->helpDt("de","2001-03-30","2001-03-30",2001,3,30,false,false);
        $this->helpDt("de","280 v.chr.","280 v.u.Z.",280,null,null,true,false);
        $this->helpDt("de","c. 280 vuz","c. 280 v.u.Z.",280,null,null,true,true);
        $this->helpDt("de","Oktober 1957","1957-10",1957,10,null,false,false);
        $this->helpDt("de","1957 oktober","1957-10",1957,10,null,false,false);
        $this->helpDt("de","OKT 1957","1957-10",1957,10,null,false,false);
        $this->helpDt("de","1957 Okt","1957-10",1957,10,null,false,false);
        $this->helpDt("de","Oktober 14, 1957","1957-10-14",1957,10,14,false,false);
        $this->helpDt("de","14-Okt-1957","1957-10-14",1957,10,14,false,false);
        $this->helpDt("de","Mär 14, 1957","1957-03-14",1957,03,14,false,false);
        $this->helpDt("de","14-März-1957","1957-03-14",1957,03,14,false,false);
    }

    public function helpDt($langCode,$rawDate,$expDate,$expYear,
        $expMonth=null,$expDay=null,$expBCE=false,$expCirca=false) {
        $expParsedOk = true;
        if(mb_substr($expDate,0,1)=="?") { $expParsedOk = false; }
        $langCodes = mb_split('/',$langCode);
        $langCode1 = $langCodes[0];
        $langCode2 = $langCodes[0];
        if(count($langCodes)>1) { $langCode2 = $langCodes[1]; }
        $dateObj = new LongCiteUtilDate($rawDate,$langCode1);
        $this->assertInstanceOf(LongCiteUtilDate::class,$dateObj);
        $actLangCode1 = $dateObj->getLangCode();
        $dateObj->setLangCode($langCode2);
        $actLangCode2 = $dateObj->getLangCode();
        $actDate  = $dateObj->getDateStr();
        $actYear  = $dateObj->getYear();
        $actMonth = $dateObj->getMonth();
        $actDay   = $dateObj->getDay();
        $actBCE   = $dateObj->getIsBCE();
        $actCirca = $dateObj->getIsCirca();
        $actParsedOk = $dateObj->getParsedOk();
        if(false) {
            LongCiteUtil::writeToTty("\n");
            LongCiteUtil::writeToTty("Raw=$rawDate.\n");
            LongCiteUtil::writeToTty("date=$expDate/$actDate.\n");
            LongCiteUtil::writeToTty("langCode1=$langCode1/$actLangCode1.\n");
            LongCiteUtil::writeToTty("langCode2=$langCode2/$actLangCode2.\n");
            LongCiteUtil::writeToTty("BCE=$expBCE/$actBCE.\n");
            LongCiteUtil::writeToTty("Circa=$expCirca/$actCirca.\n");
        }
        $this->assertEquals($langCode1,$actLangCode1);
        $this->assertEquals($langCode2,$actLangCode2);
        $this->assertEquals($expBCE,$actBCE);
        $this->assertEquals($expCirca,$actCirca);
        $this->assertEquals($expYear,$actYear);
        $this->assertEquals($expMonth,$actMonth);
        $this->assertEquals($expDay,$actDay);
        $this->assertEquals($expDate,$actDate);
        $this->assertEquals($expParsedOk,$actParsedOk);
    }

    public function testDateFormats() {
        $this->helpDF("en","1957-10-04","04-Oct-1957","4 October 1957");
        $this->helpDF("de","1957-03-04","04-Mär-1957","4 März 1957");
        $this->helpDF("en","1957-10","Oct-1957","October 1957");
        $this->helpDF("en","1957","1957","1957");
        $this->helpDF("en","1957","1957","1957");
        $this->helpDF("en","c. 1957-10-04","c. 04-Oct-1957","circa 4 October 1957");
        $this->helpDF("en","c. 1957","c. 1957","circa 1957");
        $this->helpDF("en","957 C.E.","957 C.E.","957 C.E.");
        $this->helpDF("en","957 B.C.E.","957 B.C.E.","957 B.C.E.");
    }

    public function helpDF($lang,$exp1,$exp2,$exp3) {
        $f1 = LongCiteUtilDate::NumMonthFormat;
        $f2 = LongCiteUtilDate::AbbrMonthFormat;
        $f3 = LongCiteUtilDate::FullMonthFormat;
        $d = new LongCiteUtilDate($exp1,$lang);
        $this->assertEquals($exp1,$d->getDateStr());
        $this->assertEquals($exp1,$d->getDateStr($f1));
        $this->assertEquals($exp2,$d->getDateStr($f2));
        $this->assertEquals($exp3,$d->getDateStr($f3));
    }
}
?>
