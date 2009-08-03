<div class="wrap">
	<div id="icon-tools" class="icon32"><br /></div>
<h2><?php _e("FileBrowser Editor", "filebrowser"); ?></h2>
<div class="alignleft">
<big><?php echo __('Edit:','filebrowser').' '.$_GET['selfile']; ?></big>
</div>
<form id="fileseditor" action="" method="post">
<?php 
	check_admin_referer('edit-file');
	wp_nonce_field('fileeditor'); 
	$file=$_GET['selfile'];
	if ( is_file($file) && filesize($file) > 0 ) {
		$f = fopen($file, 'r');
		$content = htmlspecialchars(fread($f, filesize($file)));
		$codepress_lang = codepress_get_lang($file);
	}
?>
		<div>
		<textarea cols="120" rows="25" name="newfilecontent" id="newfilecontent" tabindex="1" class="codepress <?php echo $codepress_lang ?>"><?php echo $content ?></textarea>
		<input type="hidden" name="action" value="updatefile" />
		<input type="hidden" name="selfile" value="<?php echo esc_attr($file) ?>" />
		<input type="hidden" name="page" value="FileBrowser" />
		</div>
<?php
	echo "<input type='submit' name='submit' class='button-primary' value='" . esc_attr__('Update File') . "' tabindex='2' />";
?>
</form> 
<br class="clear" /> 
<div>