<?php
/*
Template Name: Blank Page No Width
Template By : Nick Tennies
Tempalte designed By : Nick Tennies
*/
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?php wp_title('&laquo;', true, 'right'); ?><?php bloginfo('name'); ?></title>

<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />

<link rel="shortcut icon" type="image/x-icon" href="<?php bloginfo('template_url'); ?>/images/favicon.ico" />


<?php if ( is_singular() ){ wp_enqueue_script( 'comment-reply' ); } ?>

<?php wp_head(); ?>

</head>

<body>

<style>
#wpadminbar {
display: none;
}
</style>
                    
                    <!-- Wordpress get content -->
                    <?php if (have_posts()): ?>
                        <?php while (have_posts()) : the_post(); ?>
                        
                        <div <?php post_class(); ?>>
                            <?php the_content(''); ?>
                        </div>
                        
                        
                       <?php comments_template(); ?>
                       
                       <?php endwhile; ?>
                       
                    <?php else : ?>
                        <div class="post">
                            <h2>Nothing Found</h2>
                            <p>Sorry, no content found</p>
                            <p><a href="<?php get_option('home'); ?>">Return to homepage.</a></p>
                        </div>
                    <?php endif; ?>
                    
    	
<!-- <?php echo get_num_queries(); ?> queries. <?php timer_stop(1); ?> seconds. -->

<?php wp_footer(); ?>

</body>
</html>
