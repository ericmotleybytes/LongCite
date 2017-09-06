<?php
/// Source code file for LongCiteWikiStub class. This is a class
/// to mimic just a few MediaWiki capabilities in order to facilitate
/// simple low level unit testing of MediaWiki targeted classes and functions.
/// MIT License. See <https://opensource.org/licenses/MIT>.
/// @file
### Note: This file uses Uses doxygen style annotation comments.
### Note: This file possibly includes some PHPUnit comment directives.

require_once __DIR__ . "/LongCiteWikiUpdaterStub.php";
require_once __DIR__ . "/LongCiteWikiParserStub.php";
require_once __DIR__ . "/LongCiteWikiOutputPageStub.php";
require_once __DIR__ . "/LongCiteWikiParserOutputStub.php";
require_once __DIR__ . "/LongCiteWikiPPFrameStub.php";
require_once __DIR__ . "/LongCiteWikiMessageStub.php";
require_once __DIR__ . "/LongCiteWikiLanguageStub.php";
require_once __DIR__ . "/../includes/LongCiteMaster.php";
require_once __DIR__ . "/../includes/LongCiteMessenger.php";

/// Mimick the wfMessage global function.
/// @param - The MediaWiki message identifier (a string).
/// @return - A LongCiteWikiMessageStub object.
function wfMessage($msgId, ...$params) {
    $msgObj = new LongCiteWikiMessageStub($msgId,$params);
    return $msgObj;
}

/// A stub class mimicking MediaWiki in order to facilitate low level unit testing.
class LongCiteWikiStub {

    /// Initialize the fake stub MediaWiki.
    public static function initialize() {
        self::initHooks();
        self::initLocalSettings();
        self::initExtensionJson();
        self::initUserPrefs();
        self::initParser();
        self::initExtensionFunctions();
    }

    /// Initialize the fake stub MediaWiki hooks.
    public static function initHooks() {
        $GLOBALS["wgHooks"] = array(
            "ParserFirstCallInit"        => array(),
            "ArticleDeleteComplete"      => array(),
            "PageContentSaveComplete"    => array(),
            "OutputPageParserOutput"     => array(),
            "LoadExtensionSchemaUpdates" => array()
        );
    }

    /// Initialize a few things that would be from LocalSettings.php.
    public static function initLocalSettings() {
        $GLOBALS["wgSitename"]      = "Unit Test Stub Wiki";
        $GLOBALS["wgMetaNamespace"] = "Unit_Test_Stub_Wiki";
        $GLOBALS["wgDBprefix"]      = "mwtq_";
        $GLOBALS["wgShellLocale"]   = "en_US.utf8";
        $GLOBALS["wgLanguageCode"]  = "en";
        $GLOBALS["wgContLang"]      = LongCiteWikiLanguageStub::stubNew("en");
        $GLOBALS["wgExternalLinkTarget"] = '_blank';
    }

    /// Initialize some stuff from extension.json.
    public static function initExtensionJson() {
        global $wgAutoloadClasses;
        global $wgExtensionFunctions;
        $extJsonFile = __DIR__ . "/../extension.json";
        $extJsonStr  = file_get_contents($extJsonFile);
        if($extJsonStr===false) {
            trigger_error("Could not read $extJsonFile.",E_USER_WARNING);
            return;
        }
        $extJsonArr  = json_decode($extJsonStr,true);
        if(is_null($extJsonArr)) {
            trigger_error("Could not json_decode $extJsonFile.",E_USER_WARNING);
            return;
        }
        // Initialize $wgAutoloadClasses and
        // require_once anything in AutoloadClasses.
        if(!isset($wgAutoloadClasses)) { $wgAutoloadClasses = array(); }
        if(array_key_exists("AutoloadClasses",$extJsonArr)) {
            $autoloads = $extJsonArr["AutoloadClasses"];
            foreach($autoloads as $className => $classFile) {
                $wgAutoloadClasses[$className] = $classFile;
                $classSpec = __DIR__ . "/../" . $classFile;
                require_once "$classSpec";
            }
        }
        // Initialize $wgExtensionFunctions.
        if(!isset($wgExtensionFunctions)) { $wgExtensionFunctions = array(); }
        if(array_key_exists("ExtensionFunctions",$extJsonArr)) {
            $extfuncs = $extJsonArr["ExtensionFunctions"];
            foreach($extfuncs as $extfunc) {
                $wgExtensionFunctions[] = $extfunc;
            }
        }
    }

    /// Initialize some typically user preference stuff.
    public static function initUserPrefs() {
        $GLOBALS["wgLang"] = LongCiteWikiLanguageStub::stubNew("en");
    }

    /// Initialize a global stub parser object.
    public static function initParser() {
        global $wgParser;
        $wgParser = new LongCiteWikiParserStub();
    }

    public static function initExtensionFunctions() {
        global $wgExtensionFunctions;
        foreach($wgExtensionFunctions as $callable) {
            $result = call_user_func($callable);
        }
    }
    /// A stub routine to mimick calling saved parser callables.
    /// @param $hookName - The MediaWiki parser hook name.
    /// @param $params - Parameters for callables.
    /// @return false on error, else hash array of callable results.
    public static function stubHookCaller($hookName,$params=array()) {
        global $wgHooks;
        if(!array_key_exists($hookName,$wgHooks)) {
            trigger_error("wgHooks $hookName not found.",E_USER_WARNING);
            return false;
        }
        $results = array();
        $callables = $wgHooks[$hookName];
        $callableName = "";
        foreach($callables as $callable) {
            if(!is_callable($callable,false,$callableName)) {
                $callVar = print_r($callable,true);
                $msg = "wgHooks $hookName $callVar is not callable";
                trigger_error($msg,E_USER_WARNING);
                return false;
            }
            $result = call_user_func_array($callable,$params);
            $results[$callableName] = $result;
        }
        return $results;
    }

}
?>
