<?php get_header(); ?>

					<div id="content"><!-- open content -->
                    	
                        <div class="et_builder clearfix">
                            
                            <div class="et_lb_module et_lb_column et_lb_3_4 et_lb_first">
                            
                                <div class="et_lb_module et_lb_widget_area">
                                	<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar("Layout Builder Widget Area 1") ) : ?><?php endif; ?>
                                </div>
                            
                            	<div class="posts-content">
                                <!-- Wordpress loop checks for posts-->
                                <?php if (have_posts()): ?>
                                    <?php while ( have_posts() ) : the_post(); ?>
                                    
                                    <div <?php post_class(); ?>>	
                                        
                                        <div class="post-content">
                                            <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                                            
                                            <div class="date">
                                                <p>Written by <?php the_author(); ?> on <?php the_time('F j, Y'); ?> at <?php the_time('g:i a'); ?></p>
                                            </div>                                        
                                            
                                            <?php the_content(''); ?>
                                            
                                            <ul class="meta">
                                                <li>Posted in <?php the_category(', '); ?></li>
                                                <?php the_tags( '<li>Tags ', ', ', '</li>'); ?>
                                            </ul>
                                      
                                        </div>
                                     </div>
                                        
                                    <?php endwhile; ?>
                                    
                                    <div class="pagination">
                                        <p class="older"><?php next_posts_link('Older Posts'); ?></p>
                                        <p class="newer"><?php previous_posts_link('Newer Posts'); ?></p>
                                    </div>
                                    
                                 <?php else : ?>
                                    <div class="post">
                                        <h2>Nothing Found</h2>
                                        <p>Sorry, but you are looking for something that isn't here</p>
                                        <p><a href="<?php echo get_option('home'); ?>">Return to homepage</a></p>
                                    </div>
                                 <?php endif; ?>
                                
                                </div>
                             </div>
                                <div class="et_lb_module et_lb_column et_lb_1_4">
                                    <div class="et_lb_module et_lb_widget_area blog-sidebar">
                                        <?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar("Blog Sidebar") ) : ?><?php endif; ?>
                                    </div>
                                    <div class="et_lb_module et_lb_box et_lb_box_silver">
					<div class="et_lb_module_content clearfix"><h2 style="text-align: center;"><strong>FORM WITH US</strong></h2><p style="text-align: center;">For as Low as</p><p style="text-align: center;"><span style="vertical-align: top;">$</span><span style="font-size: 36px; color: #000;">97</span></p><p style="text-align: center;">+ state fees</p><p style="text-align: center;"><a href="http://reference180.com/plans-and-pricing" class="small-button smallblack">See Plans &amp; Pricing</a>	</p></div> <!-- end .et_lb_module_content -->
									</div>
                                </div>
                             
                             </div>
                         
                         </div>
                             
					</div><!-- close content -->
                        
<?php get_sidebar(); ?> 
             
<?php get_footer(); ?>