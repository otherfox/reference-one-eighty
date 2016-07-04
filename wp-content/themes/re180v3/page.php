<?php get_header(); ?>
		<div id="content"><!-- open content -->
        
        <!-- Wordpress get content -->
        <?php if (have_posts()): ?>
			<?php while (have_posts()) : the_post(); ?>
			
            <div <?php post_class(); ?>>
                <?php the_content(''); ?>
			</div>
            
            
		   
           <?php endwhile; ?>
           
        <?php else : ?>
        	<div class="post">
            	<h2>Nothing Found</h2>
                <p>Sorry, no content found</p>
                <p><a href="<?php get_option('home'); ?>">Return to homepage.</a></p>
            </div>
		<?php endif; ?>
        
        </div><!-- close content -->

<?php get_sidebar(); ?>

<?php get_footer(); ?>