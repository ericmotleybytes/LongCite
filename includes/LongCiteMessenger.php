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
   protected $messages     = array();  ///< Array of messages, each msg a hash array.
   protected $prefixMsgIds = array();  ///< Hash array of i18n ids, by msg type.
   protected $cssClasses   = array();  ///< Hash array of css classes, by msg type.
   protected $dumpFile = __DIR__."/../dump.out"; ///< Message dump file.

    /// Class instance constructor.
    public function __construct() {
        $this->prefixMsgIds = array(
            self::ErrorType   => "longcite-error",
            self::WarningType => "longcite-warning",
            self::NoteType    => "longcite-note",
            self::DebugType   => "longcite-debug"
        );
        $this->cssClasses = array(
            self::ErrorType   => "mw-longcite-error",
            self::WarningType => "mw-longcite-warning",
            self::NoteType    => "mw-longcite-note",
            self::DebugType   => "mw-longcite-debug"
        );
        $this->clearMessages();
    }

    /// Clear all messages from message buffer.
    public function clearMessages() {
        $this->messages = array();
    }

    /// Register a message in the message buffer.
    /// @param $msgType - String set to class constand for message type (ErrorType,
    ///  WarningType, NoteType, or DebugType).
    /// @param $msgText - String with the text of the message.
    public function registerMessage($msgType,$msgText) {
        $msgType = strtoupper($msgType);
        if(!array_key_exists($msgType,$this->prefixMsgIds)) {
            user_error("Invalid message type ($msgType)",E_USER_ERROR);
        } else {
            $msgId = $this->prefixMsgIds[$msgType];
            $prefix = wfMessage($msgId)->plain();
            #$msgText = strip_tags($msgText,"<b>");
            #$msgText = filter_var($msgText,FILTER_SANITIZE_STRING);
            #$msgText = htmlentities($msgText,ENT_QUOTES);
            $msg = array(
                "type"      => $msgType,
                "prefix"    => $prefix,
                "css-class" => $this->cssClasses[$msgType],
                "text"      => $msgText
            );
            $this->messages[] = $msg;
        }
    }

    /// Render all buffered messages for html.
    /// Does NOT automatically clear the buffer, use clearMessages().
    /// @returns A string with html for all buffered messages.
    public function renderMessagesHtml() {
        $dq = '"';  // a double-quote character.
        $result = "";
        foreach($this->messages as $message) {
            $cssClss = $message["css-class"];
            $prefix  = $message["prefix"];
            $text    = $message["text"];
            $text    = htmlentities($text,ENT_QUOTES);
            $result .= "<p class=$dq$cssClass$dq>";
            $result .= "$prefix: $text";
            $result .= "</p>\n";
        }
        return $result;
    }

    /// Render all buffered messages for text.
    /// Does NOT automatically clear the buffer, use clearMessages().
    /// @returns A string with html for all buffered messages.
    public function renderMessagesText() {
        $dq = '"';  // a double-quote character.
        $result = "";
        foreach($this->messages as $message) {
            $prefix  = $message["prefix"];
            $text    = $message["text"];
            $result .= "$prefix: $text\n";
        }
        return $result;
    }

    /// Dump messages to the dump file.
    /// @param $appendFlag - True to append to existing file, if any.
    /// returns true on success, else false.
    public function dumpToFile($appendFlag=true) {
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
}
?>
