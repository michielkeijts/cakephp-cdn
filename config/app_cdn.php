<?php
/**
 * Configuration file for the CDN distribution
 */
return [
	'MKCDN' => [
		// auto config, to auto generate a list of servers based on a sprintf 
		// string
		'autoConfig'	=> [
			// enable autoConfig (default)
			'enabled'	=>  TRUE,
			
			/**
			 *  sprintf compatible line which insers a number to generate multiple servers
			 *  - %d (first) will be replaced with determined server number
			 * -  %s is the path 
			 */			
			'serverTemplate'	=>	'https://c%d.domain.tld%s',
			// first server number (e.g. cdn0.domain.tld)
			'start'		=> 0,			
			// last server number (e.g. cdn9.domain.tld)
			'end'		=> 9,
		],
		
		// if not auto enabled, supply a list of cdn servers to distribute the content
		'servers' => [
			'http://cdn1.domain.tld',
			'http://cdn2.domain.tld',
		]
	]
];
