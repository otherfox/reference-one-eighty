<?php /*

**************************************************************************

Plugin Name:  ET Anticipate Maintenance Plugin
Plugin URI:   http://www.elegantthemes.com
Version:      1.7.1
Description:  Maintenance Plugin
Author:       Elegant Themes
Author URI:   http://www.elegantthemes.com

**************************************************************************/

define( 'ET_ANTICIPATE_PLUGIN_DIR', trailingslashit( dirname( __FILE__ ) ) );
define( 'ET_ANTICIPATE_PLUGIN_URI', plugins_url( '', __FILE__ ) );
define( 'ET_ANTICIPATE_PLUGIN_VERSION', '1.7.1' );

class ET_Anticipate
{
	var $_settings;
	var $_options_pagename = 'et_anticipate_options';
	var $_exception_urls = array( 'wp-login.php', 'async-upload.php', '/plugins/', 'wp-admin/', 'upgrade.php', 'trackback/', 'feed/' );
	var $location_folder;
	var $menu_page;
	var $update_name = 'Anticipate/anticipate-maintenance-plugin.php';

	function __construct()
	{
		$this->_settings = get_option('et_anticipate_settings') ? get_option('et_anticipate_settings') : array();

		$updates_settings = (array) get_option( 'et_automatic_updates_options' );
		$updates_fields = array(
			'username',
			'api_key',
		);

		foreach ( $updates_fields as $field ) {
			$this->_settings[ $field ] = '';

			if ( isset( $updates_settings[ $field ] ) ) {
				$this->_settings[ $field ] = $updates_settings[ $field ];
			}
		}

		$this->location_folder = trailingslashit(WP_PLUGIN_URL) . dirname( plugin_basename(__FILE__) );

		$this->_set_standart_values();

		add_action( 'admin_menu', array(&$this, 'create_menu_link') );
		add_action( 'init', array( &$this, 'maintenance_active' ), 100 );
		wp_enqueue_script('jquery');
	}

	function add_settings_link($links) {
		$settings = '<a href="'.admin_url('options-general.php?page=et_anticipate_options').'">' . __('Settings') . '</a>';
		array_unshift( $links, $settings );
		return $links;
	}

	function output_activation_warning()
	{ ?>
		<div id="message" class="error"><p>ET Anticipate plugin isn't active. Activate it here.</p></div>
	<?php }


	function create_menu_link()
	{
		$this->menu_page = add_options_page('ET Anticipate Plugin Options', 'ET Anticipate Plugin', 'manage_options',$this->_options_pagename, array(&$this, 'build_settings_page'));
		add_action( "admin_print_scripts-{$this->menu_page}", array(&$this, 'plugin_page_js') );
		add_action("admin_head-{$this->menu_page}", array(&$this, 'plugin_page_css'));
		add_filter('plugin_action_links_' . plugin_basename(__FILE__), array($this, 'add_settings_link'), 10, 2);
	}

	function build_settings_page()
	{
		if (!current_user_can('manage_options')) {
			wp_die( __('You do not have sufficient permissions to access this page.') );
		}

		if (isset($_REQUEST['saved'])) {
			if ( $_REQUEST['saved'] ) echo '<div id="message" class="updated fade"><p><strong>'.'ET Anticipate'.' settings saved.</strong></p></div>';
	}

		if ( isset($_POST['et_anticipate_settings_saved']) )
			$this->_save_settings_todb($_POST);
?>
		<div class="wrap">
			<?php screen_icon(); ?>
			<h2>ET Anticipate Plugin Options</h2>

			<form name="et_anticipate_form" method="post">
				<table class="form-table">
					<tr valign="top">
						<th scope="row">
							<label for="et_anticipate_logo"><?php _e( 'Logo URL' ); ?></label>
						</th>
						<td>
							<input name="et_anticipate_logo" type="text" id="et_anticipate_logo" value="<?php echo($this->_settings['et_anticipate_logo']); ?>" class="regular-text" />
							<span class="description">Input the URL to your logo image. </span>
						</td>
					</tr>

					<tr valign="top">
						<th scope="row">
							<label for="et_anticipate_date"><?php _e( 'Date' ); ?></label>
						</th>
						<td>
							<input name="et_anticipate_date" type="text" id="et_anticipate_date" value="<?php echo($this->_settings['et_anticipate_date']); ?>" class="regular-text" />
							<span class="description">Choose a completion date. ex: 03/16/2011 00:00</span>
						</td>
					</tr>

					<tr valign="top">
						<th scope="row">
							<label for="et_anticipate_complete_percent"><?php _e( 'Completed on' ); ?></label>
						</th>
						<td>
							<input name="et_anticipate_complete_percent" type="text" id="et_anticipate_complete_percent" value="<?php echo($this->_settings['et_anticipate_complete_percent']); ?>" class="small-text" />
							<span class="description">ex. 70 (results in 70%)</span>
						</td>
					</tr>

					<tr valign="top">
						<th scope="row">
							<label for="et_anticipate_content_pages"><?php _e( 'Slider Pages' ); ?></label>
						</th>
						<td>
							<?php
								$pages_array = get_pages();
								foreach ($pages_array as $page) {
									$checked = '';

									if (!empty($this->_settings['et_anticipate_content-pages'])) {
										 if (in_array($page->ID, $this->_settings['et_anticipate_content-pages'])) $checked = "checked=\"checked\"";
									} ?>

									<label style="padding-bottom: 5px; display: block; width: 200px; float: left;" for="<?php echo 'et_anticipate_content-pages-',$page->ID; ?>">
										<input type="checkbox" name="et_anticipate_content-pages[]" id="<?php echo 'et_anticipate_content-pages-',$page->ID; ?>" value="<?php echo ($page->ID); ?>" <?php echo $checked; ?> />
										<?php echo $page->post_title; ?>
									</label>
							<?php
								}
							?>
						</td>
					</tr>

					<tr valign="top">
						<th scope="row">
							<label for="et_anticipate_twitter_url"><?php _e( 'Twitter Page Url' ); ?></label>
						</th>
						<td>
							<input name="et_anticipate_twitter_url" type="text" id="et_anticipate_twitter_url" value="<?php echo($this->_settings['et_anticipate_twitter_url']); ?>" class="regular-text code" />
							<span class="description">ex. <code>http://twitter.com/elegantthemes</code></span>
						</td>
					</tr>

					<tr valign="top">
						<th scope="row">
							<label for="et_anticipate_facebook_url"><?php _e( 'Facebook Page Url' ); ?></label>
						</th>
						<td>
							<input name="et_anticipate_facebook_url" type="text" id="et_anticipate_facebook_url" value="<?php echo($this->_settings['et_anticipate_facebook_url']); ?>" class="regular-text code" />
							<span class="description">ex. <code>http://www.facebook.com/elegantthemes</code></span>
						</td>
					</tr>

					<tr valign="top">
						<th scope="row">
							<label for="et_anticipate_rss_url"><?php _e( 'RSS Url' ); ?></label>
						</th>
						<td>
							<input name="et_anticipate_rss_url" type="text" id="et_anticipate_rss_url" value="<?php echo($this->_settings['et_anticipate_rss_url']); ?>" class="regular-text code" />
						</td>
					</tr>

					<tr valign="top">
						<th scope="row">
							<label for="et_anticipate_cufon"><?php _e( 'Cufon' ); ?></label>
						</th>
						<td>
							<label><input type="radio" name="et_anticipate_cufon" value="1"<?php if ($this->_settings['et_anticipate_cufon'] == 1) echo ' checked="checked"'; ?>> Activate</label><br/>
							<label><input type="radio" name="et_anticipate_cufon" value="0"<?php if ($this->_settings['et_anticipate_cufon'] == 0) echo ' checked="checked"'; ?>> Deactivate</label>
						</td>
					</tr>

					<tr valign="top">
						<th scope="row">
							<p><?php _e( 'Emails' ); ?></p>
						</th>
						<td>
							<p><?php echo($this->_settings['et_anticipate_emails']); ?></p>
							<span class="description">Here is a list of people who have subscribed to your mailing list</span>
						</td>
					</tr>

					<tr valign="top">
						<th scope="row">
							<label for="username"><?php esc_html_e( 'Username' ); ?></label>
						</th>
						<td>
							<input name="username" type="password" id="username" value="<?php echo esc_attr( $this->_settings['username'] ); ?>" class="regular-text code" />
							<p class="description">
							<?php
								$url = sprintf( '<a href="%1$s" target="_blank">%2$s</a>',
									esc_url( 'https://www.elegantthemes.com/members-area/api-key.php' ),
									esc_html__( 'Elegant Themes API Key' )
								);

								printf( esc_html__( 'Enter your %1$s here.' ), $url );
							?>
							</p>
						</td>
					</tr>

					<tr valign="top">
						<th scope="row">
							<label for="api_key"><?php esc_html_e( 'API Key' ); ?></label>
						</th>
						<td>
							<input name="api_key" type="password" id="api_key" value="<?php echo esc_attr( $this->_settings['api_key'] ); ?>" class="regular-text code" />

							<p class="description">
							<?php
								$url = sprintf( '<a href="%1$s" target="_blank">%2$s</a>',
									esc_url( 'https://www.elegantthemes.com/members-area/documentation.html#update' ),
									esc_html__( 'enable updates' )
								);

								printf(
									esc_html__( 'Keeping your plugins updated is important. To %1$s for Bloom, you must first authenticate your Elegant Themes account by inputting your account Username and API Key below. Your username is the same username you use when logging into your Elegant Themes account, and your API Key can be found by logging into your account and navigating to the Account > API Key page.' ),
									$url
								);
							?>
							</p>
						</td>
					</tr>
				</table>
				<p class="submit">
					<input type="submit" name="et_anticipate_settings_saved" class="button-primary" value="<?php esc_attr_e( 'Save Changes' ); ?>" />
				</p>
			</form>
		</div> <!-- end .wrap -->
<?php
	}

	function plugin_page_js()
	{
		wp_enqueue_script('anticipate-admin-date', $this->location_folder . '/js/jquery-ui-1.7.3.custom.min.js');
		wp_enqueue_script('anticipate-admin-date-addon', $this->location_folder . '/js/jquery-ui-timepicker-addon.js');
		wp_enqueue_script('anticipate-admin-main', $this->location_folder . '/js/admin.js');
	}

	function plugin_page_css()
	{
?>
		<link rel="stylesheet" href="<?php echo $this->location_folder; ?>/css/jquery-ui-1.7.3.custom.css" type="text/css" />
<?php
	}

	function add_email( $email ){
		$emails = explode(",", $email);
		$valid_emails = array();
		$unique_emails = array();

		foreach($emails as $mail){
			if ( is_email(trim($mail)) ) $valid_emails[] = trim($mail);
		}

		if ( empty($valid_emails) ) return false;

		$valid_emails_string = implode(",", $valid_emails);
		if ( $this->_settings['et_anticipate_emails'] <> '' ) $valid_emails_string = ',' . $valid_emails_string;

		$this->_settings['et_anticipate_emails'] .= $valid_emails_string;
		$unique_emails = explode(",", $this->_settings['et_anticipate_emails']);
		$unique_emails = array_unique($unique_emails);

		$this->_settings['et_anticipate_emails'] = implode(",", $unique_emails);
		$this->_save_settings_todb();

		return true;
	}

	function _save_settings_todb($form_settings = '')
	{
		if ( $form_settings <> '' ) {
			$updates_fields = array(
				'username',
				'api_key',
			);

			foreach ( $updates_fields as $field ) {
				$updates_settings[ $field ] = $form_settings[ $field ];
			}

			update_option( 'et_automatic_updates_options', $updates_settings );

			unset($form_settings['et_anticipate_settings_saved']);

			$emails = $this->_settings['et_anticipate_emails'];

			$this->_settings = $form_settings;
			$this->_settings['et_anticipate_emails'] = $emails;

			#set standart values in case we have empty fields
			$this->_set_standart_values();
		}

		update_option('et_anticipate_settings', $this->_settings);
	}

	function _set_standart_values()
	{
		global $shortname;
		$logo = ( $shortname <> '' && get_option( $shortname . '_logo' ) <> '' ) ? get_option( $shortname . '_logo' ) : $this->location_folder . '/images/logo.png';

		$standart_values = array(
			'et_anticipate_logo' => $logo,
			'et_anticipate_date' => '',
			'et_anticipate_complete_percent' => '10',
			'et_anticipate_content-pages' => '',
			'et_anticipate_twitter_url' => '',
			'et_anticipate_facebook_url' => '',
			'et_anticipate_rss_url' => get_bloginfo('rss2_url'),
			'et_anticipate_cufon' => 1,
			'et_anticipate_emails' => '',
			'username' => '',
			'api_key' => '',
		);

		foreach ($standart_values as $key => $value){
			if ( !array_key_exists( $key, $this->_settings ) )
				$this->_settings[$key] = '';
		}

		foreach ($this->_settings as $key => $value) {
			if ( $value == '' ) $this->_settings[$key] = $standart_values[$key];
		}
	}

	function maintenance_active(){
		if ( !$this->check_user_capability() && !$this->is_page_url_excluded() )
		{
			nocache_headers();
			header("HTTP/1.0 503 Service Unavailable");
			remove_action('wp_head','head_addons',7);

			// remove the main theme css file
			remove_action( 'wp_enqueue_scripts', 'et_nimble_load_scripts_styles' ); 	// fix for the Nimble theme
			remove_action( 'wp_enqueue_scripts', 'et_fusion_load_scripts_styles' ); 	// fix for the Fusion theme
			remove_action( 'wp_enqueue_scripts', 'et_harmony_load_scripts_styles' ); 	// fix for the Harmony theme
			remove_action( 'wp_enqueue_scripts', 'et_origin_load_scripts_styles' ); 	// fix for the Origin theme
			remove_action( 'wp_enqueue_scripts', 'et_load_scripts_styles' );

			add_action('et_anticipate_footer_icons', array(&$this,'show_social_icons'));
			add_action( 'et_wp_head', array( $this, 'add_external_jquery' ) );
			include('anticipate-maintenance-page.php');
			exit();
		}
	}

	function add_external_jquery() {
		$protocol = is_ssl() ? 'https' : 'http';
		echo "<script type='text/javascript' src='{$protocol}://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js'></script>";
	}

	function check_user_capability()
	{
		if ( is_super_admin() || current_user_can('manage_options') ) return true;

		return false;
	}

	function is_page_url_excluded()
	{
		$this->_exception_urls = apply_filters('et_anticipate_exceptions',$this->_exception_urls);
		foreach ( $this->_exception_urls as $url ){
			if ( strstr( $_SERVER['PHP_SELF'], $url) || strstr( $_SERVER["REQUEST_URI"], $url) ) return true;
		}
		if ( strstr($_SERVER['QUERY_STRING'], 'feed=') ) return true;
		return false;
	}

	function get_option($setting)
	{
		return $this->_settings[$setting];
	}

	function show_social_icons()
	{
		$social_icons = array();
?>
		<div id="anticipate-social-icons">
			<?php
				$social_icons['twitter'] = array('image' => $this->location_folder . '/images/twitter.png', 'url' => $this->_settings['et_anticipate_twitter_url'], 'alt' => 'Twitter' );
				$social_icons['rss'] = array('image' => $this->location_folder . '/images/rss.png', 'url' => $this->_settings['et_anticipate_rss_url'], 'alt' => 'Rss' );
				$social_icons['facebook'] = array('image' => $this->location_folder . '/images/facebook.png', 'url' => $this->_settings['et_anticipate_facebook_url'], 'alt' => 'Facebook' );
				$social_icons = apply_filters('et_anticipate_social', $social_icons);

				foreach ($social_icons as $icon) {
					echo "<a href='{$icon['url']}' target='_blank'><img alt='{$icon['alt']}' src='{$icon['image']}' /></a>";
				}
			?>
		</div> <!-- end #anticipate-social-icons -->
<?php
	}
} // end ET_Anticipate class

add_action( 'init', 'ET_Anticipate_Init', 5 );
function ET_Anticipate_Init()
{
	global $ET_Anticipate;
	$ET_Anticipate = new ET_Anticipate();
}

function et_anticipate_init_core_updates() {
	require_once( ET_ANTICIPATE_PLUGIN_DIR . 'core/updates_init.php' );

	et_core_enable_automatic_updates( ET_ANTICIPATE_PLUGIN_URI, ET_ANTICIPATE_PLUGIN_VERSION );
}
add_action( 'plugins_loaded', 'et_anticipate_init_core_updates' );