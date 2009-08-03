<?php
switch($action) {
case 'updatefile':
	check_admin_referer('fileeditor');
	if (is_writeable($_POST['selfile']) and !empty($_POST['newfilecontent'])) {
		if ($f = fopen($_POST['selfile'], 'w+')) {
			fwrite($f, stripslashes($_POST['newfilecontent']));
			fclose($f);
			$filebrowser_message=__('File edited successfully.','filebrowser');
		} else {
			$filebrowser_message=__('Could not save to file.','filebrowser');
		} 
	} else {
		$filebrowser_message=__('Could not write file.','filebrowser');
	}
	$_GET['file']=dirname($_POST['selfile']);
	unset($_GET['action']);
	break;
case 'download':
	$file = $_GET['file'];
	check_admin_referer('download-file_'.$file);
	if (is_file($file)) {
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
		header("Content-Type: application/force-download");
		header("Content-Type: application/octet-stream");
		header("Content-Type: application/download");
		header("Content-Disposition: attachment; filename=".basename($file).";");
		header("Content-Transfer-Encoding: binary");
		header("Content-Length: ".filesize($file));
		@readfile($file);
	} else {
		header('HTTP/1.0 404 Not Found');
		die(__('File does not exist.', 'backwpup'));
	}	
	break;
case 'delete' :
	check_admin_referer('filebrowser');
	unset($_GET['copyfiles']);
	unset($_GET['movefiles']);
	if(is_array($_POST['selfiles']))
		$files = $_POST['selfiles'];
	else 
		$files[0] = $_GET['selfiles'];
	foreach ($files as $file) {
		if (is_dir($file)) {
			filebrowser_rmdirr($file);
			$filebrowser_message.=str_replace('%1',basename($file),__('Dir %1 deleted recursiv.','filebrowser')).'<br />';
		} else {
			if (@unlink($file))
				$filebrowser_message.=str_replace('%1',basename($file),__('File %1 deleted.','filebrowser')).'<br />';
			else 
				$filebrowser_message.=str_replace('%1',basename($file),__('File %1 NOT deleted.','filebrowser')).'<br />';
		}
	}
	$_GET['file']=dirname($files[0]);
	break;
case 'renamenow' :
	check_admin_referer('filebrowser');
	unset($_GET['copyfiles']);
	unset($_GET['movefiles']);
	$oldfile=$_POST['oldfile'];
	$newname=$_POST['newname'];
	if (!empty($oldfile) and !empty($newname)) {
		if (is_dir($oldfile)) {
			if (@rename($oldfile,realpath($oldfile.'/..').'/'.$newname))
				$filebrowser_message=str_replace('%1',basename($oldfile),__('Dir %1 renamed to','filebrowser').' '.$newname);
			else
				$filebrowser_message=str_replace('%1',basename($oldfile),__('Dir %1 NOT renamed.','filebrowser'));
		} else {
			if (@rename($oldfile,dirname($oldfile).'/'.$newname))
				$filebrowser_message=str_replace('%1',basename($oldfile),__('File %1 renamed to.','filebrowser').' '.$newname);
			else
				$filebrowser_message=str_replace('%1',basename($oldfile),__('File %1 NOT renamed.','filebrowser'));
		}
	}
	$_GET['file']=dirname($oldfile);
	break;
case 'permissionsnow' :
	check_admin_referer('filebrowser');
	unset($_GET['copyfiles']);
	unset($_GET['movefiles']);
	$prems=$_POST['prems'];
	$changefile=$_POST['changefile'];
	$owner=$_POST['owner'];
	$group=$_POST['group'];
	if (is_array($prems) and !empty($changefile)) {
		$mode=0;
		foreach ($prems as $octals) {
			$mode+=$octals;
		}
		if (chmod($changefile,'0'.$mode))
			$filebrowser_message=str_replace('%1',basename($changefile),__('Permissions of File %1 changed to','filebrowser').' '.$mode);
	}
	if (!empty($owner) and !empty($changefile)) {
		if (chown($changefile,$owner))
			$filebrowser_message=str_replace('%1',basename($changefile),__('Owner of File %1 changed to','filebrowser').' '.$owner);		
	}
	if (!empty($group) and !empty($changefile)) {
		if (chgrp($changefile,$group))
			$filebrowser_message=str_replace('%1',basename($changefile),__('Group of File %1 changed to','filebrowser').' '.$group);		
	}
	$_GET['file']=dirname($changefile);
	$_GET['action']='';
	$_POST['action']='';
	break;
case 'makenew' :
	check_admin_referer('filebrowser');
	unset($_GET['copyfiles']);
	unset($_GET['movefiles']);
	$newname=$_POST['newname'];
	$type=$_POST['type'];
	$folder=$_POST['dir'];
	$copyfile=$_POST['copyfile'];
	if (!empty($folder) and !empty($newname)) {
		if ($type=='dir') {
			if (@mkdir($folder.$newname,0777))
				$filebrowser_message=str_replace('%1',$newname,__('Dir %1 created.','filebrowser'));
			else 
				$filebrowser_message=str_replace('%1',$newname,__('Dir %1 NOT created.','filebrowser'));
		} elseif ($type=='file') {
			if ($fd=@fopen($folder.$newname,'w')) {
				fwrite($fd,' ');
				fclose($fd);
				//chmod($folder.$newname,$premissions);
				$filebrowser_message=str_replace('%1',$newname,__('File %1 created.','filebrowser'));
			} else {
				$filebrowser_message=str_replace('%1',$newname,__('File %1 NOT created.','filebrowser'));
			}
		}
	}
	if (is_uploaded_file($_FILES['uplodfile']['tmp_name'])) {
		if (move_uploaded_file($_FILES['uplodfile']['tmp_name'],$folder.$_FILES["uplodfile"]["name"])) 
			$filebrowser_message=str_replace('%1',$_FILES["uplodfile"]["name"],__('File %1 uploaded.','filebrowser'));
	}
	if (!empty($folder) and !empty($copyfile)) {
		if (copy($copyfile,$folder.basename($copyfile)))
			$filebrowser_message=str_replace('%1',basename($copyfile),__('File %1 copyed.','filebrowser'));
		else
			$filebrowser_message=str_replace('%1',$copyfile,__('File %1 NOT copyed.','filebrowser'));
	}
	$_GET['file']=$folder;
	$_GET['action']='';
	break;
case 'makezip' :
	check_admin_referer('filebrowser');
	unset($_GET['copyfiles']);
	unset($_GET['movefiles']);
	ignore_user_abort(true);
	@set_time_limit(300);
	$zipname=$_POST['zipname'];
	$zipfiles=explode(';',$_POST['zipfiles']);
	$folder=$_POST['dir'];
	if (strtolower(pathinfo($zipname,PATHINFO_EXTENSION))!="zip")
		$zipname.='.zip';
	if (!empty($zipfiles) and !empty($zipname)) {
		require_once(ABSPATH . 'wp-admin/includes/class-pclzip.php');
		define( 'PCLZIP_TEMPORARY_DIR', get_temp_dir());
		$zipbackupfile = new PclZip($folder.$zipname);
		if (0==$zipbackupfile -> create($zipfiles,PCLZIP_OPT_REMOVE_PATH,$folder,PCLZIP_OPT_ADD_TEMP_FILE_ON)) {
			$filebrowser_message=__('Can NOT create Zip file:','filebrowser').' '.$zipbackupfile->errorInfo(true);
		} else {
			$filebrowser_message=str_replace('%1',$zipname,__('Zip File %1 created.','filebrowser'));
		}
	}
	$_GET['file']=$folder;
	$_GET['action']='';
	break;
case 'unzip' :
	check_admin_referer('filebrowser');
	ignore_user_abort(true);
	@set_time_limit(300);
	$zipname=$_GET['selfiles'];
	$folder=dirname($_GET['selfiles']);
	if (!empty($zipname) and is_file($zipname)) {
		require_once(ABSPATH . 'wp-admin/includes/class-pclzip.php');
		define( 'PCLZIP_TEMPORARY_DIR', get_temp_dir());
		$zipbackupfile = new PclZip($zipname);
		if (0==$zipbackupfile -> extract($folder,PCLZIP_OPT_ADD_TEMP_FILE_ON)) {
			$filebrowser_message=__('Can NOT extract Zip file:','filebrowser').' '.$zipbackupfile->errorInfo(true);
		} else {
			$filebrowser_message=str_replace('%1',$zipname,__('Zip File %1 extracted.','filebrowser'));
		}
	}
	$_GET['file']=$folder;
	$_GET['action']='';
	break;
case 'copy' :
	check_admin_referer('filebrowser');
	unset($_GET['copyfiles']);
	if(is_array($_POST['selfiles'])) {
		$_GET['copyfiles']=implode(';',$_POST['selfiles']);
		$folder=$_POST['selfiles'][0];
	} else {
		$_GET['copyfiles']=$_GET['selfiles'];
		$folder=$_GET['selfiles'];
	}
	unset($_GET['movefiles']);
	$_GET['file']=dirname($folder);
	break;
case 'move' :
	check_admin_referer('filebrowser');
	unset($_GET['movefiles']);
	if(is_array($_POST['selfiles'])) {
		$_GET['movefiles']=implode(';',$_POST['selfiles']);
		$folder=$_POST['selfiles'][0];
	} else {
		$_GET['movefiles']=$_GET['selfiles'];
		$folder=$_GET['selfiles'];
	}
	unset($_GET['copyfiles']);
	$_GET['file']=dirname($folder);
	break;
case 'copynow' :
	check_admin_referer('filebrowser');
	if (!empty($_GET['copyfiles']))
		$files = explode(";",$_GET['copyfiles']);
	$to=$_GET['copyto'];
	foreach ($files as $file) {
		if (is_dir($file)) {
			if (is_dir($to.basename($file)))
				$dirto=$to.__('Copy of','filebrowser').' '.basename($file);
			else
				$dirto=$to.basename($file);
			filebrowser_copydir($file,$dirto);
			$filebrowser_message.=str_replace('%1',basename($dirto),__('Dir to %1 copyed.','filebrowser')).'<br />';
		} else {
			if (is_file($to.basename($file)))
				$fileto=$to.__('Copy of','filebrowser').' '.basename($file);
			else
				$fileto=$to.basename($file);
			if (copy($file,$fileto))
				$filebrowser_message.=str_replace('%1',basename($file),__('File %1 copyed to.','filebrowser')).' '.basename($fileto).'<br />';
			else 
				$filebrowser_message.=str_replace('%1',basename($file),__('File %1 NOT coyed.','filebrowser')).'<br />';
		}
	}	
	$_GET['file']=$to;
	unset($_GET['copyfiles']);
	break;
case 'movenow' :
	check_admin_referer('filebrowser');
	if (!empty($_GET['movefiles']))
		$files = explode(";",$_GET['movefiles']);
	$to=$_GET['moveto'];
	foreach ($files as $file) {
		if (@rename($file,$to.basename($file)))
			$filebrowser_message.=str_replace('%1',basename($file),__('File/Dir %1 moved to','filebrowser').' '.$to.basename($file)).'<br />';
		else 
			$filebrowser_message.=str_replace('%1',basename($file),__('File/Dir %1 NOT moved.','filebrowser')).'<br />';
	}
	$_GET['file']=$to;
	unset($_GET['movefiles']);
	break;
}
?>