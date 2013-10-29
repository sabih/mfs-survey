/**
 * This validates survey-form.php
 *
 * @package		WordPress
 * @subpackage	mfs-survey
 * @filename	survey-script.js
 */

/**
 * @method : validate_txt_survey()
 * @return : void
 * @desc : This function checks if survey name is empty on keyup
 */
function validate_txt_survey() {

	// To remove conflict
	jQuery.noConflict();
	
	// checks if survey name is empty
	// (If) empty change textbox border color to red
	// (Else) default textbox border color
	if(jQuery("#txt_survey").val() === "") {

		jQuery("#txt_survey").css("border","1px solid red");

	} else {
	
		jQuery("#txt_survey").css("border","1px solid #CCCCCC");
	
	}

}

/**
 * @method : validate_add_survey()
 * @return : boolean
 * @desc : This function validates survey-form.php from inserting empty values
 */
function validate_add_survey() {

	// To remove conflict
	jQuery.noConflict();
	
	// checks if survey name is empty
	// (If) empty change textbox border color to red
	// (Else) default textbox border color
	if(jQuery("#txt_survey").val() === "") {
	
		jQuery("#txt_survey").css("border","1px solid red");
		jQuery("#txt_survey").focus();
		return false;
		
	} else {
	
		jQuery("#txt_survey").css("border","1px solid #CCCCCC");	
		return true;
		
	}

}