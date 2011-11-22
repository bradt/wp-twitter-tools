<?php
/*
Plugin Name: Twitter Tools
Plugin URI: http://crowdfavorite.com/wordpress/plugins/twitter-tools/
Description: A complete integration between your WordPress blog and Twitter. Bring your tweets into your blog and pass your blog posts to Twitter. Show your tweets in your sidebar. Relies on <a href="http://wordpress.org/extend/plugins/social/">Social</a>.
Version: 3.0dev
Author: Crowd Favorite
Author URI: http://crowdfavorite.com
*/

// Copyright (c) 2007-2011 Crowd Favorite, Ltd. All rights reserved.
//
// Released under the GPL license
// http://www.opensource.org/licenses/gpl-license.php
//
// This is an add-on for WordPress
// http://wordpress.org/
//
// **********************************************************************
// This program is distributed in the hope that it will be useful, but
// WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
// **********************************************************************

require_once('classes/aktt.php');
require_once('classes/aktt_account.php');
require_once('classes/aktt_tweet.php');
require_once('widget.php');

add_action('init', array('AKTT', 'init'), 0);

/* Shortcode syntax
 *	[aktt_tweets account="alexkingorg" count="5" offset="0" include_rts="1" include_replies="1" mentions="crowdfavorite,twittertools" hashtags="wordpress,plugin,twittertools"]
 */
function aktt_shortcode_tweets($args) {
	if ($account = AKTT::default_account()) {
		$username = $account->social_acct->name();
	}
	else { // no accounts, get out
		return '';
	}
	$args = shortcode_atts(array(
		'account' => $username,
		'include_rts' => 0,
		'include_replies' => 0,
		'count' => 5
	), $args);
	$tweets = AKTT::get_tweets($args);
	ob_start();
	include('views/tweet-list.php');
	return ob_get_clean();
}
add_shortcode('aktt_tweets', 'aktt_shortcode_tweets');

/* Shortcode syntax
 *	[aktt_tweet account="alexkingorg"]
 *	[aktt_tweet id="138741523272577028"]
 */
function aktt_shortcode_tweet($args) {
	if ($account = AKTT::default_account()) {
		$username = $account->social_acct->name();
	}
	else { // no accounts, get out
		return '';
	}
	$args = shortcode_atts(array(
		'account' => $username,
		'id' => null
	), $args);
// if we have an ID, only search by that
	if (!empty($args['id'])) {
		unset($args['account']);
	}
	$args['count'] = 1;
	$tweets = AKTT::get_tweets($args);
	if (count($tweets) != 1) {
		return '';
	}
	$tweet = $tweets[0];
	ob_start();
	include('views/tweet.php');
	return ob_get_clean();
}
add_shortcode('aktt_tweet', 'aktt_shortcode_tweet');

/**
 * You must flush the rewrite rules to activate this action.
 */
// function aktt_add_tweet_rewrites() {
// 	global $wp_rewrite;
// 
// 	$rules = $wp_rewrite->generate_rewrite_rules('/tweets/%post_id%', EP_PERMALINK);
// 	
// 	foreach ($rules as &$rule) {
// 		$rule = str_replace('index.php?', 'index.php?post_type=aktt_tweet&', $rule);
// 	}
// 
// 	// All, paginated
// 	$rules['tweets/page/([0-9]+)/?$'] = 'index.php?post_type=aktt_tweet&paged=$matches[1]';
// 	// all
// 	$rules['tweets/?$'] = 'index.php?post_type=aktt_tweet';
// 
// 	$wp_rewrite->rules = $rules + $wp_rewrite->rules;
// }
// add_action('generate_rewrite_rules', 'aktt_add_tweet_rewrites');
