<?php
/**
 * Custom Widget for Latest Posts, with Post Type selection.
 */
class SSM_Latest_Posts_Widget extends WP_Widget {

    /**
     * Register widget with WordPress.
     */
    public function __construct() {
        parent::__construct(
            'ssm_latest_posts_widget', // Base ID
            __( 'Latest Custom Posts Selector', 'salam-social-manager' ), // Name
            array( 
                'classname' => 'widget_salam_social_posts', 
                'description' => __( 'A list of your most recent Posts or Salam Social Posts.', 'salam-social-manager' ),
            ) // Args
        );
    }

    /**
     * Front-end display of widget.
     *
     * @see WP_Widget::widget()
     */
    public function widget( $args, $instance ) {
        if ( ! isset( $args['widget_id'] ) ) {
            $args['widget_id'] = $this->id;
        }

        $title = ( ! empty( $instance['title'] ) ) ? $instance['title'] : __( 'Recent Posts', 'salam-social-manager' );
        $title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

        $number = ( ! empty( $instance['number'] ) ) ? absint( $instance['number'] ) : 5;
        if ( ! $number ) {
            $number = 5;
        }
        $show_date = isset( $instance['show_date'] ) ? $instance['show_date'] : false;
        
        // 1. Get the selected post type
        $post_type = isset( $instance['post_type'] ) ? $instance['post_type'] : 'post';

        /**
         * Arguments for WP_Query. Uses the selected post_type.
         */
        $r = new WP_Query( apply_filters( 'widget_posts_args', array(
            'posts_per_page'      => $number,
            'no_found_rows'       => true,
            'post_status'         => 'publish',
            'ignore_sticky_posts' => true,
            'post_type'           => $post_type, // <--- DYNAMIC POST TYPE
        ), $instance ) );

        if ( ! $r->have_posts() ) {
            return;
        }
        ?>

        <?php echo $args['before_widget']; ?>
        <?php if ( $title ) {
            echo $args['before_title'] . $title . $args['after_title'];
        } ?>
        <ul>
            <?php while ( $r->have_posts() ) : $r->the_post(); ?>
                <li>
                    <a href="<?php the_permalink(); ?>"><?php get_the_title() ? the_title() : the_ID(); ?></a>
                    <?php if ( $show_date ) : ?>
                        <span class="post-date"><?php echo get_the_date(); ?></span>
                    <?php endif; ?>
                </li>
            <?php endwhile; ?>
        </ul>
        <?php echo $args['after_widget']; ?>
        <?php
        wp_reset_postdata();
    }

    /**
     * Back-end widget form.
     *
     * @see WP_Widget::form()
     */
    public function form( $instance ) {
        $title     = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
        $number    = isset( $instance['number'] ) ? absint( $instance['number'] ) : 5;
        $show_date = isset( $instance['show_date'] ) ? (bool) $instance['show_date'] : false;
        $post_type = isset( $instance['post_type'] ) ? $instance['post_type'] : 'post'; // New field default

        // Define options for the Post Type selector
        $post_type_options = array(
            'post'          => __( 'Standard Posts', 'salam-social-manager' ),
            'salam_social'  => __( 'Salam Social Posts', 'salam-social-manager' ),
            // You can add more CPTs here if needed
        );
        ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'salam-social-manager' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
        </p>
        
        <p>
            <label for="<?php echo $this->get_field_id( 'post_type' ); ?>"><?php _e( 'Post Type:', 'salam-social-manager' ); ?></label>
            <select class="widefat" id="<?php echo $this->get_field_id( 'post_type' ); ?>" name="<?php echo $this->get_field_name( 'post_type' ); ?>">
                <?php foreach ( $post_type_options as $key => $name ) : ?>
                    <option value="<?php echo esc_attr( $key ); ?>" <?php selected( $post_type, $key ); ?>>
                        <?php echo esc_html( $name ); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </p>

        <p>
            <label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( 'Number of posts to show:', 'salam-social-manager' ); ?></label>
            <input class="tiny-text" id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="number" step="1" min="1" value="<?php echo $number; ?>" size="3" />
        </p>

        <p>
            <input class="checkbox" type="checkbox"<?php checked( $show_date ); ?> id="<?php echo $this->get_field_id( 'show_date' ); ?>" name="<?php echo $this->get_field_name( 'show_date' ); ?>" />
            <label for="<?php echo $this->get_field_id( 'show_date' ); ?>"><?php _e( 'Display post date?', 'salam-social-manager' ); ?></label>
        </p>
        <?php
    }

    /**
     * Sanitize widget form values as they are saved.
     *
     * @see WP_Widget::update()
     */
    public function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance['title'] = sanitize_text_field( $new_instance['title'] );
        $instance['number'] = (int) $new_instance['number'];
        $instance['show_date'] = isset( $new_instance['show_date'] ) ? (bool) $new_instance['show_date'] : false;
        
        // 2. Save the new post_type field
        $instance['post_type'] = sanitize_text_field( $new_instance['post_type'] );

        return $instance;
    }
}