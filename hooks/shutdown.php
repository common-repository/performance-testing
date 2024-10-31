<?php
/**
 * The admin_hook actions are handled here, but the templates are elsewhere.
 *
 * @author Jacob Santos <plugin-dev@santosj.name>
 * @license New BSD
 * @package WordPress_Plugin
 * @subpackage Performance_Testing
 * @version $Id: shutdown.php 88 2008-07-06 01:14:12Z dragonwing@dragonu.net $
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
final class DragonU_Performance_Shutdown_Hook
{
	/**
	 * Stores the shutdown action object in case PHP does not allow for creating
	 * new objects when PHP is shuting down.
	 *
	 * @var DragonU_Performance_Shutdown_Action'
	 * @since 0.2
	 */
	static private $shutdown_action = null;

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
		self::$shutdown_action = new DragonU_Performance_Shutdown_Action();
		add_action('shutdown', array(__CLASS__, 'shutdown'));
	}

	/**
	 * Executes the shutdown action.
	 *
	 * Might need to put this in the footer action, in case PHP does not allow
	 * for creating new objects in the PHP shutdown.
	 *
	 * @static
	 * @since 0.2
	 */
	static public function shutdown()
	{
		self::$shutdown_action->run();
	}
}

/**
 * The shutdown action implementation for handling the different tasks to
 * perform on shutdown.
 *
 * @since 0.2
 */
final class DragonU_Performance_Shutdown_Action
{
	public function run()
	{
		$this->captureCachegrindDirectory();
	}
	
	private function captureCachegrindDirectory()
	{
		$option = get_option(DragonU_Performance_Setup_Hooks::OPTION_NAME);

		if( empty($option['xdebug_folder']) )
		{
			$file = xdebug_get_profiler_filename();
			$option['xdebug_folder'] = dirname($file);
			update_option(DragonU_Performance_Setup_Hooks::OPTION_NAME, $option);
		}
	}
}