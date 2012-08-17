<?php
/*
Plugin Name: FileBrowser
Plugin URI: http://danielhuesken.de/portfolio/filebrowser/
Description: File/Folder Browser for WP Backend.
Author: Daniel H&uuml;sken
Version: 0.5.5
Author URI: http://danielhuesken.de
Text Domain: filebrowser
Domain Path: /lang/
*/

/*  
	Copyright 2009  Daniel Hüsken  (email : daniel@huesken-net.de)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

// don't load directly 
if ( !defined('ABSPATH') ) 
	die('-1');

//Set plugin dirname
define('FILEBROWSER_PLUGIN_DIR', dirname(plugin_basename(__FILE__)));
//Set Plugin Version
define('FILEBROWSER_VERSION', '0.5.5');
global $wp_version;
//load Text Domain
load_plugin_textdomain('filebrowser', false, dirname(plugin_basename(__FILE__)).'/lang');	
//Load functions file
require_once(plugin_dir_path(__FILE__).'app/functions.php');

//Version check
if (version_compare($wp_version, '2.8', '<')) { // Let only Activate on WordPress Version 2.8 or heiger
	add_action('admin_notices', create_function('', 'echo \'<div id="message" class="error fade"><p><strong>' . __('Sorry, FileBrowser works only under WordPress 2.8 or higher','filebrowser') . '</strong></p></div>\';'));
} else {
	//Plugin init	
	add_action('plugins_loaded', 'filebrowser_init');
}
?>
