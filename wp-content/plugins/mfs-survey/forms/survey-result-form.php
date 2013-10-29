<?php
/**
 * To display the total surveys available
 *
 * @package	WordPress
 * @subpackage	mfs-survey
 * @filename	survey-result-form.php
 */

/**
* Includes survey-tables.php
*/
require_once( __DIR__ . '/../survey-tables.php' );

/**
 * To include manage-survey-script.js file
 */
wp_register_script( 'survey-report-script', plugins_url() . '/mfs-survey/scripts/survey-report-script.js', 'jquery' );
wp_enqueue_script( 'survey-report-script' );

/**
 * To include survey-style.css file
 */
wp_enqueue_style( '', plugins_url() . '/mfs-survey/styles/survey-style.css', '' );

/**
 * Includes ajax-manage-survey.php
 */
$ajax_report_url = plugins_url() . '/mfs-survey/ajax/ajax-survey-report.php';
?>

<div class="wrap">
	<table width="100%">
		<tbody>
			<tr>
				<td width="50%">
					<h2 style="float:left;"><?php _e('Survey Result', 'mfs-survey'); ?></h2>
				</td>
				<td width="50%">
					<div style="float:right;">						
						<?php							
						// This query counts total surveys from wp_survey table
						$query = "SELECT count(survey_id) FROM $wp_survey
								WHERE survey_status = 'A' AND publish_status = 'I'";
						
						$count = $wpdb->get_var($query);
						
						// Pagination script
						$limit = 15;
						$paged = $_GET['paged'];
						$current = max( 1, $paged );
						$total_pages = ceil($count / $limit);
						$start = $current * $limit - $limit;							
							
						// This query returns survey details from wp_survey table
						$query = "SELECT survey_id, survey_name, (SELECT COUNT( page_id ) FROM $wp_survey_page P
								WHERE P.fk_survey_id = S.survey_id AND P.page_status = 'A') AS question_count,
								(SELECT COUNT( fk_user_id ) FROM $wp_survey_result R WHERE R.fk_survey_id = S.survey_id
								AND R.result_status = 'I') AS user_count FROM $wp_survey S
								WHERE survey_status = 'A' AND publish_status = 'I'
								ORDER BY survey_id DESC LIMIT $start, $limit";

						$results = $wpdb->get_results( $query );
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
	
	<!-- This form displays all the published surveys with "Report" link -->
	<table class="widefat page fixed" cellspacing="0">
		<thead>
			<tr>
				<th><?php _e('Survey Name', 'mfs-survey'); ?></th>
				<th><?php _e('Total Questions', 'mfs-survey'); ?></th>
				<th><?php _e('Total Participants', 'mfs-survey'); ?></th>
				<th><?php _e('Survey Report', 'mfs-survey'); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php				
			
			$results = $wpdb->get_results( $query );
			$chk_box_id_incr = 0;
			
			if( count( $results ) > 0 ) {
			
				// Displays survey_name and Report link for all surveys
				foreach( $results as $result ) {
				
					echo '<tr>';
					echo '<td>' . stripslashes_deep( $result->survey_name ) . '</td>';
					echo '<td>' . $result->question_count . '</td>';
					echo '<td>' . $result->user_count . '</td>';
					echo '<td><a href="admin.php?page=forms/survey-report-form.php&survey_id=' . $result->survey_id . '">' . __('Click to view report', 'mfs-survey') . '</a></td>';
					echo '</tr>';

				}
				
			}
			?>
		</tbody>
	</table>
</div>