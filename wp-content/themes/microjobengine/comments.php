<?php
/**
 * The template for displaying Comments
 *
 * The area of the page that contains comments and the comment form.
 *
 * @package MicrojobEngine
 * @since MicrojobEngine 1.0
 */

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */
if ( post_password_required() ) {
	return;
}
?>

<div id="comments" class="comments-area">

	<?php if ( have_comments() ) : ?>
		<!--<h2 class="comments-title">
			<?php
/*				printf( _nx( 'One thought on &ldquo;%2$s&rdquo;', '%1$s thoughts on &ldquo;%2$s&rdquo;', get_comments_number(), 'comments title', ET_DOMAIN ),
					number_format_i18n( get_comments_number() ), get_the_title() );
			*/?>
		</h2>-->

		<?php //twentyfifteen_comment_nav(); ?>

		<ol class="comment-list">
			<?php
				wp_list_comments( array(
					'style'       => 'ol',
					'short_ping'  => true,
					'callback'    => 'blog_comment_callback'
				) );
			?>
		</ol><!-- .comment-list -->

		<?php //twentyfifteen_comment_nav(); ?>

	<?php endif; // have_comments() ?>

	<?php
		// If comments are closed and there are comments, let's leave a little note, shall we?
		if ( ! comments_open() && get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) :
	?>
		<p class="no-comments"><?php _e( 'Comments are closed.', ET_DOMAIN ); ?></p>
	<?php endif; ?>

	<?php comment_form(); ?>

</div><!-- .comments-area -->
<?php
function blog_comment_callback( $comment, $args, $depth ){
	$GLOBALS['comment'] = $comment;
	?>
<li class="media et-comment" id="li-comment-<?php comment_ID();?>">
	<div id="comment-<?php comment_ID(); ?>" class="clearfix">
		<div class="pull-left">
			<a class="avatar-comment" href="#">
				<?php echo get_avatar( $comment->comment_author_email, 40 );?>
			</a>
		</div>
		<div class="media-body pull-right">
			<h4 class="media-heading">
				<?php
				comment_author();
				?>
			</h4>
				<span class="time-review">
                	<i class="fa fa-clock-o"></i>
                	<time>
						<?php echo ae_the_time( strtotime($comment->comment_date)); ?>
					</time>
                </span>
			<div class="comment-text">
				<?php comment_text(); ?>
			</div>
			<?php
			comment_reply_link(array_merge($args, array(
				'reply_text' => __( 'Reply ', ET_DOMAIN ).'<i class="fa fa-edit"></i>',
				'depth'      => $depth,
				'max_depth'  => $args['max_depth']
			)));
			?>
		</div>
	</div>
	<?php
}