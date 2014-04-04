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

	$response = sendRequest( $oauth_settings, $url ); //make the call

	echo "<pre>";
	var_dump( $response );
	echo "</pre>";

	function twitter_build_oauth_string( $endpoint, $params ) {	
 
		$r = array(); 
	    ksort($params);

	    foreach( $params as $key => $value ) {
	        $r[] = "$key=" . rawurlencode($value);
	    }
	 
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

	function sendRequest( $oauth_settings, $endpoint ) {
	    $header = array( twitter_build_auth_header($oauth_settings), 'Expect:'); //create header array and add 'Expect:'
	 
	    $options = array(
	    	CURLOPT_HTTPHEADER => $header, //use our authorization and expect header
           CURLOPT_HEADER => false, //don't retrieve the header back from Twitter
           CURLOPT_URL => $endpoint, //the URI we're sending the request to
           CURLOPT_POST => true, //this is going to be a POST - required
           CURLOPT_RETURNTRANSFER => true, //return content as a string, don't echo out directly
           CURLOPT_SSL_VERIFYPEER => false //don't verify SSL certificate, just do it
        ); 
	 
	    $ch = curl_init(); //get a channel
	    curl_setopt_array($ch, $options); //set options
	    $response = curl_exec($ch); //make the call
	    curl_close($ch); //hang up
	 
	    return $response;
	}

	exit;

?>