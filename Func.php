<?php
/**
 * Created by PhpStorm.
 * User: serhat.ketenci
 * Date: 11/18/2016
 * Time: 8:13 PM
 */

class Func {

    public static function urlTitle($title, $space = '-') {

        if (empty($space)) $space = '-';

        $title = str_replace( [
            'Ç', 'ç', 'Ğ', 'ğ', 'I', 'ı', 'İ', 'Ö', 'ö', 'Ş', 'ş', 'Ü', 'ü'
        ], [
            'c', 'c', 'g', 'g', 'i', 'i', 'i', 'o', 'o', 's', 's', 'u', 'u'
        ], $title);

        if (function_exists('iconv')) {
            $title = @iconv('UTF-8', 'ASCII//TRANSLIT', $title);
        }

        $title = preg_replace("/[^a-zA-Z0-9 -]/", "", $title);
        $title = mb_strtolower($title);
        $title = str_replace(" ", $space, $title);

        return trim($title, $space);
    }

    public static function textToUtf8($title) {

        if (function_exists('iconv')) {
            $title = iconv(mb_detect_encoding($title, mb_detect_order(), true), "UTF-8", $title);
            //$title = @iconv('UTF-8', 'ASCII//TRANSLIT', $title);
        }

        return trim($title);
    }

    public static function moneyFormat($money, $loadingZero = 2, $division = ',', $thousandDivision = '.') {
        return number_format($money, $loadingZero, $division, $thousandDivision);
    }

    public static function phoneFormat($string)
    {
        $international = (mb_substr($string, 0, 2) == '00' || mb_substr($string, 0, 1) == '+') ? true:false;
        $number = trim(preg_replace('#[^0-9]#s', '', $string));

        $match = true;

        $length = strlen($number);
        if ($length == 11 && mb_substr($number, 0, 1) == '0') {
            $regex = '/([0]{1})([0-9]{3})([0-9]{3})([0-9]{2})([0-9]{2})/';
            $replace = '$1 ($2) $3 $4 $5';
        } elseif($length == 10 && mb_substr($number, 0, 1) != '0') {
            $regex = '/([0-9]{3})([0-9]{3})([0-9]{2})([0-9]{2})/';
            $replace = '0 ($1) $2 $3 $4';
        }/* elseif($length == 11) {
            $regex = '/([0-9]{1})([0-9]{3})([0-9]{3})([0-9]{4})/';
            $replace = '$1 ($2) $3-$4';
        } */else {
            $match = false;
        }

        $return = ($match) ? preg_replace($regex, $replace, $number):$number;

        if ($international) {
            $return = '+'.$return;
        }

        return $return;
    }

    public static function sendMail($to, $toName, $subject, $message, $options = []) {

        if (!is_array($options)) $options = [];

        if (!filter_var($to, FILTER_VALIDATE_EMAIL)) return false;

        //$s = explode('@', $to);

        Yii::import('application.extensions.phpmailer.JPhpMailer');
        $mail = new JPhpMailer();
        $mail->IsSMTP();
      

            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth=true;

            $mail->Username = 'username';
            $mail->Password = 'password';
            $mail->SMTPSecure = "tls";

            $mail->Port=587;
            $mail->SetFrom('sendmailusername', Yii::app()->name);

        //}

        $mail->SMTPKeepAlive=true;
        $mail->CharSet='utf-8';
        $mail->SMTPDebug = 0;

        $mail->Subject=$subject;
        $mail->MsgHTML($message);
        $mail->AddAddress($to,$toName);

        if (isset($options['replayto']) && mb_strlen($options['replayto']) > 0 && filter_var($options['replayto'], FILTER_VALIDATE_EMAIL)) {
            $replayToName = (isset($options['replaytoname']) && mb_strlen($options['replaytoname']) > 0) ? $options['replaytoname']:$options['replayto'];
            $mail->AddReplyTo($options['replayto'],$replayToName);
        }

        if (isset($options['cc']) && mb_strlen($options['cc']) > 0 && filter_var($options['cc'], FILTER_VALIDATE_EMAIL)) {
            $ccName = (isset($options['ccname']) && mb_strlen($options['ccname']) > 0) ? $options['ccname']:$options['cc'];
            $mail->AddCC($options['cc'],$ccName);
        }

        if (isset($options['bcc']) && mb_strlen($options['bcc']) > 0 && filter_var($options['bcc'], FILTER_VALIDATE_EMAIL)) {
            $bccName = (isset($options['bccname']) && mb_strlen($options['bccname']) > 0) ? $options['bccname']:$options['bcc'];
            $mail->AddBCC($options['bcc'],$bccName);
        }

        return $mail->Send();
                
    }


    public static function generateID($length = 25) {
        $chars = '_-abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ_-abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $len = strlen($chars);
        $maketxt = '';

        $stat = @stat(__FILE__);
        if(empty($stat) || !is_array($stat)) $stat = array(php_uname());

        mt_srand(crc32(microtime() . implode('|', $stat)));

        for ($i = 0; $i < $length; $i ++) {
            $maketxt .= $chars[mt_rand(0, $len -1)];
        }

        return $maketxt;
    }

    public static function getRealIP() {
        if (isset($_SERVER["HTTP_CLIENT_IP"])) {
            return $_SERVER["HTTP_CLIENT_IP"];
        } elseif (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
            return $_SERVER["HTTP_X_FORWARDED_FOR"];
        } elseif (isset($_SERVER["HTTP_X_FORWARDED"])) {
            return $_SERVER["HTTP_X_FORWARDED"];
        } elseif (isset($_SERVER["HTTP_FORWARDED_FOR"])) {
            return $_SERVER["HTTP_FORWARDED_FOR"];
        } elseif (isset($_SERVER["HTTP_FORWARDED"])) {
            return $_SERVER["HTTP_FORWARDED"];
        } else {
            return $_SERVER["REMOTE_ADDR"];
        }
    }

    public static function getShortName($input, $length = 30, $ellipses = true, $strip_html = true) {

        //strip tags, if desired
        if ($strip_html) {
            $input = strip_tags(preg_replace("/&#?[a-z0-9]{2,8};/i","", preg_replace('/<(pre)(?:(?!<\/\1).)*?<\/\1>/s','',$input)));
        }

        //no need to trim, already shorter than trim length
        if (strlen($input) <= $length) {
            return $input;
        }

        //find last space within length
        $last_space = strrpos(substr($input, 0, $length), ' ');
        $trimmed_text = substr($input, 0, $last_space);

        //add ellipses (...)
        if ($ellipses) {
            $trimmed_text .= '...';
        }

        return $trimmed_text;
    }

    /**
     * Return data to browser as JSON
     * @param array $data
     */
    public static function renderJSON($data)
    {
        header('Content-type: application/json');
        print_r( CJSON::encode($data) );

        foreach (Yii::app()->log->routes as $route) {
            if($route instanceof CWebLogRoute) {
                $route->enabled = false; // disable any weblogroutes
            }
        }
        Yii::app()->end();
    }

    public static function getQRCode($url,$width=100,$height=100) {
        return 'https://chart.googleapis.com/chart?chs='.$width.'x'.$height.'&cht=qr&chl='.CHtml::normalizeUrl($url).'&choe=UTF-8';
    }

    // Time format is UNIX timestamp or
    // PHP strtotime compatible strings
    public static function dateDiff($time1, $time2, $precision = 6) {
        // If not numeric then convert texts to unix timestamps
        if (!is_int($time1)) {
            $time1 = strtotime($time1);
        }
        if (!is_int($time2)) {
            $time2 = strtotime($time2);
        }

        // If time1 is bigger than time2
        // Then swap time1 and time2
        if ($time1 > $time2) {
            $ttime = $time1;
            $time1 = $time2;
            $time2 = $ttime;
        }

        // Set up intervals and diffs arrays
        $intervals = array('year','month','day','hour','minute','second');
        $diffs = array();

        // Loop thru all intervals
        foreach ($intervals as $interval) {
            // Create temp time from time1 and interval
            $ttime = strtotime('+1 ' . $interval, $time1);
            // Set initial values
            $add = 1;
            $looped = 0;
            // Loop until temp time is smaller than time2
            while ($time2 >= $ttime) {
                // Create new temp time from time1 and interval
                $add++;
                $ttime = strtotime("+" . $add . " " . $interval, $time1);
                $looped++;
            }

            $time1 = strtotime("+" . $looped . " " . $interval, $time1);
            $diffs[$interval] = $looped;
        }

        $count = 0;
        $times = array();
        // Loop thru all diffs
        foreach ($diffs as $interval => $value) {
            // Break if we have needed precission
            if ($count >= $precision) {
                break;
            }
            // Add value and interval
            // if value is bigger than 0
            if ($value > 0) {
                // Add s if value is not 1
                if ($value != 1) {
                    $interval .= "s";
                }
                // Add value and interval to times array
                $times[] = $value . " " . $interval;
                $count++;
            }
        }

        // Return string with times
        return implode(", ", $times);
    }

    function RelativeTime($timestamp){
        $difference = time() - $timestamp;
        $periods = array("sec", "min", "hour", "day", "week",
            "month", "years", "decade");
        $lengths = array("60","60","24","7","4.35","12","10");

        if ($difference > 0) { // this was in the past
            $ending = "ago";
        } else { // this was in the future
            $difference = -$difference;
            $ending = "to go";
        }
        for($j = 0; $difference >= $lengths[$j]; $j++)
            $difference /= $lengths[$j];
        $difference = round($difference);
        if($difference != 1) $periods[$j].= "s";
        $text = "$difference $periods[$j] $ending";
        return $text;
    }
}