<?php
/// Source code file for LongCiteWikiParserStub class. This is a class
/// to mimic just a little of the MediaWiki Parser class in order to facilitate
/// simple low level unit testing of MediaWiki targeted classes and functions.
/// MIT License. See <https://opensource.org/licenses/MIT>.
/// @file
### Note: This file uses Uses doxygen style annotation comments.
### Note: This file possibly includes some PHPUnit comment directives.

/// A stub class mimicking MediaWiki in order to facilitate low level unit testing.
class LongCiteWikiParserStub {

    protected $parserHooks = array();  ///< Saved parser hook callables.
    protected $parserOutput = null;    ///< Set to a ParserOutput object.

    /// Class constructor.
    public function __construct() {
        $this->parserHooks = array();
        $this->parserOutput = new LongCiteWikiParserOutputStub();
    }

    /// Get the associated ParserOutput object.
    public function getOutput() {
        return $this->parserOutput;
    }

    /// Set a particular parser callable hook.
    /// @param $hookName - The MediaWiki parser hook name.
    /// @param $callable - The php callable.
    public function setHook($hookName,$callable) {
        if(!array_key_exists($hookName,$this->parserHooks)) {
            $this->parserHooks[$hookName] = array();
        }
        $this->parserHooks[$hookName][] = $callable;
    }

    /// A stub routine to mimick calling saved parser callables.
    /// @param $hookName - The MediaWiki parser hook name.
    /// @param $params - Parameters for callables.
    /// @return false on error, else hash array of callable results.
    public function stubHookCaller($hookName,$params=array()) {
        if(!array_key_exists($hookName,$this->parserHooks)) {
            trigger_error("Parser hook $hookName not found.",E_USER_WARNING);
            return false;
        }
        $results = array();
        $callables = $this->parserHooks[$hookName];
        $callableName = "";
        $callablesCnt = count($callables);
        foreach($callables as $callable) {
            if(!is_callable($callable,false)) {
                trigger_error("Bad callable.",E_UISER_WARNING);
                return false;
            }
            is_callable($callable,false,$callableName);  // set $callableName
            $result = call_user_func_array($callable,$params);
            $results[$callableName] = $result;
        }
        return $results;
    }

}
?>
