<?php
/*
Plugin Name: Swipe Cookie Widget
Plugin URI: http://tools.swipe.digital/cookie-widget
Description: A one click install of Swipe's Cookie Widget.
Author: Fred Bradley <fred@swipe.digital>
Version: 1.0
Author URI: http://swipe.digital
*/
class TheSwipeCookieWidget { 
	public $options;
	
	function __construct() {
		add_action( 'admin_menu', array($this, 'Cookie_add_admin_menu' ));
		add_action( 'admin_init', array($this, 'Cookie_settings_init' ));
		add_action( 'wp_footer', array($this, 'forfooter' ));
		add_shortcode('cookie-jar', array($this, 'shortcode_cookie_jar'));
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
	
	
	function Cookie_add_admin_menu(  ) { 
	
		add_submenu_page( 'options-general.php', 'Swipe Cookie Widget', 'Swipe Cookie Widget', 'manage_options', 'swipecookiewidget', array($this, 'Cookie_options_page') );
	
	
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
		<style>
			.sidebar {
				float:right;
				background: #fff;
				padding:20px;
				width:25%;
			}
			.main_col {
				background:#fff;
				padding:20px;
				width:80%;
				float: left;
			}
			.sidebar img {
				max-width: 100%;
			}
			.columns {
				margin-right:300px;
			}
			.columns .sidebar {
				margin-right:-300px;
			}
			.columns .main_col {
				float:left;
			}
			.center {
				margin: auto;
				text-align: center;
				display: block;
			}
			@media (max-width:768px) {
				.main_col {
					width:auto;
				}
				.columns {
					margin:0px;
					clear:both;
				}
				.columns .sidebar {
					margin:0px;
					margin-top:20px;
					clear:both;
					padding:0px;
									width: 100%;

				}
			}
			
		</style>
		
		<div class="wrap">
			<div class="columns">
				<h2>Swipe Cookie Widget Settings</h2>

				<div class="main_col">
					<form action='options.php' method='post'>
					
					<?php
					settings_fields( 'pluginPage' );
					do_settings_sections( 'pluginPage' );
					submit_button();
					?>
					
					</form>
				</div>
				<div class="sidebar">
					<a class="center" href="http://swipe.digital">
						<img src="http://swipe.digital/wp-content/uploads/2015/06/swipe_logo_site1.png" class="center" />
					</a>
					<p>Hello there is this interesting isn't it. Hello there is this interesting isn't it.</p>
				</div>
			</div>
		</div>
		<?php
	
	}
}
$cookie_plugin = new TheSwipeCookieWidget();
?>