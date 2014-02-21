<?php
///////////////////////////////////////////////////////////////////////////
// http://www.dzone.com/snippets/functions-add-remove-nodes-xml

function removNode($myXML, $node, $attribute, $id) {
    $xmlDoc = new DOMDocument();
    $xmlDoc->load($myXML);
    $xpath = new DOMXpath($xmlDoc);
   
    if( $attribute!='' || $id!='' )
    $nodeList = $xpath->query('//'.$node.'[@'.$attribute.'="'.$id.'"]');
    else
    $nodeList = $xpath->query('//'.$node.'');
   
    if ($nodeList->length)
    {
        $node = $nodeList->item(0)  ;
        $node->parentNode->removeChild($node);
    }
    $xmlDoc->save($myXML) ;
} 

///////////////////////////////////////////////////////////////////////////
// http://stackoverflow.com/questions/2312075/get-xpath-of-xml-node-within-recursive-function

function xmlRecurse($xmlObj,$depth=0,$xpath=null) {
  if (!isset($xpath)) {
    $xpath='/'.$xmlObj->getName().'/';
  }
  $position = array();

  foreach($xmlObj->children() as $child) {

    $name = $child->getName();
    if(isset($position[$name])) {
      ++$position[$name];
    }
    else {
      $position[$name]=1;
    }
    $path=$xpath.$name.'['.$position[$name].']';

    echo str_repeat('-',$depth).">".$name.": $path\n";
    foreach($child->attributes() as $k=>$v){
        echo "Attrib".str_repeat('-',$depth).">".$k." = ".$v."\n";
    }

    xmlRecurse($child,$depth+1,$path.'/');
  }
}

///////////////////////////////////////////////////////////////////////////
//http://stackoverflow.com/questions/2261530/fix-malformed-xml-in-php-before-processing-using-domdocument-functions

function cleanupXML($xml) {
    $xmlOut = '';
    $inTag = false;
    $xmlLen = strlen($xml);
    for($i=0; $i < $xmlLen; ++$i) {
        $char = $xml[$i];
        // $nextChar = $xml[$i+1];
        switch ($char) {
        case '<':
          if (!$inTag) {
              // Seek forward for the next tag boundry
              for($j = $i+1; $j < $xmlLen; ++$j) {
                 $nextChar = $xml[$j];
                 switch($nextChar) {
                 case '<':  // Means a < in text
                   $char = htmlentities($char);
                   break 2;
                 case '>':  // Means we are in a tag
                   $inTag = true;
                   break 2;
                 }
              }
          } else {
             $char = htmlentities($char);
          }
          break;
        case '>':
          if (!$inTag) {  // No need to seek ahead here
             $char = htmlentities($char);
          } else {
             $inTag = false;
          }
          break;
        default:
          if (!$inTag) {
             $char = htmlentities($char);
          }
          break;
        }
        $xmlOut .= $char;
    }
    return $xmlOut;
  }
  
///////////////////////////////////////////////////////////////////////////
//http://us2.php.net/manual/en/domdocument.schemavalidate.php

function libxml_display_error($error)
{
    $return = "<br/>\n";
    switch ($error->level) {
        case LIBXML_ERR_WARNING:
            $return .= "<b>Warning $error->code</b>: ";
            break;
        case LIBXML_ERR_ERROR:
            $return .= "<b>Error $error->code</b>: ";
            break;
        case LIBXML_ERR_FATAL:
            $return .= "<b>Fatal Error $error->code</b>: ";
            break;
    }
    $return .= trim($error->message);
    if ($error->file) {
        $return .=    " in <b>$error->file</b>";
    }
    $return .= " on line <b>$error->line</b>\n";

    return $return;
}

function libxml_display_errors() {
    $errors = libxml_get_errors();
    foreach ($errors as $error) {
        print libxml_display_error($error);
    }
    libxml_clear_errors();
}

///////////////////////////////////////////////////////////////////////////

function xml2array($xml) {
        $xmlary = array();
                
        $reels = '/<(\w+)\s*([^\/>]*)\s*(?:\/>|>(.*)<\/\s*\\1\s*>)/s';
        $reattrs = '/(\w+)=(?:"|\')([^"\']*)(:?"|\')/';

        preg_match_all($reels, $xml, $elements);

        foreach ($elements[1] as $ie => $xx) {
                $xmlary[$ie]["name"] = $elements[1][$ie];
                
                if ($attributes = trim($elements[2][$ie])) {
                        preg_match_all($reattrs, $attributes, $att);
                        foreach ($att[1] as $ia => $xx) {
	                        $xmlary[$ie]["attributes"][$att[1][$ia]] = (($att[1][$ia] != 'id')&&($att[1][$ia] != 'name')&&($att[1][$ia] != 'class'))?base64_encode($att[2][$ia]):$att[2][$ia];
                        }
                }

                $cdend = strpos($elements[3][$ie], "<");
                if ($cdend > 0) {
                        $xmlary[$ie]["text"] = base64_encode(substr($elements[3][$ie], 0, $cdend - 1));
                }

                if (preg_match($reels, $elements[3][$ie]))
                        $xmlary[$ie]["elements"] = xml2array($elements[3][$ie]);
                else if ($elements[3][$ie]) {
                        $xmlary[$ie]["text"] = base64_encode($elements[3][$ie]);
                }
        }

        return $xmlary;
}

///////////////////////////////////////////////////////////////////////////
//http://www.viper007bond.com/2011/06/29/easily-create-xml-in-php-using-a-data-array/

function generate_xml_element( $dom, $data ) {
	
    if ( empty( $data['name'] ) )
        return false;
 
    // Create the element
    $element_value = ( ! empty( $data['text'] ) ) ? base64_decode($data['text']) : null;
    $element = $dom->createElement( $data['name'], $element_value );
	
    // Add any attributes
    if ( ! empty( $data['attributes'] ) && is_array( $data['attributes'] ) ) {
        foreach ( $data['attributes'] as $attribute_key => $attribute_value ) {
            $element->setAttribute( $attribute_key, ($attribute_value));
        }
    }
	
    // Any other items in the data array should be child elements
    if ( ! empty( $data['elements'] ) && is_array( $data['elements'] ) ) {
	    foreach ( $data['elements'] as $data_key => $child_data ) {
	        if ( ! is_numeric( $data_key ) )
	            continue;
	 
	        $child = generate_xml_element( $dom, $child_data );
	        if ( $child )
	            $element->appendChild( $child );
	    }
    }
 
    return $element;
}

///////////////////////////////////////////////////////////////////////////
//http://www.bin-co.com/php/scripts/array2json/

function array2json($arr) { 
    //if(function_exists('json_encode')) return json_encode($arr); //Lastest versions of PHP already has this functionality. 
    $parts = array(); 
    $is_list = false; 

    //Find out if the given array is a numerical array 
    $keys = array_keys($arr); 
    $max_length = count($arr)-1; 
    if(($keys[0] == 0) and ($keys[$max_length] == $max_length)) {//See if the first key is 0 and last key is length - 1 
        $is_list = true; 
        for($i=0; $i<count($keys); $i++) { //See if each key correspondes to its position 
            if($i != $keys[$i]) { //A key fails at position check. 
                $is_list = false; //It is an associative array. 
                break; 
            } 
        } 
    } 

    foreach($arr as $key=>$value) { 
        if(is_array($value)) { //Custom handling for arrays 
            if($is_list) $parts[] = array2json($value); /* :RECURSION: */ 
            else $parts[] = '"' . $key . '":' . array2json($value); /* :RECURSION: */ 
        } else { 
            $str = ''; 
            if(!$is_list) $str = '"' . $key . '":'; 

            //Custom handling for multiple data types 
            if(is_numeric($value)) $str .= $value; //Numbers 
            elseif($value === false) $str .= 'false'; //The booleans 
            elseif($value === true) $str .= 'true'; 
            else $str .= '"' . addslashes($value) . '"'; //All other things 
            // :TODO: Is there any more datatype we should be in the lookout for? (Object?) 

            $parts[] = $str; 
        } 
    } 
    $json = implode(',',$parts); 
     
    if($is_list) return '[' . $json . ']';//Return numerical JSON 
    return '{' . $json . '}';//Return associative JSON 
} 

///////////////////////////////////////////////////////////////////////////

function compare_xml(DOMDocument $xml1, DOMDocument $xml2, $text_strict = false) {
	// compare text content
	if ($text_strict) {
		if ($xml1 != $xml2) return "mismatched text content (strict)";
	} else {
		if (trim("$xml1") != trim("$xml2")) return "mismatched text content";
	}
	
	return true;
}

///////////////////////////////////////////////////////////////////////////

function xml_is_equal(SimpleXMLElement $xml1, SimpleXMLElement $xml2, $text_strict = false) {
	// compare text content
	if ($text_strict) {
		if ("$xml1" != "$xml2") return "mismatched text content (strict)";
	} else {
		if (trim("$xml1") != trim("$xml2")) return "mismatched text content";
	}

	// check all attributes
	$search1 = array();
	$search2 = array();
	foreach ($xml1->attributes() as $a => $b) {
		$search1[$a] = "$b";		// force string conversion
	}
	foreach ($xml2->attributes() as $a => $b) {
		$search2[$a] = "$b";
	}
	if ($search1 != $search2) return "mismatched attributes";
	
	// check all namespaces
	$ns1 = array();
	$ns2 = array();
	foreach ($xml1->getNamespaces() as $a => $b) {
		$ns1[$a] = "$b";
	}
	foreach ($xml2->getNamespaces() as $a => $b) {
		$ns2[$a] = "$b";
	}
	if ($ns1 != $ns2) return "mismatched namespaces";
	
	// get all namespace attributes
	foreach ($ns1 as $ns) {			// don't need to cycle over ns2, since its identical to ns1
		$search1 = array();
		$search2 = array();
		foreach ($xml1->attributes($ns) as $a => $b) {
			$search1[$a] = "$b";
		}
		foreach ($xml2->attributes($ns) as $a => $b) {
			$search2[$a] = "$b";
		}
		if ($search1 != $search2) return "mismatched ns:$ns attributes";
	}
	
	// get all children
	$search1 = array();
	$search2 = array();
	foreach ($xml1->children() as $b) {
		if (!isset($search1[$b->getName()]))
			$search1[$b->getName()] = array();
		$search1[$b->getName()][] = $b;
	}
	foreach ($xml2->children() as $b) {
		if (!isset($search2[$b->getName()]))
			$search2[$b->getName()] = array();
		$search2[$b->getName()][] = $b;
	}
	// cycle over children
	if (count($search1) != count($search2)) return "mismatched children count";		// xml2 has less or more children names (we don't have to search through xml2's children too)
	foreach ($search1 as $child_name => $children) {
		if (!isset($search2[$child_name])) return "xml2 does not have child $child_name";		// xml2 has none of this child
		if (count($search1[$child_name]) != count($search2[$child_name])) return "mismatched $child_name children count";		// xml2 has less or more children
		foreach ($children as $child) {
			// do any of search2 children match?
			$found_match = false;
			$reasons = array();
			foreach ($search2[$child_name] as $id => $second_child) {
				if (($r = xml_is_equal($child, $second_child)) === true) {
					// found a match: delete second
					$found_match = true;
					unset($search2[$child_name][$id]);
				} else {
					$reasons[] = $r;
				}
			}
			if (!$found_match) return "xml2 does not have specific $child_name child: " . implode("; ", $reasons);
		}
	}
	
	// finally, cycle over namespaced children 
	foreach ($ns1 as $ns) {			// don't need to cycle over ns2, since its identical to ns1
		// get all children
		$search1 = array();
		$search2 = array();
		foreach ($xml1->children() as $b) {
			if (!isset($search1[$b->getName()]))
				$search1[$b->getName()] = array();
			$search1[$b->getName()][] = $b;
		}
		foreach ($xml2->children() as $b) {
			if (!isset($search2[$b->getName()]))
				$search2[$b->getName()] = array();
			$search2[$b->getName()][] = $b;
		}
		// cycle over children
		if (count($search1) != count($search2)) return "mismatched ns:$ns children count";		// xml2 has less or more children names (we don't have to search through xml2's children too)
		foreach ($search1 as $child_name => $children) {
			if (!isset($search2[$child_name])) return "xml2 does not have ns:$ns child $child_name";		// xml2 has none of this child
			if (count($search1[$child_name]) != count($search2[$child_name])) return "mismatched ns:$ns $child_name children count";		// xml2 has less or more children
			foreach ($children as $child) {
				// do any of search2 children match?
				$found_match = false;
				foreach ($search2[$child_name] as $id => $second_child) {
					if (xml_is_equal($child, $second_child) === true) {
						// found a match: delete second
						$found_match = true;
						unset($search2[$child_name][$id]);
					}
				}
				if (!$found_match) return "xml2 does not have specific ns:$ns $child_name child";
			}
		}
	}	
	
	// if we've got through all of THIS, then we can say that xml1 has the same attributes and children as xml2.
	return true;
}
?>