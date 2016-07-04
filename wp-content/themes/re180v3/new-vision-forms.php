<?php
/*
Template Name: New Vision Forms			
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

<title><?php wp_title("",true); ?></title>

<link rel="shortcut icon" type="image/x-icon" href="https://reference180.com/wp-content/themes/re180v3/images/favicon.ico" />


<?php wp_head(); ?>

</head>

<body class="page-<?php echo get_the_ID(); ?> new-vision-body">

	<div id="wrapper"><!-- open wrapper -->     
    	<div id="header-wrapper" class="wrapper nv-new-wrapper nv-forms-wrapper"><!-- open header wrapper -->
			<div id="header-container" class="container"> <!-- open header container -->         
				<div id="header" class="nv-new-header"> <!-- open header-->
                     
                     <a class="logo" href="<?php //echo get_settings('home'); ?>http://reference180.com/"><img src="<?php bloginfo('template_url'); ?>/images/header/reference180-logo.png" alt="reference180 business startups made simple" height="53"/></a> <!-- re180 Logo -->

                     <p class="contact-header">customer service<br /><strong>800.440.8193</strong> M-F 8-5 MST</p>
                                                                      
                </div> <!-- close header -->           
            </div><!-- close header container --> 
      </div><!-- close header wrapper -->

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

    <div id="footer-wrapper" class="wrapper"><!-- open footer wrapper -->
		<div id="footer-container" class="container"> <!-- open footer container -->			
            <div id="footer"> <!-- open footer -->
            
                    <ul id="legal">
                        <li><a href="/terms-and-conditions">terms & conditions</a> | </li> 
                        <li><a href="/refund-policy">refund policy</a> | </li>
                        <li><a href="/earnings-disclaimer">earnings disclaimer</a> | </li>
                        <li><a href="/privacy-policy">privacy policy</a> | </li>
                        <li><a href="/anti-spam-policy">anti-spam policy</a></li>
                    </ul>
           
                    <ul id="credits">
						<li><a href="http://wordpress.org" class="wordpress">Powered by Wordpress</a></li>
						<li>: &copy; 2011 &ndash; 2014 reference180.com</li>
					</ul> 	
                    
			</div><!-- close footer -->
  		</div><!-- close footer wrapper -->
	</div> <!-- open footer container -->
    	
<!-- <?php echo get_num_queries(); ?> queries. <?php timer_stop(1); ?> seconds. -->




<!-- Start Alexa Certify Javascript -->
<script type="text/javascript" src="https://d31qbv1cthcecs.cloudfront.net/atrk.js"></script><script type="text/javascript">_atrk_opts = { atrk_acct: "EDEhf1asZt00Wb", domain:"reference180.com"}; atrk ();</script><noscript><img src="https://d5nxst8fruw4z.cloudfront.net/atrk.gif?account=EDEhf1asZt00Wb" style="display:none" height="1" width="1" alt="" /></noscript>
<!-- End Alexa Certify Javascript -->

<?php wp_footer(); ?>

</body>
</html>