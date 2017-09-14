<?php
/// Source code file for LongCiteTest unit testing class.
/// MIT License. See <https://opensource.org/licenses/MIT>.
/// @file
### Note: This file uses Uses doxygen style annotation comments.
### Note: This file possibly includes some PHPUnit comment directives.

use PHPUnit\Framework\TestCase;

/// Some LongCite phpunit tests.
class LongCiteJsonTest extends TestCase {

    /// Test something simple functions, mostly to check phpunit itself.
    public function testMath() {
        // check simple math.
        $this->assertEquals(4,2+2);
    }

    // check json files in project directory.
    public function testHomeJson() {
        $wild = __DIR__.'/../*.json';
        $jsonFiles = glob($wild);
        $this->assistJson($jsonFiles);
    }

    // check json files in i18n directory.
    public function testI18nJson() {
        $wild = __DIR__.'/../i18n/*.json';
        $jsonFiles = glob($wild);
        $this->assistJson($jsonFiles);
        # check that every files has the same entries as the "en" file.
        $enJsonFile = __DIR__.'/../i18n/en.json';
        $enJsonStr = file_get_contents($enJsonFile);
        $enJsonArr = json_decode($enJsonStr,true);
        foreach($jsonFiles as $jsonFile) {
            if($jsonFile==$enJsonFile) { continue; }
            $langCode = basename($jsonFile,".json");
            $jsonStr = file_get_contents($jsonFile);
            $jsonArr = json_decode($jsonStr,true);
            // verify every entry in the non-en file exists in the en file.
            foreach($jsonArr as $jsonKey => $jsonVal) {
                $this->assertTrue(array_key_exists($jsonKey,$enJsonArr),
                    "$langCode $jsonKey not in en.");
            }
            // verify every entry in the en file exists in the non-en file.
            foreach($enJsonArr as $enJsonKey => $enJsonVal) {
                $this->assertTrue(array_key_exists($enJsonKey,$jsonArr),
                    "en $enJsonKey not in $langCode.");
            }
        }
    }

    public function assistJson($jsonFiles) {
        foreach($jsonFiles as $jsonFile) {
            $this->assertFileExists($jsonFile);
            $jsonStr = file_get_contents($jsonFile);
            $this->assertNotFalse($jsonStr);
            $jsonArr = json_decode($jsonStr,true);
            $this->assertNotNull($jsonArr);
        }
    }

}
?>
