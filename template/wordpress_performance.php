<?php
/**
 * Displays the Plugin Performance Statistics, after the user chooses the plugin
 * to load statistics for.
 *
 * @version $Id: wordpress_performance.php 142 2008-07-07 06:23:45Z dragonwing@dragonu.net $
 * @since 0.3
 *
 * @package WordPress_Plugin
 * @subpackage Performance_Testing
 */

if( !defined('ABSPATH') )
{
	die();
}

performance_wordpress_statistics_load();

function performance_wordpress_statistics_load() {
?>
<div class="wrap">

	<h2>WordPress Performance Statistics</h2>

	<h3>Longest Running Functions</h3>

	<table class="widefat">
		<thead>
			<tr>

				<th scope="col">Function</th>
				<th scope="col">Line</th>
				<th scope="col">Location</th>
				<th scope="col">Time</th>

			</tr>
		</thead>
		<tbody>
			<?php
			$longest_functions = performance_get_longest_running_functions();
			if( count($longest_functions) == 0 ) :
			?>
			<tr class="alternate" valign="top">
				<th colspan="4">There are currently no functions that run the longest.</th>
			</tr>
			<?php
			else :
			foreach((array) $longest_functions as $row ) :
			?>
			<tr class="alternate" valign="top">
				<td><?php echo $row->function_name; ?></td>
				<td><?php echo $row->line_number; ?></td>
				<td><?php echo $row->function_location; ?></td>
				<td><?php echo $row->calculated_time; ?></td>
			</tr>
			<?php endforeach; endif; ?>
		</tbody>
	</table>

	<h3>Most Used Functions</h3>

	<table class="widefat">
		<thead>
			<tr>

				<th scope="col">Function</th>
				<th scope="col">Line</th>
				<th scope="col">Location</th>
				<th scope="col">Called</th>
				<th scope="col">Total Time</th>

			</tr>
		</thead>
		<tbody>
			<?php
			$most_used_functions = performance_get_most_used_functions();
			if( count($most_used_functions) == 0 ) :
			?>
			<tr class="alternate" valign="top">
				<th colspan="5">There are currently no functions that are most used.</th>
			</tr>
			<?php
			else :
			foreach((array) $most_used_functions as $row ) :
			?>
			<tr class="alternate" valign="top">
				<td><?php echo $row->function_name; ?></td>
				<td><?php echo $row->line_number; ?></td>
				<td><?php echo $row->function_location; ?></td>
				<td><?php echo $row->use_count; ?></td>
				<td><?php echo $row->calculated_time; ?></td>
			</tr>
			<?php endforeach; endif; ?>
		</tbody>
	</table>

	<h3>Most Called Functions</h3>

	<table class="widefat">
		<thead>
			<tr>

				<th scope="col">Function</th>
				<th scope="col" style="width: 100px;">Called</th>
				<th scope="col" style="width: 100px;">Total Time</th>
				<th scope="col" style="width: 100px;">Function Time</th>

			</tr>
		</thead>
		<tbody>
			<?php
			$most_called = performance_get_most_called_functions();
			if( count($most_called) == 0 ) :
			?>
			<tr class="alternate" valign="top">
				<th colspan="3">There are currently no functions that are most called.</th>
			</tr>
			<?php
			else :
			foreach((array) $most_called as $row ) :
			?>
			<tr class="alternate" valign="top">
				<td><?php echo $row->function_name; ?></td>
				<td><?php echo $row->use_count; ?></td>
				<td><?php echo $row->calculated_time; ?></td>
				<td style="text-align: right;"><?php echo round($row->calculated_time/$row->use_count, 4); ?></td>
			</tr>
			<?php endforeach; endif; ?>
		</tbody>
	</table>

</div>
<?php } 

function performance_get_longest_running_functions()
{
	global $wpdb;

	$query = $wpdb->get_results("SELECT 
			function_ID, function_name, function_location, line_number, SUM(total_time) as calculated_time
		FROM 
			pt_function
		WHERE 
			function_location != 'php:internal' AND 
			function_location NOT LIKE '%/plugin/%'
		GROUP BY
			function_name
		ORDER BY
			calculated_time DESC
		LIMIT 30
		");
	return $query;
}

function performance_get_most_used_functions()
{
	global $wpdb;

	$query = $wpdb->get_results("SELECT 
			function_ID, function_name, function_location, line_number, total_time, COUNT(function_name) AS use_count, SUM(total_time) AS calculated_time
		FROM 
			pt_function
		WHERE 
			function_location != 'php:internal' AND 
			function_location NOT LIKE '%/plugin/%'
		GROUP BY
			function_name
		ORDER BY
			use_count DESC
		LIMIT 30
		");
	return $query;
}

function performance_get_most_called_functions()
{
	global $wpdb;

	$query = $wpdb->get_results("SELECT 
			function_name, COUNT(function_name) as use_count, SUM(time_overhead) AS calculated_time
		FROM 
			pt_called_function
		WHERE 
			function_name NOT LIKE '%php::%'
		GROUP BY
			function_name
		ORDER BY
			use_count DESC
		LIMIT 30
		");
	return $query;
}

?>