<?php
	include('include.php');
	include('array2xml.php');
	include 'XMLValidation.php';

	$opt = $_GET["opt"];
	

	///////////////////////////////////////////////////////////////////////////
	if ($opt == 'xpath_class') {
		$xml = new XMLValidation();
		
		//FD1
		//$xml->url = 'http://m.ww10.1800flowers.com';
		//$xml->xpath = "//div[contains(@class, 'mw_menu_content_inner_wrapper')]//div[@id='bluemenu']/ul";
		//$xml->removeXPath = "//li[@id='MOBILE_Fruit_Bouquets']|//div[contains(@class, 'mw_hide')]";
		//$xml->schema = 'schema/menu_FD1.xsd';
		
		//FD4
		$xmlObj['_menu_slide'] = new StdClass;
		$xmlObj['_menu_slide']->url = 'http://m3.www3.1800flowers.com/';
		$xmlObj['_menu_slide']->xpath = "//div[@id='_menu_slide']/ul";
		$xmlObj['_menu_slide']->removeXPath = '';
		$xmlObj['_menu_slide']->schema = 'schema/menu_FD4.xsd';
		
		$xmlObj['_body_nav'] = new StdClass;
		$xmlObj['_body_nav']->url = 'http://m3.www3.1800flowers.com/';
		$xmlObj['_body_nav']->xpath = "//div[@id='_body_nav']/ul";
		$xmlObj['_body_nav']->removeXPath = '';
		$xmlObj['_body_nav']->schema = 'schema/menu_FD4.xsd';
		
		//$xml->XMLschemaValidate($xmlObj, true, 'tmp/menu_FD4.xml');
		$xml->XMLschemaValidate($xmlObj, true, '');
	}
	
	
	///////////////////////////////////////////////////////////////////////////
	if ($opt == 'xpath_http') {
	
		$html = file_get_contents("http://m.ww10.1800flowers.com");
		$doc = new DOMDocument();
		$doc->strictErrorChecking = false;
		$doc->recover=true;
		@$doc->loadHTML("<html><body>".$html."</body></html>");
		
/*
		$file = 'tmp/home_1.html';
		$html = file_get_contents($file);
		$html = cleanupXML($html);
		$doc = new DOMDocument();
		$doc->strictErrorChecking = false;
		$doc->recover=true;
		$doc->formatOutput = true;
		@$doc->loadHTML($html);
*/
		
		
		$xpath = new DOMXpath($doc);
		//$elements = $xpath->query("//div[@id='mw_menu_container']//div[@id='bluemenu']/ul");
		$elements = $xpath->query("//div[contains(@class, 'mw_menu_content_inner_wrapper')]//div[@id='bluemenu']/ul");
		$mainMenu = new DomDocument('1.0', 'UTF-8');
		$mainMenu->formatOutput = true;
		$node = $mainMenu->appendChild($mainMenu->createElement('root'));
		foreach($elements as $element) $node->appendChild($mainMenu->importNode($element,1));
		
		$mainMenu->saveXML();
		
		// remove mbox and hidden element
		$removeXpath = new DOMXPath($mainMenu);
		$remove_nodes = $removeXpath->query("//li[@id='MOBILE_Fruit_Bouquets']|//div[contains(@class, 'mw_hide')]");
		foreach($remove_nodes as $remove_node) {
			$remove_node->parentNode->removeChild($remove_node);
		}
		$mainMenu->saveXML();
		//$mainMenu->save('tmp/menu.xml');
		
		

		// $mainMenu = new DomDocument('1.0', 'UTF-8');
		// $mainMenu->preserveWhiteSpace = FALSE;
		// $mainMenu->load('tmp/menu.xml');

		
		// Enable user error handling
		libxml_use_internal_errors(true);
		
		if (!$mainMenu->schemaValidate('tmp/menu.xsd')) {
		    print '<b>DOMDocument::schemaValidate() Generated Errors!</b>';
		    libxml_display_errors();
		} else {
			print 'XML content is validated';
		}
		echo $mainMenu->saveXML();

	}

	///////////////////////////////////////////////////////////////////////////
	if ($opt == 'xpath') {
	
		$file1 = 'tmp/home_1.html';
		$dom1 = new DOMDocument();
		$dom1->loadHTML($file1, LIBXML_PARSEHUGE);
		//$dom1 = new DOMDocument();
		//$dom1->load($file1, LIBXML_PARSEHUGE);
		
		$xpathvar = new Domxpath($dom1);
		
		$query = "//div[@id='mw_header']";
/*
        $queryResult = $xpathvar->query($query);
        //$queryResult = $dom1->xpath->evaluate($query);
        print_r($queryResult);
        foreach($queryResult as $result){
                echo $result->nodeValue;
        }
*/
        
        $elements = $xpathvar->query($query);

		if (!is_null($elements)) {
		  foreach ($elements as $element) {
		    echo "<br/>[". $element->nodeName. "]";
		
		    $nodes = $element->childNodes;
		    foreach ($nodes as $node) {
		      echo $node->nodeValue. "\n";
		    }
		  }
		}

	}
	///////////////////////////////////////////////////////////////////////////
	if ($opt == 'compare_xml') {
	
		$file1 = 'tmp/home_1.xml';
		$dom1 = new DOMDocument();
		$dom1->load($file1, LIBXML_PARSEHUGE);
		
		$file2 = 'tmp/home_2.xml';
		$dom2 = new DOMDocument();
		$dom2->load($file2, LIBXML_PARSEHUGE);
		
		try
		{
			$result = compare_xml($dom1, $dom2);
			if ($result === true) {
			   echo 'The XML documents are the same.';
			} else {
			   // they are different: print the reason why
			   echo ("XML documents are different: $result");
			}
		}
		catch (Exception $e) 
		{
		    echo $e->getMessage();
		}
		
		exit;
	
	}
	///////////////////////////////////////////////////////////////////////////
	if ($opt == 'compare_xml_array') {
	
		$file1 = 'tmp/home_1.txt';
		$array1 = unserialize(file_get_contents($file1));
		
		$file2 = 'tmp/home_2.txt';
		$array2 = unserialize(file_get_contents($file2));
		
		try 
		{
		    $xml1 = new array2xml('root');
		    $xml1->createNode($array1);
		    
		    $xml2 = new array2xml('root');
		    $xml2->createNode($array2);
	
			$result = xml_is_equal($xml1, $xml2);
			if ($result === true) {
			   echo 'The XML documents are the same.';
			} else {
			   // they are different: print the reason why
			   echo ("XML documents are different: $result");
			}
		} 
		catch (Exception $e) 
		{
		    echo $e->getMessage();
		}
		
		exit;
	
	}
	///////////////////////////////////////////////////////////////////////////
	if ($opt == 'read_xml') {
		$file2 = 'tmp/'.($_GET['file']?$_GET['file']:'home_2.txt');
		$array2 = unserialize(file_get_contents($file2));
		try {
			$xml2 = new array2xml('root');
		    $xml2->createNode($array2);
		    header("Content-type: text/xml; charset=utf-8");
			echo $xml2;
		} catch (Excemtion $e) {
			echo $e->getMessage();
		}
		
		exit;
	}
	///////////////////////////////////////////////////////////////////////////
	if ($opt == 'read_array') {
		$file2 = 'tmp/'.($_GET['file']?$_GET['file']:'home_2.txt');
		$array2 = unserialize(file_get_contents($file2));
		//echo '<pre>';
		//print_r($array2[0]);
		//echo '</pre>';
		
		$doc = new DOMDocument();
		$child = generate_xml_element( $doc, $array2[0] );
		if ( $child )
		    $doc->appendChild( $child );
		$doc->formatOutput = true; // Add whitespace to make easier to read XML
		$xml = $doc->saveXML();
		
		print_r($xml);
	}
	///////////////////////////////////////////////////////////////////////////
	if ($opt == 'get_http') {
	
		$file = 'tmp/'.($_GET['file']?$_GET['file']:'home_2.xml');
		//$data = file_get_contents('http://m.ww10.1800flowers.com');
		//$dump = (xml2array(utf8_encode($data)));
		
		//$doc = new DOMDocument();
		//$child = generate_xml_element( $doc, $dump[0] );
		//if ( $child ) $doc->appendChild( $child );
		//$doc->formatOutput = true; // Add whitespace to make easier to read XML
		//$doc->save($file);
        
		//$xml = $doc->saveXML();
		//print_r($xml);

		//file_put_contents($file, $data);
		//file_put_contents($file, serialize($dump));
		
		
		
		$html = file_get_contents("http://m.ww10.1800flowers.com");
		$doc = new DOMDocument();
		$doc->strictErrorChecking = false;
		$doc->recover=true;
		@$doc->loadHTML($html);
		$doc->save($file);
	
	}

?>