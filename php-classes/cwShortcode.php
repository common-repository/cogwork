<?php
require_once (CW_PHP_CLASSES_DIR . 'cwConnector.php');

class cwShortcode extends cwConnector
{
    //Accepted languages for cw shortcode
    private static $supportedLanguages = [
        'sv',
        'en',
        'fi',
        'es'
    ];

    public function __construct($attributes, $content, $tag)
    {
        parent::__construct();

        if (! is_array($attributes)) {
            $attributes = [];
        }

        // For backward compability with older installations (prior to cogwork 1.0)
        if (! empty($tag) && mb_strtolower(trim((string) $tag)) == 'cwshop') {
            $attributes['type'] = 'shop';
        }

        $this->parseShortCodeAttributes($attributes);
    }

    //Set language for cw shortcode
    static private function getDefaultLanguage()
    {
        //Get language set in WordPress editor
        if (function_exists('get_locale')) {
            $wordpressLanguage = self::extractPrimaryLanguage(get_locale());
            if (! empty($wordpressLanguage)) {
                return $wordpressLanguage;
            }
        }
        //Get language set on server
        if (! empty($_SERVER["HTTP_ACCEPT_LANGUAGE"])) {
            $serverLang = self::extractPrimaryLanguage($_SERVER["HTTP_ACCEPT_LANGUAGE"]);
            if (! empty($serverLang)) {
                return $serverLang;
            }
        }

        return "";
    }
     //Get the language with the highest q value in string and also shorten the language code to the two first chars
    static private function extractPrimaryLanguage($inputlanguage)
    {
        $language = "";

        if (! (strlen($inputlanguage) > 1)) {
            return '';
        }

        // Split possible languages into array
        $x = explode(",", $inputlanguage);
        foreach ($x as $val) {
            // check for q-value and create associative array. No q-value means highest q-value 1 by language standards
            if (preg_match("/(.*);q=([0-1]{0,1}.\d{0,4})/i", $val, $matches))
                $lang[$matches[1]] = (float) $matches[2];
            else
                $lang[$val] = 1.0;
        }

        // return language with highest q-value if equal q-value the first value with highest q-value will be selected
        $qval = 0.0;
        foreach ($lang as $key => $value) {
            if ($value > $qval) {
                $qval = (float) $value;
                $langCode = mb_substr($key, 0, 2);
                if (in_array($langCode, self::$supportedLanguages)) {
                    $language = $langCode;
                }
            }
        }

        return $language;
    }

    private function parseShortCodeAttributes($attributes)
    {
        if (count($attributes) == 0) {
            return;
        }

        $this->params['new'] = null; // Does not require a value

        foreach ($attributes as $attributeName => $attributeValue) {

            $strKey = trim((string) $attributeName);
            $strVal = trim((string) $attributeValue);

            $strKeyLow = mb_strtolower($strKey);
            $strValLow = mb_strtolower($strVal);

            if ($strKeyLow == 'org') {
                $this->orgCode = $strValLow;
                continue;
            }

            if ($strKeyLow == 'type') {
                $this->contentType = $strValLow;
                continue;
            }

            if ($strKeyLow == 'protocol') {
                if (in_array($strValLow, array(
                    'http',
                    'https'
                ))) {
                    $this->protocol = $strValLow;
                }
                continue;
            }

            if ($strKeyLow == 'host') {
                $this->host = $strValLow;
                continue;
            }

            if (is_numeric($strKeyLow) && empty($this->contentType)) {
                $this->contentType = $strValLow;
                continue;
            }

            $this->params[$strKey] = $strVal;
        }
         //Set language for cw shortcode
        if (empty($this->params['lang'])) {
            $this->params['lang'] = self::getDefaultLanguage();
        }

        // Code to forward request parameters from the local php server to the shop api
        // If you do not want to include a search form you can skip the next lines below

        // TODO: Do we use and need 'localUrl' on the server side?
        $localUrl = (! empty($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
        $this->params['localUrl'] = $localUrl;

        // TODO: $_REQUEST contains cookies. Do we need cookie info here? Possibly for Google Adwords etc. If not, use only $_GET and $_POST
        // $localRequest = array();
        // if (isset($_GET) && count($_GET) > 0) {
        // $localRequest = array_merge($localRequest, $_GET);
        // }
        // if (isset($_POST) && count($_POST) > 0) {
        // $localRequest = array_merge($localRequest, $_POST);
        // }
        // $this->params['localJsonEncodedRequest'] = json_encode($localRequest);

        $this->params['localJsonEncodedRequest'] = json_encode($_REQUEST);
    }
}
?>