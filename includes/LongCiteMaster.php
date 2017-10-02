<?php
/// Source code file for the LongCiteMaster:: class.
/// MIT License. See <https://opensource.org/licenses/MIT>.
/// @file
### Note: This file uses Uses doxygen style annotation comments.
### Note: This file possibly includes some PHPUnit comment directives.

// MediaWiki check
// Protects against register_globals vulnerabilities.
// This line must be present before any global variable is referenced.
if (!defined('MEDIAWIKI')) {
    echo "ERROR: This code must run via MediaWiki." . PHP_EOL;
    exit(1);
}

/// Master control class for the LongCite MediaWiki extension.
/// Defines utility routines and data structures.
class LongCiteMaster {
    const DefaultInputLanguageCode = "en";  ///< Parsing input default always en.
    const DefaultCharacterEncoding = "UTF-8"; ///< Standard for MediaWiki.

    protected static $activeMaster = null;  ///< Main active master object instance.

    public static function clearActiveMaster() {
        self::$activeMaster = null;
    }

    public static function getActiveMaster() {
        return self::$activeMaster;
    }

    public static function initialize() {
        $lcm = "LongCiteMessenger";
        if(is_null(self::getActiveMaster())) {
            self::newActiveMaster();
            $master = self::getActiveMaster();
            $masterGuid = $master->getGuid();
            $lcm::debugMessage("LCM: Instantiated new master object ($masterGuid).");
        } else {
            $master = self::getActiveMaster();
            $masterGuid = $master->getGuid();
            $lcm::debugMessage("LCM: Reusing old master object ($masterGuid).");
        }
    }

    public static function newActiveMaster() {
        $master = new LongCiteMaster();
        self::$activeMaster = $master;
        return $master;
    }

    protected $messenger = null;  ///< Set to instance of LongCiteMessenger:: class.
    protected $cssLoaded = false; ///< Set true when CSS resource loaded to output page.
    protected $sqlTableFile =
        __DIR__.'/../GeneratedTables.sql'; ///< Generated file for update.php.
    protected $cssModule = "ext.longCite"; ///< LongCite CSS module name.
    protected $supportedLangCodes = array("en","de","es");  ///< supported output codes.
    protected $outputLangCode = "en";    ///< Can be changed to another supported code.
    protected $parser = null;            ///< Gets parser object as setup hook.
    protected $tagObjects = array();         ///< Gets tag objects the parser finds.
    protected $existingCitationKeys = array(); ///< Citation keys already used.
    protected $guid = "";
    protected $alreadyRegistered = false;
    protected $alreadySetupParsers = array();

    /// Class instance constructor.
    function __construct() {

        global $wgLang;
        $status = mb_internal_encoding(self::DefaultCharacterEncoding);
        if($status===false) {
            $msg = "Problem with mb_internal_encoding(";
            $msg .= self::DefaultCharacterEncoding . ").";
            trigger_error($msg,E_USER_WARNING);
        }
        $status = mb_regex_encoding(self::DefaultCharacterEncoding);
        if($status===false) {
            $msg = "Problem with mb_regex_encoding(";
            $msg .= self::DefaultCharacterEncoding . ").";
            trigger_error($msg,E_USER_WARNING);
        }
        $cssLoaded = false;
        $this->messenger = new LongCiteMessenger(); // instantiate now
        // Determine the default output language.
        if(isset($wgLang)) {
            $candCode = $wgLang->getCode();
            if(in_array($candCode,$this->supportedLangCodes)) {
                $this->outputLangCode = $candCode;
            }
        }
        // reinit list of tag objects.
        $this->tagObjects = array();
        // Register the LongCite extension.
        $this->register();
        // reset existing citation keys
        $this->existingCitationKeysReset();
        // set internal guid
        $this->guid = LongCiteUtil::generateOpensslGuid();
    }

    public function addTagObject($tag) {
        $this->tagObjects[] = $tag;
    }

    public function existingCitationKeysAdd($citationKeys) {
        if(!is_array($citationKeys)) { $citationKeys = array($citationKeys); }
        foreach($citationKeys as $citationKey) {
            if(!in_array($citationKey,$this->existingCitationKeys)) {
                $this->existingCitationKeys[] = $citationKey;
            }
        }
        return $this->existingCitationKeys;
    }

    public function existingCitationKeysGet() {
        return $this->existingCitationKeys;
    }

    public function existingCitationKeysReset() {
        $this->existingCitationKeys = array();
    }

    public function generateSqlTableFile($sqlFile) {
        global $wgDBprefix;
        // Create the create table sql command on the fly so that
        // we can prepend the correct database prefix.
        if(isset($wgDBprefix)) {
            $dbPrefix = $wgDBprefix;
        } else {
            $dbPrefix = "";
        }
        $sqlHdl = fopen($sqlFile,"w");
        if($sqlHdl===false) {
            trigger_error("Could not create $sqlFile.",E_USER_WARNING);
            return "Error in setupSchema";
        }
        fwrite($sqlHdl,"CREATE TABLE IF NOT EXISTS " . $dbPrefix . "longcite_citation (\n");
        fwrite($sqlHdl," longcite_guid char(32),\n");
        fwrite($sqlHdl," longcite_id   varchar(255) CHARACTER SET utf8,\n");
        fwrite($sqlHdl," longcite_host varchar(255) CHARACTER SET utf8,\n");
        fwrite($sqlHdl," longcite_page varchar(255) CHARACTER SET utf8,\n");
        fwrite($sqlHdl," longcite_json text CHARACTER SET utf8,\n");
        fwrite($sqlHdl," UNIQUE KEY " . $dbPrefix . "longcite_guid_pk  (longcite_guid),\n");
        fwrite($sqlHdl," KEY        " . $dbPrefix . "longcite_id_idx   (longcite_id),\n");
        fwrite($sqlHdl," KEY        " . $dbPrefix . "longcite_page_idx (longcite_page)\n");
        fwrite($sqlHdl,");\n");
        fclose($sqlHdl);
        $result = file_get_contents($sqlFile);
        return $result;
    }

    public function getGuid() {
        return $this->guid;
    }

    public function getInputLangCode() {
        return self::DefaultInputLanguageCode;
    }

    /// Get the LongCiteMessenger:: instance to use.
    /// @returns A LongCiteMessenger:: instance.
    public function getMessenger() {
        if(is_null($this->messenger)) {
            $this->messenger = new LongCiteMessenger();
        }
        return $this->messenger;
    }

    /// Get the default tag render output language.
    public function getOutputLangCode() {
        return $this->outputLangCode;
    }

    /// Get parser object.
    /// @return A parser object.
    public function getParser() {
        return $this->parser;
    }

    /// Get the name of the generated sql table file for update.php.
    /// @return The name of the generated sql file.
    public function getSqlTableFile() {
        return $this->sqlTableFile;
    }

    public function getSupportedLangCodes() {
        return $this->supportedLangCodes;
    }

    public function getTagObjects() {
        return $this->tagObjects;
    }


    public function isCssLoaded() {
        return $this->cssLoaded;
    }

    /// Load CSS resource module if and only if not already loaded needed.
    /// @param $outputObj - An instance of either OutputPage or ParserOutput.
    public function loadCssModule($outputObj) {
        if(!$this->isCssLoaded()) {
            $outputObj->addModules($this->cssModule);
            $this->setCssLoaded(true);
        }
    }

    /// Called when an article delete completes.
    /// See $wgHooks['ArticleDeleteComplete'].
    /// See https://www.mediawiki.org/wiki/Manual:Hooks/ArticleDeleteComplete.
    /// @param $article - The article that was deleted.
    ///   WikiPage in MW >= 1.18, Article in 1.17.x and earlier.
    /// @param $user - The user that deleted the article.
    /// @param $reason - The reason the article was deleted.
    /// @param $id - The id of the article that was deleted (added in 1.13).
    /// @param $content - The content of the deleted article,
    ///   or null in case of an error.
    /// @param $logEntry: the log entry used to record the deletion.
    /// @return The function should return true to continue hook processing or
    ///   false to abort.
    public function onArticleDeleteComplete(
        &$article, &$user, $reason, $id, $content=null, $logEntry
    ) {
        $pageId = $article->getTitle()->getPrefixedDBkey();
        # TBD
        return true;
    }

    /// Called on when page content save completes.
    /// See $wgHooks['PageContentSaveComplete'].
    /// See https://www.mediawiki.org/wiki/Manual:Hooks/PageContentSaveComplete.
    /// @param $article - WikiPage modified.
    /// @param $user - User performing the modification.
    /// @param $content - New content, as a Content object.
    /// @param $summary - Edit summary/comment.
    /// @param $isMinor - Whether or not the edit was marked as minor.
    /// @param $isWatch - (No longer used).
    /// @param $section - (No longer used).
    /// @param $flags - Flags passed to WikiPage::doEditContent().
    /// @param $revision - Revision object of the saved content.
    ///   If the save did not result in the creation of a new revision
    ///   (for example, the submission was equal to the latest revision),
    ///   this parameter may be null (null edits, or "no-op"). However,
    ///   there are reports (see phab:T128838) that it's instead set to
    ///   that latest revision.
    /// @param $status - Status object about to be returned by doEditContent().
    /// @param $baseRevId - The rev ID (or false) this edit was based on.
    /// @return The function should return true to continue hook processing or
    ///   false to abort. This hook will be triggered both by edits made through
    ///   the edit page, and by edits made through the API.
    public function onPageContentSaveComplete(
        $article,$user,$content,$summary,$isMinor,$isWatch,$section,
        $flags,$revision,$status,$baseRevId
    ) {
        $pageTitle = $article->getTitle();
        $pageId = $article->getTitle()->getPrefixedDBkey();
        # TBD
        return true;
    }

    /// Called after parse, before the HTML is added to the output.
    /// See $wgHooks['OutputPageParserOutput'].
    /// See https://www.mediawiki.org/wiki/Manual:Hooks/OutputPageParserOutput.
    /// @param $outputPage - The OutputPage (object) to which wikitext is added,
    /// @param $parserOutput: - A ParserOutput object.
    public function onOutputPageParserOutput(
       $outputPage, $parserOutput
    ) {
        # TBD: Add css as follows?
        # $outputPage->addModules("ext.longCite");  # css
        # See https://www.mediawiki.org/wiki/Manual:$wgResourceModules.
        # See https://www.mediawiki.org/wiki/Manual:$wgResourceLoaderDebug.
        # Check if already added???
        ##if($this->cssLoaded===false) {
        ##    $outputPage->addModules("ext.longCite");  # css
        ##    $this->cssLoaded = true;
        ##}
    }

    public function setCssLoaded($flag) {
        if($flag) {
            $this->cssLoaded = true;
        } else {
            $this->cssLoaded = false;
        }
    }

    public function register() {
        global $wgHooks;
        // check if already registered.
        if($this->alreadyRegistered) { return; }
        // register the extension
        // set setup parser hook
        $wgHooks = array();  // reinit
        $wgHooks['ParserFirstCallInit'][] = array(
            &$this,"setupParser"
        );
        // set top level runtime hooks
        $wgHooks['ArticleDeleteComplete'][] = array(
            &$this,"onArticleDeleteComplete"
        );
        $wgHooks['PageContentSaveComplete'][] = array(
            &$this,"onPageContentSaveComplete"
        );
        #$wgHooks['OutputPageParserOutput'][] = array(
        #    &$this,"onOutputPageParserOutput"
        #);
        // set database schema updates hook
        $wgHooks['LoadExtensionSchemaUpdates'][] = array(
            &$this,"setupSchema"
        );
        // remember that we registered
        $this->alreadyRegistered = true;
    }

    /// Set the default tag render output language.
    public function setOutputLangCode($code) {
        if(in_array($code,$this->supportedLangCodes)) {
            $this->outputLangCode = $code;
        }
        return $this->outputLangCode;
    }

    /// Called when the parser initializes for the first time.
    /// See $wgHooks['ParserFirstCallInit'].
    /// See https://www.mediawiki.org/wiki/Manual:Hooks/ParserFirstCallInit.
    /// See https://www.mediawiki.org/wiki/Manual:Parser.php.
    /// @param $parser - Parser object being initialized.
    public function setupParser(&$parser) {
        $lcm = "LongCiteMessenger";
        $this->parser = $parser;
        $parserId = spl_object_hash($parser);
        if(in_array($parserId,$this->alreadySetupParsers)) {
            // parser has already been setup
            $lcm::debugMessage("LCM:SP: Parser $parserId setup already.");
        } else {
            // setup parser hooks.
            $lcm::debugMessage("LCM:SP: Parser $parserId setup running.");
            $parser->setHook('longcite'   ,array($this,"tagLongCite"));
            $parser->setHook('longcitedef',array($this,"tagLongCiteDef"));
            $parser->setHook('longciteref',array($this,"tagLongCiteRef"));
            $parser->setHook('longciteren',array($this,"tagLongCiteRen"));
            $parser->setHook('longcitehlp',array($this,"tagLongCiteHlp"));
            $parser->setHook('longciteopt',array($this,"tagLongCiteOpt"));
            $this->alreadySetupParsers[] = $parserId;
        }
        $tags = implode(";",$parser->getTags());
        $lcm::debugVariable($tags,"LCM:SP: ..tags");
    }

    /// Called when maintenance/update.php is run to allow extensions
    /// to update the database schema.
    /// See $wgHooks['LoadExtensionSchemaUpdates'].
    /// See https://www.mediawiki.org/wiki/Manual:Hooks/LoadExtensionSchemaUpdates.
    /// @param $updater - The DatabaseUpdater object.
    public function setupSchema($updater) {
        $sqlFile = $this->getSqlTableFile();
        $sql = $this->generateSqlTableFile($sqlFile);
        $updater->addExtensionTable("longcite_citation",$sqlFile);
    }

    /// Called when parser finds <longcite>. Mostly for testing and debugging.
    /// @param $input - Content between <longcitedef> and </longcitedef>.
    /// @param $args - Hash array of settings within opening <longcitedef> tag.
    /// @param $parser - The parser object.
    /// @param $frame - Recursive parsing frame.
    /// @return A string with rendered HTML.
    public function tagLongCite($input, $args, $parser, $frame) {
        $tagObj = new LongCiteTag($this, $input, $args, $parser, $frame);
        $this->addTagObject($tagObj);
        $result = $tagObj->doPreprocessing();
        $result = $tagObj->render();
        return $result;
    }
    /// Called when parser finds <longcitedef>.
    /// @param $input - Content between <longcitedef> and </longcitedef>.
    /// @param $args - Hash array of settings within opening <longcitedef> tag.
    /// @param $parser - The parser object.
    /// @param $frame - Recursive parsing frame.
    /// @return A string with rendered HTML.
    public function tagLongCiteDef($input, $args, $parser, $frame) {
        $lcm = "LongCiteMessenger";
        $lcm::debugMessage("LCM:TLCD: In tagLongCiteDef.");
        $lcm::debugTrace("LCM:TLCD:tagLongCiteDef");
        $tagObj = new LongCiteDefTag($this, $input, $args, $parser, $frame);
        $tagGuid = $tagObj->getGuid();
        $lcm::debugVariable($tagGuid,"LCM:TLCD: ..tagGuid");
        $lcm::debugVariable($args,"LCM:TLCD: ..args");
        $inStr = mb_substr($input,0,40) . "...";
        $inStr = mb_ereg_replace('\n','<nl>',$inStr);
        $lcm::debugVariable($inStr,"LCM:TLCD: ..input");
        $parHash = spl_object_hash($parser);
        $lcm::debugVariable($parHash,"LCM:TLCD: ..parserHash");
        $lcm::debugVariable($frame,"LCM:TLCD: ..frame");
        # do stuff
        $this->addTagObject($tagObj);
        $result = $tagObj->doPreprocessing();
        $result = $tagObj->render();
        return $result;
    }

    /// Called when parser finds <longciteref>.
    /// @param $input - Content between <longciteref> and </longciteref>.
    /// @param $args - Hash array of settings within opening <longciteref> tag.
    /// @param $parser - The parser object.
    /// @param $frame - Recursive parsing frame.
    /// @return A string with rendered HTML.
    /// Called when parser finds <longcitehlp>.
    /// @param $input - Content between <longcitehlp> and </longcitehlp>.
    /// @param $args - Hash array of settings within opening <longcitehlp> tag.
    /// @param $parser - The parser object.
    /// @param $frame - Recursive parsing frame.
    /// @return A string with rendered HTML.
    public function tagLongCiteHlp($input, $args, $parser, $frame) {
        $tagObj = new LongCiteHlpTag($this, $input, $args, $parser, $frame);
        $this->addTagObject($tagObj);
        $result = $tagObj->doPreprocessing();
        $result = $tagObj->render();
        return $result;
    }

    /// Called when parser finds <longciteopt>.
    /// @param $input - Content between <longciteopt> and </longciteopt>.
    /// @param $args - Hash array of settings within opening <longciteopt> tag.
    /// @param $parser - The parser object.
    /// @param $frame - Recursive parsing frame.
    /// @return A string with rendered HTML.
    public function tagLongCiteOpt($input, $args, $parser, $frame) {
        $tagObj = new LongCiteOptTag($this, $input, $args, $parser, $frame);
        $this->addTagObject($tagObj);
        $result = $tagObj->doPreprocessing();
        $result = $tagObj->render();
        return $result;
    }

    public function tagLongCiteRef($input, $args, $parser, $frame) {
        $tagObj = new LongCiteRefTag($this, $input, $args, $parser, $frame);
        $this->addTagObject($tagObj);
        $result = $tagObj->doPreprocessing();
        $result = $tagObj->render();
        return $result;
    }

    /// Called when parser finds <longciteren>.
    /// @param $input - Content between <longciteren> and </longciteren>.
    /// @param $args - Hash array of settings within opening <longciteren> tag.
    /// @param $parser - The parser object.
    /// @param $frame - Recursive parsing frame.
    /// @return A string with rendered HTML.
    public function tagLongCiteRen($input, $args, $parser, $frame) {
        $tagObj = new LongCiteRenTag($this, $input, $args, $parser, $frame);
        $this->addTagObject($tagObj);
        $result = $tagObj->doPreprocessing();
        $result = $tagObj->render();
        return $result;
    }

    /// Call wfMessage the way we usually like.
    /// @param $msg_key - The i18n message key.
    /// @param $params - String parameters for wfMessage.
    private function wikiMessage($isInputLang=false,$msgKey, ...$params) {
        if($isInputLang) {
            $langCode = $this->getInputLangCode();
        } else {
            $langCode = $this->getOutputLangCode();
        }
        $msgObj = wfMessage($msgKey);
        $theParams = array();
        foreach($params as $param) {
            if(is_array($param)) {
                foreach($param as $par) {
                    $theParams[] = $par;
                }
            } else {
                $theParams[] = $param;
            }
        }
        if(count($theParams)>0) {
            $msgObj->params($theParams);
        }
        $msgObj = $msgObj->inLanguage($langCode);
        return $msgObj;
    }

    public Function wikiMessageIn($msgKey, ...$params) {
        return $this->wikiMessage(true,$msgKey,$params);
    }

    public Function wikiMessageOut($msgKey, ...$params) {
        return $this->wikiMessage(false,$msgKey,$params);
    }

}
?>
