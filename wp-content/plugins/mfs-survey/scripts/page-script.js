/**
 * This validates page-form.php
 *
 * @package		WordPress
 * @subpackage	mfs-survey
 * @filename	page-script.js
 */
 
/**
 * @method : validate_txt_page()
 * @return : void
 * @desc : This function checks if page name is empty on keyup
 */
function validate_txt_page() {

	// To remove conflict
	jQuery.noConflict();
	
	// checks if page name is empty
	// (If) empty, change textbox border color to red
	// (Else) default textbox border color
	if(jQuery("#txt_page").val() === "") {
	
		jQuery("#txt_page").css("border","1px solid red");
	
	} else {
	
		jQuery("#txt_page").css("border","1px solid #CCCCCC");
	
	}

}

/**
 * @method : validate_sel_survey()
 * @return : void
 * @desc : This function checks if survey name is empty on change
 */
function validate_sel_survey() {

	// To remove conflict
	jQuery.noConflict();
	
	// checks if survey name is empty
	// (If) empty change select border color to red
	// (Else) default select border color
	if(jQuery("#sel_survey").val() === "") {
	
		jQuery("#sel_survey").css("border","1px solid red");
	
	} else {
	
		jQuery("#sel_survey").css("border","1px solid #CCCCCC");
	
	}

}
	
/**
 * @method : validate_add_page()
 * @return : boolean
 * @desc : This function validates page-form.php from inserting empty values
 */
function validate_add_page() {
	
	// To remove conflict
	jQuery.noConflict();
	
	var empty_flag = 1;
	
	// checks if page name is empty
	// (If) empty change textbox border color to red
	// (Else) default textbox border color	
	if(jQuery("#txt_page").val() === "") {
	
		jQuery("#txt_page").css("border","1px solid red");
		jQuery("#txt_page").focus();
		empty_flag = 0;
		
	} else {
	
		jQuery("#txt_page").css("border","1px solid #CCCCCC");
	
	}
	
	// checks if survey name is empty
	// (If) empty change droplist border color to red
	// (Else) default droplist border color
	if(jQuery("#sel_survey").val() === "") {
	
		jQuery("#sel_survey").css("border","1px solid red");
		jQuery("#sel_survey").focus();
		empty_flag = 0;
	
	} else {
	
		jQuery("#sel_survey").css("border","1px solid #CCCCCC");			
	
	}
	
	var survey_id = jQuery("#sel_survey").val();
	
	//validation for number
	if (isNaN(survey_id)) {
	
		// alerts 'This is not a valid Survey'
		jQuery('<div></div>').appendTo('body')
		.html('<div style="text-align:center;"><h4>'+page_object.survey_not_valid+'</h4></div>')
		.dialog({
			modal: true, title: 'Survey Not Valid', zIndex: 10000, autoOpen: true,
			
			buttons: {
				OK: function () {					
					jQuery(this).dialog("close");
				}
			},
			close: function (event, ui) {
				jQuery(this).remove();
			}
		});
		empty_flag = 0;
		
	}
	
	survey_id = parseInt(survey_id);
	
	if(empty_flag === 0) {
	
		return false;			
	
	}
	
}