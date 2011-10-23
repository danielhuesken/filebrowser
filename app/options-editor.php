<?php
// don't load directly 
if ( !defined('ABSPATH') ) 
	die('-1');	
?>
<div class="wrap">
	<div id="icon-tools" class="icon32"><br /></div>
<h2><?php _e("FileBrowser Editor", "filebrowser"); ?></h2>
<div class="alignleft">
<b><?php echo __('Edit:','filebrowser').' '.$_GET['selfile']; ?></b>
</div>
<br class="clear" /> 
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
		<div style="border: 1px solid black; padding: 0px;">
		<textarea cols="100" rows="25" name="newfilecontent" id="newfilecontent" tabindex="1"><?php echo $content ?></textarea>
		<input type="hidden" name="action" value="updatefile" />
		<input type="hidden" name="selfile" value="<?php echo esc_attr($file) ?>" />
		<input type="hidden" name="page" value="FileBrowser" />
		</div>
<?php
	echo '<input type="submit" name="submit" class="button-primary" value="' . esc_attr__('Update File') . '" tabindex="2" />';
?>
</form> 
<br class="clear" /> 
</div>
		<script type="text/javascript">
		var editor = CodeMirror.fromTextArea('newfilecontent', {
			height: "450px",
			parserfile: ["parsexml.js", "parsedummy.js", "parsecss.js", "tokenizejavascript.js", "parsejavascript.js","tokenizephp.js", "parsephp.js","parsephphtmlmixed.js","parselua.js"],
			stylesheet: ["<?PHP echo plugins_url('',__FILE__); ?>/codemirror/css/xmlcolors.css",
						"<?PHP echo plugins_url('',__FILE__); ?>/codemirror/css/jscolors.css",
						"<?PHP echo plugins_url('',__FILE__); ?>/codemirror/css/csscolors.css",
						"<?PHP echo plugins_url('',__FILE__); ?>/codemirror/css/luacolors.css", 
						"<?PHP echo plugins_url('',__FILE__); ?>/codemirror/css/phpcolors.css"],
			path: "<?PHP echo plugins_url('',__FILE__); ?>/codemirror/js/",
			continuousScanning: 500,
			lineNumbers: true,
			textWrapping: false
		});
		</script>