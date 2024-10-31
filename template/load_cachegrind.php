<?php
/**
 * Displays the list of cachegrind files to allow the user to import the data
 * from the files.
 *
 * @version $Id: load_cachegrind.php 149 2008-07-10 05:19:38Z dragonwing@dragonu.net $
 * @since 0.3
 *
 * @package WordPress_Plugin
 * @subpackage Performance_Testing
 */

if( !defined('ABSPATH') )
{
	die();
}

if( isset($_POST['submit']) )
{
	performance_cachegrind_file_process();
}

performance_cachegrind_file_load();

function performance_cachegrind_file_process()
{
	set_time_limit(500);

	require PERFORMANCE_PLUGIN_BASE . '/library/cachegrind.php';
	
	echo '<div class="wrap"><a href="?page=performance/template/load_cachegrind.php">View Form...</a></div>';

	$performanceSettings = get_option(DragonU_Performance_Setup_Hooks::OPTION_NAME);
	$dir = $performanceSettings['xdebug_folder'];
	unset($performanceSettings);

	foreach($_POST as $filename)
	{
		if( file_exists(untrailingslashit($dir).'/'.$filename) )
		{
			$process = new Cachegrind_File_Parser(untrailingslashit($dir).'/'.$filename);

			$process->run();
		}
	}
}

function performance_cachegrind_file_load() {
?>
<div class="wrap">

	<h2>Load Cachegrind File</h2>

	<p>Choose the file or files you want to get the statistics from. After the file has been added, the file will be bold. The reason you are still able to include the file again, is if you don't have the cachegrind output set to an unique filename for each run.</p>

	<?php 
	$performanceSettings = get_option(DragonU_Performance_Setup_Hooks::OPTION_NAME);
	if( !empty($performanceSettings['xdebug_folder']) && is_readable($performanceSettings['xdebug_folder']) ) :
		$dir = new DirectoryIterator($performanceSettings['xdebug_folder']);
	?>
	<form method="post" action="" id="cachegrind-process">
		<table class="form-table">
			<thead>
				<tr>
	
					<th scope="col" style="width: 20px">Process</th>
					<th scope="col">File Name</th>
					<th scope="col" style="width: 70px;">Size</th>
	
				</tr>
			</thead>
			<tbody>

		<?php foreach($dir as $file) : if( $file->isDot() ) continue; ?>
				<tr valign="top">
					<th scope="row">
					<label>
						<input type="checkbox" name="<?php echo $file->getFilename(); ?>" value="<?php echo $file->getFilename(); ?>" />
					</label>
					</th>
					<td style="overflow: hidden;">
					<?php echo $file->getFilename(); ?>
					</td>
					<td nowrap="nowrap">
					<?php echo size_format($file->getSize()); ?>
					</td>
				</tr>
		<?php endforeach; ?>
			</tbody>
		</table>

		<div class="submit">
			<input type="submit" value="Process files" id="cachegrind-submit" name="submit" />
		</div>
	</form> 
	<?php endif; ?>
</div>
<?php } ?>