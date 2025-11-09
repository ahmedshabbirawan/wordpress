<?php
/**
 * Template Name: Salam Social Posts Archive
 * Template Post Type: archive
 * Description: Displays a list of all Salam Social custom posts.
 */

get_header(); ?>

<style>

/* Example CSS for a simple 3-column grid */
.salam-social-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 30px;
}

.salam-social-card {
    border: 1px solid #eee;
    padding: 15px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    text-align: center;
}

.salam-social-card a {
    text-decoration: none;
    color: inherit;
}

.post-card-thumbnail img {
    width: 100%;
    height: auto;
    display: block;
    margin-bottom: 15px;
}

.entry-title {
    font-size: 1.2em;
    margin: 0;
}

</style>

<div id="primary" class="content-area">
    <main id="main" class="site-main">

        <header class="page-header">
            <h1 class="page-title"><?php post_type_archive_title( '', true ); ?></h1>
        </header><?php if ( have_posts() ) : ?>

            <div class="salam-social-grid">
            
            <?php
            /* Start the Loop */
            while ( have_posts() ) : the_post();
            ?>

                <article id="post-<?php the_ID(); ?>" <?php post_class( 'salam-social-card' ); ?>>
                    
                    <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
                        
                        <?php if ( has_post_thumbnail() ) : ?>
                            <div class="post-card-thumbnail">
                                <?php the_post_thumbnail( 'medium' ); // Use 'medium', 'large', or a custom size ?>
                            </div>
                        <?php endif; ?>
                        
                        <header class="entry-header">
                            <h2 class="entry-title"><?php the_title(); ?></h2>
                        </header></a>

                </article><?php endwhile; ?>
            
            </div><?php the_posts_pagination( array(
                'prev_text' => __( 'Previous', 'salam-social-manager' ),
                'next_text' => __( 'Next', 'salam-social-manager' ),
            ) ); ?>

        <?php else : ?>

            <p><?php _e( 'Sorry, no Salam Social posts were found.', 'salam-social-manager' ); ?></p>

        <?php endif; ?>

    </main></div><?php
get_footer();