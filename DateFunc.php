<?php
/**
 * Created by PhpStorm.
 * User: serhat.ketenci
 * Date: 10/27/16
 * Time: 7:57 PM
 */

class DateFunc {

    public static function fixDatePhrases($date) {
        $find = array(
            'Jan', 'Feb', 'Mar', 'Apr', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec',
            'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'
        );
        $fix = array(
            Yii::t('date', 'Jan'), Yii::t('date', 'Feb'), Yii::t('date', 'Mar'), Yii::t('date', 'Apr'), Yii::t('date', 'Jun'), Yii::t('date', 'Jul'), Yii::t('date', 'Aug'), Yii::t('date', 'Sep'), Yii::t('date', 'Oct'), Yii::t('date', 'Nov'), Yii::t('date', 'Dec'),
            Yii::t('date', 'January'), Yii::t('date', 'February'), Yii::t('date', 'March'), Yii::t('date', 'April'), Yii::t('date', 'May'), Yii::t('date', 'June'), Yii::t('date', 'July'), Yii::t('date', 'August'), Yii::t('date', 'September'), Yii::t('date', 'October'), Yii::t('date', 'November'), Yii::t('date', 'December')
        );

        return str_replace($find, $fix, $date);
    }

    public static function getFormattedDate($date, $formatType = 'dateFormat') {
        return DateFunc::fixDatePhrases(date(Yii::app()->params[$formatType], strtotime($date)));
    }
} 