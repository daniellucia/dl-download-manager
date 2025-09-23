<?php

get_header(); 

global $post;
$versions = new \DL\DownloadManager\Versions($post);
$post_instance = new \DL\DownloadManager\Post();
?>

<div id="primary" class="content-area dl-download-single">
    <main id="main" class="site-main">
        
        <?php while (have_posts()) : the_post(); ?>
            
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                
                <header class="entry-header">
                    <?php the_title('<h1 class="entry-title">', '</h1>'); ?>
                </header>

                <?php if (has_post_thumbnail()) : ?>
                    <div class="entry-thumbnail">
                        <?php the_post_thumbnail('large'); ?>
                    </div>
                <?php endif; ?>

                <div class="entry-content">
                    <div class="dl-post-content">
                        <?php the_content(); ?>
                    </div>
                    
                    <?php $post_instance->renderSingleTemplate($post, $versions->get()); ?>
                </div>

                <footer class="entry-footer">
                    <?php
                    
                    $categories = get_the_terms($post->ID, 'dldownload_category');
                    if ($categories && !is_wp_error($categories)) :
                    ?>
                        <div class="download-categories">
                            <strong><?php echo __('Categories:', 'dl-download-manager'); ?></strong>
                            <?php
                            $cat_names = array();
                            foreach ($categories as $category) {
                                $cat_names[] = '<a href="' . esc_url(get_term_link($category)) . '">' . esc_html($category->name) . '</a>';
                            }
                            echo implode(', ', $cat_names);
                            ?>
                        </div>
                    <?php endif; ?>
                </footer>

            </article>

            <?php
            
            if (comments_open() || get_comments_number()) :
                comments_template();
            endif;
            ?>

        <?php endwhile; ?>

    </main>
</div>

<?php
//get_sidebar();
get_footer();
