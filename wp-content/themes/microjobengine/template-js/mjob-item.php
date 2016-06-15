<script type="text/template" id="mjob-item-loop">
    <div class="inner clearfix">
        <# if( !is_admin && is_author && is_featured == 1 ){ #>
        <div class="ribbon-featured"><span><?php _e('Featured', ET_DOMAIN); ?></span></div>
        <# } #>
        <div class="vote">
            <div class="rate-it star" data-score="{{= rating_score }}"></div>
            <span class="total-review">({{= mjob_total_reviews }})</span>
        </div>
        <?php if(!is_search()) : ?>
        <# if( is_featured != 1){ #>
        <div class="bookmark">
            <p class="marks {{= status_class }}">{{= status_text }}</p>
        </div>
        <# } #>
        <?php else: ?>
            <# if(et_featured == 1) { #>
                <div class="bookmark">
                    <p class="marks featured-color"><?php _e('Featured', ET_DOMAIN) ?></p>
                </div>
            <# } #>
        <?php endif;?>

        <div class="set-status">
            <a href="{{= permalink }}"><img width="100%" src="{{= the_post_thumbnail }}" alt=""></a>
            <# if( is_admin || is_author){ #>
            <div class="status">
                <input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce('ae-mjob_post-sync');?>" />
                <a  href="#">{{= mjob_status }}</a><br/>
                <# if(  post_status == 'pending') { #>
                    <ul>
                        <# if( !is_admin ){ #>
                            <li><a href="{{= edit_link }}" target="_blank" data-toggle="tooltip" data-placement="top" title="<?php _e('Edit', ET_DOMAIN) ?>"  class=""><i class="fa fa-pencil"></i></a></li>
                            <li><a href="#" data-action="delete" title="<?php _e('Delete', ET_DOMAIN) ?>" data-toggle="tooltip" data-placement="top" class="action"><i class="fa fa-trash-o"></i></a></li>
                        <# } else { #>
                            <li><a href="#" data-action="approve" title="<?php _e('Approve', ET_DOMAIN) ?>" data-toggle="tooltip" data-placement="top" class="action"><i class="fa fa-check"></i></a></li>
                            <li><a href="#" data-action="reject" title="<?php _e('Reject', ET_DOMAIN) ?>" data-toggle="tooltip" data-placement="top" class="action"><i class="fa fa-times"></i></a></li>
                            <li><a href="{{= edit_link}}" target="_blank" data-action="edit" title="<?php _e('edit', ET_DOMAIN) ?>" data-toggle="tooltip" data-placement="top" class=""><i class="fa fa-pencil"></i></a></li>
                        <# } #>

                    </ul>
                <# }else if( post_status == 'publish' && !is_search){ #>
                    <ul>
                        <li><a href="{{= edit_link}}" target="_blank"  title="<?php _e('Edit', ET_DOMAIN) ?>" data-toggle="tooltip" data-placement="top" class=""><i class="fa fa-pencil"></i></a></li>
                        <li><a href="#" data-action="pause" title="<?php _e('Pause', ET_DOMAIN) ?>" data-toggle="tooltip" data-placement="top" class="action"><i class="fa fa-pause"></i></a></li>
                        <li><a href="#" data-action="archive" title="<?php _e('Archive', ET_DOMAIN) ?>" data-toggle="tooltip" data-placement="top" class="action"><i class="fa fa-archive"></i></a></li>
                    </ul>
                <# }else if( post_status == 'archive' && !is_search){ #>
                    <ul>
                        <li><a href="<?php echo et_get_page_link('post-service');?>?id={{= ID }}" title="<?php _e('Renew', ET_DOMAIN) ?>" data-toggle="tooltip" data-placement="top"><i class="fa fa-refresh"></i></a></li>
                        <li><a href="#" data-action="delete" title="<?php _e('Delete', ET_DOMAIN) ?>" data-toggle="tooltip" data-placement="top" class="action"><i class="fa fa-trash-o"></i></a></li>
                    </ul>
                <# }else if( post_status == 'reject' && !is_search){ #>
                    <ul>
                        <li><a href="{{= edit_link}}" target="_blank" title="<?php _e('Edit', ET_DOMAIN) ?>" data-toggle="tooltip" data-placement="top" class=""><i class="fa fa-pencil"></i></a></li>
                        <li><a href="#" data-action="delete" title="<?php _e('Delete', ET_DOMAIN) ?>" data-toggle="tooltip" data-placement="top" class="action"><i class="fa fa-trash-o"></i></a></li>
                    </ul>
                <# }else if( post_status == 'pause' && !is_search){ #>
                    <ul>
                        <li><a href="#" data-action="unpause" title="<?php _e('Unpause', ET_DOMAIN) ?>" rel="tooltip" data-toggle="tooltip" data-placement="top" class="action"><i class="fa fa-play"></i></a></li>
                        <li><a href="{{= edit_link}}" target="_blank" data-action="edit" title="<?php _e('Edit', ET_DOMAIN) ?>" rel="tooltip" data-toggle="tooltip" data-placement="top" class=""><i class="fa fa-pencil"></i></a></li>
                        <li><a href="#" data-action="delete" title="<?php _e('Delete', ET_DOMAIN) ?>" rel="tooltip" data-toggle="tooltip" data-placement="top" class="action"><i class="fa fa-trash-o"></i></a></li>
                    </ul>
                <# }else if( post_status == 'draft' && !is_search){ #>
                    <ul>
                        <li><a href="<?php echo et_get_page_link('post-service');?>?id={{= ID }}" title="<?php _e('Submit', ET_DOMAIN) ?>" data-toggle="tooltip" data-placement="top"><i class="fa fa-arrow-up"></i></a></li>
                        <li><a href="#" data-action="delete" title="<?php _e('Delete', ET_DOMAIN) ?>" data-toggle="tooltip" data-placement="top" class="action"><i class="fa fa-trash-o"></i></a></li>
                    </ul>
                <# }else if( post_status == 'inactive' && !is_search){ #>
                    <ul>
                        <li><a href="<?php echo et_get_page_link('post-service');?>?id={{= ID }}&rod=1" title="<?php _e('Re-order', ET_DOMAIN) ?>" data-toggle="tooltip" data-placement="top"><i class="fa fa-play"></i></a></li>
                        <li><a href="{{= edit_link}}" target="_blank" data-action="edit" title="<?php _e('Edit', ET_DOMAIN) ?>" rel="tooltip" data-toggle="tooltip" data-placement="top" class=""><i class="fa fa-pencil"></i></a></li>
                        <li><a href="#" data-action="delete" title="<?php _e('Delete', ET_DOMAIN) ?>" data-toggle="tooltip" data-placement="top" class="action"><i class="fa fa-trash-o"></i></a></li>
                    </ul>
                <# }else if(!is_search){ #>
                    <ul>
                        <li><a href="{{= edit_link}}" target="_blank" title="<?php _e('Edit', ET_DOMAIN) ?>" data-toggle="tooltip" data-placement="top" class=""><i class="fa fa-pencil"></i></a></li>
                        <li><a href="#" data-action="pause" title="<?php _e('Pause', ET_DOMAIN) ?>" data-toggle="tooltip" data-placement="top" class="action"><i class="fa fa-pause"></i></a></li>
                        <li><a href="#" data-action="archive" title="<?php _e('Archive', ET_DOMAIN) ?>" data-toggle="tooltip" data-placement="top" class="action"><i class="fa fa-archive"></i></a></li>
                    </ul>
                <# } #>


            </div>
            <# } #>
        </div>
        <h2 class="name-job"><a href="{{= permalink}}">{{= post_title}}</a></h2>
        <div class="author" title="{{= author_name }}">
            <p><span class="by-author">by</span> {{= author_name}}</p>
        </div>
        <div class="price">
            <span>{{= et_budget_text }}</span>
        </div>
    </div>
</script>