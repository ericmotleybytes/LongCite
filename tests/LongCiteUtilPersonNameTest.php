<?php
/// Source code file for LongCiteUtilPersonTest unit testing class.
/// MIT License. See <https://opensource.org/licenses/MIT>.
/// @file
### Note: This file uses Uses doxygen style annotation comments.
### Note: This file possibly includes some PHPUnit comment directives.

require_once __DIR__ . "/../includes/LongCiteUtilPersonName.php";
require_once __DIR__ . "/../includes/LongCiteUtil.php";

use PHPUnit\Framework\TestCase;

/// Some LongCite phpunit tests.
class LongCiteUtilPersonNameTest extends TestCase {

    /// Test name functions.
    public function testNames() {
        $langCode = "en";
        $rawName  = '[Dr.][Prof.;Mr.] ';
        $rawName .= 'Robert "Bob" T. Von_Jones ';
        $rawName .= '{Jr.} (M.D.;J.D.) ';
        $rawName .= " --of Pittsburg-- ";
        $rawName .= " ((CEO of Dorks, Inc.))";
        $nameObj = new LongCiteUtilPersonName($rawName,$langCode);
        $this->assertInstanceOf(LongCiteUtilPersonName::class,$nameObj);
        $expTitles = array("Dr.","Prof.","Mr.");
        $expPoss  = array("CEO of Dorks, Inc.");
        $expCreds = array("M.D.","J.D.");
        $expParts = array(
            ["Dr.","title"],
            ["Prof.","title"],
            ["Mr.","title"],
            ["Robert","name"],
            ["Bob","nickname"],
            ["T.","name"],
            ["Von Jones","surname"],
            ["Jr.","qualifier"],
            ["of Pittsburg","disambiguator"],
            ["M.D.","credential"],
            ["J.D.","credential"],
            ["CEO of Dorks, Inc.","position"]
        );
        $expJson = json_encode($expParts);
        $expAdjName = 'Robert "Bob" T. Von_Jones';
        $this->assertEquals($expTitles,$nameObj->getRawTitles());
        $this->assertEquals($expPoss,$nameObj->getRawPositions());
        $this->assertEquals($expCreds,$nameObj->getRawCredentials());
        $actParts = $nameObj->getAnnNameParts();
        $actJson = json_encode($actParts);
        $this->assertEquals($expJson,$actJson);
        $expStr  = 'Dr. Prof. Mr. Robert "Bob" T. _Von Jones_, Jr.';
        $expStr .= " of Pittsburg, M.D., J.D.";
        $expStr .= " (CEO of Dorks, Inc.)";
        $actStr = $nameObj->GetRenderedNameAll();
        $this->assertEquals($expStr,$actStr);
    }

}
?>
