	<?php
	  // You can find your Partner and Secret codes under the Developers
	  // area of the Backlot Account tab  
	
	 include_once('./config.php');
	 
	   class xml2Array
	   {
		 var $arrOutput = array();
		 var $resParser;
		 var $strXmlData;
	 
		 function parse($strInputXML) {
	   
		   $this->resParser = xml_parser_create ();
		   xml_set_object($this->resParser,$this);
		   xml_set_element_handler($this->resParser, "tagOpen", "tagClosed");   
		   xml_set_character_data_handler($this->resParser, "tagData");
		   
		   $this->strXmlData = xml_parse($this->resParser,$strInputXML );
		   if(!$this->strXmlData) {
			 die(sprintf("XML error: %s at line %d",
			 xml_error_string(xml_get_error_code($this->resParser)),
			 xml_get_current_line_number($this->resParser)));
		   }
							   
		   xml_parser_free($this->resParser);
		   return $this->arrOutput;
		 }
		 
		 function tagOpen($parser, $name, $attrs)
		 {
		   $tag=array("name"=>$name,"attrs"=>$attrs); 
		   array_push($this->arrOutput,$tag);
		 }
	   
		 function tagData($parser, $tagData)
		 {
		   if(trim($tagData))
		   {
			 if(isset($this->arrOutput[count($this->arrOutput)-1]['tagData']))
			 {
			   $this->arrOutput[count($this->arrOutput)-1]['tagData'] .= $tagData;
			 } 
			 else
			 {
			   $this->arrOutput[count($this->arrOutput)-1]['tagData'] = $tagData;
			 }
		   }
		 }
	   
		 function tagClosed($parser, $name)
		 {
		   $this->arrOutput[count($this->arrOutput)-2]['children'][] = $this->arrOutput[count($this->arrOutput)-1];
		   array_pop($this->arrOutput);
		 }
	  }
	
	  class OoyalaBacklotAPI
	  {  
		static function query($params, $queryId)
		{
			if ($queryId != "") {
			   return OoyalaBacklotAPI::send_request('query', $params, $queryId);
			} else {
				 return OoyalaBacklotAPI::send_request('query', $params, "");
			}
		}
	  
		private static function send_request($request_type, $params, $queryId = "")
		{    	
			$responseStr = "";
			if (!$responseStr) {  
			// Add an expire time of 1 day unless otherwise specified.
			// Floor the time to 15 second increments so we get better
			// cache efficiency and reduce our request time.
			if (!array_key_exists('expires', $params)) {
			  $params['expires'] = (floor(time() / 15) * 15) + 900;
			}  
			
			$string_to_sign = OOYALA_SECRET_CODE;
			$url = 'http://api.ooyala.com/partner/'.$request_type.'?pcode='.OOYALA_PARTNER_CODE;
	  
			$keys = array_keys($params);
			sort($keys);
	  
			foreach ($keys as $key) {
			  $string_to_sign .= $key.'='.$params[$key];
			  $url .= '&'.rawurlencode($key).'='.rawurlencode($params[$key]);
			}
	
			$digest = hash('sha256', $string_to_sign, true);
			$signature = ereg_replace('=+$', '', trim(base64_encode($digest)));
			$url .= '&signature='.rawurlencode($signature);  
			//echo $url;
			$responseStr = file_get_contents($url);
			}
		  return $responseStr;
		} 
		
		// reference http://theserverpages.com/php/manual/en/function.xml-parse.php on how to use
		static function print_results( $xml, $act )
		{
		  $objXML = new xml2Array();
		  $arrOutput = $objXML->parse( $xml );
  	    $items     = $arrOutput[0]["children"];
		  
		  for ( $i = 0 ; $i < sizeof($items) ; $i++ )
		  {  
			  $id =  "'". ltrim($items[$i]["children"][0]["tagData"],"\x22")."'";  
			  $printed_something = false;
			  for ( $j = 0 ; $j < sizeof($items[$i]["children"]) ; $j++ )
			  {
				if ( $items[$i]["children"][$j]["name"] == "THUMBNAIL" )
				{
				  $printed_something = true;
				  echo "
				  <div class=\"figure\"><div class=\"photo\"><img onclick=ChooseVideo(".$id."); src='".$items[$i]["children"][$j]["tagData"]."' width=150/></div>
				  <p>".$items[$i]["children"][1]["tagData"]."</p></div>
				  ";	  
				}
			  }
		  }
		   //Start of Prev/Next page
	     echo "<div id=\"pager\">";
		 if ($act == "last_eight") {
		  if ($arrOutput[0]["attrs"]["PAGEID"] > 0) { 
		   $prevpageid = $arrOutput[0]["attrs"]["PAGEID"];
		   $prevpageid = $prevpageid-8;
		   echo "<input type=button value=\"Prev Page\" onclick=MakeRequest(\"last_eight\",\"$prevpageid\");>"; }
		  if ($arrOutput[0]["attrs"]["NEXTPAGEID"] > 0) { 
		   $nextpageid = $arrOutput[0]["attrs"]["NEXTPAGEID"];
		   echo "<input type=button value=\"Next Page\" onclick=MakeRequest(\"last_eight\",\"$nextpageid\");>"; 
		  }
		 }else{
	    if ($arrOutput[0]["attrs"]["PAGEID"] > 0) { 
		   $prevpageid = $arrOutput[0]["attrs"]["PAGEID"];
		   $prevpageid = $prevpageid-8;
		   echo "<input type=button value=\"Prev Page\" onclick=SearchRequest(this.form.ooyalasearch.value,\"$prevpageid\");>"; }
		  if ($arrOutput[0]["attrs"]["NEXTPAGEID"] > 0) { 
		   $nextpageid = $arrOutput[0]["attrs"]["NEXTPAGEID"];
		   echo "<input type=button value=\"Next Page\" onclick=SearchRequest(this.form.ooyalasearch.value,\"$nextpageid\");>"; 
		  }
		echo "</div>";
		 }//end of Prev/Next Page
		  
		}
	  }
	  
	  /******************************
		  MANAGE REQUESTS
	  /******************************/
	
	  $do = $_GET['do'];
	
	  if ( $do == "search" ) {
		$key_word = $_GET['key_word'];
		$pageid = $_GET['pageid'];
		if (($pageid != "")&&( $key_word != "" )) {
		  $all      = OoyalaBacklotAPI::query(array( 'text' => $key_word,
													 'status' => 'live',
													 'orderBy' => 'uploadedAt,desc',
													 'limit' => '8',
													 'pageID' => $pageid,
													 'queryMode' => 'AND' ),"");
		  echo OoyalaBacklotAPI::print_results( $all, $do );
		}
		else if ( $key_word != "" ){
		 $all      = OoyalaBacklotAPI::query(array( 'text' => $key_word,
													 'status' => 'live',
													 'orderBy' => 'uploadedAt,desc',
													 'limit' => '8',
													 'queryMode' => 'AND' ),"");
		  echo OoyalaBacklotAPI::print_results( $all, $do );	
		}
	  } else if ( $do == "last_eight" ) {
		  $pageid = $_GET['pageid'];
		  if ($pageid != "") {
		  $all      = OoyalaBacklotAPI::query(array( 'status' => 'live',
													 'orderBy' => 'uploadedAt,desc',
													 'pageID' => $pageid,
													 'limit' => '8' ),"");
		  echo OoyalaBacklotAPI::print_results( $all, $do );
	  }else{		  
		  $all      = OoyalaBacklotAPI::query(array( 'status' => 'live',
													 'orderBy' => 'uploadedAt,desc',
													 'limit' => '8' ),"");
		  echo OoyalaBacklotAPI::print_results( $all, $do );}
	  } else {
		echo "nothing to do";
	  }
	?>