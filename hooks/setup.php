<?php
/**
 * Sets up the activation, deactivation, and init hooks
 *
 * Implements an installer, upgrader, and uninstaller.
 *
 * @author Jacob Santos <plugin-dev@santosj.name>
 * @license New BSD
 * @package WordPress_Plugin
 * @subpackage Performance_Testing
 * @version $Id: setup.php 111 2008-07-06 18:17:33Z dragonwing@dragonu.net $
 */

/**
 * Sets up the activation, deactivation, and init hooks.
 *
 * Most of the methods are static, so that the constructor
 * just adds class with the method name. This allows for
 * the Constructor to be called like a function and lose
 * the reference when we are done.
 *
 * @final
 * @since 0.1
 */
final class DragonU_Performance_Setup_Hooks
{
	const OPTION_NAME = 'performance_testing_options';

	/**
	 * PHP5 style Constructor
	 *
	 * Hooks the object static methods into the respective actions.
	 *
	 * @since 0.1
	 * @access public
	 * @return DragonU_Performance_Setup_Hooks
	 */
	public function __construct()
	{
		add_action('init', array(__CLASS__, 'init'));
	}

	static public function init()
	{
		$options = get_option(self::OPTION_NAME);

		if( $options['version'] != PERFORMANCE_PLUGIN_VERSION )
		{
			new DragonU_Performance_Setup_Installer($options['version'], true); // Upgrade
		}
	}

	/**
	 * Display message that plugin couldn't be activated, because XDebug isn't available.
	 *
	 * @since 0.3
	 */
	static function cannotInstallMessage()
	{
		echo '<div id="message" class="updated fade"><p>Cannot activate plugin, because XDebug is not installed. Please install XDebug before activating.</p></div>';
	}

	/**
	 * Setups whether the plugin needs to be installed/upgraded or not.
	 *
	 * Called on activation of the plugin.
	 *
	 * @static
	 * @since 0.1
	 */
	static public function activation()
	{
		if( !function_exists('xdebug_get_profiler_filename') )
		{
			deactivate_plugins(plugin_basename(PERFORMANCE_PLUGIN_BASE.'/performance.php'));
			wp_die("Cannot activate plugin, because XDebug is not installed. Please install XDebug before activating.");
			return;
		}

		$options = get_option(self::OPTION_NAME);

		if( false === $options )
		{
			new DragonU_Performance_Setup_Installer(); // Install
		}
		else if( $options['version'] != PERFORMANCE_PLUGIN_VERSION )
		{
			new DragonU_Performance_Setup_Installer($options['version'], true); // Upgrade
		}
	}

	/**
	 * Will uninstall, if user requests it.
	 *
	 * Called on deactivation of the plugin.
	 *
	 * @static
	 * @since 0.1
	 */
	static public function deactivation()
	{
		$options = get_option(self::OPTION_NAME);
		
		if( is_array($options) && 1 == $options['uninstall'] )
		{
			new DragonU_Performance_Setup_Uninstaller(); // Uninstall
		}
	}
}

/**
 * Creates the options for which the plugin will use during
 * execution and allow for changes to be made in the
 * administration.
 *
 * @final
 * @since 0.1
 */
final class DragonU_Performance_Setup_Installer
{

	/**
	 * Holds the Performance Plugin Version Number
	 * @access private
	 * @var float
	 * @since 0.1
	 */
	var $version;

	/**
	 * Whether the Performance Plugin was installed
	 * @access private
	 * @var float
	 * @since 0.1
	 */
	var $installed;

	/**
	 * PHP5 style Constructor
	 *
	 * Sets up the version property and calls install/upgrade
	 * execution path.
	 *
	 * @since 0.1
	 * @access public
	 * @param float $version Current installed version number.
	 * @param bool $installed Whether the plugin has already been installed.
	 * @return DragonU_Performance_Setup_Installer
	 */
	function __construct($version=0.1, $installed=false)
	{
		$this->version = $version;
		$this->installed = $installed;

		$this->runVersion01();
		$this->runVersion02();
		$this->runVersion03();
	}

	/**
	 * Installs version 0.1 of the Performance Plugin
	 *
	 * Checks the currently installed version and will skip the version setup if
	 * the system is already installed. This is the first version, so this will
	 * always be installed, so there is no need to compare versions.
	 *
	 * @since 0.1
	 * @access private
	 * @return bool False if not need to run.
	 */
	private function runVersion01()
	{
		if( true === $this->installed )
			return false;

		$option = array();
		$option['version'] = '0.1';

		add_option(DragonU_Performance_Setup_Hooks::OPTION_NAME, $option, null, 'no');
	}

	/**
	 * Installs or upgrades 0.1 to 0.2 version of the Performance Plugin.
	 *
	 * @since 0.2
	 * @access private
	 * @return bool False if not need to run.
	 */
	private function runVersion02()
	{
		if( version_compare('0.2', $this->version, '<=') )
			return false;

		$option = get_option(DragonU_Performance_Setup_Hooks::OPTION_NAME);
		$option['version'] = '0.2';
		$option['uninstall'] = 0;
		$option['xdebug_folder'] = '';

		update_option( DragonU_Performance_Setup_Hooks::OPTION_NAME, $option );
	}

	/**
	 * Installs or upgrades 0.2 to 0.3 version of the Performance Plugin.
	 *
	 * @since 0.3
	 * @access private
	 * @return bool False if not need to run.
	 */
	private function runVersion03()
	{
		global $wpdb;
		if( version_compare('0.3', $this->version, '<=') )
			return false;

		$option = get_option(DragonU_Performance_Setup_Hooks::OPTION_NAME);
		$option['version'] = '0.3';

		$wpdb->query('CREATE TABLE IF NOT EXISTS `pt_file` (
				`file_ID` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT ,
				`file_version` VARCHAR( 100 ) NOT NULL ,
				`filename` VARCHAR( 255 ) NOT NULL ,
				`part` MEDIUMINT NOT NULL ,
				`file_events` VARCHAR( 100 ) NOT NULL ,
				`the_date` DATETIME NOT NULL ,
				PRIMARY KEY ( `file_ID` ) ,
				INDEX ( `filename` )
			) ENGINE = INNODB');

		$wpdb->query('CREATE TABLE IF NOT EXISTS `pt_function` (
				`function_ID` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT ,
				`file_ID` BIGINT NOT NULL ,
				`function_location` VARCHAR( 255 ) NOT NULL ,
				`function_name` VARCHAR( 255 ) NOT NULL ,
				`line_number` INT NOT NULL ,
				`total_time` BIGINT NOT NULL ,
				PRIMARY KEY ( `function_ID` ) ,
				INDEX ( `file_ID` ),
				INDEX ( `function_location` ),
				INDEX ( `function_name` )
			) ENGINE = INNODB');

		$wpdb->query('CREATE TABLE IF NOT EXISTS `pt_called_function` (
				`called_ID` bigint(20) unsigned NOT NULL auto_increment,
				`function_ID` bigint(20) NOT NULL,
				`function_name` varchar(255) NOT NULL,
				`called_amount` mediumint(9) NOT NULL,
				`called_time` int(11) NOT NULL,
				`called_line` int(11) NOT NULL,
				`time_overhead` bigint(20) NOT NULL,
				PRIMARY KEY  (`called_ID`),
				KEY `function_ID` (`function_ID`)
			) ENGINE = INNODB');

		update_option( DragonU_Performance_Setup_Hooks::OPTION_NAME, $option );
	}

}

/**
 * Creates the uninstaller for which the plugin will use to
 * clean up the variables created to manage the plugin options.
 *
 * @final
 * @version 0.1
 */
final class DragonU_Performance_Setup_Uninstaller
{

	/**
	 * PHP5 style Constructor
	 *
	 * Removes everything that was setup by Installer
	 *
	 * @since 0.1
	 * @access public
	 * @return DragonU_Performance_Setup_Uninstaller
	 */
	function __construct()
	{
		$this->removeVersion01();
		$this->removeVersion03();
	}

	/**
	 * Removes the installation of Version 0.1.
	 *
	 * @since 0.1
	 * @access private
	 */
	private function removeVersion01()
	{
		delete_option(DragonU_Performance_Setup_Hooks::OPTION_NAME);
	}

	/**
	 * Removes the installation of Version 0.3.
	 *
	 * @since 0.3
	 * @access private
	 */
	private function removeVersion03()
	{
		global $wpdb;
		$wpdb->query('DROP TABLE IF EXISTS pt_file');
		$wpdb->query('DROP TABLE IF EXISTS pt_function');
		$wpdb->query('DROP TABLE IF EXISTS pt_called_function');
	}

}