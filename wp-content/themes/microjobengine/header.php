<?php
/**
 * The Header for our theme
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 * @package WordPress
 * @subpackage MicrojobEngine
 * @since MicrojobEngine 1.0
 */
global $current_user;
?><!DOCTYPE html>
<!--[if IE 7]>
<html class="ie ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html class="ie ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 7) | !(IE 8) ]><!-->
<html <?php language_attributes(); ?>>
<!--<![endif]-->
<head>
    <?php global $user_ID; ?>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1 ,user-scalable=no">
	<title><?php wp_title( '|', true, 'right' ); ?></title>
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
	<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>">
    <?php ae_favicon('/assets/img/favicon.png'); ?>
	<?php
    wp_head();
    ?>
</head>
<body <?php body_class(); ?>>
	<header id="et-header">
		<div class="et-pull-top">
			<div class="row">
				<!--Logo-->
				<div class="col-lg-7 col-md-7 col-sm-9 col-xs-6 header-left">
					<div id="logo-site">
						<a href="<?php echo get_site_url() ?>"><?php mJobLogo('site_logo'); ?></a>
					</div>
					<div class="search-bar">
						<?php
						mJobShowSearchForm();
						?>
					</div>
				</div>
				<?php if(is_user_logged_in()) {
					$cls = 'myaccount-login';
				}
				else{
					$cls = 'myaccount-unlogin';
				}
				?>
				<!--Function right-->
				<div id="myAccount" class="col-lg-5 col-md-5 col-sm-3 col-xs-6 float-right header-right <?php echo $cls; ?>">
					<?php
					if(is_user_logged_in()) {
						mJobShowUserHeader();
					} else { ?>
						<a class="btn btn-default display-desktop btn-authentication-pop" data-placement="bottom" data-popover-content="#a1" data-toggle="popover" data-trigger="focus" href="#" tabindex="0"><?php _e('SIGN UP | LOGIN', ET_DOMAIN); ?></a>
						<!-- Content for Popover #1 -->
						<div class="hidden" id="a1">
							<div class="popover-body">
								<p><?php _e('You can access our site through Facebook, Google, Twitter, or any email. Choose any option:', ET_DOMAIN); ?></p>
								<?php mJobShowAuthenticationLink(); ?>
							</div>
						</div>
						<a class="btn btn-default hireSignup display-mobile display-tablet" href="#" ><?php _e('LOGIN', ET_DOMAIN); ?></a>
					<?php }
					?>


				</div>
			</div>
		</div>
		<?php if( !is_page_template('page-user-authentication.php') && !is_singular('mjob_post') && !is_page_template('page-full-width.php')): ?>
		<div class="et-pull-bottom" id="et-nav">
			<div class="navbar navbar-default megamenu">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
						<span class="sr-only">Toggle navigation</span>
						<i class="fa fa-bars"></i>
					</button>
				</div>
				<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
					<?php
						if(has_nav_menu('et_header_standard')) {
							wp_nav_menu(array(
								'theme_location' => 'et_header_standard',
								'menu_class' => 'nav navbar-nav', // Class UL
								'container' => '',
								'fallback_cb'       => 'wp_bootstrap_navwalker::fallback',
								'walker'            => new wp_bootstrap_navwalker()
							));
						} else if(current_user_can('manage_options')) {
							?>
							<ul>
								<li><a href="<?php echo admin_url('/nav-menus.php'); ?>"><?php _e('Add a menu', ET_DOMAIN); ?></a></li>
							</ul>
							<?php
						}
					?>
				</div>
			</div>
		</div>
		<?php endif; ?>
	</header><!--End Header-->
<?php
global $user_ID;
if($user_ID) {
	echo '<script type="data/json"  id="user_id">'. json_encode(array('id' => $user_ID, 'ID'=> $user_ID) ) .'</script>';
}