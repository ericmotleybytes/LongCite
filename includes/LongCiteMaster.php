<?php
/// Source code file for the LongCiteMaster:: class.
/// MIT License. See <https://opensource.org/licenses/MIT>.
/// @file
### Note: This file uses Uses doxygen style annotation comments.
### Note: This file possibly includes some PHPUnit comment directives.

/// Master control class for the LongCite MediaWiki extension.
/// Defines utility routines and data structures.
class LongCiteMaster {

    protected static $activeMaster = null;  ///< Main active master object instance.

    public static function clearActiveMaster() {
        self::$activeMaster = null;
    }

    public static function getActiveMaster() {
        return self::$activeMaster;
    }

    public static function initialize() {
        if(is_null(self::getActiveMaster())) {
            self::newActiveMaster();
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

    /// Class instance constructor.
    function __construct() {
        $cssLoaded = false;
        #$this->messenger = null; // defer instantiation
        $this->messenger = new LongCiteMessenger(); // instantiate now
        $this->messenger->registerMessage(LongCiteMessenger::DebugType,
            "Instantiated new LongCiteMaster at " . time());
        #$this->messenger->dumpToFile(false);
        $this->messenger->dumpToFile(true);
        $this->messenger->clearMessages();
        // Register the LongCite extension.
        $this->register();
        #// Set up the extension tags.
        #$this->setupParser($GLOBALS['wgParser']);
    }

    public function isCssLoaded() {
        return $this->cssLoaded;
    }

    public function setCssLoaded($flag) {
        if($flag) {
            $this->cssLoaded = true;
        } else {
            $this->cssLoaded = false;
        }
    }

    /// Load CSS resource module if and only if not already loaded needed.
    /// @param $outputObj - An instance of either OutputPage or ParserOutput.
    public function loadCssModule($outputObj) {
        if(!$this->isCssLoaded()) {
            $outputObj->addModules($this->cssModule);
            $this->setCssLoaded(true);
        }
    }

    public function register() {
        global $wgHooks;
        $m = $this->getMessenger();
        $m->registerMessage(LongCiteMessenger::DebugType,"In Master::register");
        $m->dumpToFile();
        $m->clearMessages();
        // register the extension
        #$wgExtensionFunctions[] = array(&$this,"setup");
        #$wgExtensionCredits['parserhook'][] = array( 
        #    'name' => 'LongCite',
        #    'author' => 'Eric Alan Christiansen',
        #    'description' => 'Adds tags for reference citation management.',
        #    'url' => 'http://www.mediawiki.org/wiki/Extension:LongCite'
        #);
        // set setup parser hook
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
    }

    /// Get the name of the generated sql table file for update.php.
    /// @return The name of the generated sql file.
    public function getSqlTableFile() {
        return $this->sqlTableFile;
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
        fwrite($sqlHdl," longcite_id   varchar(255),\n");
        fwrite($sqlHdl," longcite_page varchar(255),\n");
        fwrite($sqlHdl," longcite_json varchar(20000),\n");
        fwrite($sqlHdl," UNIQUE KEY " . $dbPrefix . "longcite_guid_pk  (longcite_guid),\n");
        fwrite($sqlHdl," KEY        " . $dbPrefix . "longcite_id_idx   (longcite_id),\n");
        fwrite($sqlHdl," KEY        " . $dbPrefix . "longcite_page_idx (longcite_page)\n");
        fwrite($sqlHdl,");\n");
        fclose($sqlHdl);
        $result = file_get_contents($sqlFile);
        return $result;
    }

    /// Called when the parser initializes for the first time.
    /// See $wgHooks['ParserFirstCallInit'].
    /// See https://www.mediawiki.org/wiki/Manual:Hooks/ParserFirstCallInit.
    /// See https://www.mediawiki.org/wiki/Manual:Parser.php.
    /// @param $parser - Parser object being initialized.
    public function setupParser(&$parser) {
        #if(is_null($parser)) {
        #    $parser = $GLOBALS['wgParser'];
        #}
        $m = $this->getMessenger();
        $m->registerMessage(LongCiteMessenger::DebugType,"In Master::setupParser");
        $m->dumpToFile();
        $m->clearMessages();
        // set hooks for parser functions
        #$wgParser->setHook('longcitedef',array($this,"tagLongCiteDef"));
        #$wgParser->setHook('longciteref',array($this,"tagLongCiteRef"));
        #$wgParser->setHook('longciteren',array($this,"tagLongCiteRen"));
        #$wgParser->setHook('longcitehlp',array($this,"tagLongCiteHlp"));
        $parser->setHook('longcitedef',array($this,"tagLongCiteDef"));
        $parser->setHook('longciteref',array($this,"tagLongCiteRef"));
        $parser->setHook('longciteren',array($this,"tagLongCiteRen"));
        $parser->setHook('longcitehlp',array($this,"tagLongCiteHlp"));
        $parser->setHook('longciteopt',array($this,"tagLongCiteOpt"));
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
        $m = $this->getMessenger();
        $m->registerMessage(LongCiteMessenger::DebugType,
            "In Master::onArticleDeleteComplete for '$pageId'");
        $m->dumpToFile();
        $m->clearMessages();
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
        $m = $this->getMessenger();
        $d = LongCiteMessenger::DebugType;
        $m->registerMessage($d,"In Master::onPageContentSaveComplete for '$pageId'");
        $m->dumpToFile();
        $m->clearMessages();
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

    /// Called when parser finds <longcitedef>.
    /// @param $input - Content between <longcitedef> and </longcitedef>.
    /// @param $args - Hash array of settings within opening <longcitedef> tag.
    /// @param $parser - The parser object.
    /// @param $frame - Recursive parsing frame.
    /// @return A string with rendered HTML.
    public function tagLongCiteDef($input, $args, $parser, $frame) {
        $m = $this->getMessenger();
        $m->registerMessage(LongCiteMessenger::DebugType,"In Master::tagLongCiteDef");
        $m->dumpToFile();
        $m->clearMessages();
        $tagObj = new LongCiteDefTag($this);
        $result = $tagObj->render($input,$args,$parser,$frame);
        return $result;
    }

    /// Called when parser finds <longciteref>.
    /// @param $input - Content between <longciteref> and </longciteref>.
    /// @param $args - Hash array of settings within opening <longciteref> tag.
    /// @param $parser - The parser object.
    /// @param $frame - Recursive parsing frame.
    /// @return A string with rendered HTML.
    public function tagLongCiteRef($input, $args, $parser, $frame) {
        $m = $this->getMessenger();
        $m->registerMessage(LongCiteMessenger::DebugType,"In Master::tagLongCiteRef");
        $m->dumpToFile();
        $m->clearMessages();
        $tagObj = new LongCiteRefTag($this);
        $result = $tagObj->render($input,$args,$parser,$frame);
        return $result;
    }

    /// Called when parser finds <longciteren>.
    /// @param $input - Content between <longciteren> and </longciteren>.
    /// @param $args - Hash array of settings within opening <longciteren> tag.
    /// @param $parser - The parser object.
    /// @param $frame - Recursive parsing frame.
    /// @return A string with rendered HTML.
    public function tagLongCiteRen($input, $args, $parser, $frame) {
        $m = $this->getMessenger();
        $m->registerMessage(LongCiteMessenger::DebugType,"In Master::tagLongCiteRen");
        $m->dumpToFile();
        $m->clearMessages();
        $tagObj = new LongCiteRenTag($this);
        $result = $tagObj->render($input,$args,$parser,$frame);
        return $result;
    }

    /// Called when parser finds <longcitehlp>.
    /// @param $input - Content between <longcitehlp> and </longcitehlp>.
    /// @param $args - Hash array of settings within opening <longcitehlp> tag.
    /// @param $parser - The parser object.
    /// @param $frame - Recursive parsing frame.
    /// @return A string with rendered HTML.
    public function tagLongCiteHlp($input, $args, $parser, $frame) {
        $m = $this->getMessenger();
        $m->registerMessage(LongCiteMessenger::DebugType,"In Master::tagLongCiteHlp");
        $m->dumpToFile();
        $m->clearMessages();
        $tagObj = new LongCiteHlpTag($this);
        $result = $tagObj->render($input,$args,$parser,$frame);
        return $result;
    }

    /// Called when parser finds <longciteopt>.
    /// @param $input - Content between <longciteopt> and </longciteopt>.
    /// @param $args - Hash array of settings within opening <longciteopt> tag.
    /// @param $parser - The parser object.
    /// @param $frame - Recursive parsing frame.
    /// @return A string with rendered HTML.
    public function tagLongCiteOpt($input, $args, $parser, $frame) {
        $m = $this->getMessenger();
        $m->registerMessage(LongCiteMessenger::DebugType,"In Master::tagLongCiteOpt");
        $m->dumpToFile();
        $m->clearMessages();
        $tagObj = new LongCiteOptTag($this);
        $result = $tagObj->render($input,$args,$parser,$frame);
        return $result;
    }

    /// Get the LongCiteMessenger:: instance to use.
    /// @returns A LongCiteMessenger:: instance.
    public function getMessenger() {
        if(is_null($this->messenger)) {
            $this->messenger = new LongCiteMessenger();
        }
        return $this->messenger;
    }

}
?>
