<?php
/*
Plugin Name: Swipe's Cookie Widget
Plugin URI: http://tools.swipe.digital/cookie-widget
Description: A one click install of Swipe's Cookie Widget, a bar showing cookie compliance at the bottom of your website.
Author: swipe.digital <fred@swipe.digital>
Version: 1.2.1
Author URI: http://www.swipe.digital
GitHub Plugin URI: https://github.com/SwipeDigital/cookie-widget
Github Branch:	master
*/
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

class TheSwipeCookieWidget { 
	public $options;
	
	function __construct() {
		add_action( 'admin_menu', array($this, 'Cookie_add_admin_menu' ));
		add_action( 'admin_init', array($this, 'Cookie_settings_init' ));
		add_action( 'wp_footer', array($this, 'forfooter' ));
		add_shortcode( 'cookie-jar', array($this, 'shortcode_cookie_jar' ));
		add_action( 'admin_init', array($this, 'init' ));
	}
	
	
	function shortcode_cookie_jar($atts, $content = null) {
		$a = shortcode_atts(array(
			"branding" => true,
			"website" => $_SERVER['HTTP_HOST'],
			"infolink" => null
		), $atts);
	
		if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") {
			$http = "https://";
		} else {
			$http = "http://";
		}
		
		$options = "data-website-url=\"".$http.$a['website']."\" ";
		
		if ($a['infolink'] !== "") {
			$options = $options.' data-info-link="'.$a['infolink'].'" ';
		}
		
		if ($a['branding']!== true) {
			$options = $options." data-branding=\"false\" ";
		}
		
		$this->options = $options;
		
		return '<a id="swipe_cookie_widget" '.$options.'href="http://swipe.digital/" title="Swipe Digital - Free Tools for Website Developers">Swipe Digital - Free Tools for Website Developers</a><script type="text/javascript" language="javascript" src="http://tools.swipe.digital/cookie-widget/embed.js"></script>';
	}
	
	
	function forfooter() {
		$options = get_option('Cookie_settings');
		
		if ($options['cookieoff']!=1):
			if ($options['infolink'] !== NULL) {
				$infolink = $options['infolink'];
				$shortcode = "[cookie-jar infolink='".$infolink."']";
			} else {
				$shortcode = "[cookie-jar]";
			}
			echo "\n<!-- ".$options['cookieoff']." BEGIN: Cookie Compliance, by Swipe Digital - http://swipe.digital -->\n";
		    echo do_shortcode($shortcode);
			echo "\n<!-- END: Cookie Compliance, by Swipe Digital - http://swipe.digital -->\n\n";
		else:
			echo "\n<!-- Swipe Digital Cookie Code is off for automatically adding to the footer! -->";
		endif;
	}
	
	function init() {
		wp_register_style('myPluginStylesheet', plugins_url('style.css', __FILE__));
	}
	function Cookie_add_admin_menu(  ) { 
	
		$page = add_submenu_page( 'options-general.php', 'Swipe Cookie Widget', 'Swipe Cookie Widget', 'manage_options', 'swipecookiewidget', array($this, 'Cookie_options_page') );
	add_action('admin_print_styles-'.$page, array($this,'my_plugin_admin_styles'));
	
	}
	function my_plugin_admin_styles() {
		wp_enqueue_style('myPluginStylesheet');
	}
	
	
	function Cookie_settings_init(  ) { 
	
		register_setting( 'pluginPage', 'Cookie_settings' );
	
		add_settings_section(
			'Cookie_pluginPage_section', 
			__( 'Settings', 'swipedigital' ), 
			array($this, 'Cookie_settings_section_callback'), 
			'pluginPage'
		);
	
		add_settings_field( 
			'Cookieinfolink', 
			__( 'More Info Link', 'swipedigital' ), 
			array($this, 'Cookie_text_field_0_render'), 
			'pluginPage', 
			'Cookie_pluginPage_section' 
		);
		
		add_settings_field(
			'Cookieoncheckbox',
			__( 'Don\'t automatically add to footer', 'swipedigital'),
			array($this, 'Cookie_checkbox_render'),
			'pluginPage',
			'Cookie_pluginPage_section'
		);
	
	
	}
	
	function Cookie_checkbox_render() {
		$options = get_option('Cookie_settings');
		if (isset($options['cookieoff']) && $options['cookieoff']==1) :
			$checked = "checked=\"checked\" ";
		else:
			$checked = "";
		endif;
		?>
		<input type="checkbox" name="Cookie_settings[cookieoff]" value="1" <?php echo $checked; ?>/>
		<span class="help-text">Mark this if you wish to control the display of the cookie bar on a per page basis (by adding the <code>[cookie-jar]</code> shortcode on each page you want to include it on). If this checkbox is un-ticked the cookie warning will show in the footer of every page of your site.</span>
		<?php
	}
	function Cookie_text_field_0_render(  ) { 
	
		$options = get_option( 'Cookie_settings' );
		?>
		<input type='text' placeholder="/privacy" name='Cookie_settings[infolink]' value='<?php echo $options['infolink']; ?>'>

		<?php
	
	}
	
	
	function Cookie_settings_section_callback(  ) { 
	
		//echo __( 'Please', 'swipedigital' );
	
	}
	
	
	function Cookie_options_page(  ) { 
	
		?>

		
		<div class="wrap">
				<h2>Swipe Cookie Widget Settings</h2>
				<div class="section group">
					<div class="col span_8_of_12">
						<form action='options.php' method='post'>
					
					<?php
					settings_fields( 'pluginPage' );
					do_settings_sections( 'pluginPage' );
					submit_button();
					?>
					
					</form>

					</div>
					<div class="col span_4_of_12">
						<div class="sidebar-block">
							<a class="center" href="http://swipe.digital">
								<img src="http://swipe.digital/wp-content/uploads/2015/06/swipe_logo_site1.png" class="center" />
							</a>
							<h3>About Swipe Digital</h3>
							<p>Swipe Digital is a fresh & innovative online agency working with brands and events to produce great websites & applications.</p><p>Support: <a href="http://support.swipe.digital" target="_blank">http://support.swipe.digital</a></p>
						</div>
						<div class="sidebar-block">
							<h3>About this plugin</h3>
							<p>European laws require that digital publishers give visitors to their sites and apps information about their use of cookies and other forms of local storage. In many cases these laws also require that consent be obtained.</p>

<p>Swipe Digital have created a helpful snippet of code that will automatically place a bar at the bottom of your site informing visitors of the use of Cookies. When a user clicks "OK", a cookie is set for 90 days and the bar disappears for that time.</p>

<p>Use of this plugin is free. If you require an unbranded version of this plugin, please feel free get in contact with the developer.</p>
<p>Further reading:</p>
<blockquote><ul><li><a href="https://wiki.openrightsgroup.org/wiki/UK_Cookie_Law#Consent" target="_blank">https://wiki.openrightsgroup.org/wiki/UK_Cookie_Law#Consent</a></li></ul></blockquote>
					</div>
	
				</div></div>
				
			</div>
		<?php
	
	}
}
$cookie_plugin = new TheSwipeCookieWidget();
?>
