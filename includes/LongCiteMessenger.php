<?php
/// Source code file for the LongCiteMessenger:: class.
/// @copyright Copyright (c) 2017, Eric Alan Christiansen.\n
/// MIT License. See <https://opensource.org/licenses/MIT>.
/// @file
### Note: This file uses Uses doxygen style annotation comments.
### Note: This file possibly includes some PHPUnit comment directives.

/// Class for creating "messages" for the resultant page.
/// Defines utility routines and data structures.
class LongCiteMessenger {

    const ErrorType   = "ERROR";     ///< Constant id for error messages.
    const WarningType = "WARNING";   ///< Constant id for warning messages.
    const NoteType    = "NOTE";      ///< Constant id for note messages.
    const DebugType   = "DEBUG";     ///< Constant id for debug messages.
    const TraceType   = "TRACE";     ///< Constant id for trace messages.
    protected $messages     = array();  ///< Array of messages, each msg a hash array.
    protected $prefixMsgIds = array();  ///< Hash array of i18n ids, by msg type.
    protected $cssClasses   = array();  ///< Hash array of css classes, by msg type.
    protected $enables      = array();  ///< Hash array of true/false by msg type.
    protected $langCode     = "en";     ///< Output language code.
    protected $dumpFile = __DIR__."/../dump.out"; ///< Message dump file.
    protected $doTrigger = false;      ///< Use php trigger_error (for debugging only!).

    /// Class instance constructor.
    public function __construct($langCode="en") {
        $this->langCode = $langCode;
        $this->prefixMsgIds = array(
            self::ErrorType   => "longcite-msgtyp-error",
            self::WarningType => "longcite-msgtyp-warning",
            self::NoteType    => "longcite-msgtyp-note",
            self::DebugType   => "longcite-msgtyp-debug",
            self::TraceType   => "longcite-msgtyp-trace"
        );
        $this->cssClasses = array(
            self::ErrorType   => "mw-longcite-msgtyp-error",
            self::WarningType => "mw-longcite-msgtyp-warning",
            self::NoteType    => "mw-longcite-msgtyp-note",
            self::DebugType   => "mw-longcite-msgtyp-debug",
            self::TraceType   => "mw-longcite-msgtyp-trace"
        );
        $debugOption = LongCite::getOption(LongCite::DebugOption);
        $traceOption = LongCite::getOption(LongCite::TraceOption);
        $this->enables = array(
            self::ErrorType   => true,
            self::WarningType => true,
            self::NoteType    => true,
            self::DebugType   => $debugOption,
            self::TraceType   => $traceOption
        );
        $this->clearMessages();
    }

    /// Clear all messages from message buffer.
    public function clearMessages() {
        $this->messages = array();
        return true;
    }

    /// Dump messages to the dump file.
    /// @param $appendFlag - True to append to existing file, if any.
    /// returns true on success, else false.
    public function dumpToFile($appendFlag=true) {
        if (!$this->getEnableDebug()) { return false; }
        if($appendFlag) {
            $mode = "a";
        } else {
            $mode = "w";
        }
        $f = fopen($this->dumpFile,$mode);
        if($f===false) { return false; }
        $text = $this->renderMessagesText();
        $bytes = fwrite($f,$text);
        if($bytes===false) {
            fclose($f);
            return false;
        }
        $status = fclose($f);
        return $status;
    }

    public function getDoTrigger() {
        return $this->doTrigger;
    }

    public function getEnable($msgType) {
        $msgType = strtoupper($msgType);
        if(!array_key_exists($msgType,$this->prefixMsgIds)) {
            user_error("Invalid message type ($msgType)",E_USER_ERROR);
            return null;
        }
        return $this->enables[$msgType];
    }

    public function getEnableDebug() {
        return $this->getEnable(self::DebugType);
    }

    public function getEnableError() {
        return $this->getEnable(self::ErrorType);
    }

    public function getEnableNote() {
        return $this->getEnable(self::NoteType);
    }

    public function getEnableTrace() {
        return $this->getEnable(self::TraceType);
    }

    public function getEnableWarning() {
        return $this->getEnable(self::WarningType);
    }

    /// Return the current language code (for prefixes).
    public function getLangCode() {
        return $this->langCode;
    }

    /// Return a count of buffered messages.
    public function getMessageCount() {
        return count($this->messages);
    }

    public function registerMessageDebug($msgText) {
        return $this->registerMessage(self::DebugType,$msgText);
    }

    public function registerMessageError($msgText) {
        return $this->registerMessage(self::ErrorType,$msgText);
    }

    public function registerMessageNote($msgText) {
        return $this->registerMessage(self::NoteType,$msgText);
    }

    public function registerMessageTrace($msgText="") {
        $backTrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS,2);
        $info = $backTrace[1];
        $function = $info["function"];
        $class    = $info["class"];
        $file     = $info["file"];
        $line     = $info["line"];
        $file     = basename($file,".php");
        $msgText  = LongCiteUtil::eregTrim("($class::$function:$line) $msgText");
        return $this->registerMessage(self::TraceType,$msgText);
    }

    public function registerMessageWarning($msgText) {
        return $this->registerMessage(self::WarningType,$msgText);
    }

    /// Register a message in the message buffer.
    /// @param $msgType - String set to class constand for message type (ErrorType,
    ///  WarningType, NoteType, or DebugType).
    /// @param $msgText - String with the text of the message.
    public function registerMessage($msgType,$msgText) {
        $msgType = strtoupper($msgType);
        if(!array_key_exists($msgType,$this->prefixMsgIds)) {
            user_error("Invalid message type ($msgType)",E_USER_ERROR);
            return false;
        }
        if(!$this->enables[$msgType]) { return false; }
        $msgId = $this->prefixMsgIds[$msgType];
        $code = $this->langCode;
        $prefix = wfMessage($msgId)->inLanguage($code)->plain();
        if(!is_string($msgText)) {
            trigger_error("Message text is not a string.",E_USER_ERROR);
            return false;
        }
        $msg = array(
            "type"      => $msgType,
            "prefix"    => $prefix,
            "css-class" => $this->cssClasses[$msgType],
            "text"      => $msgText
        );
        $this->messages[] = $msg;
        if($this->doTrigger) {
            $tmp = print_r($msg,true);
            if($msgType==self::WarningType) {
                trigger_error($tmp,E_USER_WARNING);
            } elseif($msgType==self::ErrorType) {
                trigger_error($tmp,E_USER_ERROR);
            } elseif($msgType==self::NoteType) {
                trigger_error($tmp,E_USER_NOTICE);
            }
        }
        return true;
    }

    /// Render all buffered messages for html.
    /// Does NOT automatically clear the buffer, use clearMessages().
    /// @returns A string with html for all buffered messages.
    public function renderMessagesHtml($clear=false) {
        $dq = '"';  // a double-quote character.
        $result = "";
        foreach($this->messages as $message) {
            $cssClass = $message["css-class"];
            $prefix   = $message["prefix"];
            $text     = htmlspecialchars($message["text"]);
            $text     = mb_ereg_replace('\n','<br/>',$text);
            $result  .= "<p class=$dq$cssClass$dq>";
            $result  .= "$prefix: $text";
            $result  .= "</p>\n";
        }
        if($clear) { $this->clearMessages(); }
        return $result;
    }

    /// Render all buffered messages for text.
    /// Does NOT automatically clear the buffer, use clearMessages().
    /// @returns A string with html for all buffered messages.
    public function renderMessagesText($clear=false) {
        $result = "";
        foreach($this->messages as $message) {
            $prefix  = $message["prefix"];
            $text    = $message["text"];
            $result .= "$prefix: $text\n";
        }
        if($clear) { $this->clearMessages(); }
        return $result;
    }

    public function renderMessagesToTty($clear=false,$useHtml=false) {
        if($useHtml) {
            $text = $this->renderMessagesHtml();
        } else {
            $text = $this->renderMessagesText();
        }
        if(strlen($text)==0) { return true; }
        $ttyDevice = "/dev/tty";
        $tty = fopen($ttyDevice,"a");
        if($tty===false) {
            trigger_error("Cannot open $ttyDevice.",E_USER_WARNING);
            return false;
        }
        $bytes = fwrite($tty,$text);
        if($bytes===false) {
            trigger_error("Cannot write to $ttyDevice.",E_USER_WARNING);
            return false;
        }
        $status = fclose($tty);
        if($status===false) {
            trigger_error("Cannot properly close $ttyDevice.",E_USER_WARNING);
            return false;
        }
        if($clear) { $this->clearMessages(); }
        return true;
    }

    public function setDoTrigger($triggerFlag) {
        $this->doTrigger = $triggerFlag;
    }

    public function setEnable($msgType,$flag) {
        $msgType = strtoupper($msgType);
        if(!array_key_exists($msgType,$this->prefixMsgIds)) {
            user_error("Invalid message type ($msgType)",E_USER_ERROR);
            return;
        }
        if($flag) {
            $this->enables[$msgType] = true;
        } else {
            $this->enables[$msgType] = false;
        }
    }

    public function setEnableDebug($flag) {
        $this->setEnable(self::DebugType,$flag);
    }

    public function setEnableError($flag) {
        $this->setEnable(self::ErrorType,$flag);
    }

    public function setEnableNote($flag) {
        $this->setEnable(self::NoteType,$flag);
    }

    public function setEnableTrace($flag) {
        $this->setEnable(self::TraceType,$flag);
    }

    public function setEnableWarning($flag) {
        $this->setEnable(self::WarningType,$flag);
    }

    /// Set the current language code (for prefixes).
    public function setLangCode($langCode) {
        $this->langCode = $langCode;
    }

    /// Write immediately to /dev/tty if debug enabled (and tty available).
    public function writeToTty($msg) {
        if(!$this->getEnableDebug()) { return false; }
        $tty = fopen("/dev/tty","a");
        if($tty===false) { return false; }
        $bytes = fwrite($tty,$msg);
        if($bytes===false) {
            fclose($tty);
            return false;
        }
        $status = fclose($tty);
        if($status===false) { return $status; }
        return $bytes;
    }
}
?>
