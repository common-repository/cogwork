<?php

class cwService
{

    /*
     * Values for cwDomain and userDomain are retrived from the cwCore.php in php-classes.
     */
    
    public static function addServiceInfo($options)
    {
        $flags = $options['flags'];

        $countflags = count($flags);

        // return if no flags exists
        if (! is_array($flags) && ! $countflags > 0) {
            return "";
        }

        // default values
        $name = cwCore::userDomainName();
        $topDomain = "minaaktiviteter.se";
        $mail = "info@cogwork.se";
        $params = $options['params'];
        $cwDomain = cwCore::userDomain();

        $url = "https://" .  $cwDomain . "/";

        $text = "";

        /*
         * For loop is used so the user can decide
         * what order they want the values.
         */
        for ($i = 0; $i < $countflags; $i ++) {

            // Add space before text from second and onwards.
            if ($i > 0) {
                $text .= ", ";
            }

            switch ($flags[$i]) {
                case "name":
                    $text .= $name;
                    break;
                case "link":
                    $text .= '<a href="' . $url . '"';
                    $text .= ' target="_blank">';
                    $text .= $name;
                    $text .= '</a>';
                    break;
                case "url":
                    $text .= '<a href="' . $url . '"';
                    $text .= ' target="_blank">';
                    $text .= $url;
                    $text .= '</a>';
                    break;
                case "urltext":
                    $text .= $url;
                    break;
                case "email":
                    $text .= '<a href="mailto:' . $mail . '"';
                    $text .= '>';
                    $text .= $mail;
                    $text .= '</a>';
                    break;                                           
            }
        }

        return $text;
    }
}

?>