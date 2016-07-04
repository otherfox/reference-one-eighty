<?php
/* =====================================

Custom Form Styling

=======================================*/
?>


<form role="search" method="get" id="searchform" action="<?php echo home_url( '/' ); ?>">
    <div>
        <input type="text" id="searchbox" value="Search..." name="s" id="s" onclick="if(this.value=='Search...'){this.value=''}" onblur="if(this.value==''){this.value='Search...'}"/>
        <input type="image" id="searchsubmit" value="submit" src="<?php bloginfo('template_url'); ?>/images/header/Edition-25.png" onMouseOver="this.src='<?php bloginfo('template_url'); ?>/images/header/Edition-25-active.png'" onMouseOut="this.src='<?php bloginfo('template_url'); ?>/images/header/Edition-25.png'"/>
    </div>
</form>
