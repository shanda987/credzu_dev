<?php
add_action('widgets_init', 'mjob_register_sidebars');
function mjob_register_sidebars() {
	/**
    * Creates a sidebar Footer 1
    * @param string|array  Builds Sidebar based off of 'name' and 'id' values.
    */
    $args = array(
        'name'          => __( 'Footer 1', ET_DOMAIN ),
        'id'            => 'mjob-footer-1',
        'description'   => '',
        'class'         => '',
        'before_widget' => '<aside id="%1$s" class="widget %2$s">',
        'after_widget'  => '</aside>',
        'before_title'  => '<h2 class="widgettitle">',
        'after_title'   => '</h2>'
    );

    register_sidebar( $args );

    /**
    * Creates a sidebar Footer 2
    * @param string|array  Builds Sidebar based off of 'name' and 'id' values.
    */
    $args = array(
        'name'          => __( 'Footer 2', ET_DOMAIN ),
        'id'            => 'mjob-footer-2',
        'description'   => '',
        'class'         => '',
        'before_widget' => '<aside id="%1$s" class="widget %2$s">',
        'after_widget'  => '</aside>',
        'before_title'  => '<h2 class="widgettitle">',
        'after_title'   => '</h2>'
    );

    register_sidebar( $args );
    

    /**
    * Creates a sidebar Footer 3
    * @param string|array  Builds Sidebar based off of 'name' and 'id' values.
    */
    $args = array(
        'name'          => __( 'Footer 3', ET_DOMAIN ),
        'id'            => 'mjob-footer-3',
        'description'   => '',
        'class'         => '',
        'before_widget' => '<aside id="%1$s" class="widget %2$s">',
        'after_widget'  => '</aside>',
        'before_title'  => '<h2 class="widgettitle">',
        'after_title'   => '</h2>'
    );

    register_sidebar( $args );


    /**
    * Creates a sidebar Footer 4
    * @param string|array  Builds Sidebar based off of 'name' and 'id' values.
    */
    $args = array(
        'name'          => __( 'Footer 4', ET_DOMAIN ),
        'id'            => 'mjob-footer-4',
        'description'   => '',
        'class'         => '',
        'before_widget' => '<aside id="%1$s" class="widget %2$s">',
        'after_widget'  => '</aside>',
        'before_title'  => '<h2 class="widgettitle">',
        'after_title'   => '</h2>'
    );

    register_sidebar( $args );

}