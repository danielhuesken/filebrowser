<?PHP
// don't load directly 
if ( !defined('ABSPATH') ) 
	die('-1');	
	
	//Thems Option menu entry
	function filebrowser_menu_entry() {
		$hook = add_management_page(__('FileBrowser','filebrowser'), __('FileBrowser','filebrowser'), '10', 'FileBrowser','filebrowser_options_page') ;
		add_action('load-'.$hook, 'filebrowser_options_load');
		add_contextual_help($hook,filebrowser_show_help());
	}	
	
	// Help too display
	function filebrowser_show_help() {
		$help .= '<div class="metabox-prefs">';
		$help .= '<a href="http://wordpress.org/tags/filebrowser" target="_blank">'.__('Support').'</a>';
		$help .= ' | <a href="http://wordpress.org/extend/plugins/filebrowser/faq/" target="_blank">' . __('FAQ') . '</a>';
		$help .= ' | <a href="http://danielhuesken.de/portfolio/filebrowser" target="_blank">' . __('Plugin Homepage', 'filebrowser') . '</a>';
		$help .= ' | <a href="http://wordpress.org/extend/plugins/filebrowser" target="_blank">' . __('Plugin Home on WordPress.org', 'filebrowser') . '</a>';
		$help .= ' | <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&amp;business=daniel%40huesken-net%2ede&amp;item_name=Daniel%20Huesken%20Plugin%20Donation&amp;item_number=FileBrowser&amp;no_shipping=0&amp;no_note=1&amp;tax=0&amp;currency_code=EUR&amp;lc=DE&amp;bn=PP%2dDonationsBF&amp;charset=UTF%2d8" target="_blank">' . __('Donate') . '</a>';
		$help .= "</div>\n";	
		$help .= '<div class="metabox-prefs">';
		$help .= __('Version:', 'backwpup').' '.FILEBROWSER_VERSION.' | ';
		$help .= __('Author:', 'backwpup').' <a href="http://danielhuesken.de" target="_blank">Daniel H&uuml;sken</a>';
		$help .= "</div>\n";
		return $help;
	}
	
	//Options Page
	function filebrowser_options_page() {
		global $filebrowser_message,$gotofolder;
		if (!current_user_can(10)) 
			wp_die('No rights');
		if(!empty($filebrowser_message)) 
			echo '<div id="message" class="updated fade"><p><strong>'.$filebrowser_message.'</strong></p></div>';
		switch($_GET['action']) {
		case 'edit':
			require_once(WP_PLUGIN_DIR.'/'.FILEBROWSER_PLUGIN_DIR.'/app/options-editor.php');
			break;
		default:
			require_once(WP_PLUGIN_DIR.'/'.FILEBROWSER_PLUGIN_DIR.'/app/options.php');
			break;
		}
	}
	
	//Options Page
	function filebrowser_options_load() {
		global $filebrowser_message,$gotofolder;
		$gotofolder=str_replace('\\','/',ABSPATH);
		if (!current_user_can(10)) 
			wp_die('No rights');
		//Css for Admin Section
		wp_enqueue_style('FileBrowser',plugins_url('/'.FILEBROWSER_PLUGIN_DIR.'/app/css/options.css'),'',FILEBROWSER_VERSION,'screen');
		if ($_GET['action']=='edit')
			wp_enqueue_script('CodeMirror',plugins_url('/'.FILEBROWSER_PLUGIN_DIR.'/app/codemirror/js/codemirror.js'),'','0.62',false);
		if ($_POST['action2']!='-1')
			$action=$_POST['action2'];
		if ($_POST['action']!='-1')
			$action=$_POST['action'];
		if (!empty($_GET['action']) and empty($action))
			$action=$_GET['action'];
		//For change folder by hand
		if ($_POST['doactiongo']==__('Go','filebrowser')) {
			if (@is_dir(str_replace('\\','/',realpath($_POST['root'].$_POST['newfolder'])))) {
				$gotofolder=str_replace('\\','/',realpath($_POST['root'].$_POST['newfolder']));
			} else {
				$gotofolder=$_POST['oldusedfolder'];
				$filebrowser_message=__('Could not jump to folder.','filebrowser');
			}
		}
		//For save Options
		require_once(WP_PLUGIN_DIR.'/'.FILEBROWSER_PLUGIN_DIR.'/app/options-save.php');
	}
	
	//add edit setting to plugins page
	function filebrowser_plugin_options_link($links) {
		$settings_link='<a href="admin.php?page=FileBrowser" title="' . __('Go to Settings Page','filebrowser') . '" class="edit">' . __('Settings') . '</a>';
		array_unshift( $links, $settings_link ); 
		return $links;
	}
	
	//add links on plugins page
	function filebrowser_plugin_links($links, $file) {
		if ($file == FILEBROWSER_PLUGIN_DIR.'/filebrowser.php') {
			$links[] = '<a href="http://wordpress.org/extend/plugins/filebrowser/faq/" target="_blank">' . __('FAQ') . '</a>';
			$links[] = '<a href="http://wordpress.org/tags/filebrowser/" target="_blank">' . __('Support') . '</a>';
			$links[] = '<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&amp;business=daniel%40huesken-net%2ede&amp;item_name=Daniel%20Huesken%20Plugin%20Donation&amp;item_number=FileBrowser&amp;no_shipping=0&amp;no_note=1&amp;tax=0&amp;currency_code=EUR&amp;lc=DE&amp;bn=PP%2dDonationsBF&amp;charset=UTF%2d8" target="_blank">' . __('Donate') . '</a>';
		}
		return $links;
	}
	
	function filebrowser_copydir($src,$dst) {
		$dir = @opendir($src);
		@mkdir($dst);
		while(false !== ( $file = readdir($dir)) ) {
			if (( $file != '.' ) && ( $file != '..' )) {
				if ( is_dir($src . '/' . $file) ) 
					filebrowser_copydir($src . '/' . $file,$dst . '/' . $file);
				else 
					@copy($src . '/' . $file,$dst . '/' . $file);
			}
		}
		closedir($dir);
	} 
	
	function filebrowser_rmdirr($folder) {
        $folder=trailingslashit($folder);
		if($dir = @opendir($folder)) {
            while (($file= readdir($dir)) !== false) {
                if(!is_dir($folder.$file) and $file!='..' and $file!='.') {
                    @unlink($folder.$file);
                } elseif(is_dir($folder.$file) and $file!='..' and $file!='.') {
                    filebrowser_rmdirr($folder.$file);
				}
            }
            closedir($dir);
            @rmdir($folder);
        }
    }
	
	function filebrowser_fileicon($file='') {
		$iconspath=WP_PLUGIN_URL.'/'.FILEBROWSER_PLUGIN_DIR.'/app/icons/';
		if ($file=='')
			return $iconspath.'file.png';
		if (is_dir($file))
			return $iconspath.'folder.png';
		if (is_link($file))
			return $iconspath.'link.png';
		if (is_executable($file))
			return $iconspath.'application.png';
		if (is_file($file)) {
			$extension = strtolower(pathinfo($folder.$file,PATHINFO_EXTENSION));
			if (in_array($extension,array('sh','bat','cmd')))
				return $iconspath.'application_xp_terminal.png';
			if ($extension=='ai')	
				return $iconspath.'ai.png';
			if ($extension=='aiff')
				return $iconspath.'aiff.png';
			if ($extension=='bz2')
				return $iconspath.'bz2.png';
			if ($extension=='c')
				return $iconspath.'c.png';
			if ($extension=='chm')
				return $iconspath.'chm.png';
			if ($extension=='conf')
				return $iconspath.'conf.png';
			if ($extension=='cpp')
				return $iconspath.'cpp.png';
			if ($extension=='css')
				return $iconspath.'css.png';
			if ($extension=='cvs')
				return $iconspath.'csv.png';
			if ($extension=='dep')
				return $iconspath.'deb.png';
			if ($extension=='dll')
				return $iconspath.'aplication.png';
			if ($extension=='divx')
				return $iconspath.'divx.png';
			if ($extension=='doc' or $extension=='docx')
				return $iconspath.'doc.png';
			if ($extension=='dot' or $extension=='dotx')
				return $iconspath.'dot.png';
			if ($extension=='eml')
				return $iconspath.'eml.png';
			if ($extension=='ttf' or $extension=='ttc')
				return $iconspath.'font.png';
			if ($extension=='gz')
				return $iconspath.'gz.png';
			if ($extension=='hlp')
				return $iconspath.'hlp.png';
			if ($extension=='htm' or $extension=='html')
				return $iconspath.'html.png';
			if ($extension=='iso' or $extension=='nrg')	
				return $iconspath.'iso.png';
			if (in_array($extension,array('jpg','jpeg','gif','png','bmp','ico')))
				return $iconspath.'image.png';
			if ($extension=='js')	
				return $iconspath.'js.png';
			if ($extension=='m')	
				return $iconspath.'m.png';
			if ($extension=='mm')
				return $iconspath.'mm.png';
			if ($extension=='mov')
				return $iconspath.'mov.png';
			if ($extension=='mp3')
				return $iconspath.'mp3.png';
			if ($extension=='mpg')
				return $iconspath.'mpg.png';
			if ($extension=='odc')
				return $iconspath.'odc.png';
			if ($extension=='odf')
				return $iconspath.'odf.png';
			if ($extension=='odg')
				return $iconspath.'odg.png';
			if ($extension=='odi')
				return $iconspath.'odi.png';
			if ($extension=='odp')
				return $iconspath.'odp.png';
			if ($extension=='ods')
				return $iconspath.'ods.png';
			if ($extension=='odt')
				return $iconspath.'odt.png';
			if ($extension=='ogg')
				return $iconspath.'ogg.png';
			if ($extension=='pdf')
				return $iconspath.'pdf.png';
			if ($extension=='pgp')
				return $iconspath.'pgp.png';
			if (in_array($extension,array('php','php3','php4','phtml','phtm')))
				return $iconspath.'php.png';
			if ($extension=='pl')	
				return $iconspath.'pl.png';
			if ($extension=='ppt' or $extension=='pptx')
				return $iconspath.'ppt.png';
			if ($extension=='ps')
				return $iconspath.'ps.png';
			if ($extension=='py')
				return $iconspath.'py.png';
			if ($extension=='ram')
				return $iconspath.'ram.png';
			if ($extension=='rar')
				return $iconspath.'rar.png';
			if ($extension=='rb')
				return $iconspath.'rb.png';
			if ($extension=='rm')
				return $iconspath.'rm.png';
			if ($extension=='rpm')
				return $iconspath.'rpm.png';
			if ($extension=='rtf')
				return $iconspath.'rtf.png';
			if ($extension=='sql')
				return $iconspath.'sql.png';
			if ($extension=='swf')
				return $iconspath.'swf.png';
			if ($extension=='sxc')
				return $iconspath.'sxc.png';
			if ($extension=='sxd')
				return $iconspath.'sxd.png';
			if ($extension=='sxi')
				return $iconspath.'sxi.png';
			if ($extension=='sxw')
				return $iconspath.'sxw.png';
			if ($extension=='tar')
				return $iconspath.'tar.png';
			if ($extension=='tex')
				return $iconspath.'tex.png';
			if ($extension=='tgz')
				return $iconspath.'tgz.png';
			if (in_array($extension,array('txt','nfo','log','ini','inf')))
				return $iconspath.'txt.png';
			if ($extension=='vcf')
				return $iconspath.'vcf.png';
			if ($extension=='vsd' or $extension=='vsdx')
				return $iconspath.'vsd.png';
			if ($extension=='wav')
				return $iconspath.'wav.png';
			if ($extension=='wma')
				return $iconspath.'wma.png';
			if ($extension=='wmv')
				return $iconspath.'wmv.png';
			if ($extension=='xls' or $extension=='xslx')
				return $iconspath.'xls.png';
			if ($extension=='xml')
				return $iconspath.'xml.png';
			if ($extension=='xpi')
				return $iconspath.'xpi.png';
			if ($extension=='xvid')
				return $iconspath.'xvid.png';
			if ($extension=='zip')
				return $iconspath.'zip.png';
			if ($extension=='voc')
				return $iconspath.'music.png';
			if ($extension=='flv')
				return $iconspath.'film.png';
		}
		return $iconspath.'file.png';
	}
	
	function filebrowser_premissions($file) {	
		$perms = fileperms($file);
		if (($perms & 0xC000) == 0xC000) // Socket
			$info = 's';
		elseif (($perms & 0xA000) == 0xA000) // Symbolic Link
			$info = 'l';
		elseif (($perms & 0x8000) == 0x8000) // Regular
			$info = '-';
		elseif (($perms & 0x6000) == 0x6000) // Block special	
			$info = 'b';
		elseif (($perms & 0x4000) == 0x4000) // Directory	
			$info = 'd';
		elseif (($perms & 0x2000) == 0x2000) // Character special	
			$info = 'c';
		elseif (($perms & 0x1000) == 0x1000) // FIFO pipe	
			$info = 'p';
		else // Unknown
			$info = 'u';

		// Owner
		$info .= (($perms & 0x0100) ? 'r' : '-');
		$info .= (($perms & 0x0080) ? 'w' : '-');
		$info .= (($perms & 0x0040) ?
					(($perms & 0x0800) ? 's' : 'x' ) :
					(($perms & 0x0800) ? 'S' : '-'));
		// Group
		$info .= (($perms & 0x0020) ? 'r' : '-');
		$info .= (($perms & 0x0010) ? 'w' : '-');
		$info .= (($perms & 0x0008) ?
					(($perms & 0x0400) ? 's' : 'x' ) :
					(($perms & 0x0400) ? 'S' : '-'));
		// World
		$info .= (($perms & 0x0004) ? 'r' : '-');
		$info .= (($perms & 0x0002) ? 'w' : '-');
		$info .= (($perms & 0x0001) ?
					(($perms & 0x0200) ? 't' : 'x' ) :
					(($perms & 0x0200) ? 'T' : '-'));

		return $info;
	}
	
	//file size
	function filebrowser_formatBytes($bytes, $precision = 2) {
		$units = array('B', 'KB', 'MB', 'GB', 'TB');
		$bytes = max($bytes, 0);
		$pow = floor(($bytes ? log($bytes) : 0) / log(1024));
		$pow = min($pow, count($units) - 1);
		$bytes /= pow(1024, $pow);
		return round($bytes, $precision) . ' ' . $units[$pow];
	} 
	
	// add all action and so on only if plugin loaded.
	function filebrowser_init() {
		//add Menu
		add_action('admin_menu', 'filebrowser_menu_entry');
		//Additional links on the plugin page
		if (current_user_can(10)) 
			add_filter('plugin_action_links_'.FILEBROWSER_PLUGIN_DIR.'/filebrowser.php', 'filebrowser_plugin_options_link');
		if (current_user_can('install_plugins')) 		
			add_filter('plugin_row_meta', 'filebrowser_plugin_links',10,2);
	} 	

?>