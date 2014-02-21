<?php
	include 'cURL.php';
	
	class XMLValidation {
		
		private $url = '';
		private $xpath = '';
		private $schema = '';
		private $removeXPath = '';
		
		private function errorConfig() {
			$error = false;
			if ($this->url == '') {
				echo 'Missing URL variable.<br/>';
				$error = true;
			}
			if ($this->xpath == '') {
				echo 'Missing XPath variable.<br/>';
				$error = true;
			}
			if ($this->schema == '') {
				echo 'Missing Schema variable.<br/>';
				$error = true;
			}
			return $error;
		}
		
		private function getHTMLDoc($url) {
			$curl = new cURL();
			$html = $curl->get($url);
			//$html = strtr($html, array("\n" => ''));
			$html = preg_replace("/[\r\n]\s*|\n\s+|\r\s+|\t\s+/", "\n", $html);
			$html = preg_replace("/\s+</", "<", $html);
			$doc = new DOMDocument();
			$doc->strictErrorChecking = false;
			$doc->recover=true;
			@$doc->loadHTML("<html><body>".$html."</body></html>");
			return $doc;
		}
		
		private function XPath($doc, $xpath, $xpathToRemove, $saveToFile = '') {
			$DOMXpath = new DOMXpath($doc);
			//$elements = $xpath->query("//div[@id='mw_menu_container']//div[@id='bluemenu']/ul");
			$elements = $DOMXpath->query($xpath);
			$DOMFilter = new DomDocument('1.0', 'UTF-8');
			$DOMFilter->preserveWhiteSpace = true;
			$DOMFilter->formatOutput = true;
			$node = $DOMFilter->appendChild($DOMFilter->createElement('root'));
			foreach($elements as $element) $node->appendChild($DOMFilter->importNode($element,1));
			
			$DOMFilter->saveXML();
			
			// remove mbox and hidden element
			if ($xpathToRemove) {
				$removeXpath = new DOMXPath($DOMFilter);
				$remove_nodes = $removeXpath->query($xpathToRemove);
				foreach($remove_nodes as $remove_node) {
					$remove_node->parentNode->removeChild($remove_node);
				}
			}
			if ($saveToFile) {
				if (!file_exists($saveToFile)) $DOMFilter->save($saveToFile);
			}
			else $DOMFilter->saveXML();
			return $DOMFilter;
		}
		
		public function XMLschemaValidate($xmlObj, $debug_obj = false, $file_xml = '') {
			
			$HTMLdoc = new DomDocument('1.0', 'UTF-8');
			
			foreach($xmlObj as $key=>$obj) {
				if ($debug_obj) {
					//echo '<br/>key = '.$key;
					echo '<pre>';
					print_r($obj);
					echo '</pre>';
				}
				$old_url = $this->url;
				if(is_object($obj)) {
					$this->url = $obj->url;
					$this->xpath = $obj->xpath;
					$this->schema = $obj->schema;
					$this->removeXPath = $obj->removeXPath;
				}
				
				//Check getting variables
				if ($this->errorConfig()) exit;
				
				//getHTMLDoc if new URL applied
				if ($old_url != $this->url) $HTMLdoc = $this->getHTMLDoc($this->url);
				
				//XPath filtering
				$DOMFilter = new DomDocument('1.0', 'UTF-8');
				$DOMFilter = $this->XPath($HTMLdoc, $this->xpath, $this->removeXPath, $file_xml);
				
				if ($file_xml) {
					$DOMFilter->preserveWhiteSpace = FALSE;
					$DOMFilter->load($file_xml);
				}
				
				// Enable user error handling
				libxml_use_internal_errors(true);
				if (!$DOMFilter->schemaValidate($this->schema)) {
					print '<br/><b>DOMDocument::schemaValidate() Generated Errors!</b>';
					$this->libxml_display_errors();
				} else {
					print '<br/>'.$key.': XML content is validated';
				}
				//echo $DOMFilter->saveXML();
			}
		}

		private function libxml_display_error($error) {
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
				$return .= " in <b>$error->file</b>";
			}
			$return .= " on line <b>$error->line</b>\n";
			return $return;
		}

		private function libxml_display_errors() {
			$errors = libxml_get_errors();
			foreach ($errors as $error) {
				print $this->libxml_display_error($error);
			}
			libxml_clear_errors();
			}
		}
?>