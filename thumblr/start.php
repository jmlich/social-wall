<?php

session_start();

include ("./lib/tumblrPHP.php");

// Enter your Consumer / Secret Key:
$consumer = "9GQn99bJqEXRjaPkdBGHEbIkN4AGM8NZpRmuxEEzNJTWAPwdXv";
$secret = "ScGtJo0V4tcqUMznrikeuni764ZZHphVwu0fe306iKDiLQutB9";

// Create a new instance of the Tumblr Class with your Conumser and Secret when you create your app.
$tumblr = new Tumblr($consumer, $secret);

// Get the request tokens based on your consumer and secret and store them in $token
$token = $tumblr->getRequestToken();

// Set session of those request tokens so we can use them after the application passes back to your callback URL
$_SESSION['oauth_token'] = $token['oauth_token'];
$_SESSION['oauth_token_secret'] = $token['oauth_token_secret'];

// Grab the Authorize URL and pass through the variable of the oauth_token
$data = $tumblr->getAuthorizeURL($token['oauth_token']);

// The user will be directed to the "Allow Access" screen on Tumblr

echo $data;
exit();

header("Location: " . $data);
?>