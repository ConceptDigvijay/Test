<?php
/**
 * Marathi Number to Words Conversion
 * Converts numbers to Marathi words for receipts
 */

class MarathiNumberConverter {
    
    private static $ones = array(
        '', 'एक', 'दोन', 'तीन', 'चार', 'पाच', 'सहा', 'सात', 'आठ', 'नऊ',
        'दहा', 'अकरा', 'बारा', 'तेरा', 'चौदा', 'पंधरा', 'सोळा', 'सतरा', 'अठरा', 'एकोणीस'
    );
    
    private static $tens = array(
        '', '', 'वीस', 'तीस', 'चाळीस', 'पन्नास', 'साठ', 'सत्तर', 'ऐंशी', 'नव्वद'
    );
    
    private static $hundreds = array(
        '', 'शंभर', 'दोनशे', 'तीनशे', 'चारशे', 'पाचशे', 'सहाशे', 'सातशे', 'आठशे', 'नऊशे'
    );
    
    public static function convertToWords($number) {
        if ($number == 0) {
            return 'शून्य';
        }
        
        $number = (int)$number;
        $result = '';
        
        // Handle crores (10,000,000)
        if ($number >= 10000000) {
            $crores = (int)($number / 10000000);
            $result .= self::convertTwoDigit($crores) . ' कोटी ';
            $number %= 10000000;
        }
        
        // Handle lakhs (100,000)
        if ($number >= 100000) {
            $lakhs = (int)($number / 100000);
            $result .= self::convertTwoDigit($lakhs) . ' लाख ';
            $number %= 100000;
        }
        
        // Handle thousands (1,000)
        if ($number >= 1000) {
            $thousands = (int)($number / 1000);
            $result .= self::convertThreeDigit($thousands) . ' हजार ';
            $number %= 1000;
        }
        
        // Handle hundreds
        if ($number >= 100) {
            $hundreds = (int)($number / 100);
            $result .= self::$hundreds[$hundreds] . ' ';
            $number %= 100;
        }
        
        // Handle remaining two digits
        if ($number > 0) {
            $result .= self::convertTwoDigit($number);
        }
        
        return trim($result) . ' रुपये मात्र';
    }
    
    private static function convertTwoDigit($number) {
        if ($number < 20) {
            return self::$ones[$number];
        }
        
        $tens = (int)($number / 10);
        $ones = $number % 10;
        
        $result = self::$tens[$tens];
        if ($ones > 0) {
            $result .= self::$ones[$ones];
        }
        
        return $result;
    }
    
    private static function convertThreeDigit($number) {
        $result = '';
        
        if ($number >= 100) {
            $hundreds = (int)($number / 100);
            $result .= self::$hundreds[$hundreds] . ' ';
            $number %= 100;
        }
        
        if ($number > 0) {
            $result .= self::convertTwoDigit($number);
        }
        
        return trim($result);
    }
}
?>