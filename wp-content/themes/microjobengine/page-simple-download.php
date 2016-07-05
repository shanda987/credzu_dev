<?php
/*
 * Template name: Download template
 */
if( isset($_GET['id']) ){
    $id = $_GET['id'];
    $file = get_attached_file($id);
    $file_name = basename( $file );
    $fp = fopen($file, 'rb');
    header("Content-Type: application/octet-stream");
    header("Content-Disposition: attachment; filename=$file_name");
    header("Content-Length: " . filesize($file));
    fpassthru($fp);
}
elseif( isset($_GET['cid']) && isset($_GET['n'])){
    $id = $_GET['cid'];
    $files  = get_post_meta($id, 'agreement_files', true);
    $f = '';
    if( !empty($files) ){
        foreach( $files as $file ){
            if( !empty($_GET['n']) && $file['name'] == $_GET['n'] ){
                $f = $file;
                break;
            }
        }
    }
    if( !empty($f) ) {
        $file = $f['path'];
        $fp = fopen($file, 'rb');
        $file_name = $f['name'];
        header("Content-Type: application/octet-stream");
        header("Content-Disposition: attachment; filename=$file_name");
        header("Content-Length: " . filesize($file));
        fpassthru($fp);
    }
}
else{
    if( isset($_GET['pid']) ){
        $id = $_GET['pid'];
        $file = get_post_meta($id, 'pdf_path', true);
        $fp = fopen($file, 'rb');
        $filename = basename($file);
        header("Content-Type: application/octet-stream");
        header("Content-Disposition: attachment; filename=$file_name");
        header("Content-Length: " . filesize($file));
        fpassthru($fp);
    }
}
?>