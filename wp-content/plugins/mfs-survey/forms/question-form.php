<?php
/**
 * To add new question in a page
 *
 * @package	WordPress
 * @subpackage	mfs-survey
 * @filename	question-form.php
 */
 
/**
 * To include question-script.js file
 */
wp_register_script( 'question-script', plugins_url() . '/mfs-survey/scripts/question-script.js', 'jquery' );
wp_enqueue_script( 'question-script' );
wp_localize_script('question-script','ajaxdataparameter',array( 'ajax_url' => admin_url( 'admin-ajax.php' )));
wp_enqueue_script( 'jquery' );
wp_enqueue_script( 'jquery-ui-dialog' );

/**
 * To include survey-style.css file
 */
wp_enqueue_style( '', plugins_url() . '/mfs-survey/styles/jquery-ui.css', '' );
wp_enqueue_style( '', plugins_url() . '/mfs-survey/styles/survey-style.css', '' );

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
 * Includes list.php to populate survey, page and question_type droplist
*/
require_once( __DIR__ . '/../list.php' );



// Checking if "page_saved" cookie available
// If available then display message as "Page saved"
if ( isset( $_COOKIE['page_saved'] ) ) {
	
	?>
	<div id="dv_page_message" class="updated highlight">
		<p><?php _e('Page saved', 'mfs-survey'); ?></p>
	</div>	
	<?php

}

// Checking if "question_saved" cookie available
// If available then display message as "Question saved"
if ( isset( $_COOKIE['question_saved'] ) ) {
	
	?>
	<div id="dv_question_message" class="updated highlight">
		<p><?php _e('Question saved', 'mfs-survey'); ?></p>
	</div>	
	<?php

}

// Checking if "question_available" cookie available
// If available then display error message
if ( isset( $_COOKIE['question_available'] ) ) {

	?>
	<div id="dv_question_error_message" class="error highlight">
		<p><?php _e('Question already exits for this page.', 'mfs-survey'); ?>
		<?php _e('Please choose other page.', 'mfs-survey'); ?><br/>
		<?php _e('For editing this page move to "Manage Survey" section.', 'mfs-survey'); ?>
		</p>
	</div>	
	<?php

}

/**
 * Includes ajax-survey.php
 */
$ajax_survey_url = plugins_url() . '/mfs-survey/ajax/ajax-survey.php';
?>

<div class="wrap">
	<h2><?php _e('Add Question', 'mfs-survey'); ?></h2>

	<!-- This form saves question details with page_id -->
	<form action="edit.php?page=mfs-survey/survey-action.php&amp;noheader=true" method="post" name="form_add_question" id="form_add_question" onsubmit="return validate_add_question();" autocomplete="off">
		
		<table class="widefat page fixed" cellspacing="0">
			<thead>
				<tr>
					<th>
						<?php _e('Survey Name', 'mfs-survey'); ?>
					</th>
					<th>
						<?php _e('Page Name', 'mfs-survey'); ?>
					</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>
						<!-- Call function get_page_by_survey from question-script.js onchange  -->
						<select id="sel_survey" name="sel_survey" onchange="get_page_by_survey();">
							<option value="">-- <?php _e('Please Choose', 'mfs-survey'); ?> --</option>
							<?php 
							// call function populate_survey_droplist from list.php
							echo populate_survey_droplist(); 
							?>
						</select>
						<img class="ajax-loader" src="<?php echo plugins_url()?>/mfs-survey/images/ajax-loader.gif" alt="Sending ..." style="display:none;">
					</td>
					<td>
						<select id="sel_page" name="sel_page" onchange="validate_sel_page();">
							<option value="">-- <?php _e('Please Choose', 'mfs-survey'); ?> --</option>
						</select>
					</td>
				</tr>
			</tbody>
		</table>

		<br />
		
		<table class="widefat page fixed" cellspacing="0">
			<thead>
				<tr>
					<th>
						<?php _e('Question Type', 'mfs-survey'); ?>
					</th>
					<th>
						<?php _e('Next Page Name', 'mfs-survey'); ?>
					</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>
						<select id="sel_question_type" name="sel_question_type" onchange="validate_sel_question_type();">
							<option value="">-- <?php _e('Please Choose', 'mfs-survey'); ?> --</option>
							<?php
							// call function populate_question_type_droplist from list.php
							$question_type_list = populate_question_type_droplist();
							$options = "";							
							foreach ($question_type_list as $key => $val) {
							
								$options .= '<option value="' . $key . '">' . $val . '</option>';
							
							}							
							echo $options;
							?>
						</select>
					</td>
					<td>
						<select id="sel_next_page" name="sel_next_page">
							<option value="">-- <?php _e('Please Choose', 'mfs-survey'); ?> --</option>
						</select>
					</td>
				</tr>
			</tbody>
		</table>
		
		<br />
		
		<table class="widefat page" cellspacing="0" id="tbl_question" style="display:none;">
			<thead>
				<tr>
					<th colspan="2">
						<label id="lbl_question_header"><?php _e('Please add your question and option', 'mfs-survey'); ?></label>
					</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td width="10%">
						<?php _e('Add Question', 'mfs-survey'); ?>
					</td>
					<td>
						<input style="width:100%" type="text" name="txt_question" id="txt_question" onkeyup="validate_txt_question();" />
					</td>
				</tr>
				<!-- Option textbox with "+" and "-" button gets added onchange of "sel_question_type" droplist -->
			</tbody>
		</table>
		
		<input type="hidden" name="hid_option_ids" id="hid_option_ids" value="" />
		
		<p class="submit">
			<input type="submit" name="btn_submit_question" id="btn_submit_question" value="<?php _e('Save', 'mfs-survey'); ?>" class="button" onclick="diplay_options();" />
		</p>			
		
	</form>
</div>