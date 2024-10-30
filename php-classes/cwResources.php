<?php

class cwResources {
    
    /**
     * @param string|number $str
     * @param string $charCase
     * @param string $allowedSpecialChars
     * @param string $newSeparator
     * @param string $removeRepeatedChars
     *
     * @return string
     *
     * Parameter $allowedSpecialChars
     * - If null or empty string, only numbers and english letters will be allowed
     * - Can consist of several chars. All will be allowed by themself regardless of order
     * - First char is considered standard separator and all none allowed separators will be replaced by this char
     * - Problematic chars like "'/\ should normaly not be allowed
     *
     * Empty values, e.g. NULL, 0, "0" or "", always returns an empty String ("")
     */


 

    public static function washCode( $str, $charCase='lower', $allowedSpecialChars='_-.', $newSeparator='firstAllowedSpecialChar', $removeRepeatedChars=false, $returnEmptyStringIfAllEmptyChars=true) {
        
        if (!isset($str)) {
            return '';
        }
        if (!is_scalar($str)) {
            return '';
        }
        $str = trim((string) $str);
        if ($returnEmptyStringIfAllEmptyChars && empty($str)) {
            return '';
        }
        
        if (!isset($allowedSpecialChars)) {
            $allowedSpecialChars = '';
        }
        $sepArr = str_split($allowedSpecialChars);
        
        if ( $newSeparator == 'firstAllowedSpecialChar' ) {
            $sep = isset( $sepArr[0] ) ? $sepArr[0] : '';
        } else {
            $sep = $newSeparator;
        }
        
        
        // Replace common separators with supplied default separator
        $commonSeparators = array( "\t", "\n", "\r", " ", "_", "-", ",", ";",":", "@" );
        foreach ( $commonSeparators as $cs ) {
            if ( !in_array( $cs, $sepArr ) ) {
                $str = str_replace( $cs, $sep, $str );
            }
        }
        
        // Replace common chars with closest counterparts in standard (English) 7-bit ASCII
        $specChars = array(
            'Å'=>'A', 'Ä'=>'A', 'Á'=>'A', 'À'=>'A', 'Ö'=>'O', 'Ü'=>'U', 'Æ'=>'A', 'É'=>'E', 'È'=>'E',
            'å'=>'a', 'ä'=>'a', 'á'=>'a', 'à'=>'a', 'ö'=>'o', 'ü'=>'u', 'æ'=>'a', 'é'=>'e', 'è'=>'e',
        );
        foreach ( $specChars as $specChar=>$replacementChar ) {
            $str = str_replace( $specChar, $replacementChar, $str );
        }
        
        // Keep only allowed chars (ignore all other)
        $charArr = str_split( $str );
        $str = '';
        foreach( $charArr as $char ) {
            // Check first if char is included in $allowedSpecialChars. If so add to new string and continue
            if ( in_array( $char, $sepArr ) ) {
                $str.= $char;
                continue;
            }
            // Next check if char is a number or an english letter (a..z)
            $a = ord( $char );
            if ( $a >= 48 && $a <=  57 ) $str.= $char; // Chars 0..9
            if ( $a >= 65 && $a <=  90 ) $str.= $char; // Chars A..Z
            if ( $a >= 97 && $a <= 122 ) $str.= $char; // Chars a..z
        }
        
        if ($returnEmptyStringIfAllEmptyChars && empty($str)) {
            return '';
        }
        
        if ( $sep > '' ) {
            
            // Replace repeated separators by a single separator
            while ( mb_substr_count( $str, $sep.$sep ) > 0 ) {
                $str = str_replace( $sep.$sep, $sep, $str );
            }
            
            // We might have ended up with only a single separator left
            if ($str == $sep) {
                return '';
            }
            
            // Do not allow string to start with a separator
            while ( mb_substr( $str, 0, 1 ) == $sep ) {
                $str = mb_substr( $str, 1 );
            }
            
            // Do not allow string to end with a separator
            while ( mb_substr( $str, strlen( $str ) - 1, 1 ) == $sep ) {
                $str = mb_substr( $str, 0, strlen( $str ) - 1 );
            }
        }
        
        if ( $charCase == 'upper' ) $str = mb_strtoupper( $str );
        if ( $charCase == 'lower' ) $str = mb_strtolower( $str );
        
        if ($removeRepeatedChars) {
            $charArr = str_split( $str );
            $str = '';
            $lastChar = '';
            foreach($charArr as $char) {
                if ($char != $lastChar) {
                    $str.= $char;
                }
                $lastChar = $char;
            }
        }
        
        if ($returnEmptyStringIfAllEmptyChars && empty($str)) {
            return ''; // We might have ended upp with just "0" or other none allowed empty value
        }
        
        return $str;
    }


    private function encryptPrivate($data = "", $encryptDecrypt = 'e'){
		// Set default output value
		$output = null;
		// Set secret keys
		$secret_key = 'da1nasf01a8g^3*s'; // Change this!
		$secret_iv = 'vca123123id1adf0lad'; // Change this!
		$key = hash('sha256',$secret_key);
		$iv = substr(hash('sha256',$secret_iv),0,16);
		// Check whether encryption or decryption
		if($encryptDecrypt == 'e'){
		   // We are encrypting
		   $output = base64_encode(openssl_encrypt($data,"AES-256-CBC",$key,0,$iv));
		} else if($encryptDecrypt == 'd'){
		   // We are decrypting
		   $output = openssl_decrypt(base64_decode($data),"AES-256-CBC",$key,0,$iv);
		}
		// Return the final value
		return $output;
   }

   public function encrypt($data, $encryptDecrypt) {
       return $this->encryptPrivate($data, $encryptDecrypt);
   }


    
}

?>