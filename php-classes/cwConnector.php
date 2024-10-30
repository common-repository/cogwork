<?php

class cwConnector {


	protected static $jsonDataUrl = 'api/json/';
   
	protected $errors = array();

	protected $params = array(
			'org' => null,
			'contentType' => ''
	);

	protected $orgCode = 'demoklubben';
	protected $protocol = 'https';
	protected $host = 'minaaktiviteter';
	protected $apiToken = '';


	protected $contentType = '';
	protected $contentDataArr = null;

	private $contentLoaded = false;

	public function __construct() {
		$this->loadSettings();		
	}

	/**
	 * @return array
	 */
	public function toArray() {
		$arr = array();
		$arr['contentType'] = $this->contentType;
		$arr['orgCode'] = $this->orgCode;
		$arr['getJsonDataUrl()'] = $this->getJsonDataUrl();
		$arr['params'] = $this->params;
		return $arr;
	}

	/**
	 * Loads local plugin settings to object properties
	 */
	private function loadSettings() {

		$options = get_option('cogwork_option');

		if (!empty($options['orgCode'])) {
			$this->orgCode = $options['orgCode'];
		}

		if (!empty($options['websiteCode'])) {
			$this->host = $options['websiteCode'];
		}

		if (!empty($options['protocol'])) {
			$this->protocol = $options['protocol'];
		}

		if (!empty($options['apiToken'])) {
			$cwResources = new cwResources();
			$this->apiToken = $cwResources->encrypt($options['apiToken'], 'd');			
			
		}

		// Add compability with older version of the plugin by using unencrypted apiKey value for apiToken if it exist
		elseif (!empty($options['apiKey'])) {			
			$this->apiToken = $options['apiKey'];		
		}

	
	
	}

	/**
	 * @return array
	 */
	public function getContentDataArray() {

		$this->loadContentDataArray();

		return $this->contentDataArr;
	}

	/**
	 * @param string $contentType
	 */
	public function setContentType($contentType) {

		if (!is_scalar($contentType)) {
			return;
		}

		$this->contentType = trim((string) $contentType);
	}

	public function getHtmlContent() {	
         
		$this->loadContentDataArray();
		$dataArr = &$this->contentDataArr;

		$html = "";

		if($dataArr) {
		
			$html = "\n";
			$html.= "\n<!-- ";
			$html.= "\n    Content retrieved by CogWork API";
			$html.= "\n    Host = ".$this->host;
			$html.= "\n    Content type = " .$this->contentType;
			$html.= "\n    JSON Data URL = ".$this->getJsonDataUrl(false);
			$html.= "\n-->";

			// When we reached add_shortcode it is to late to use wp_enqueue_style and wp_enqueue_script
			if (isset($dataArr['cssFiles']) && is_array($dataArr['cssFiles'])) {
				foreach ($dataArr['cssFiles'] as $fileData) { 
					
					if (empty($fileData['url'])) { continue; }
					$html.= "\n".'<link rel="stylesheet" href="'.htmlspecialchars($fileData['url']).'" type="text/css" media="all" />';
				}
			}

		
			if (isset($dataArr['javascriptFilesBefore']) && is_array($dataArr['javascriptFilesBefore'])) {
				foreach ($dataArr['javascriptFilesBefore'] as $fileData) {
					if (empty($fileData['url'])) { continue; }
					$html.= "\n".'<script type="text/javascript" src="'.htmlspecialchars($fileData['url']).'"></script>';
				}
			}
		

			if (!empty($dataArr['htmlBlock'])) {
				$html.= "\n".$dataArr['htmlBlock'];
			}


			if (isset($dataArr['javascriptFilesAfter']) && is_array($dataArr['javascriptFilesAfter'])) {
				foreach ($dataArr['javascriptFilesAfter'] as $fileData) {
					if (empty($fileData['url'])) { continue; }
					$html.= "\n".'<script type="text/javascript" src="'.htmlspecialchars($fileData['url']).'"></script>';
				}
			}	 
	    }


		if(!empty($this->errors) && current_user_can('administrator')) {
			$html.= "<p>Error messages: </br>";
			$html.= implode("</br>", $this->errors);			
			$html.= "</br>Error messages are only showed for administrators</p>";
		} 
       

	

		return $html;
	}

	private function adjustParams() {

		if (isset($this->orgCode)) {
			$this->params['org'] = $this->orgCode;
		}
		if (!empty($this->contentType)) {
			$this->params['contentType'] = $this->contentType;
		}

		$cwLogindUserId = get_current_user_id();

		if(!empty($cwLogindUserId)) {
	
			$userCwKey = get_the_author_meta('cwKey', $cwLogindUserId);
			if(!empty($userCwKey)) {
				$this->params['UserKey'] = $userCwKey;
			}		

		}

	}

	public function addParam($paramName, $paramValue) {
		$this->params[$paramName] = $paramValue;
	}
   
	private function getJsonDataUrl($includeapiToken=false) {

		$this->adjustParams();

		$hostUrl = $this->protocol.'://';
		switch ($this->host) {
			case 'test'  : $hostUrl.= 'test.minaaktiviteter.se/'; break;
			case 'local' : $hostUrl.= 'localhost/cw/public_html/'; break;
			case 'dans'  : $hostUrl.= 'dans.se/'; break;
			case 'idrott'  : $hostUrl.= 'idrott.se/'; break;
			default      : $hostUrl.= 'minaaktiviteter.se/'; break;
		}

		$urlVarArr = array();

		if (isset($this->params) && count($this->params) > 0) {
			foreach ( $this->params as $key => $val ) {
				if (empty($key)) {
					continue;
				}

				if (is_numeric($key)) {
					$urlVar = urlencode($val);
					$val = null;
				} else {
					$urlVar = urlencode($key);
				}

				if (isset($val)) {
					$urlVar.= '='.urlencode($val);
				}

				$urlVarArr[] = $urlVar;
			}
		}
		if ($includeapiToken && !empty($this->apiToken)) {
			$urlVarArr[] = 'pw='.urlencode($this->apiToken);
		}
		$urlVarStr = implode('&', $urlVarArr);

		$url = $hostUrl.self::$jsonDataUrl.'?'.$urlVarStr;

		return $url;
	}



	/**
	 * @return string
	 *
	 * Sends a request to the CogWork server and returns the json formatted server response
	 */
	private function loadContent() {
		$result = null;
		$url = $this->getJsonDataUrl(true);

	

		// Arguments for the wp_remote_get function
		$args = array(
			'timeout' => 30
		);
		
		$response = wp_remote_get($url, $args);

		// errors check
		if (is_wp_error($response) ){			
			$this->errors[] = $response->get_error_message();			
		}
		elseif(wp_remote_retrieve_response_code( $response ) === 200 ){
			// Everything is OK, add json data to result
			$result = wp_remote_retrieve_body( $response );
		}

		return $result;
				
	}

	private function loadContentDataArray() {

		$this->getJsonDataUrl(true);
		// Skip if already loaded
		if ($this->contentLoaded) {
			return;
		}	

		$str = $this->loadContent();        
        
        if($str) {

			$decodestring =  json_decode($str, true);  
		    if($decodestring) {
				$this->contentDataArr = $decodestring;  
				$this->contentLoaded = true;
			}
			else {
				$this->errors[] ='No JSON data was recieved from Curl call';
			}

		}
	}

	/* Log in to WordPress with Mina Aktiviteter credential */
	private function cwPrivateLogin($username, $password) {
	
		$array = [];
		$array['error'] = "Misslyckad anslutning";
		 	 
	
		$url = $this->getUserLoginUrl($username, $password);

		$result = false;
		$args = array(
			'timeout' => 30,
		);	
	    
			$response = wp_remote_get($url, $args);			

			if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200  ){			
				$body = wp_remote_retrieve_body($response);	
				if($body ) {
					$result = json_decode($body);	
				}	
			}	        	
                    
            if($result) {

				if(!empty($result->userLogin) && empty($result->userLogin->errors)) {
					$array['role'] = empty($result->userLogin->wordpressRole) ? "" : strtolower($result->userLogin->wordpressRole);
					$array['cwKey'] = empty($result->userLogin->userKey) ? "" : $result->userLogin->userKey;
					$array['email'] = empty($result->userLogin->email) ? "" : $result->userLogin->email;
				    $array['error'] = false;	
																				
				}

				elseif(!empty($result->userLogin->errors[0]) && $result->userLogin->errors[0] === 'Wrong password') {
					$array['error'] = "Felaktigt lösenord.";				
				}	
			}
					
     		    	
		
		

		return $array;
	}


	public function cwLogin($username, $password) {	
		   return $this->cwPrivateLogin($username, $password);	   
	}

	/* Log in to WordPress with Mina Aktiviteter credential */
	private function getUserLoginUrl($username, $password) {

	
		$url = "";
		$hostUrl = $this->protocol.'://';
		$options = get_option('cogwork_option');
		$apiPasword = "";
	
		// Add compability with older version of the plugin by using unencrypted apiKey value	
		
		//Deccrypt apiToken
		if($options['apiToken']) {
		   $cwResources = new cwResources();
		   $apiPasword =  $cwResources->encrypt($options['apiToken'], 'd');
		}

	    elseif(array_key_exists('apiKey', $options) && $options['apiKey']) {			
			$apiPasword = $options['apiKey'];		
		}

		switch ($this->host) {
			case 'test'  : $hostUrl.= 'test.minaaktiviteter.se/'; break;
			case 'local' : $hostUrl.= 'localhost/cw/public_html/'; break;	
			default      : $hostUrl.= 'minaaktiviteter.se/'; break;
		}

	    $orgCode = $this->orgCode;

		$url = $hostUrl;
		$url .= 'api/public/json/userLogin/?';
		$url .= '&org=' . urlencode($orgCode);
		$url .= '&pw='  .  urlencode($apiPasword);
		$url .= '&username=' . urlencode($username);
		$url .=	'&userPw=' . urlencode($password);

		return $url;
	}



}
