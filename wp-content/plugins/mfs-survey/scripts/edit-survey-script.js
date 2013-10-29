/**
 * This validates edit-survey.php
 *
 * @package		WordPress
 * @subpackage	mfs-survey
 * @filename	edit-survey-script.js
 */

/**
 * @method : delete_page()
 * @param : ajax_edit_page_url string
 * @param : survey_id integer
 * @param : page_id integer
 * @return : void
 * @desc : Delete page_details for this "page_id" through ajax call
 */
function delete_page( ajax_edit_page_url, survey_id, page_id, current_page_url ) {	
	
	// To remove conflict
	jQuery.noConflict();
	
	// Displays text 'Are you sure to delete this Page?' on Delete click
	jQuery('<div></div>').appendTo('body')
	.html('<div style="text-align:center;"><h4>'+edit_survey_object.delete_page+'</h4></div>')
	.dialog({
		modal: true, title: 'Delete Page', zIndex: 10000, autoOpen: true,
		
		buttons: {
			No: function () {
				jQuery(this).dialog("close");
			},
			Yes: function () {
				//window.location.href = page_delete_url;
				delete_page_confirm(ajax_edit_page_url, survey_id, page_id, current_page_url);
				jQuery(this).dialog("close");
			}
		},
		close: function (event, ui) {
			jQuery(this).remove();
		}
	});
	
	return false;

}

function delete_page_confirm(ajax_edit_page_url, survey_id, page_id, current_page_url) {

	// To remove conflict
	jQuery.noConflict();
	
	var survey_id = survey_id;
	
	//validation for number
	if (isNaN(survey_id)) {
	
		// alerts 'This is not a valid Survey'
		jQuery('<div></div>').appendTo('body')
		.html('<div style="text-align:center;"><h4>'+edit_survey_object.survey_not_valid+'</h4></div>')
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
		return false;
		
	}
	
	survey_id = parseInt(survey_id);
	
	var page_id = page_id;
	
	//validation for number	
	if (isNaN(page_id)) {
	
		// alerts 'This is not a valid Page'
		jQuery('<div></div>').appendTo('body')
		.html('<div style="text-align:center;"><h4>'+edit_survey_object.page_not_valid+'</h4></div>')
		.dialog({
			modal: true, title: 'Page Not Valid', zIndex: 10000, autoOpen: true,
			
			buttons: {
				OK: function () {
					jQuery(this).dialog("close");
				}
			},
			close: function (event, ui) {
				jQuery(this).remove();
			}
		});
		return false;
		
	}
	
	page_id = parseInt(page_id);
	
	jQuery(document).ready(function($) {

		var data = {
		
			data_survey_id: survey_id,
			data_page_id: page_id
			
		};

		jQuery.post(ajax_edit_page_url, data, function(response) {
			
			window.location.href = current_page_url;
		
		});
		
	});

}


/**
 * @method : page_checkbox_status()
 * @param : page_id integer
 * @param : id_no integer
 * @return : void
 * @desc : This function sets checkbox id and status in hidden field
 */
function page_checkbox_status( page_id, id_no ) {

	// To remove conflict
	jQuery.noConflict();
	
	var chkbox_id = "chk_page" + id_no;
	var chk_flag = jQuery('#' + chkbox_id).is(":checked"); 
	
	if( chk_flag === true ) {
		
		//get the value of hidden field
		var chk_ids_prev = jQuery("#hid_page_checked_ids").val();
		
		//add id and status
		var chk_ids_now = chk_ids_prev + "," + page_id + "||A";
		
	}
	else if( chk_flag === false ) {
	
		//get the value of hidden field
		var chk_ids_prev = jQuery("#hid_page_checked_ids").val();
		
		//add id and status
		var chk_ids_now = chk_ids_prev + "," + page_id + "||I";
	
	}
	
	//set hidden field value null
	jQuery("#hid_page_checked_ids").val('');
	
	//set the hidden vield value
	jQuery("#hid_page_checked_ids").val(chk_ids_now);

}

/**
 * @method : sel_next_page_value()
 * @param : page_title string
 * @param : question_id integer
 * @param : id_no integer
 * @return : void
 * @desc : This function sets question_id and sel_next_page id in hidden field
 */
function sel_next_page_value( page_title, question_id, id_no ) {

	// To remove conflict
	jQuery.noConflict();	
	
	var sel_next_page_id = "sel_next_page" + id_no;
	var sel_next_page_id = jQuery('#' + sel_next_page_id).val();
	
	if (isNaN(sel_next_page_id)) {
		
		var next_page_message = "Next Page for '" + page_title + "' is not valid"
		
		// alerts 'next_page_message'
		jQuery('<div></div>').appendTo('body')
		.html('<div style="text-align:center;"><h4>'+next_page_message+'</h4></div>')
		.dialog({
			modal: true, title: 'Next Page Not Valid', zIndex: 10000, autoOpen: true,
			
			buttons: {
				OK: function () {
					jQuery(this).dialog("close");
				}
			},
			close: function (event, ui) {
				jQuery(this).remove();
			}
		});
		return false;
		
	}
	
	//get the value of hidden field
	var sel_next_page_prev = jQuery("#hid_sel_next_page_ids").val();
	
	//add previous id's and sel_next_page id
	var sel_next_page_now = sel_next_page_prev + "," + question_id + "||"+ sel_next_page_id;
	
	//set hidden field value null
	jQuery("#hid_sel_next_page_ids").val('');
	
	//set the hidden vield value
	jQuery("#hid_sel_next_page_ids").val(sel_next_page_now);
	
}

/**
 * @method : validate_sel_start_page()
 * @return : void
 * @desc : This function validates sel_start_page from inserting empty values
 */
function validate_sel_start_page() {

	// To remove conflict
	jQuery.noConflict();
	
	// checks if sel_start_page is empty
	// (If) empty change select border color to red
	// (Else) default select border color
	if(jQuery("#sel_start_page").val() === "") {
	
		jQuery("#sel_start_page").css("border","1px solid red");
	
	} else {
	
		jQuery("#sel_start_page").css("border","1px solid #CCCCCC");
	
	}

}

/**
 * @method : validate_edit_survey()
 * @return : boolean
 * @desc : This function validates form_edit_survey from inserting empty values
 */
function validate_edit_survey() {

	// To remove conflict
	jQuery.noConflict();
	
	var empty_flag = 1;
	
	if(jQuery("#sel_start_page").val() === "") {
	
		jQuery("#sel_start_page").css("border","1px solid red");
		jQuery("#sel_start_page").focus();
		empty_flag = 0;
		
	} else {
	
		jQuery("#sel_start_page").css("border","1px solid #CCCCCC");
	
	}
	
	var page_id = jQuery("#sel_start_page").val();
	
	//validation for number
	if (isNaN(page_id)) {
		
		// alerts 'Start Page is not valid'
		jQuery('<div></div>').appendTo('body')
		.html('<div style="text-align:center;"><h4>'+edit_survey_object.start_page_not_valid+'</h4></div>')
		.dialog({
			modal: true, title: 'Start page not valid', zIndex: 10000, autoOpen: true,
			
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
	
	page_id = parseInt(page_id);
	
	if(empty_flag === 0) {
	
		return false;			
	
	}

}