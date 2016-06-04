<?php
/*
 * Template name: Download template
 */
if( isset($_GET['id']) ){
    $id = $_GET['id'];
}
$file = get_attached_file($id);
$file_name = basename( $file );
$fp = fopen($file, 'rb');
header("Content-Type: application/octet-stream");
header("Content-Disposition: attachment; filename=$file_name");
header("Content-Length: " . filesize($file));
fpassthru($fp);
?>