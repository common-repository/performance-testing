<?php
/**
 * The admin_hook actions are handled here, but the templates are elsewhere.
 *
 * @author Jacob Santos <plugin-dev@santosj.name>
 * @license New BSD
 * @package WordPress_Plugin
 * @subpackage Performance_Testing
 * @version $Id: admin.php 149 2008-07-10 05:19:38Z dragonwing@dragonu.net $
 */

/**
 * Sets up the administration pages and the menus.
 *
 * Most of the methods are static, so that the constructor just adds class with
 * the method name. This allows for the Constructor to be called like a function
 * and lose the reference when we are done.
 *
 * @final
 * @since 0.2
 */
final class DragonU_Performance_Admin_Hooks
{

	/**
	 * PHP5 style Constructor
	 *
	 * Hooks the object static methods into the respective actions.
	 *
	 * @since 0.2
	 * @access public
	 * @return DragonU_Performance_Admin_Hooks
	 */
	public function __construct()
	{
		add_action('admin_menu', array(__CLASS__, 'admin_head'));
		add_action('admin_init', array(__CLASS__, 'admin_init'));
	}

	/**
	 * Runs on the cachegrind_load.php page to capture AJAX process calls.
	 *
	 * @since 0.4
	 * @access public
	 */
	public function admin_init()
	{
		if( $_GET['page'] == 'performance/template/load_cachegrind.php' && isset($_GET['cachegrind']) )
		{
			if( check_ajax_referer('performance-ajax', false, false) )
			{
				ignore_user_abort(true);
				@set_time_limit(500);
	
				require PERFORMANCE_PLUGIN_BASE . '/library/cachegrind.php';
	
				$performanceSettings = get_option(DragonU_Performance_Setup_Hooks::OPTION_NAME);
				$dir = $performanceSettings['xdebug_folder'];
				unset($performanceSettings);
	
				if( file_exists(untrailingslashit($dir).'/'.$_GET['cachegrind']) )
				{
					$process = new Cachegrind_File_Parser(untrailingslashit($dir).'/'.$_GET['cachegrind']);
					$process->run();
				}
	
				echo "{ 'code': 1 }";
				exit;
			}
			else
			{
				
				echo "{ 'code': 2 }";
				exit;
			}
		}
	}

	/**
	 * Sets up the administration page hook.
	 *
	 * Called when WordPress is initialized.
	 *
	 * @static
	 * @since 0.2
	 */
	static public function admin_head()
	{
		if( is_admin() )
		{
			// Main Performance Testing Page
			add_menu_page(
				'Settings - Performance Testing', // Page Title
				'Performance Testing', // Menu Title
				'manage_options', // Capability
				PERFORMANCE_PLUGIN_BASE . '/template/settings.php' // Plugin template file
			);

			// MySQL Statistics Page
			add_submenu_page(
				PERFORMANCE_PLUGIN_BASE . '/template/settings.php', // Parent Plugin template file
				'MySQL Statistics - Performance Testing', // Page Title
				'MySQL Statistics', // Menu Title
				'manage_options', // Capability
				PERFORMANCE_PLUGIN_BASE . '/template/mysql_statistics.php' // Plugin Template file
			);

			// Load Cachegrind Files Page
			add_submenu_page(
				PERFORMANCE_PLUGIN_BASE . '/template/settings.php', // Parent Plugin template file
				'Load Cachegrind Files - Performance Testing', // Page Title
				'Load Performance Files', // Menu Title
				'manage_options', // Capability
				PERFORMANCE_PLUGIN_BASE . '/template/load_cachegrind.php' // Plugin Template file
			);

			// WordPress Performance Page
			add_submenu_page(
				PERFORMANCE_PLUGIN_BASE . '/template/settings.php', // Parent Plugin template file
				'WordPress Performance - Performance Testing', // Page Title
				'WordPress Performance', // Menu Title
				'manage_options', // Capability
				PERFORMANCE_PLUGIN_BASE . '/template/wordpress_performance.php' // Plugin Template file
			);

			// Plugin Statistics Page
			add_submenu_page(
				PERFORMANCE_PLUGIN_BASE . '/template/settings.php', // Parent Plugin template file
				'Plugin Performance - Performance Testing', // Page Title
				'Plugin Performance', // Menu Title
				'manage_options', // Capability
				PERFORMANCE_PLUGIN_BASE . '/template/plugin_performance.php' // Plugin Template file
			);
		}
		
		if( $_GET['page'] == 'performance/template/load_cachegrind.php' )
		{
			self::cachegrindJavaScript();
		}
	}
	
	private static function cachegrindJavaScript()
	{
?>
<script type="text/javascript">
/* <![CDATA[ */
jQuery(function() {
	jQuery.ajaxSetup({
		timeout: 500000
	});
	
	jQuery(':checkbox').click(function() {
		thelink = 'admin.php?page=performance/template/load_cachegrind.php&_ajax_nonce=<?php echo wp_create_nonce('performance-ajax'); ?>';

		jQuery(this).parent().parent().append('Processing...');
		cachegrind =  jQuery(this).val();
		getlink = thelink+'&cachegrind='+cachegrind;
		
		var element = this;

		jQuery.getJSON(getlink, function(data) {
			if( data.code == 1 ) {
				jQuery(element).parent().parent().parent().fadeOut();
			} else {
				jQuery(element).parent().parent().append(' Incorrect nonce!');
			}
		});
	});
	
	jQuery('#cachegrind-submit').click(function(event) {
		event.preventDefault();
	});
});
/* ]]> */
</script>
<?php
	}
}
