<?php
/*
 * Template name: View Pdf template
 */
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
$file = $f['path'];
$filename = $f['name'];
header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="'.$filename.'"');
header('Content-Transfer-Encoding: binary');
header('Accept-Ranges: bytes');
echo file_get_contents($file);