<?php

class cwLink
{

    /*
         Values for cwDomain and userDomain are retrived from the cwCore.php in php-classes     
    */
	

    public static function addAdminLink($options)
    {
		  
        $params = $options['params'];
        $flags = $options['flags'];
        $flagsexists = is_array($flags);
  
        // return if no url parameter
        if (empty($params['url'])) {
            return '';
        }
        
        $paramsUrl = $params['url'];

        // change topdomain to cwDomain option if parameter exists
         $topUrl = 'https://' . cwCore::userDomain() . '/';

        // Replace /index.php with / in the url 
        $pathUrl = str_replace('/index.php', '/', $paramsUrl);

        $url = $topUrl . $pathUrl;
		
        //Add organisation code to link if value exists
        $userDomain = cwCore::userOrgCode();
        if (! empty($userDomain)) {
           //Check if url already have query string 
            if (strpos($url, '?')) {
                $url .= "&org=" .  $userDomain;
            }            
            else {
                $url .= "?org=" .  $userDomain;
            }
        }		

        // Default text is the url
        $textContent = $url;

        // Set text to text parameter if parameter exists
        if (! empty($params['text'])) {
            $textContent = $params['text'];
        }

        $classes = array();

        // Set classes if classes parameter is specified
        if (! empty($params['classes'])) {
            $classes[] = $params['classes'];
        } 
        // Else set inline class if flag exist
        elseif ($flagsexists && in_array('inline', $flags)) {
            $classes[] = 'cwLinkInline';
        } 
        
        // Default set classes that in css create iconlink
        else {
            $classes[] = 'cwSmallIconLeft';
            $classes[] = 'cwIconService';
        }

        // WP always stores text with html-chars so we most not use htmlspecialchars() here
        $html = '<a href="' . $url . '" ';
        if (count($classes) > 0) {
            $html .= 'class="' . implode(' ', $classes) . '" ';
        }

        // Set link title
        if (! empty($params['info'])) {
            $html .= 'title="' . $params['info'] . '" ';
        }

        // pages that require login should have nofollow for search bots
        if ($flagsexists && in_array('logedin', $flags)) {
            $html .= 'rel="nofollow" ';
        }

        // Open links in new windwow
        $html .= 'target="_blank">';

        // Set textcontent and then addd closing tag.
        $html .= $textContent;
        $html .= '</a>';

        // Add login required text after link
        if ($flagsexists && in_array('logedin', $flags)) {

            $html .= " (inloggning krävs)";
        }

        // Add contanaing elements if flags exist
        if ($flagsexists && in_array('p', $flags)) {
            $html = "<p>" . $html . "</p>";
        }

        if ($flagsexists && in_array('div', $flags)) {
            $html = "<div>" . $html . "</div>";
        }

        return $html;
    }
}

?>