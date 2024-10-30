<?php

class cwShortCodeProcessor
{
   private static function transformShortcodeOptions($attributes, $content, $tag)
   
    {
        $options = array(
            'tag' => mb_strtolower(trim((string) $tag)),
            'params' => array(),
            'flags' => array(),
            'debug' => false
        );

        if ((is_array($attributes) && count($attributes) > 0)) {

            foreach ($attributes as $attributeName => $attributeValue) {

                $key = mb_strtolower(trim((string) $attributeName));
                $val = trim((string) $attributeValue);

                // If no value is assign to an attribute it will show up in $attributes with a numeric index
                if (is_numeric($key)) {

                    if (mb_strtolower($val) == 'debug') {
                        $options['debug'] = true;
                        continue;
                    }

                    if (! in_array($key, $options['flags'])) {
                        $options['flags'][] = $val;
                    }
                    continue;
                }

                $options['params'][$key] = $val;
            }
            
            return $options;            
        } 
        
        else {
            
            return $options;
        }
    }

    public static function process($attributes, $content, $tag)
    {
        $options = self::transformShortcodeOptions($attributes, $content, $tag);

        // Note that $options['tag'] is always lowercase
        switch ($options['tag']) {

            case 'cw':
            case 'cwshop': // For backward compability only
                require_once (CW_PHP_CLASSES_DIR . 'cwShortcode.php');
                $shortcode = new cwShortcode($attributes, $content, $tag);
                $html = $shortcode->getHtmlContent();
                break;

            case 'cwlink':
                require_once (CW_PHP_CLASSES_DIR . 'cwLink.php');
                $html = cwLink::addAdminLink($options);
                break;

            case 'cwchildpages':
            case 'cwtoc': // For backward compability only
                require_once (CW_PHP_CLASSES_DIR . 'cwToc.php');
                $html = cwToc::childPagesList($options);
                break;

            case 'cwservice':
                require_once (CW_PHP_CLASSES_DIR . 'cwService.php');
                $html = cwService::addServiceInfo($options);
                break;

            default:
                $html = '';
        }

        if (! empty($options['debug'])) {

            $html .= "\n" . '<pre>$attributes = ' . print_r($attributes, true) . '</pre>';
            $html .= "\n" . '<pre>$content = ' . print_r($content, true) . '</pre>';
            $html .= "\n" . '<pre>$tag = ' . print_r($tag, true) . '</pre>';
            $html .= "\n" . '<pre>$options = ' . print_r($options, true) . '</pre>';
        }

        return $html;
    }
}
?>