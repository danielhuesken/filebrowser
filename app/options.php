 <?php
 	//Set dir
	$folder=trailingslashit(str_replace('\\','/',realpath($_GET['file'])));
	if (empty($_GET['file']))
		$folder=trailingslashit(str_replace('\\','/',ABSPATH));
 ?>
<div class="wrap">
	<div id="icon-tools" class="icon32"><br /></div>
<h2><?php _e("FileBrowser", "filebrowser"); ?>&nbsp;<a href="<?PHP echo wp_nonce_url('admin.php?page=FileBrowser&action=new&file='.$folder, 'filebrowser').'#new'; ?>" class="button add-new-h2"><?php esc_html_e('Add New'); ?></a></h2>

<form id="filebrowser" action="" method="post" enctype="multipart/form-data">
<?php wp_nonce_field('filebrowser'); ?>
<input type="hidden" name="page" value="FileBrowser" />
<div class="tablenav"> 
 
<div class="alignleft actions"> 
<select name="action" class="select-action"> 
<option value="-1" selected="selected"><?PHP _e('Bulk Actions','filebrowser'); ?></option> 
<option value="delete"><?PHP _e('Delete','filebrowser'); ?></option>
<option value="copy"><?PHP _e('Copy','filebrowser'); ?></option> 
<option value="move"><?PHP _e('Move','filebrowser'); ?></option>
<option value="zip"><?PHP _e('Zip','filebrowser'); ?></option> 
</select> 
<input type="submit" value="<?PHP _e('Apply','filebrowser'); ?>" name="doaction" id="doaction" class="button-secondary action" /> 
</div> 
 
<br class="clear" /> 
</div> 

<div class="clear"></div> 
<div class="alignleft">
<big><?php echo $folder; ?></big>&nbsp;
<a href="admin.php?page=FileBrowser&file=<?PHP echo $folder.'..'; if (!empty($_GET['copyfiles'])) echo '&copyfiles='.esc_attr($_GET['copyfiles']); if (!empty($_GET['movefiles'])) echo '&movefiles='.esc_attr($_GET['movefiles']);?>"><img src="<?PHP echo WP_PLUGIN_URL.'/'.FILEBROWSER_PLUGIN_DIR; ?>/app/icons/arrow_undo.png" border="0" title="<?PHP _e('..','filebrowser');?>" /></a>&nbsp;
<a href="admin.php?page=FileBrowser&file=<?PHP echo $folder; if (!empty($_GET['copyfiles'])) echo '&copyfiles='.esc_attr($_GET['copyfiles']); if (!empty($_GET['movefiles'])) echo '&movefiles='.esc_attr($_GET['movefiles']);?>"><img src="<?PHP echo WP_PLUGIN_URL.'/'.FILEBROWSER_PLUGIN_DIR; ?>/app/icons/arrow_refresh.png" border="0" title="<?PHP _e('Refresh','filebrowser');?>" /></a>&nbsp;
<?PHP if (!empty($_GET['copyfiles'])) { ?>
	<a href="<?PHP echo wp_nonce_url('admin.php?page=FileBrowser&action=copynow&copyfiles='.esc_attr($_GET['copyfiles']).'&copyto='.esc_attr($folder), 'filebrowser'); ?>"><img src="<?PHP echo WP_PLUGIN_URL.'/'.FILEBROWSER_PLUGIN_DIR; ?>/app/icons/arrow_in.png" border="0" title="<?PHP _e('Copy hier','filebrowser');?>" /></a>&nbsp;
<?PHP } ?>
<?PHP if (!empty($_GET['movefiles'])) { ?>
	<a href="<?PHP echo wp_nonce_url('admin.php?page=FileBrowser&action=movenow&movefiles='.esc_attr($_GET['movefiles']).'&moveto='.esc_attr($folder), 'filebrowser'); ?>"><img src="<?PHP echo WP_PLUGIN_URL.'/'.FILEBROWSER_PLUGIN_DIR; ?>/app/icons/arrow_in.png" border="0" title="<?PHP _e('Move hier','filebrowser');?>" /></a>&nbsp;
<?PHP } ?>
</div>
<table class="widefat fixed" cellspacing="0"> 
	<thead> 
	<tr> 
	<th scope="col" id="cb" class="manage-column column-cb check-column" style=""><input type="checkbox" /></th> 
	<th scope="col" id="name" class="manage-column column-name" style=""><?PHP _e('Name','filebrowser'); ?></th> 
	<th scope="col" id="size" class="manage-column column-size" style=""><?PHP _e('Size','filebrowser'); ?></th> 
	<th scope="col" id="date" class="manage-column column-mdate" style=""><?PHP _e('Date','filebrowser'); ?></th> 
	<?PHP if (function_exists('posix_getpwuid') and function_exists('posix_getgrgid')) {?>
		<th scope="col" id="premissions" class="manage-column column-premissions" style=""><?PHP _e('Permissions','filebrowser'); ?></th> 
	<?PHP } ?>
	</tr> 
	</thead> 
 
	<tfoot> 
	<tr> 
	<th scope="col" class="manage-column column-cb check-column" style=""><input type="checkbox" /></th> 
	<th scope="col" class="manage-column column-name" style=""><?PHP _e('Name','filebrowser'); ?></th> 
	<th scope="col" class="manage-column column-size" style=""><?PHP _e('Size','filebrowser'); ?></th> 
	<th scope="col" class="manage-column column-mdate" style=""><?PHP _e('Date','filebrowser'); ?></th> 
	<?PHP if (function_exists('posix_getpwuid') and function_exists('posix_getgrgid')) {?>
		<th scope="col" class="manage-column column-premissions" style=""><?PHP _e('Permissions','filebrowser'); ?></th>
	<?PHP } ?>
	</tr> 
	</tfoot> 
 
	<tbody id="the-list" class="list:post"> 
	
	<?PHP
	$dirs=array();
	//Open dir
	if ( $dir = @opendir( $folder ) ) {
		while (false !== ($file = readdir($dir))) {
			if ($file=='.' or $file=='..')
				continue;
			if (is_dir($folder.$file))
				$dirs[]=$folder.$file;
		}
		@closedir( $dir );
		sort($dirs);
	}
	$files=array();
	if ( $dir = @opendir( $folder ) ) {
		while (false !== ($file = readdir($dir))) {
			if (!is_dir($folder.$file))
				$files[]=$folder.$file;
		}
		@closedir( $dir );
		sort($files);
	}
	$files=array_merge($dirs,$files);

	
	
	if ($_GET['action']=='new') {
	?>
	<tr class="alternate status-inherit" valign="top"> 
		<th scope="row" class="check-column">&nbsp;</th> 
		<td class="name column-name">
			<a name="new"><img src="<?PHP echo WP_PLUGIN_URL.'/'.FILEBROWSER_PLUGIN_DIR.'/app/icons/file.png'; ?>" height="16" width="16" border="0" title="<?PHP _e('Create empty','filebrowser'); ?>" /></a>&nbsp;
			<input type="text" class="regular-text" name="newname" size="30" value="" />
			<select name="type"><option value="dir"><?PHP _e('Directory','filebrowser');?></option><option value="file"><?PHP _e('File','filebrowser');?></option></select><br />
			<input type="hidden" name="dir" value="<?PHP echo esc_attr($folder);?>" />
			<input type="hidden" name="action" value="makenew" />
			<img src="<?PHP echo WP_PLUGIN_URL.'/'.FILEBROWSER_PLUGIN_DIR.'/app/icons/disk.png'; ?>" height="16" width="16" border="0" title="<?PHP _e('Upload file','filebrowser'); ?>" />&nbsp;<input name="uplodfile" type="file" size="40" class="file"> <?PHP echo _e('Max size:','filebrowser').' '.ini_get('upload_max_filesize');?><br />
			<img src="<?PHP echo WP_PLUGIN_URL.'/'.FILEBROWSER_PLUGIN_DIR.'/app/icons/connect.png'; ?>" height="16" width="16" border="0" title="<?PHP _e('Copy file from URL','filebrowser'); ?>" />&nbsp;<input type="text" class="regular-text" size="40" name="copyfile" value="" />&nbsp;
			<input type="submit" value="<?PHP _e('Apply','filebrowser'); ?>" name="doaction" id="doaction" class="button-secondary action" />
		</td>
		<td class="column-size">&nbsp;</td> 
		<td class="column-mdate">&nbsp;</td>
		<td class="column-premissions">&nbsp;</td>
		</td> 
	</tr>
	<?PHP 
	}
	
	if ($_GET['action']=='zip' or $_POST['action']=='zip' or $_POST['action2']=='zip') {
	?>
	<tr class="alternate status-inherit" valign="top"> 
		<th scope="row" class="check-column">&nbsp;</th> 
		<td class="name column-name">
			<a name="new"><img src="<?PHP echo WP_PLUGIN_URL.'/'.FILEBROWSER_PLUGIN_DIR.'/app/icons/zip.png'; ?>" height="16" width="16" border="0" /></a>&nbsp;
			<input type="text" class="regular-text" name="zipname" value="<?PHP echo basename($file);?>" />
			<input type="hidden" name="dir" value="<?PHP echo esc_attr($folder);?>" />
			<?PHP
			if(is_array($_POST['selfiles'])) {
				$zipfiles=implode(';',$_POST['selfiles']);
			} else {
				$zipfiles=$_GET['selfiles'];
			}		
			?>
			<input type="hidden" name="zipfiles" value="<?PHP echo esc_attr($zipfiles);?>" />
			<input type="hidden" name="action" value="makezip" />
			<input type="submit" value="<?PHP _e('Create Zip','filebrowser'); ?>" name="doaction" id="doaction" class="button-secondary action" />
		</td>
		<td class="column-size">&nbsp;</td> 
		<td class="column-mdate">&nbsp;</td>
		<td class="column-premissions">&nbsp;</td>
		</td> 
	</tr>
	<?PHP 
	}
	
	foreach ($files as $file) {
			if (!($filestats=stat($file)))
				unset($filestats);
	?>
	<tr class="alternate status-inherit" valign="top"> 
		<th scope="row" class="check-column">
			<input type="checkbox" name="selfiles[]" value="<?PHP echo esc_attr($file);?>" />
		</th> 
		<?PHP if (is_dir($file)) {?>
		<td class="name column-name">
					<a name="<?PHP echo basename($file);?>"><img src="<?PHP echo filebrowser_fileicon($file); ?>" height="16" width="16" border="0" /></a>&nbsp;
					<?PHP if ($_GET['action']=='rename' and $_GET['filerename']==$file) {?>
						<input type="text" class="regular-text" name="newname" value="<?PHP echo basename($file);?>" /><input type="hidden" name="oldfile" value="<?PHP echo esc_attr($file);?>" /><input type="hidden" name="action" value="renamenow" /><input type="submit" value="<?PHP _e('Apply','filebrowser'); ?>" name="doaction" class="button-secondary action" />
					<?PHP } else { ?>
						<strong><a href="admin.php?page=FileBrowser&file=<?PHP echo esc_attr($file); if (!empty($_GET['copyfiles'])) echo '&copyfiles='.esc_attr($_GET['copyfiles']); if (!empty($_GET['movefiles'])) echo '&movefiles='.esc_attr($_GET['movefiles']);?>"><?PHP echo basename($file);?></a></strong>
					<?PHP } ?>
					<div class="row-actions">
						<?PHP if (is_writable($file)) {?>
							<span class="delete"><a class="submitdelete" href="<?PHP echo wp_nonce_url('admin.php?page=FileBrowser&action=delete&selfiles='.esc_attr($file), 'filebrowser'); ?>" onclick="if ( confirm('<?PHP echo esc_js(__("You are about to delete this File/Dir. \n  'Cancel' to stop, 'OK' to delete.","filebrowser")) ?>') ){return true;}return false;"><?PHP _e('Delete','filebrowser'); ?></a> | </span>
						<?PHP } ?>
						<span class="copy"><a href="<?PHP echo wp_nonce_url('admin.php?page=FileBrowser&action=copy&selfiles='.esc_attr($file), 'filebrowser'); ?>"><?PHP _e('Copy','filebrowser'); ?></a> | </span>
						<span class="move"><a href="<?PHP echo wp_nonce_url('admin.php?page=FileBrowser&action=move&selfiles='.esc_attr($file), 'filebrowser'); ?>"><?PHP _e('Move','filebrowser'); ?></a> | </span>
						<span class="rename"><a href="<?PHP echo wp_nonce_url('admin.php?page=FileBrowser&action=rename&filerename='.esc_attr($file).'&file='.esc_attr(dirname($file)), 'rename-file_'.esc_attr($file)).'#'.basename($file); ?>"><?PHP _e('Rename','filebrowser'); ?></a> | </span>
						<?PHP if (function_exists('posix_getpwuid') and function_exists('posix_getgrgid')) {?>
							<span class="premissions"><a href="<?PHP echo wp_nonce_url('admin.php?page=FileBrowser&action=premissions&filepremissions='.esc_attr($file), 'premissions-file_'.esc_attr($file)).'#'.basename($file); ?>"><?PHP _e('Premissions','filebrowser'); ?></a> | </span>
						<?PHP } ?>
						<span class="zip"><a href="<?PHP echo wp_nonce_url('admin.php?page=FileBrowser&action=zip&selfiles='.esc_attr($file), 'filebrowser'); ?>"><?PHP _e('Zip','filebrowser'); ?></a></span>
					</div>
		</td> 		
		<?PHP } else { ?>
		<td class="name column-name">
					<a name="<?PHP echo basename($file);?>"><img src="<?PHP echo filebrowser_fileicon($file); ?>" height="16" width="16" border="0" /></a>&nbsp;
					<?PHP if ($_GET['action']=='rename' and $_GET['filerename']==$file) {?>
						<input type="text" class="regular-text" name="newname" value="<?PHP echo basename($file);?>" /><input type="hidden" name="oldfile" value="<?PHP echo esc_attr($file);?>" /><input type="hidden" name="action" value="renamenow" /><input type="submit" value="<?PHP _e('Apply','filebrowser'); ?>" name="doaction" class="button-secondary action" />
					<?PHP } else { ?>
						<strong><a href="<?PHP echo wp_nonce_url('admin.php?page=FileBrowser&action=download&file='.esc_attr($file), 'download-file_'.esc_attr($file)); ?>"><?PHP echo basename($file);?></a></strong>
					<?PHP } ?>
					<div class="row-actions">
						<?PHP if (is_writable($file) and in_array(strtolower(pathinfo($file,PATHINFO_EXTENSION)),array('php','txt','log','html','htm','php3','css','js','ini','nfo','sh','cmd','bat','htaccess'))) {?>
							<span class="edit"><a href="<?PHP echo wp_nonce_url('admin.php?page=FileBrowser&action=edit&selfile='.esc_attr($file), 'edit-file'); ?>"><?PHP _e('Edit','filebrowser'); ?></a> | </span>
						<?PHP } ?>
						<?PHP if (is_writable($file)) {?>
							<span class="delete"><a class="submitdelete" href="<?PHP echo wp_nonce_url('admin.php?page=FileBrowser&action=delete&selfiles='.esc_attr($file), 'filebrowser'); ?>" onclick="if ( confirm('<?PHP echo esc_js(__("You are about to delete this File/Dir. \n  'Cancel' to stop, 'OK' to delete.","filebrowser")) ?>') ){return true;}return false;"><?PHP _e('Delete','filebrowser'); ?></a> | </span>
						<?PHP } ?>
						<span class="copy"><a href="<?PHP echo wp_nonce_url('admin.php?page=FileBrowser&action=copy&selfiles='.esc_attr($file), 'filebrowser'); ?>"><?PHP _e('Copy','filebrowser'); ?></a> | </span>
						<span class="move"><a href="<?PHP echo wp_nonce_url('admin.php?page=FileBrowser&action=move&selfiles='.esc_attr($file), 'filebrowser'); ?>"><?PHP _e('Move','filebrowser'); ?></a> | </span>
						<span class="rename"><a href="<?PHP echo wp_nonce_url('admin.php?page=FileBrowser&action=rename&filerename='.esc_attr($file).'&file='.esc_attr(dirname($file)), 'rename-file_'.esc_attr($file)).'#'.basename($file); ?>"><?PHP _e('Rename','filebrowser'); ?></a> | </span>
						<?PHP if (function_exists('posix_getpwuid') and function_exists('posix_getgrgid')) {?>
							<span class="premissions"><a href="<?PHP echo wp_nonce_url('admin.php?page=FileBrowser&action=premissions&filepremissions='.esc_attr($file), 'premissions-file_'.esc_attr($file)).'#'.basename($file); ?>"><?PHP _e('Premissions','filebrowser'); ?></a> | </span>
						<?PHP }
						if (strtolower(pathinfo($file,PATHINFO_EXTENSION))=="zip") {?>
							<span class="zip"><a href="<?PHP echo wp_nonce_url('admin.php?page=FileBrowser&action=unzip&selfiles='.esc_attr($file), 'filebrowser'); ?>"><?PHP _e('UnZip here','filebrowser'); ?></a></span>
						<?PHP } else { ?>
							<span class="zip"><a href="<?PHP echo wp_nonce_url('admin.php?page=FileBrowser&action=zip&selfiles='.esc_attr($file), 'filebrowser'); ?>"><?PHP _e('Zip','filebrowser'); ?></a></span>
						<?PHP } ?>
					</div>
		</td> 
		<?PHP } ?>
		<td class="column-size">
			<?PHP
			if (is_dir($file))
				echo '-';
			else
				echo filebrowser_formatBytes($filestats['size']); 
			?>
		</td> 
		<td class="column-mdate">
			<?PHP
			if (is_array($filestats)) {
				echo date(get_option('date_format'),$filestats['mtime']).' '.date(get_option('time_format'),$filestats['mtime']);
			}
			?>
		</td> 
		
		<?PHP if (function_exists('posix_getpwuid') and function_exists('posix_getgrgid')) {?>
		<td class="column-premissions">
			<?PHP
			$owner=posix_getpwuid($filestats['uid']);
			$grp=posix_getgrgid($filestats['gid']);
			if ($_GET['action']=='premissions' and $_GET['filepremissions']==$file) {
				$prem=filebrowser_premissions($file);
				$owner=posix_getpwuid($filestats['uid']);
				$grp=posix_getgrgid($filestats['gid']);
				echo __('Own:','filebrowser').' <input class="checkbox" value="400" type="checkbox"'.checked(substr($prem,1,1),'r',false).' name="prems[]" />r <input class="checkbox" value="200" type="checkbox"'.checked(substr($prem,2,1),'w',false).' name="prems[]" />w <input class="checkbox" value="100" type="checkbox"'.checked(substr($prem,3,1),'x',false).' name="prems[]" />x<br />';
				echo __('Grp:','filebrowser').' <input class="checkbox" value="40" type="checkbox"'.checked(substr($prem,4,1),'r',false).' name="prems[]" />r <input class="checkbox" value="20" type="checkbox"'.checked(substr($prem,5,1),'w',false).' name="prems[]" />w <input class="checkbox" value="10" type="checkbox"'.checked(substr($prem,6,1),'x',false).' name="prems[]" />x<br />';
				echo __('Pub:','filebrowser').' <input class="checkbox" value="4" type="checkbox"'.checked(substr($prem,7,1),'r',false).' name="prems[]" />r <input class="checkbox" value="2" type="checkbox"'.checked(substr($prem,8,1),'w',false).' name="prems[]" />w <input class="checkbox" value="1" type="checkbox"'.checked(substr($prem,9,1),'x',false).' name="prems[]" />x<br />';
				echo __('Own:','filebrowser').' <input class="small-text" value="'.esc_attr($owner['name']).'" type="text" name="owner" /><br />';
				echo __('Grp:','filebrowser').' <input class="small-text" value="'.esc_attr($grp['name']).'" type="text" name="group" /><br />';
				echo '<input type="hidden" name="changefile" value="'.esc_attr($file).'" /><input type="hidden" name="action" value="permissionsnow" /><input type="submit" value="'.__('Apply','filebrowser').'" name="doaction" class="button-secondary action" />';
			} else {
				if (is_array($filestats)) {
					echo filebrowser_premissions($file).'<br />';
					echo $owner['name'].' ';
					echo $grp['name'];
				}
			}
			?>
		</td>
		<?PHP } ?>
	</tr>
	<?PHP 
	}
	?>
	</tbody> 
</table> 
  
<div class="tablenav"> 
<div class="alignleft actions"> 
<select name="action2" class="select-action"> 
<option value="-1" selected="selected"><?PHP _e('Bulk Actions','backwpup'); ?></option> 
<option value="delete"><?PHP _e('Delete','filebrowser'); ?></option>
<option value="copy"><?PHP _e('Copy','filebrowser'); ?></option> 
<option value="move"><?PHP _e('Move','filebrowser'); ?></option>
<option value="zip"><?PHP _e('Zip','filebrowser'); ?></option>
</select> 
<input type="submit" value="<?PHP _e('Apply','backwpup'); ?>" name="doaction2" id="doaction2" class="button-secondary action" /> 
</div> 
 
<br class="clear" /> 
</div> 
</form> 
<br class="clear" /> 
 
<div>