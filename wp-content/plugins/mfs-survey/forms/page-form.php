<?php
/**
 * To add new page in a survey
 *
 * @package		WordPress
 * @subpackage	mfs-survey
 * @filename	page-form.php
 */
 
/**
 * To include page-script.js file
 */
wp_register_script( 'page-script', plugins_url() . '/mfs-survey/scripts/page-script.js', 'jquery' );
wp_enqueue_script( 'page-script' );

wp_register_script( 'jquery-min', plugins_url() . '/mfs-survey/scripts/jquery/jquery.min.js', 'jquery' );
wp_enqueue_script( 'jquery-min' );

wp_register_script( 'jquery-ui-min', plugins_url() . '/mfs-survey/scripts/jquery/jquery-ui.min.js', 'jquery');
wp_enqueue_script( 'jquery-ui-min' );

/**
 * To include survey-style.css file
 */
wp_enqueue_style( '', plugins_url() . '/mfs-survey/styles/jquery-ui.css', '' );
wp_enqueue_style( '', plugins_url() . '/mfs-survey/styles/survey-style.css', '' );

// Passing alert message as an array for translation to be applicable
$translation_array = array (
						'survey_not_valid' => __( 'This is not valid Survey' )
					);
wp_localize_script( 'page-script', 'page_object', $translation_array );

/**
 * Includes list.php to populate survey droplist
 */
require_once( __DIR__ . '/../list.php' );

// Checking if "survey_saved" cookie available
// If available then display message as "Survey saved"
if ( isset( $_COOKIE['survey_saved'] ) ) {
	
	?>
	<div id="dv_survey_message" class="updated highlight">
		<p><?php _e('Survey saved', 'mfs-survey'); ?></p>
	</div>	
	<?php

}

// Checking if "page_available" cookie available
// If available then display error message
if ( isset( $_COOKIE['page_available'] ) ) {

	?>
	<div id="dv_page_error_message" class="error highlight">
		<p>
			<?php _e('Page name already exits for this survey. Please provide other name.', 'mfs-survey'); ?>
		</p>
	</div>	
	<?php

}
?>

<div class="wrap">
	<h2><?php _e('Add Page', 'mfs-survey'); ?></h2>
	
	<!-- This form saves page name with survey_id -->
	<form action="edit.php?page=mfs-survey/survey-action.php&amp;noheader=true" method="post" name="form_add_page" id="form_add_page" onsubmit="return validate_add_page();" autocomplete="off">	
		
		<table class="widefat page fixed" cellspacing="0">
			<thead>
				<tr>
					<th>
						<?php _e('Page Name', 'mfs-survey'); ?>
					</th>
					<th>
						<?php _e('Survey name', 'mfs-survey'); ?>
					</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>
						<input type="text" name="txt_page" id="txt_page" onkeyup="validate_txt_page();" />
					</td>
					<td>
						<select id="sel_survey" name="sel_survey" onchange="validate_sel_survey();">
							<option value="">-- <?php _e('Please Choose', 'mfs-survey'); ?> --</option>
							<?php
							// call function populate_survey_droplist from list.php
							echo populate_survey_droplist(); 
							?>
						</select>
					</td>
				</tr>
			</tbody>
		</table>
		
		<p class="submit">
			<input type="submit" name="btn_submit_page" id="btn_submit_page" value="<?php _e('Save', 'mfs-survey'); ?>" class="button" />
		</p>
		
	</form>
</div>