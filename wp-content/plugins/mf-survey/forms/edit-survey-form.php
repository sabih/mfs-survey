<?php
/**
 * To edit survey details
 *
 * @package		WordPress
 * @subpackage	mf-survey
 * @filename	edit-survey-form.php
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
 * To include edit-survey-script.js file
 */
wp_register_script( 'edit-survey-script', plugins_url() . '/mf-survey/scripts/edit-survey-script.js', 'jquery' );
wp_enqueue_script( 'edit-survey-script' );

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
						'delete_page' => __( 'Are you sure to delete this Page?' ),
						'survey_not_valid' => __( 'This is not a valid Survey' ),
						'page_not_valid' => __( 'This is not a valid Page' ),
						'start_page_not_valid' => __( 'Start Page is not valid' )
					);
wp_localize_script( 'edit-survey-script', 'edit_survey_object', $translation_array );

/**
 * Includes ajax-edit-page.php
 */
$ajax_edit_page_url = plugins_url() . '/mf-survey/ajax/ajax-edit-page.php';

$survey_id = $_GET["survey_id"];

// Convert to int
$survey_id = intval( $survey_id );

// This query returns start_page_id and survey_name from wp_survey table
$result = $wpdb->get_row( $wpdb->prepare( "SELECT fk_start_page_id, fk_end_page_id, survey_name FROM $wp_survey WHERE survey_id = %d", $survey_id ));
$survey_name = stripslashes_deep( $result->survey_name );
$start_page_id = $result->fk_start_page_id;
$end_page_id = $result->fk_end_page_id;

$current_page_url = $_SERVER["REQUEST_URI"];
?>

<div class="wrap">
	<h2><?php _e('Edit Survey', 'mf-survey'); ?></h2>
	
	<!-- This form saves survey name -->
	<form action="edit.php?page=mf-survey/survey-action.php&amp;noheader=true" method="post" name="form_edit_survey" id="form_edit_survey" onsubmit="return validate_edit_survey();" autocomplete="off">
		<table class="widefat page fixed" cellspacing="0">
			<thead>
				<tr>
					<th>
						<?php _e('Survey Name', 'mf-survey'); ?>
					</th>
					<th>
						<?php _e('Select Start Page', 'mf-survey'); ?>
					</th>
					<th>
						<?php _e('Select End Page', 'mf-survey'); ?>
					</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><?php echo $survey_name; ?></td>
					<td>
						<select id="sel_start_page" name="sel_start_page" onchange="validate_sel_start_page();">
							<option value="">-- <?php _e('Please Choose', 'mf-survey'); ?> --</option>
							<?php
							// call function populate_page_droplist() from list.php
							echo populate_page_droplist( $survey_id, $start_page_id );
							?>									
						</select>
					</td>
					<td>
						<select id="sel_end_page" name="sel_end_page">
							<option value="">-- <?php _e('Please Choose', 'mf-survey'); ?> --</option>
							<?php
							// call function populate_page_droplist() from list.php
							echo populate_page_droplist( $survey_id, $end_page_id );
							?>									
						</select>
					</td>
				</tr>
			</tbody>
		</table>
		
		<br />	
		
		<table class="widefat page fixed" id="tbl_edit_survey" cellspacing="0">
		
			<?php
			// This query returns page details from wp_survey_page table
			$query = 
			"
				SELECT page_id, page_title, page_status 
				FROM $wp_survey_page 
				WHERE fk_survey_id = %d
				ORDER BY page_id
			";
			$results = $wpdb->get_results( $wpdb->prepare( $query, $survey_id ));
			?>
			
			<thead>
				<tr>
					<th><?php _e('Page Name', 'mf-survey'); ?></th>
					<th><?php _e('Active', 'mf-survey'); ?></th>
					<th><?php _e('Next Page', 'mf-survey'); ?></th>
					<th width="5%"></th>
					<th width="5%"></th>
				</tr>
			</thead>
			<tbody>
				<?php
				$id_incr = 0;
				
				if( count( $results ) > 0 ) {
					
					// Displays page_title and page_status for all pages
					// Checkbox checked if page_status is active and unchecked if inactive
					foreach( $results as $result ) {
						
						$page_id = $result->page_id;
						
						// Page title
						echo "<tr id='row_".$page_id."'><td>";
						echo stripslashes_deep( $result->page_title );
						
						// Checkbox displaying page_status
						echo "</td><td><input type='checkbox' id='chk_page$id_incr' name='chk_page[]'";
							if( $result->page_status === 'A' ) {
								echo "checked='checked'";
							}
						echo " onclick='page_checkbox_status(" . $page_id . ", " . $id_incr . ");'/></td>";					
						
						// "Droplist" displaying next_page
						// This query returns question_id, question_data from wp_survey_question table
						$query = "
									SELECT question_id, question_data
									FROM $wp_survey_question 
									WHERE fk_page_id = %d AND
									question_type = 'Button'
								";
						$row = $wpdb->get_row( $wpdb->prepare( $query, $page_id ));
						
						$question_id = $row->question_id;						
						$question_data = $row->question_data;
						$question_data = unserialize( ( $question_data ) );						
						$next_page_id = $question_data[next_page];			
						
						if ($question_data != NULL) {
							
							echo '<td>
									<select id="sel_next_page'.$id_incr.'" name="sel_next_page[]" onchange="sel_next_page_value(\'' . esc_html( $result->page_title ) . '\', ' . $question_id . ', ' . $id_incr . ')">
										<option value="">-- '.__('Please Choose', 'mf-survey').' --</option>'.
										
										// call function populate_next_page_droplist from list.php
										// @param survey_id, page_id, next_page_id
										populate_next_page_droplist( $survey_id, $page_id, $next_page_id )
									.'</select>
								</td>';
								
							// Edit link
							echo "
								<td style='text-align:right;'>
									<a href='admin.php?page=forms/edit-page-form.php&survey_id=".$survey_id."&page_id=".$page_id."'>".__('Edit', 'mf-survey')."</a>
								</td>";
								
						} else {
						
							echo '<td>'.__('Add question for this page', 'mf-survey').'</td><td></td>';
						
						}
						
						$id_incr++;					
					
						// Delete link
						echo "
							<td style='text-align:right;'>
								<a onclick='delete_page(\"$ajax_edit_page_url\", ".$survey_id.", ".$page_id.", \"$current_page_url\");' href='javascript:void(0);'>".__('Delete', 'mf-survey')."</a>
							</td></tr>"; 					
					
					}
					
				}				
				?>
			</tbody>
		</table>
		
		<input type="hidden" name="hid_page_checked_ids" id="hid_page_checked_ids" value="" />
		<input type="hidden" name="hid_sel_next_page_ids" id="hid_sel_next_page_ids" value="" />
		<input type="hidden" name="hid_survey_id" id="hid_survey_id" value="<?php echo $survey_id; ?>" />
		<input type="hidden" name="hid_url" id="hid_url" value="<?php echo $current_page_url; ?>" />
		
		<p class="submit">
			<input type="submit" name="btn_edit_survey" id="btn_edit_survey" value="<?php _e('Save', 'mf-survey'); ?>" class="button" />
		</p>
		
	</form>
</div>