<?php
/**
 * To edit question
 *
 * @package		WordPress
 * @subpackage	mf-survey
 * @filename	edit-question-form.php
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
 * To include edit-question-script.js file
 */
wp_register_script( 'edit-question-script', plugins_url() . '/mf-survey/scripts/edit-question-script.js', 'jquery' );
wp_enqueue_script( 'edit-question-script' );

/**
 * To include question-script.js file
 */
wp_register_script( 'question-script', plugins_url() . '/mf-survey/scripts/question-script.js', 'jquery' );
wp_enqueue_script( 'question-script' );

wp_register_script( 'jquery-min', plugins_url() . '/mf-survey/scripts/jquery/jquery.min.js', 'jquery' );
wp_enqueue_script( 'jquery-min' );

wp_register_script( 'jquery-ui-min', plugins_url() . '/mf-survey/scripts/jquery/jquery-ui.min.js', 'jquery');
wp_enqueue_script( 'jquery-ui-min' );

// Passing alert messages as an array for translation to be applicable
$translation_array = array (
						'survey_not_valid' => __( 'This is not valid Survey' ),
						'add_page' => __( 'Please add Page for this Survey' ),
						'add_question' => __( 'Please add your question' ),
						'add_question_option' => __( 'Please add your question and option' ),
						'page_not_valid' => __( 'This is not a valid Page' ),
						'next_page_not_valid' => __( 'Next Page is not valid' ),
						'question_type_not_valid' => __( 'This is not valid Question Type' ),
						'option' => __( 'Option' )
					);
wp_localize_script( 'question-script', 'question_object', $translation_array );

/**
 * To include survey-style.css file
 */
wp_enqueue_style( '', plugins_url() . '/mf-survey/styles/jquery-ui.css', '' );
wp_enqueue_style( '', plugins_url() . '/mf-survey/styles/survey-style.css', '' );

$page_id = $_GET["page_id"];

// Convert to int
$page_id = intval( $page_id );

$question_id = $_GET["question_id"];

// Convert to int
$question_id = intval( $question_id );

// This query returns page_title from wp_survey_page table
$page_title = $wpdb->get_row( $wpdb->prepare( "SELECT page_title FROM $wp_survey_page WHERE page_id = %d", $page_id ));
$page_title = stripslashes_deep( $page_title->page_title );

// This query returns question details from wp_survey_question table
$query = "
			SELECT question_id, question_type, question_data, question_status 
			FROM $wp_survey_question 
			WHERE question_id = %d
		";
$result = $wpdb->get_row( $wpdb->prepare( $query, $question_id ));
?>

<div class="wrap">
	<h2><?php _e('Edit Question', 'mf-survey'); ?></h2>
	
	<form action="edit.php?page=mf-survey/survey-action.php&amp;noheader=true" method="post" name="form_edit_question" id="form_edit_question" onsubmit="return validate_add_question();" autocomplete="off">
		<table class="widefat page fixed" cellspacing="0">
			<thead>
				<tr>
					<th>
						<?php _e('Page Name', 'mf-survey'); ?>
					</th>
					<th>
						<?php _e('Question Type', 'mf-survey'); ?>
					</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><?php echo $page_title; ?></td>							
					<td>
						<select id="sel_question_type" name="sel_question_type" onchange="validate_sel_question_type();">
							<option value="">-- <?php _e('Please Choose', 'mf-survey'); ?> --</option>
							<?php
							$question_type = $result->question_type;
							
							// call function populate_question_type_droplist from list.php
							$question_type_list = populate_question_type_droplist();
							
							foreach ($question_type_list as $key => $val) {
							
								echo '<option value="'.$key.'"';
									if ($key === $question_type) {
										echo 'selected="selected"';
									}
								echo '>'.$val.'</option>';
							
							}
							?>
						</select>
					</td>
				</tr>
			</tbody>
		</table>
		
		<br />
		
		<table class="widefat page" cellspacing="0" id="tbl_question">
			<thead>
				<tr>
					<th colspan="2">
						<?php _e('Edit Question and Options', 'mf-survey'); ?>
					</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td width="10%">
						<?php _e('Question', 'mf-survey'); ?>
					</td>
					<?php
					$question_data = $result->question_data;
					$question_data = unserialize( ( $question_data ) );
					
					foreach ( $question_data as $key => $value) {

						if ( $key === 'question' ) {
						
							$question = esc_html( stripslashes_deep( $value ));
							echo '<td><input style="width:100%" type="text" name="txt_question" id="txt_question" value="'.$question.'" onkeyup="validate_txt_question();" /></td>
							</tr>';
							
						}
						
						if ( $key === 'option' ) {
							
							$option_id = 0;
							
							foreach ($value as $key => $value) {
							
								echo '<tr>
									<td width="10%">
										'.__('Option', 'mf-survey').'
									</td><td>';
								$answer = esc_html( stripslashes_deep( $value ));
								echo '<input type="text" id="txt_option' . $option_id . '" name="txt_option" value="' . $answer . '" />';
								
								if ( $key === 0 ) {
									
									echo 
									'
										<input type="button" name="btn_add_option[]" value="+" onclick="add_row();" class="button" id="btn_add_option" />
										<input type="button" name="btn_remove_option[]" value="-" onclick="remove_row(\'one_row\');" class="button" style="display:none" id="btn_remove_option" />
									';
								
								}
								
								if ($key === 1) {
								
									// This will display "-" Remove button if there is more than 1 option
									?>
									<script>
										jQuery.noConflict();
										jQuery(window).load(function(){
											
											// Call show_remove_button() which displays remove button
											show_remove_button();
											
										});
									</script>
									
									<?php
								
								}
								
								echo '</td></tr>';
								$option_id++;
							
							}
						
						}

					}
					
					$current_page_url = $_SERVER["REQUEST_URI"];
					?>
			</tbody>
		</table>
		
		<input type="hidden" name="hid_option_ids" id="hid_option_ids" value="" />
		<input type="hidden" name="hid_page_id" id="hid_page_id" value="<?php echo $page_id; ?>" />
		<input type="hidden" name="hid_question_id" id="hid_question_id" value="<?php echo $question_id; ?>" />
		<input type="hidden" name="hid_url" id="hid_url" value="<?php echo $current_page_url; ?>" />
		
		<p class="submit">
			<input type="submit" name="btn_edit_question" id="btn_edit_question" value="<?php _e('Save', 'mf-survey'); ?>" class="button" onclick="diplay_options();" />
		</p>
		
	</form>
</div>