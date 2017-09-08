<?php
/// Source code file for the LongCite:: class.
/// @copyright Copyright (c) 2017, Eric Alan Christiansen.\n
/// MIT License. See <https://opensource.org/licenses/MIT>.
/// @file
### Note: This file uses Uses doxygen style annotation comments.
### Note: This file possibly includes some PHPUnit comment directives.

/// Class for static LongCite options.
class LongCite {
    const DebugOption = "debug";
    const TraceOption = "trace";

    protected static $options = array(
        self::DebugOption => false,
        self::TraceOption => false
    ); ///< General LongCite options.

    public static function setOption($option,$value) {
        if(!array_key_exists($option,self::$options)) {
            trigger_error("Bad LongCite local option name ($option).");
            return false;
        }
        if(in_array($option,array(self::DebugOption,self::TraceOption))) {
            if(is_bool($value)) {
                self::$options[$option] = $value;
            } else if(is_string($value)) {
                $str = strtolower($value);
                if(in_array($str,array("true","t","yes","y","1"))) {
                    self::$options[$option] = true;
                } elseif(in_array($str,array("false","f","no","n","0"))) {
                    self::$options[$option] = false;
                } else {
                    trigger_error("Bad LongCite local $option value ($value).");
                    return false;
                }
            } else {
                trigger_error("Bad LongCite local $option value.");
                return false;
            }
        } else {
            self::$options[$option] = $value;
        }
        return true;
    }

    public static function getOption($option) {
        if(!array_key_exists($option,self::$options)) {
            trigger_error("Bad LongCite local option name ($option).");
            return false;
        }
        return self::$options[$option];
    }

}
?>
