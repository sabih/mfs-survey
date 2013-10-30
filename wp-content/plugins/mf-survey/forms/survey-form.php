<?php
/**
 * To add new survey
 *
 * @package		WordPress
 * @subpackage	mf-survey
 * @filename	survey-form.php
 */

/**
 * To include survey-script.js file
 */ 
wp_register_script( 'survey-script', plugins_url() . '/mf-survey/scripts/survey-script.js', 'jquery' );
wp_enqueue_script( 'survey-script' );

/**
 * To include survey-style.css file
 */ 
wp_enqueue_style( '', plugins_url() . '/mf-survey/styles/survey-style.css', '' );

// Checking if "survey_available" cookie available
// If available then display error message
if ( isset( $_COOKIE['survey_available'] ) ) {
	
	?>
	<div id="dv_survey_error_message" class="error highlight">
		<p>
			<?php _e('Survey name already exits. Please provide other name.', 'mf-survey'); ?>
		</p>
	</div>	
	<?php

}
?>

<div class="wrap">
	<h2><?php _e('Add Survey', 'mf-survey'); ?></h2>

	<!-- This form saves survey name -->
	<form action="edit.php?page=mf-survey/survey-action.php&amp;noheader=true" method="post" name="form_add_survey" id="form_add_survey" onsubmit="return validate_add_survey();" autocomplete="off">	
		
		<table class="widefat page fixed" cellspacing="0">
			<thead>
				<tr>
					<th>
						<?php _e('Survey Name', 'mf-survey'); ?>
					</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>
						<input type="text" name="txt_survey" id="txt_survey" onkeyup="validate_txt_survey();" />
					</td>
				</tr>
			</tbody>
		</table>		
		
		<p class="submit">
			<input type="submit" name="btn_submit_survey" id="btn_submit_survey" value="<?php _e('Save', 'mf-survey'); ?>" class="button" />
		</p>
		
	</form>
</div>