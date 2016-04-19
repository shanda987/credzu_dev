<?php
global $post, $user_ID, $ae_post_factory;
$obj = $ae_post_factory->get('mjob_order');
$current = $obj->convert($post);
if( ($post->post_status == 'publish' ||  $post->post_status == 'delivery') ):
    if(  ($post->post_status == 'publish' && $user_ID == $current->mjob_author ) || ($post->post_status == 'delivery' && $user_ID == $current->post_author ) ):
?>
<div class="filter-by">
    <select name="post_status" class="order-action">
        <option value=""><?php _e('Select an action', ET_DOMAIN); ?></option>
        <?php if($post->post_status == 'publish' && $user_ID == $current->mjob_author ): ?>
        <option value="late"><?php _e('Late', ET_DOMAIN); ?></option>
        <?php elseif($post->post_status == 'delivery' && $user_ID == $current->post_author ): ?>
        <option value="finished"><?php _e('Accept', ET_DOMAIN); ?></option>
        <?php endif; ?>
    </select>
</div>
<?php
    endif;
    endif; ?>
<!--<div class="view-as">-->
<!--    <ul>-->
<!--        <span>View as</span>-->
<!--        <li class="grid"><i class="fa fa-th"></i></li>-->
<!--        <li class="list"><i class="fa fa-align-justify"></i></li>-->
<!--    </ul>-->
<!--</div>-->