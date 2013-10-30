<?php
/**
 * To display the survey report
 *
 * @package	WordPress
 * @subpackage	mf-survey
 * @filename	survey-report-form.php
 */

/**
* Includes survey-tables.php
*/
require_once( __DIR__ . '/../survey-tables.php' );

/**
 * To include survey-style.css file
 */
wp_enqueue_style( '', plugins_url() . '/mf-survey/styles/survey-style.css', '' );

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
					<h2 style="float:left;"><?php _e('Survey Report for', 'mf-survey'); ?> "<?php echo $survey_name; ?>"</h2>
				</td>
				<td width="50%">
					<div style="float:right;">						
						<?php
							
							// This query counts total results with inactive status for a particular survey
							$query = 
								"
									SELECT count(result_id) 
									FROM $wp_survey_result
									WHERE fk_survey_id = %d AND
									result_status = 'I'
								";
							
							$count = $wpdb->get_var( $wpdb->prepare( $query, $survey_id ));
							
							//pagination script
							$limit = 15;
							$paged = $_GET['paged'];
							$current = max( 1, $paged );
							$total_pages = ceil($count / $limit);
							$start = $current * $limit - $limit;							
							
							// This query returns all questions for a particular survey
							$query_questions = "
								SELECT question_id, question_data
								FROM $wp_survey_question 
								WHERE fk_page_id IN
									(
										SELECT page_id
										FROM $wp_survey_page
										WHERE fk_survey_id = %d										
									)
								AND question_type != 'Button'
							";
							
							// This query returns user_id, question_id, user_name and answer for a particular survey
							$query_answers = 
							"
								SELECT a.fk_user_id AS user_id, a.fk_question_id AS question_id, 
								u.user_nicename AS user, a.answer AS answer
								FROM $wp_survey_answer a
								INNER JOIN $wp_users u ON
								u.ID = a.fk_user_id
								WHERE a.fk_result_id IN
									(
										SELECT result_id
										FROM $wp_survey_result
										WHERE result_status = 'I' AND
										fk_survey_id = %d
									)
								ORDER BY a.fk_result_id DESC
							";
							
						?>							
							
						<div class="tablenav top">
							<div class="tablenav-pages">
								<span class="displaying-num"><?php echo $count?> <?php echo __('items'); ?></span>
								<?php
								echo paginate_links(
									array(
										'current' 	=> $current,
										'prev_text'	=> '&laquo; ' . __('Prev'),
										'next_text'    	=> __('Next') . ' &raquo;',
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
	
	<form method="post" name="form_survey_report" id="form_survey_report" onsubmit="return validate_edit_survey();" autocomplete="off">	
		<?php			
			$questions = $wpdb->get_results( $wpdb->prepare( $query_questions, $survey_id ) );
			$answers = $wpdb->get_results( $wpdb->prepare( $query_answers, $survey_id ) );
		?>
	
		<div class="dv_survey_report">
			<table class="widefat page report" cellspacing="0">
				<thead>
					<tr>
						<th>
							<div>
								<?php _e('User Name', 'mf-survey'); ?>
							</div>
						</th>
						<?php
						
						$answer_array = array();
						
						foreach($answers as $user_answer)
						{
							//Get the username
							$answer_array[$user_answer->user_id]['user_name'] = $user_answer->user;
							
							//Get the user answers
							$answer_array[$user_answer->user_id][$user_answer->question_id] = $user_answer;
							
						}
						
						$answer = array();
						$index = 0;
						
						foreach ($answer_array as $key => $value) {
						
							$answer[$index] = $answer_array[$key];
							$index++;
						
						}
						
						// This returns $limit elements from $answer array
						$output = array_slice($answer, $start, $limit);
						
						// To show the questions loop through questions
						foreach( $questions as $result ) {
						
							$question_data = stripslashes_deep($result->question_data);							
							$question_data = unserialize( ( $question_data ) );
							
							foreach ( $question_data as $key => $value ) {
							
								if ( $key === 'question' ) {
								
									echo '<th><div>Q. '.$value.'</div></th>';
								
								}
							
							}
							
						}
						
						?>
					</tr>
				</thead>
				<tbody>
					<?php
					
						// To show the output loop through the user_result
						foreach($output as $user_result) {
							
							echo '<tr><td><div><b>'.$user_result['user_name'].'</b></div></td>';
							foreach($questions as $result) {
								
								$question_id = stripslashes_deep($result->question_id);
								$row_values = $user_result[$question_id];								
								echo '<td><div>'.$row_values->answer.'</div></td>';
								
							}
							
							echo '</tr>';
							
						}
						
					?>
				</tbody>
			</table>
		</div>
		
	</form>
</div>