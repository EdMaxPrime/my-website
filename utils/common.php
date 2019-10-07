<?php 

namespace Max;

//https://stackoverflow.com/questions/1233290/making-sure-php-substr-finishes-on-a-word-not-a-character
function substr_word_wrap($string, $max_num_chars) {
    if(strlen($string) > $max_num_chars) {
        $s = substr($string, 0, $max_num_chars + 1);
        return substr($s, 0, strrpos($s, ' '));
    } else {
        return $string;
    }
}

//Given a timestring in UTC/GMT time (as per SQLite standard), will return something like "5:45pm on 24th March 2012"
function nyTime($timeString) {
    $date = new \DateTime($timeString, new \DateTimeZone("GMT"));
    return $date->setTimezone(new \DateTimeZone("America/New_York"))->format('g:ia \o\n jS F Y');
}

//Given a timestring in UTC/GMT time (as per SQLite standard), will return something like "March 24th 2012"
function nyDate($timeString) {
    $date = new \DateTime($timeString, new \DateTimeZone("GMT"));
    return $date->setTimezone(new \DateTimeZone("America/New_York"))->format('F jS Y');
}

/* 
Converts file size in bytes into a human readable string like "5.88 MB". Only goes up to Terabytes. Uses base 1024, not 1000
@param size       the number of bytes in the file
@param precision  if this is positive, then it specifies the number of places after the decimal point. If negative, it specifies how many significant digits before the decimal point
@return           a string comrpised of the number, a space, then the unit
*/
function formatFileSize($size, $precision = 2) {
    $base = log(floatval($size)) / log(1024); //take logarithm base 1024 of the size in bytes to determine which unit to use
    $suffix = array("B", "KB", "MB", "GB", "TB");
    return round(pow(1024, $base - floor($base)), $precision) . " " . $suffix[floor($base)];
}

?>