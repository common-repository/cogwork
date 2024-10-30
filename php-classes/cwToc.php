<?php

class cwToc {

	public static function childPagesList($options) {
        
	   $params = $options['params'];
       $flags = $options['flags'];

       //Create variabel for if flags exist so only have to check once.
	   $flagsexists = is_array($flags);
        
        //Default pageid variabel is current page id    
        $pageId = get_the_ID();
            
        //Show all pages on website by setting pageid to 0
        if($flagsexists && in_array("sitemap", $flags)) {
             $pageId = 0;
        } 

        //Show childpages of a specific pages by getting id from searchpath
        elseif(!empty($params['url'])) {
            $urlId = url_to_postid("/" . $params['url']);
            
            //url_to_postid returns 0 on failure so in that case return
            if($urlId == 0) {
                return;
            }

            $pageId = $urlId;
        }    
        
        //By default show list of published pages
        $arrayPostStatus = array("publish"); 

        //Show instead list of private pages
        if($flagsexists && in_array("private", $flags)) {
            $arrayPostStatus = array("private");         
        }    
               
        //Get childpages from id
        $childPages = wp_list_pages(array('echo'=>false, 'child_of'=> $pageId, 'post_status'=>$arrayPostStatus, 'title_li'=>'','sort_column'  => 'menu_order',));
        
        //If no childpages return
		if(empty($childPages)) {
			return;
		}
			
        //Add childpages to ul list         	
        $html = "<ul>". $childPages . "</ul>";
				
        //Set headertext default Undersidor
        $headerText = "Undersidor";

        if(!empty($params['header'])) {
             $headerText = $params['header'];
        }     
                	
        	        
        //Add containing elements
        if($flagsexists && in_array("fieldset", $flags)) {
            $html = '<div><fieldset class="cwFieldsetSubpages">' .
               "<legend>" . $headerText . "</legend>" .
               $html . "</fieldset></div>";          
        }
        elseif($flagsexists && in_array("div", $flags))  {
            $html = "<div class='cwIndexContainer'><h3>" . $headerText . "</h3>"
                . $html . "</div>";
        } 
  
        return $html;
           
    
    }  
}
    
?>