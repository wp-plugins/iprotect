<?php
/*
Plugin Name: iProtect
Plugin URI: http://wordpress.org/extend/plugins/iprotect/
Description: Help protect your images in posts
Version: 0.1.0
Author: AJ Hill
Author URI: http://www.tiredangel.com/
*/

/*
	Copyright 2009 AJ Hill (email: ajhill007@gmail.com)

	License: CC-BY-SA

	Please see the following link for the license
	under which this work is released:

	http://creativecommons.org/licenses/by-sa/3.0/

	UNLESS OTHERWISE MUTUALLY AGREED TO BY THE PARTIES IN
	WRITING, LICENSOR OFFERS THE WORK AS-IS AND MAKES NO
	REPRESENTATIONS OR WARRANTIES OF ANY KIND CONCERNING
	THE WORK, EXPRESS, IMPLIED, STATUTORY OR OTHERWISE,
	INCLUDING, WITHOUT LIMITATION, WARRANTIES OF TITLE,
	MERCHANTIBILITY, FITNESS FOR A PARTICULAR PURPOSE,
	NONINFRINGEMENT, OR THE ABSENCE OF LATENT OR OTHER
	DEFECTS, ACCURACY, OR THE PRESENCE OF ABSENCE OF ERRORS,
	WHETHER OR NOT DISCOVERABLE. SOME JURISDICTIONS DO
	NOT ALLOW THE EXCLUSION OF IMPLIED WARRANTIES, SO SUCH
	EXCLUSION MAY NOT APPLY TO YOU.

	EXCEPT TO THE EXTENT REQUIRED BY APPLICABLE LAW, IN NO
	EVENT WILL LICENSOR BE LIABLE TO YOU ON ANY LEGAL THEORY
	FOR ANY SPECIAL, INCIDENTAL, CONSEQUENTIAL, PUNITIVE OR
	EXEMPLARY DAMAGES ARISING OUT OF THIS LICENSE OR THE USE
	OF THE WORK, EVEN IF LICENSOR HAS BEEN ADVISED OF THE
	POSSIBILITY OF SUCH DAMAGES.
*/

//
// Do some work to ensure we can identify the path to the
// plugin directory for referencing the transparent image.
//
// This is per: http://codex.wordpress.org/Determining_Plugin_and_Content_Directories
//

// Pre-2.6 compatibility
if ( ! defined( 'WP_CONTENT_URL' ) )
      define( 'WP_CONTENT_URL', get_option( 'siteurl' ) . '/wp-content' );
if ( ! defined( 'WP_CONTENT_DIR' ) )
      define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
if ( ! defined( 'WP_PLUGIN_URL' ) )
      define( 'WP_PLUGIN_URL', WP_CONTENT_URL. '/plugins' );
if ( ! defined( 'WP_PLUGIN_DIR' ) )
      define( 'WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins' );

/**
 * iprotectImages()
 *
 * Main method which should tweak the output of the post
 * to protect images using the obfuscation method.
 */

function iprotectImages($content)
{
	// Define the pattern
	$pattern = <<<HEREDOC
/ \< \s* img \s* (?<class>class \s* = \s* [\"][^\"]+[\"])? \s* (?<title>title \s* = \s* [\"][^\"]*[\"])? \s*  (?<src>src \s* = \s* [\"](?<url>[^\"]*)[\"])? \s* (?<alt>alt \s* = \s* [\"][^\"]*[\"])? \s* (?<width>width \s* = \s* [\"] (?<x>[^\"]*) [\"])? \s* (?<height>height \s* = \s* [\"] (?<y>[^\"]*) [\"])? \s* \/ \> /ix
HEREDOC;

	// Replacement pattern
	/**
	 * $# = data
	 *
	 * 1 = class
	 * 2 = title
	 * 3 = src
	 * 4 = url
	 * 5 = alt
	 * 6 = width
	 * 7 = x only
	 * 8 = height
	 * 9 = y only
 	*/

	# $replacement = '<img $1 $2 $3 $5 $6 $8 />';
	$replacement = '<div $6 $8 style="background-image:url($4); background-repeat: no-repeat;"><img src="' . WP_PLUGIN_URL . '/iprotect/trans.gif" $5 $6 $8 /></div>';

	// Replace images with holder pattern...
	$content = preg_replace($pattern,$replacement,$content);

	// Return the results
	return $content;
}

// Register the function with the post rendering hook
add_filter('the_content','iprotectImages',99,1);

// End of file