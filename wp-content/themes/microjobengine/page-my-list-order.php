<?php
/**
 * Template Name: My orders listing
 *
 * @param void
 * @return void
 * @since 1.0
 * @package MicrojobEngine
 * @category void
 * @author JACK BUI
 */
get_header(); ?>
<div id="content">
    <div class="block-page">
        <div class="container dashboard withdraw">
            <div class="row title-top-pages">
                <p class="block-title"><?php _e('MY ORDERS & TASKS', ET_DOMAIN); ?></p>
                <p><a href="<?php echo et_get_page_link('dashboard'); ?>" class="btn-back"><i class="fa fa-angle-left"></i><?php _e('Back to dashboard', ET_DOMAIN); ?></a></p>
            </div>
             <div class="row profile">
                 <div class="col-lg-3 col-md-3 col-sm-12 col-sx-12 block-items-detail profile">
                    <?php get_sidebar('my-profile'); ?>
                 </div>
                 <div class="col-lg-9 col-md-9 col-sm-12 col-sx-12 outer-revenues">
                    <div class="information-items-detail box-shadow">
                        <div class="tabs-information">
                            <ul class="nav nav-tabs" role="tablist">
                                <li role="presentation" class="active"><a href="#order" aria-controls="order" role="tab" data-toggle="tab">My order</a></li>
                                <li role="presentation"><a href="#task" aria-controls="task" role="tab" data-toggle="tab">Tasks</a></li>
                            </ul>
                            <div class="tab-content">
                                <div role="tabpanel" class="tab-pane active order-container-control" id="order">
                                    <div class="filter-order">
                                        <?php get_template_part('template/filter', 'order'); ?>
                                    </div>
                                    <?php get_template_part('template/dashboard-list', 'orders'); ?>
                                </div>
                                <div role="tabpanel" class="tab-pane task-container-control" id="task">
                                    <div class="filter-order">
                                        <?php get_template_part('template/filter', 'task'); ?>
                                    </div>
                                    <?php get_template_part('template/dashboard-list', 'tasks'); ?>
                                </div>
                            </div>
                        </div>
                    </div>
<!--                    <div class="load-more">-->
<!--                        <a href="" class="link-more">Load more<i class="fa fa-angle-right"></i></a>-->
<!--                    </div>-->
                </div>
            </div>
        </div>
    </div>
</div>
<?php
get_footer();
