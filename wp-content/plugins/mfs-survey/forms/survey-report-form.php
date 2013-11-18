<?php
/**
 * To display the survey report
 *
 * @package	WordPress
 * @subpackage	mfs-survey
 * @filename	survey-report-form.php
 */

/**
* Includes survey-tables.php
*/
require_once( __DIR__ . '/../survey-tables.php' );

/**
 * To include survey-style.css file
 */
wp_enqueue_style( '', plugins_url() . '/mfs-survey/styles/survey-style.css', '' );

$survey_id = $_GET["survey_id"];

// Convert to int
$survey_id = intval( $survey_id );

$wp_users = $wpdb->prefix . "users";

// This query returns survey_name from wp_survey table
$result = $wpdb->get_row( $wpdb->prepare( "SELECT survey_name FROM $wp_survey WHERE survey_id = %d", $survey_id ));
$survey_name = stripslashes_deep( $result->survey_name );
?>
<div class="wrap">
	<table width="100%">
		<tbody>
			<tr>
				<td width="50%">
					<h2 style="float:left;"><?php _e('Survey Report for', 'mfs-survey'); ?> "<?php echo $survey_name; ?>"</h2>
				</td>
				<td width="50%">
					<div style="float:right;">
						<?php
						// This query counts total results with inactive status for a particular survey
						$query = "SELECT count(result_id) FROM $wp_survey_result
								WHERE fk_survey_id = %d AND result_status = 'I'";
						
						$count = $wpdb->get_var( $wpdb->prepare( $query, $survey_id ));
						
						// Pagination script
						$limit = 15;
						$paged = $_GET['paged'];
						$current = max( 1, $paged );
						$total_pages = ceil($count / $limit);
						$start = $current * $limit - $limit;
						?>							

						<div class="tablenav top">
							<div class="tablenav-pages">
								<span class="displaying-num"><?php echo $count?> <?php echo __('items'); ?></span>
								<?php
								echo paginate_links(
									array(
										'current' 	=> $current,
										'prev_text'	=> '&laquo; ' . __('Prev'),
										'next_text'    => __('Next') . ' &raquo;',
										'base' 		=> @add_query_arg('paged','%#%'),
										'format'  	=>  '?page=%#%',
										'total'   	=> $total_pages
									)
								);
								?>
							</div>								
						</div>
					</div>
				</td>
			</tr>
		</tbody>
	</table>	
	<?php
	$survey_users = $wpdb->get_results( $wpdb->prepare( "SELECT fk_user_id, result_id from $wp_survey_result WHERE fk_survey_id = %d AND result_status = 'I' LIMIT %d, %d", $survey_id, $start, $limit ) );

	// Load thickbox js and css files
	wp_enqueue_script('thickbox');
	wp_enqueue_style('thickbox');
	?>
	<div class="dv_survey_report">
		<table class="widefat page report" cellspacing="0">
			<thead>
				<tr>
					<th>
						<?php _e('User', 'mfs-survey'); ?>
					</th>
					<th>
						<?php _e('Email', 'mfs-survey'); ?>
					</th>
					<th>
						<?php _e('View Result', 'mfs-survey'); ?>
					</th>
				</tr>
			</thead>
			<tbody>
				<?php
				if ( count($survey_users) > 0 ) {
					// Create the nonce and get the users for given survey
					$nonce = wp_create_nonce("mfs_survey_view_result_nonce");
					$survey_users = $wpdb->get_results( $wpdb->prepare( "SELECT fk_user_id, result_id from $wp_survey_result WHERE fk_survey_id = %d AND result_status = 'I'", $survey_id ) );
					
					// To show the output loop through the user_result
					foreach($survey_users as $survey_data) {
						$user = get_userdata($survey_data->fk_user_id);
						$thickbox_url = admin_url('admin-ajax.php?action=mfs_survey_view_result&result_id=' . $survey_data->result_id . '&nonce=' . $nonce . '&TB_iframe=1&width=800&height=450');
						
						echo '<tr>';
						echo '<td>' . $user->user_nicename . '</td>';
						echo '<td><a href="mailto:' . $user->user_email . '">' . $user->user_email . '</a></td>';
						echo '<td><a href="' . $thickbox_url . '" class="thickbox" title="' . __('View result of ', 'mfs-survey') . $user->user_nicename . __(' for ', 'mfs-survey') . $survey_name . '">' . __('Click to view result', 'mfs-survey') . '</a></td>';
						echo '</tr>';
					}
				} else {
					?>
					<tr>
						<th colspan="3">
							<?php _e('No users participated in this survey', 'mfs-survey'); ?>
						</th>
					</tr>
					<?php
				}
				?>
			</tbody>
		</table>
	</div>
</div>