<?php
/**
 * Displays the MySQL Performance Statistics, if mysqlnd is available.
 *
 * @version $Id: mysql_statistics.php 87 2008-07-06 01:02:26Z dragonwing@dragonu.net $
 * @since 0.2
 *
 * @package WordPress_Plugin
 * @subpackage Performance_Testing
 */

if( !defined('ABSPATH') )
{
	die();
}

performance_mysql_load();

function performance_mysql_load() {
?>
<div class="wrap">

	<h2>MySQL Performance Statistics</h2>


<?php if( !function_exists('mysqli_get_cache_stats') ) : ?>
	<p>Mysqlnd is not installed or not installed correctly. Please install or configure mysql, or mysqli for mysqlnd to load the MySQL statistics.</p>
<?php else : ?>

	<p>Yeah! You have mysqlnd installed, however, support hasn't be completed for the mysqlnd statistics at this time. I'm waiting to get it installed myself, so if you can send me a screenshot of this page, I'll more than welcome it and will have this completed it no time.</p>

	<table class="form-table">
		<thead>
			<tr>
				<th scope="row">Mysqli cache stats</th>
				<td>
					<pre>
					<?php var_dump(mysqli_get_cache_stats()); ?>
					</pre>
				</td>
			</tr>
			<tr>
				<th scope="row">Mysqli client stats</th>
				<td>
					<pre>
					<?php var_dump(mysqli_get_client_stats()); ?>
					</pre>
				</td>
			</tr>
			<tr>
				<th scope="row">Mysqli connection stats</th>
				<td>
					<pre>
					<?php var_dump(mysqli_get_connection_stats()); ?>
					</pre>
				</td>
			</tr>
		</thead>
		<tbody>
		<?php foreach(mysqli_get_cache_stats() as $name => $stats) : ?>
			<tr>
				<th scope="row"><?php echo $name; ?></th>
				<td>
				<pre>
				<?php var_dump($stats); ?>
				</pre>
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
<?php endif; ?>
</div>
<?php } ?>