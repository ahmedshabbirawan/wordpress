<?php

class SSM_Post_Type {

    public function __construct() {
        add_action( 'init', array( $this, 'register_cpt' ) );
        add_action( 'init', array( $this, 'register_taxonomy' ) );
    }

    /**
     * Register the Salam Social Post custom post type.
     */
    public function register_cpt() {
        $labels = array(
            'name'                  => _x( 'Salam Social Posts', 'Post Type General Name', SSM_DOMAIN ),
            'singular_name'         => _x( 'Salam Social Post', 'Post Type Singular Name', SSM_DOMAIN ),
            'menu_name'             => __( 'Salam Social', SSM_DOMAIN ),
            'all_items'             => __( 'All Salam Social Posts', SSM_DOMAIN ),
            'add_new_item'          => __( 'Add New Salam Social Post', SSM_DOMAIN ),
            'add_new'               => __( 'Add New', SSM_DOMAIN ),
        );
        $args = array(
            'label'                 => __( 'Salam Social Post', SSM_DOMAIN ),
            'description'           => __( 'Manages Salam Social Posts with custom attributes.', SSM_DOMAIN ),
            'labels'                => $labels,
            'supports'              => array( 'title', 'editor', 'thumbnail' ), // Supports required: title, editor, thumbnail
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_position'         => 5,
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => true,
            'can_export'            => true,
            'has_archive'           => true,
            'exclude_from_search'   => false,
            'publicly_queryable'    => true,
            'capability_type'       => 'post',
            'show_in_rest'          => true, // 🔥
        );
        register_post_type( 'salam_social', $args ); // Key: salam_social
    }

    /**
     * Register the Salam Social Categories custom taxonomy.
     */
    public function register_taxonomy() {
        $labels = array(
            'name'                       => _x( 'Salam Social Categories', 'Taxonomy General Name', SSM_DOMAIN ),
            'singular_name'              => _x( 'Salam Social Category', 'Taxonomy Singular Name', SSM_DOMAIN ),
            'menu_name'                  => __( 'Categories', SSM_DOMAIN ),
            'all_items'                  => __( 'All Categories', SSM_DOMAIN ),
            'parent_item'                => __( 'Parent Category', SSM_DOMAIN ),
            'parent_item_colon'          => __( 'Parent Category:', SSM_DOMAIN ),
            'new_item_name'              => __( 'New Category Name', SSM_DOMAIN ),
            'add_new_item'               => __( 'Add New Category', SSM_DOMAIN ),
            'edit_item'                  => __( 'Edit Category', SSM_DOMAIN ),
            'update_item'                => __( 'Update Category', SSM_DOMAIN ),
        );
        $args = array(
            'labels'                     => $labels,
            'hierarchical'               => true, // Category-like (hierarchical)
            'public'                     => true,
            'show_ui'                    => true,
            'show_admin_column'          => true,
            'show_in_nav_menus'          => true,
            'show_tagcloud'              => false,
        );
        register_taxonomy( 'salam_social_category', array( 'salam_social' ), $args ); // Key: salam_social_category
    }
}
