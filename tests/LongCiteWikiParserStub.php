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
    protected $functionLangObj = null; ///< Function language object.
    protected $targetLangObj   = null; ///< Target language object.
    protected $optionsObj      = null;

    /// Class constructor.
    public function __construct() {
        $this->parserHooks = array();
        $this->parserOutput = new LongCiteWikiParserOutputStub();
        $this->optionsObj   = new LongCiteWikiParserOptionsStub();
        $this->functionLangObj = $GLOBALS["wgLang"];  // language obj for parser functions such as {{FORMATNUM:}}
        $this->targetLangObj   = $GLOBALS["wgLang"];  // language obj for content being parsed.
    }

    /// Get the Function language object.
    public function getFunctionLang() {
        return $this->functionLangObj;
    }

    /// Get the associated ParserOutput object.
    public function getOutput() {
        return $this->parserOutput;
    }

    /// Get the target language object.
    public function getTargetLanguage() {
        return $this->targetLangObj;
    }

    public function getOptions() {
        return $this->optionsObj;
    }

    public function recursivePreprocess($text,$frame=false) {
        if($frame!==false) {
            // do frame substitutions, (but non recursively)
            foreach($frame->getArguments() as $key => $val) {
                $text = str_replace('{{{'.$key.'}}}',$val,$text);
            }
        }
        return $text;
    }

    /// Half-parse wikitext to half-parsed HTML.
    /// This recursive parser entry point can be called from an extension tag hook.
    /// The output of this function IS NOT SAFE PARSED HTML; it is "half-parsed"
    /// instead, which means that lists and links have not been fully parsed yet,
    /// and strip markers are still present. Use recursiveTagParseFully() to fully
    /// parse wikitext to output-safe HTML. Use this function if you're a parser
    /// tag hook and you want to parse wikitext before or after applying additional
    /// transformations, and you intend to return the result as hook output, which
    /// will cause it to go through the rest of parsing process automatically.
    /// If $frame is not provided, then template variables (e.g., {{{1}}}) within
    /// $text are not expanded
    /// @param $text - The text the extension wants to have parsed.
    /// @param $frame - The associated PPFrame object, or false.
    /// @return Half-parsed HTML (still unsafe).
    public function recursiveTagParse($text,$frame=false) {
        return $this->recursivePreprocess($text,$frame);
    }

    public function recursiveTagParseFully($text,$frame=false) {
        return $this->recursivePreprocess($text,$frame);
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

    public function getTags() {
        return array_keys($this->parserHooks);
    }

    public function clearTagHooks() {
        $this->parserHooks = array();
    }

    public function stubGetHooks() {
        $result = "";
        foreach($this->parserHooks as $hookName => $callables) {
            $callableCnt = 0;
            foreach($callables as $callable) {
                $callableCnt++;
                $line = "ParserHook=$hookName";
                $line .= " Callable_$callableCnt=";
                if(is_array($callable)) {
                    $classPart = $callable[0];
                    $funcPart = $callable[1];
                    if(is_object($classPart)) {
                        $line .= "(" . get_class($classPart) . ")::";
                    } else {
                        $line .= $classPart . "::";
                    }
                    $line .= $funcPart;
                } else {
                    $line .= $callable;
                }
                $line .= ".\n";
                $result .= $line;
            }
        }
        return $result;
    }

    /// A stub routine to mimick calling saved parser callables.
    /// @param $hookName - The MediaWiki parser hook name.
    /// @param $params - Parameters for callables.
    /// @return false on error, else hash array of callable results.
    public function stubCallHook($hookName,$input="",$args=array(),$frame=false) {
        # See https://www.mediawiki.org/wiki/Manual:Parser.php.
        if(!array_key_exists($hookName,$this->parserHooks)) {
            trigger_error("Parser hook $hookName not found.",E_USER_WARNING);
            return false;
        }
        $parser = $this;
        $params = array($input,$args,$parser,$frame);
        $results = array();
        $callables = $this->parserHooks[$hookName];
        $callableName = "";
        $callablesCnt = count($callables);
        foreach($callables as $callable) {
            if(!is_callable($callable,false)) {
                trigger_error("Bad callable.",E_USER_WARNING);
                return false;
            }
            is_callable($callable,false,$callableName);  // set $callableName
            $result = call_user_func_array($callable,$params);
            $results[$callableName] = $result;
        }
        return $results;
    }

    /// A stub routine to set function language object.
    public function stubSetFunctionLang($langObj) {
        $this->functionLangObj = $langObj;
    }

    /// A stub routine to set target language object. On a real wiki this could be
    /// set with the "&uselang=<iso2char>" such as "&uselang=de". Also "&uselang=qqx"
    /// will display all i18n system messages.
    public function stubSetTargetLanguage($langObj) {
        $this->targetLangObj = $langObj;
    }

}
?>
