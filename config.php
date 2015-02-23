<?php

$config = array(

  'dbhost' => '',
  'dbuser' => '',
  'dbpass' => '',
  'dbname' => '',


  'news_info' => array(
    'cache_max_age' => 100, // seconds
  ),
  'pages' => array(

  
  array(
    'time' => 10,
    'url' => './pages/devconf.html',
  ),
  array(
    'time' => 10,
    'url' => 'random_photo.php',
  ),
  array(
    'time' => 10,
    'url' => 'random_scheduser.php',
  ),
  array(
    'time' => 10,
    'url' => 'random_avatar.php',
  ),

  array(
    'time' => 211,
//    'time' => 10,
    'url' =>'./pages/Truth_Happens.html',
  ),

  array(
    'time' => 10,
    'url' =>'./pages/lt.html',
  ),

/*
  array(
    'time' => 20,
    'url' => './pages/fleda.html',
  ),
  */
/*
  array(
    'time' => 10,
    'url' =>'./pages/badge.html',
  ),
  */
/*
  array(
    'time' => 10,
    'url' =>'./pages/openalt.html',
  ),
*/

),
  'news' => array(
/*
    array(
      'type' => 'test',
    ),
*/

    array(
      'type' => 'rss',
      'value' => 'http://devconf.cz/rss.xml',
      'author' => 'devconf.cz',
      'avatar' => 'http://www.devconf.cz/wall/img/avatar3.png',
    ),
/*
    array(
      'type' => 'twitter_homescreen',
      'value' => 'devconf_cz',
      'auth' => array(
        'consumer_key'    => '',	// consumer key
        'consumer_secret' => '',	// consumer secret
        'user_token'      => '',	// access token
        'user_secret'     => '',	// access token secret
       )
    ),

    array(
      'type' => 'twitter_hashtag',
      'value' => '#devconfcz',
      'auth' => array(
        'consumer_key'    => '',	// consumer key
        'consumer_secret' => '',	// consumer secret
        'user_token'      => '',	// access token
        'user_secret'     => '',	// access token secret
       ),
       'without_retweets' => true,
    ),

    array(
      'type' => 'flickr_hashtag',
      'value' => 'devconfcz',
      'auth' => array(
        'api_key' => '',
        'api_secret' => '',
      ),
    ),

    array(
      'type' => 'google_plus_hashtag',
      'value' => '#devconfcz',
      'auth' => array(
         'api_key' => '',
       ),
       'cache_directory' => '/pub/redhatwa/devconf.cz/tmp/wall_google_cache',
    ),
    array(
      'type' => 'instagram_hashtag',
      'value' => 'devconfcz',
      'auth' => array(
        'apiKey'      => '',
        'apiSecret'   => '',
        'apiCallback' => 'http://localhost' // must point to success.php
      )
    ),
    array(
      'type' => 'vine_hashtag',
      'value' => 'devconfcz',
      'auth' => array(
        'user' => '',
        'password' => ''
      )
    ),


    array(
      'type' => 'facebook_hashtag',
      'value' => '#devconfcz',
      'auth' => array(
        'appId' => '',
        'secret' => '',
        'allowSignedRequest' => true, // optional but should be set to false for non-canvas apps
       ),
    ),
    array(
      'type' => 'foursquare',
      'value' => '/files/data.json',
      'default_checkin_message' => "I'm at %s",
      'event_start' => '2015-02-06T00:00:00+01:00',
      'event_end' => '2015-02-08T23:59:00+01:00',

    ),
*/
/*
    array(
      'type' => 'tumblr_hashtag',
      'value' => '#devconfcz',
      'auth' => array(
      ),
    ),
*/
  ),
);

?>