<?php
/**
 * Manages Performance Testing Settings.
 *
 * @version $Id: settings.php 140 2008-07-07 05:48:53Z dragonwing@dragonu.net $
 * @since 0.2
 *
 * @package WordPress_Plugin
 * @subpackage Performance_Testing
 */

if( !defined('ABSPATH') )
{
	die();
}


$error = false;

if( isset($_POST['install_db']) )
{
	$error = performance_settings_install_db();
}
else if( isset($_POST['uninstall_db']) )
{
	$error = performance_settings_uninstall_db();
}

if( isset($_POST['xdebug_folder']) || isset($_POST['uninstall_plugin']) )
{
	performance_settings_save_settings();
}

performance_settings_load($error);

// Template Functions

function performance_settings_install_db()
{
	check_admin_referer('performance-testing-settings-update');
	
	if( file_exists(WP_CONTENT_DIR.'/db.php') and is_writable(WP_CONTENT_DIR.'/db.php') )
	{
		if( false === unlink(WP_CONTENT_DIR.'/db.php') )
		{
			return 'Could not remove current db.php file from wp-content folder. Please remove it by yourself.';
		}
	}

	if( false === copy(PERFORMANCE_PLUGIN_BASE.'/library/db.php', WP_CONTENT_DIR.'/db.php') )
	{
		return 'Could not copy mysqli port. Please copy the file from '.PERFORMANCE_PLUGIN_BASE.'/library/db.php to '.WP_CONTENT_DIR.'/db.php.';
	}

	return false;
}

function performance_settings_uninstall_db()
{
	check_admin_referer('performance-testing-settings-update');
	
	if( file_exists(WP_CONTENT_DIR.'/db.php') and is_writable(WP_CONTENT_DIR.'/db.php') )
	{
		if( false === unlink(WP_CONTENT_DIR.'/db.php') )
		{
			return 'Could not remove current db.php file from wp-content folder. Please remove it by yourself.';
		}
	}
	
	return false;
}

function performance_settings_save_settings()
{
	check_admin_referer('performance-testing-settings-update');

	$performanceSettings = get_option(DragonU_Performance_Setup_Hooks::OPTION_NAME);
	$performanceSettings['xdebug_folder'] = $_POST['xdebug_folder'];
	$performanceSettings['uninstall'] = isset($_POST['uninstall_plugin']) ? 1 : 0;
	
	update_option(DragonU_Performance_Setup_Hooks::OPTION_NAME, $performanceSettings);
}

function performance_settings_load($error = false) {
?>
<div class="wrap">

	<h2>Performance Testing Settings</h2>
<?php if( !!$error ) : ?>

	<p><?php echo $error; ?></p>
<?php endif; ?>

	<form method="post" action="">
	<?php wp_nonce_field('performance-testing-settings-update'); ?>

<?php if( function_exists('mysqli_connect') ) : ?>
	<h3>Install MySQLi DB Port</h3>

	<table class="form-table">
		<tbody>
<?php if( !function_exists('mysqli_get_cache_stats') || !function_exists('mysql_get_cache_stats') ) : ?>
			<tr>
				<td colspan="2">
					<strong>Mysqlnd is not installed or not installed correctly. You do not need to install the MySQLi Port.</strong>
				</td>
			<tr>
<?php endif; ?>

			<tr valign="top">
				<th scope="row">MySQLi DB Port</th>
				<td>
	<?php if( !file_exists(WP_CONTENT_DIR.'/db.php') ) : ?>
				<label>
					<input type="checkbox" value="" name="install_db" />
					Install
				</label>
	<?php else : ?>
				<label>
					<input type="checkbox" value="" name="uninstall_db" />
					Uninstall
				</label>
	<?php endif; ?>
				</td>
			</tr>
		</tbody>
	</table>
<?php endif; ?>

	<h3>Settings</h3>
	<table class="form-table">
		<tbody>
<?php $performanceSettings = get_option(DragonU_Performance_Setup_Hooks::OPTION_NAME); ?>
			<tr valign="top">
				<th scope="row">Version</th>
				<td><?php echo $performanceSettings['version']; ?></td>
			</tr>
			<tr valign="top">
				<th scope="row">Cachegrind Directory Location</th>
				<td><input type="text" value="<?php echo $performanceSettings['xdebug_folder']; ?>" name="xdebug_folder" /></td>
			</tr>
			<tr valign="top">
				<th scope="row">Uninstall Plugin on Deactivation</th>
				<td><input type="checkbox" name="uninstall_plugin"<?php if($performanceSettings['uninstall'] == 1): ?> checked="checked"<?php endif; ?> /> Uninstall (Delete all MySQL tables, and Plugin option)</td>
			</tr>
		</tbody>
	</table>

	<div class="submit">
		<input type="submit" value="Update Settings" name="submit"/>
	</div>

	</form>
</div>
<?php }