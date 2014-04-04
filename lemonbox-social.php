<?php
	
	/*
	Plugin Name: Lemonbox Social
	Description: Social Plugin for WordPress
	Version: 0.1
	License: GPL
	Author: Marcus Battle
	Author URI: http://marcusbattle.com
	*/
	
	$api_key = 'aOAO3JTRfTOZY2l6DrrlBTjop';
	$api_secret = 'lxHPMwZ8oqKxm8XOxn6pkzoHLWHbmwP7TNxb8KqEkA7ZztjCvJ';

	$oauth_settings = array(
		'oauth_callback' => home_url() . '/callback-twitter',
		'oauth_consumer_key' => $api_key,
		'oauth_nonce' => time(),
		'oauth_signature_method' => 'HMAC-SHA1',
		'oauth_timestamp' => time(),
		'oauth_version' => '1.0'
	);

	$url = 'https://api.twitter.com/oauth/request_token';

	$oauth_string = twitter_build_oauth_string( $url, $oauth_settings );

	$composite_key = twitter_build_composite_key( $api_secret ); //first request, no request token yet
	$oauth_settings['oauth_signature'] = base64_encode(hash_hmac('sha1', $url, $composite_key, true)); //sign the base string

	$auth_header = twitter_build_auth_header( $oauth_settings );

	echo $auth_header;

	function twitter_build_oauth_string( $endpoint, $params ) {	
 
		$r = array(); //temporary array
	    ksort($params); //sort params alphabetically by keys

	    foreach( $params as $key => $value ) {
	        $r[] = "$key=" . rawurlencode($value); //create key=value strings
	    }//end foreach                
	 
	    return "POST&" . rawurlencode($endpoint) . '&' . rawurlencode(implode('&', $r)); //return complete base string
	}

	function twitter_build_composite_key( $api_secret = '', $request_token = '' ) {
	    
	    return rawurlencode($api_secret) . '&' . rawurlencode($request_token);

	}

	function twitter_build_auth_header( $oauth_settings ){
	    $r = 'Authorization: OAuth '; //header prefix
	 
	    $values = array(); //temporary key=value array
	    foreach($oauth_settings as $key=>$value)
	        $values[] = "$key=\"" . rawurlencode($value) . "\""; //encode key=value string
	 
	    $r .= implode(', ', $values); //reassemble
	    return $r; //return full authorization header
	}

	exit;

?>