<?php
/*
Template Name: New Vision				
Template By : Nick Tennies
Tempalte designed By : Nick Tennies
*/
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<!-- Optimize width for mobile devices -->
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
<!-- End optimize width -->

<meta name="google-site-verification" content="L6vbcfU2TLzN0adl6qm5mAb1evr9tWHVDRBtU6sjyKQ" />

<title><?php wp_title("",true); ?></title>

<link rel="shortcut icon" type="image/x-icon" href="<?php bloginfo('template_url'); ?>/images/favicon.ico" />

<?php wp_head(); ?>

</head>

<body class="page-<?php echo get_the_ID(); ?> new-vision-body">

	<div id="wrapper"><!-- open wrapper -->
        <div id="opt-in-wrapper" class="wrapper">
        	<div id="opt-in-container" class="container">
                	<?php echo do_shortcode('[custom_form page="nv-sale-header"]') ?>
            </div>
        </div> 
        <div id="menu-top-wrapper" class="wrapper">
        	<div id="menu-top-container" class="container">
                <div id="login-cart" class="nv-menu-top-container">                		
						<?php  wp_nav_menu( array( 'theme_location' => 'nv-menu-top', 'sort_column' => 'menu_order', 'fallback_cb' => 'display_home', 'container_class' => 'nv-menu-top' ) ); ?>
                </div>
            </div>
        </div>      
    	<div id="header-wrapper" class="wrapper nv-new-wrapper"><!-- open header wrapper -->
			<div id="header-container" class="container"> <!-- open header container -->         
				<div id="header" class="nv-new-header"> <!-- open header-->
                     
                     <a class="logo" href="<?php //echo get_settings('home'); ?>http://reference180.com/"><img src="<?php bloginfo('template_url'); ?>/images/header/reference180-logo.png" alt="reference180 business startups made simple" height="53"/></a> <!-- re180 Logo -->
                     
                     <?php wp_nav_menu( array( 'theme_location' => 'nv-menu-right', 'sort_column' => 'menu_order', 'fallback_cb' => 'display_home', 'container_class' => 'nv-nav-right' ) ); ?>
                 
                     
                     <span id="mobile-menu-icon"><img src="<?php bloginfo('template_url'); ?>/images/mobile/mobile-menu-icon.png" alt="MENU" height="45"/></span>

                                                                      
                </div> <!-- close header -->           
            </div><!-- close header container --> 
      </div><!-- close header wrapper -->

      <div id="nav-wrapper" class="wrapper"><!-- open nav wrapper -->
        	<div id="nav-container" class="container"> <!-- open nav container -->         
                <div id="nav"> <!-- open nav-->
                             
                     <?php wp_nav_menu( array( 'theme_location' => 'menu-left', 'sort_column' => 'menu_order', 'fallback_cb' => 'display_home', 'container_class' => 'nav-left' ) ); ?>
                                                                 
                </div> <!-- close nav -->           
        	</div><!-- close nav container --> 
      </div><!-- close nav wrapper -->

      <div id="main-wrapper" class="wrapper"><!-- open header wrapper -->
			<div id="main-container" class="container"> <!-- open header container --> 
                <div id="main"> <!-- open main -->
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
        
			</div><!-- close main -->
		</div><!-- close main container -->
    </div><!-- close main wrapper -->
    <div id="form-my-llc-wrapper" class="wrapper">
    	<div id="form-my-llc-container" class="container">
    		<div id="form-my-llc">
            	<h2>Ready to form your company?</h2>
                <a href="http://reference180.com/plans-and-pricing" class="button">See Plans &amp; Pricing</a>
            </div>
    	</div>	
    </div>
    <div id="footer-wrapper" class="wrapper"><!-- open footer wrapper -->
		<div id="footer-container" class="container"> <!-- open footer container -->			
            <div id="footer"> <!-- open footer -->
            
                    <ul id="legal">
                        <li><a href="http://reference180.com/terms-and-conditions">terms & conditions</a> | </li> 
                        <li><a href="http://reference180.com/refund-policy">refund policy</a> | </li>
                        <li><a href="http://reference180.com/earnings-disclaimer">earnings disclaimer</a> | </li>
                        <li><a href="http://reference180.com/privacy-policy">privacy policy</a> | </li>
                        <li><a href="http://reference180.com/anti-spam-policy">anti-spam policy</a></li>
                    </ul>
           
                    <ul id="credits">
						<li><a href="http://wordpress.org" class="wordpress">Powered by Wordpress</a></li>
						<li>: &copy; 2011 &ndash; <?= date('Y') ?> reference180.com</li>
					</ul> 	
                    
			</div><!-- close footer -->
  		</div><!-- close footer wrapper -->
	</div> <!-- open footer container -->
    	
<!-- <?php echo get_num_queries(); ?> queries. <?php timer_stop(1); ?> seconds. -->



<?php wp_footer(); ?>

</body>
</html>