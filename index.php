<?php

	include 'XMLValidation.php';
	
	
	///////////////////////////////////////////////////////////////////////////
	
		$xml = new XMLValidation();
		
		//FD1
		//$xml->url = 'http://m.ww10.1800flowers.com';
		//$xml->xpath = "//div[contains(@class, 'mw_menu_content_inner_wrapper')]//div[@id='bluemenu']/ul";
		//$xml->removeXPath = "//li[@id='MOBILE_Fruit_Bouquets']|//div[contains(@class, 'mw_hide')]";
		//$xml->schema = 'schema/menu_FD1.xsd';
		
		//FD4
 		
 		// $xmlObj['_header'] = new StdClass;
		// $xmlObj['_header']->url = 'http://m3.www3.1800flowers.com/';
		// $xmlObj['_header']->xpath = "//header";
		// $xmlObj['_header']->removeXPath = '';
		// $xmlObj['_header']->schema = 'schema/header_FD4.xsd';
// 		
		// $xmlObj['_body_nav'] = new StdClass;
		// $xmlObj['_body_nav']->url = 'http://m3.www3.1800flowers.com/';
		// $xmlObj['_body_nav']->xpath = "//div[@id='_body_nav']/ul";
		// $xmlObj['_body_nav']->removeXPath = '';
		// $xmlObj['_body_nav']->schema = 'schema/menu_FD4.xsd';
// 		
		// $xmlObj['_fagf'] = new StdClass;
		// $xmlObj['_fagf']->url = 'http://m3.www3.1800flowers.com/';
		// $xmlObj['_fagf']->xpath = "//div[@id='_fagf']";
		// $xmlObj['_fagf']->removeXPath = '';
		// $xmlObj['_fagf']->schema = 'schema/fagf_FD4.xsd';
// 		
// 		
		// $xmlObj['_menu_slide'] = new StdClass;
		// $xmlObj['_menu_slide']->url = 'http://m3.www3.1800flowers.com/';
		// $xmlObj['_menu_slide']->xpath = "//div[@id='_menu_slide']/ul";
		// $xmlObj['_menu_slide']->removeXPath = '';
		// $xmlObj['_menu_slide']->schema = 'schema/menu_FD4.xsd';
		
		$xmlObj['_footer'] = new StdClass;
		$xmlObj['_footer']->url = 'http://m3.www3.1800flowers.com/';
		$xmlObj['_footer']->xpath = "//footer";
		$xmlObj['_footer']->removeXPath = '';
		$xmlObj['_footer']->schema = 'schema/footer_FD4.xsd';
		
		$xml->XMLschemaValidate($xmlObj, true, '');
	
	///////////////////////////////////////////////////////////////////////////
	
	

?>