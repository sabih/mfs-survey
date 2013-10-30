<?php
/**
 * To edit page details for a particular survey
 *
 * @package		WordPress
 * @subpackage	mf-survey
 * @filename	edit-page-form.php
 */

/**
* Includes survey-tables.php
*/
require_once( __DIR__ . '/../survey-tables.php' );

/**
 * Includes list.php to populate page droplist
 */
require_once( __DIR__ . '/../list.php' );

/**
 * To include edit-page-script.js file
 */
wp_register_script( 'edit-page-script', plugins_url() . '/mf-survey/scripts/edit-page-script.js', 'jquery' );
wp_enqueue_script( 'edit-page-script' );

wp_register_script( 'jquery-min', plugins_url() . '/mf-survey/scripts/jquery/jquery.min.js', 'jquery' );
wp_enqueue_script( 'jquery-min' );

wp_register_script( 'jquery-ui-min', plugins_url() . '/mf-survey/scripts/jquery/jquery-ui.min.js', 'jquery');
wp_enqueue_script( 'jquery-ui-min' );

/**
 * To include survey-style.css file
 */
wp_enqueue_style( '', plugins_url() . '/mf-survey/styles/jquery-ui.css', '' );
wp_enqueue_style( '', plugins_url() . '/mf-survey/styles/survey-style.css', '' );

// Passing alert message as an array for translation to be applicable
$translation_array = array (
						'delete_question' => __( 'Are you sure to delete this Question?' ),
						'page_not_valid' => __( 'This is not a valid Page' )
					);
wp_localize_script( 'edit-page-script', 'edit_page_object', $translation_array );

/**
 * Includes ajax-edit-question.php
 */
$ajax_edit_question_url = plugins_url() . '/mf-survey/ajax/ajax-edit-question.php';

$survey_id = $_GET["survey_id"];

// Convert to int
$survey_id = intval( $survey_id );

$page_id = $_GET["page_id"];

// Convert to int
$page_id = intval( $page_id );

// This query returns survey_name from wp_survey table
$survey_name = $wpdb->get_row( $wpdb->prepare( "SELECT survey_name FROM $wp_survey WHERE survey_id = %d", $survey_id ));
$survey_name = stripslashes_deep( $survey_name->survey_name );

// This query returns page_title from wp_survey_page table
$page_title = $wpdb->get_row( $wpdb->prepare( "SELECT page_title FROM $wp_survey_page WHERE page_id = %d", $page_id ));

$page_title = esc_html( stripslashes_deep( $page_title->page_title ));

$current_page_url = $_SERVER["REQUEST_URI"];
?>

<div class="wrap">
	<h2><?php _e('Edit Page', 'mf-survey'); ?></h2>
	
	<form action="edit.php?page=mf-survey/survey-action.php&amp;noheader=true" method="post" name="form_edit_page" id="form_edit_page" onsubmit="return validate_edit_page();" autocomplete="off">
		<table class="widefat page fixed" cellspacing="0">
			<thead>
				<tr>
					<th>
						<?php _e('Survey Name', 'mf-survey'); ?>
					</th>
					<th>
						<?php _e('Current Page', 'mf-survey'); ?>
					</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><?php echo $survey_name; ?></td>
					<td>
						<input type="text" id="txt_page_title" name="txt_page_title" value="<?php echo $page_title; ?>" onkeyup="validate_txt_page_title();" />
					</td>
				</tr>
			</tbody>
		</table>
		
		<br />
		
		<table class="widefat page fixed" cellspacing="0">
			<thead>
				<tr>
					<th><?php _e('Question', 'mf-survey'); ?></th>
					<th><?php _e('Active', 'mf-survey'); ?></th>
					<th width="5%"></th>
					<th width="5%"></th>
				</tr>
			</thead>
			<tbody>
				<?php
				// This query returns question details from wp_survey_question table
				$query = "
							SELECT question_id, question_type, question_data, question_status 
							FROM $wp_survey_question 
							WHERE fk_page_id = %d AND
							question_type != 'Button'
							ORDER BY question_id
						";
				$results = $wpdb->get_results( $wpdb->prepare( $query, $page_id ));
				$chk_box_id_incr = 0;				
	
				if( count( $results ) > 0 ) {
				
					// Displays question and question_status for all questions
					// Checkbox checked if question_status is active and unchecked if inactive
					foreach( $results as $result ) {
					
						$question_id = $result->question_id;
						echo "<tr id='row_".$question_id."'><td>";
						
						$question_data = $result->question_data;
						$question_data = unserialize( ( $question_data ) );

						foreach ( $question_data as $key => $value ) {
						
							if ( $key === 'question' ) {
							
								echo stripslashes_deep( $value );
							
							}
						
						}
						
						echo "</td><td><input type='checkbox' id='chk_question$chk_box_id_incr' name='chk_question[]'";
							if( $result->question_status === 'A' ) {
								echo "checked='checked'";
							}
						echo "onclick='question_checkbox_status(" . $result->question_id . ", " . $chk_box_id_incr . ");'/></td>";
						$chk_box_id_incr++;
						
						// Edit link
						echo "
							<td style='text-align:right;'>								
								<a href='admin.php?page=forms/edit-question-form.php&page_id=".$page_id."&question_id=".$question_id."'>".__('Edit', 'mf-survey')."</a>
							</td>";
						
						// Delete link
						echo "
							<td style='text-align:right;'>
								<a href='javascript:void(0);' onclick='delete_question(\"$ajax_edit_question_url\", ".$page_id.", ".$question_id.", \"$current_page_url\");'>".__('Delete', 'mf-survey')."</a>
							</td></tr>";
					
						$question_type = $result->question_type;
					}
					
				}
				?>
			</tbody>
		</table>
		<?php		
		// If question_type = Radio button then display next page for each option
		if($question_type === 'Radiobutton') {
		?>
		<br />
		
		<table class="widefat page" cellspacing="0" id="tbl_question_<?php echo $question_id; ?>">
			<thead>
				<tr>
					<th colspan="3">
						<?php _e('Edit Next Page for Options', 'mf-survey'); ?>
					</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<?php
					$question_data = $result->question_data;
					$question_data = unserialize( ( $question_data ) );
					
					foreach ( $question_data as $key => $value) {
					
						if ( $key === 'option' ) {
							
							$option_id = 0;
							
							foreach ($value as $key => $value) {
							
								echo '<tr>
									<td width="10%">
										'.__('Option', 'mf-survey').'
									</td><td>';
								$answer = esc_html( stripslashes_deep( $value ));
								echo $answer;
								echo '<input type="hidden" name="hid_option[]" id="hid_option_'.$option_id.'" value="'.$answer.'" />';
								echo '</td>';
								
								// This query returns question_id, question_data from wp_survey_question table
								$next_page_query = "
											SELECT question_id, question_data
											FROM $wp_survey_question 
											WHERE fk_page_id = %d AND
											question_type = 'Button'
										";
								$row = $wpdb->get_row( $wpdb->prepare( $next_page_query, $page_id ));
								
								$question_id = $row->question_id;						
								$next_page_question_data = $row->question_data;
								$next_page_question_data = unserialize( ( $next_page_question_data ) );
								$next_page_id = $next_page_question_data[next_page];
								$radio_next_page_id = $next_page_question_data[radio_next_page];
								
								// "Droplist" displaying next_page
								echo '<td width="50%">
										<select id="sel_next_page'.$option_id.'" name="sel_next_page[]">
											<option value="">-- '.__('Please Choose', 'mf-survey').' --</option>';
											
											if ($radio_next_page_id === NULL) {
												
												// call function populate_next_page_droplist from list.php
												// @param survey_id, page_id, next_page_id
												echo populate_next_page_droplist( $survey_id, $page_id, $next_page_id );
											
											} else {
												
												// call function populate_next_page_droplist from list.php
												// @param survey_id, page_id, radio_next_page_id[$answer]
												echo populate_next_page_droplist( $survey_id, $page_id, $radio_next_page_id[$answer] );
												
											}
								
								echo '</select>
								</td></tr>';
								
								$option_id++;
							
							}
						
						}
					
					}
					?>
			</tbody>
		</table>
		<?php
		}
		?>
		<input type="hidden" name="hid_question_checked_ids" id="hid_question_checked_ids" value="" />
		<input type="hidden" name="hid_survey_id" id="hid_survey_id" value="<?php echo $survey_id; ?>" />
		<input type="hidden" name="hid_page_id" id="hid_page_id" value="<?php echo $page_id; ?>" />
		<input type="hidden" name="hid_url" id="hid_url" value="<?php echo $current_page_url; ?>" />
		
		<p class="submit">
			<input type="submit" name="btn_edit_page" id="btn_edit_page" value="<?php _e('Save', 'mf-survey'); ?>" class="button" />
		</p>
		
	</form>
</div>