<?php
/*
 * Template name: View Pdf template
 */
if( isset( $_GET['type'] ) && $_GET['type'] == 'attachment'){
    if (isset($_GET['pid'])) {
        $id = $_GET['pid'];
        //$file = get_attached_file($id);
        $pid = get_post($id);
        if( $pid && !is_wp_error($pid) ){
            $file = $pid->guid;
            $filename = basename($file);
            header('Content-Type: '.$pid->post_mime_type);
            header('Content-Disposition: inline; filename="' . $filename . '"');
            header('Content-Transfer-Encoding: binary');
            header('Accept-Ranges: bytes');
            echo file_get_contents($file);
            ob_end_flush();
        } else {
            _e("The document doesn't exist.", ET_DOMAIN);
        }
    }
}
else {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $file = get_attached_file($id);
        $filename = basename($file);
    } else if (isset($_GET['cid'])) {
        $id = $_GET['cid'];
        $files = get_post_meta($id, 'agreement_files', true);
        $f = '';
        if (!empty($files)) {
            foreach ($files as $file) {
                if (!empty($_GET['n']) && $file['name'] == $_GET['n']) {
                    $f = $file;
                    break;
                }
            }
        }
        $file = $f['path'];
        $filename = $f['name'];
        var_dump($file);
    } else if (isset($_GET['aid']) && $_GET['aid'] == 1) {
        global $user_ID;
        $profile = mJobProfileAction()->getProfile($user_ID);
        $file = $profile->company_agreement_link;
        $filename = basename($file);
    } else {
        $id = $_GET['pid'];
        $file = get_post_meta($id, 'pdf_path', true);
        $filename = basename($file);
    }
    if (!empty($file)) {
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="' . $filename . '"');
        header('Content-Transfer-Encoding: binary');
        header('Accept-Ranges: bytes');
        echo file_get_contents($file);
    } else {
        _e("The document doesn't exist.", ET_DOMAIN);
    }
}