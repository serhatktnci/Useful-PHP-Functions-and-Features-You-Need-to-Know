<?php
/**
 * Created by PhpStorm.
 * User: serhat.ketenci
 * Date: 11/18/2016
 * Time: 8:38 PM
 */

class Html extends CHtml {

    public static function encode($text) {
        return static::fixChars(parent::encode($text));
    }

    public static function fixChars($text) {
        return str_replace( [
            "Â°", "\t", "Ã¢", "&rsquo;", "&Uuml;", "&uuml;", "&#351;", "&#350;", "&Ouml;", "&ouml;", "&#305;", "&#304;", "&#287;", "&#286;", "&ccedil;", "&Ccedil;", "Ã‡", "Ã§", "ÄŸ", "ÅŸ", "Ä±", "Ã¶", "%E7", "%FD", "%C7", "%F0", "%FE", "Ã¼", "Å", "Ä°", "Ã–", "â€œ", "â€", "Ãœ", "Ä", "Ã®", "'", "\"", "€™", "â€˜", "â", "|"
        ], [
            "°", "", "", "", "", "", "â", "", "Ü", "ü", "ş", "Ş", "Ö", "ö", "ı", "İ", "ğ", "Ğ", "ç", "Ç", "Ç", "ç", "ğ", "ş", "ı", "ö", "ç", "ı", "Ç", "ğ", "ş", "ü", "Ş", "İ", "Ö", "\"", "\"", "Ü", "Ğ", "î", "", "", "", "", "", ""
        ], $text);
    }

}