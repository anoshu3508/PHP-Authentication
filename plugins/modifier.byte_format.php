<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     modifier.byte_format.php
 * Type:     modifier
 * Name:     byte_format
 * Purpose:  数値をバイト表示にフォーマット
 * -------------------------------------------------------------
 */
function smarty_modifier_byte_format($number) {
    return ByteUnits\bytes($number)->format();
}