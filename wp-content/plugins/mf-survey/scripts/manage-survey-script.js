/**
 * This validates manage-survey.php
 *
 * @package		WordPress
 * @subpackage	mf-survey
 * @filename	manage-survey-script.js
 */

/**
 * @method : publish_survey()
 * @return : boolean
 * @desc : This function publish survey on confirmation
 */
function publish_survey(survey_publish_url) {

	// To remove conflict
	jQuery.noConflict();
	
	jQuery('<div></div>').appendTo('body')
	.html('<div style="text-align:center;"><h4>'+manage_survey_object.publish_survey+'</h4></div>')
	.dialog({
		modal: true, title: 'Publish Survey', zIndex: 10000, autoOpen: true,
		
		buttons: {
			No: function () {
				jQuery(this).dialog("close");
			},
			Yes: function () {
				window.location.href = survey_publish_url;
				jQuery(this).dialog("close");
			}
		},
		close: function (event, ui) {
			jQuery(this).remove();
		}
	});
	
	return false;

}

/**
 * @method : delete_survey()
 * @return : boolean
 * @desc : This function delete survey on confirmation
 */
function delete_survey(survey_delete_url) {
	
	// To remove conflict
	jQuery.noConflict();
	
	jQuery('<div></div>').appendTo('body')
	.html('<div style="text-align:center;"><h4>'+manage_survey_object.delete_survey+'</h4></div>')
	.dialog({
		modal: true, title: 'Delete Survey', zIndex: 10000, autoOpen: true,
		
		buttons: {
			No: function () {
				jQuery(this).dialog("close");
			},
			Yes: function () {
				window.location.href = survey_delete_url;
				jQuery(this).dialog("close");
			}
		},
		close: function (event, ui) {
			jQuery(this).remove();
		}
	});
	
	return false;
	
}

/**
 * @method : survey_checkbox_status()
 * @param : survey_id integer
 * @param : id_no integer
 * @return : void
 * @desc : This function sets checkbox id and survey status in hidden field
 */
function survey_checkbox_status( survey_id, id_no ) {

	// To remove conflict
	jQuery.noConflict();
	
	var chkbox_id = "chk_survey" + id_no;
	var chk_flag = jQuery('#' + chkbox_id).is(":checked"); 
	
	if( chk_flag === true ) {
		
		//get the value of hidden field
		var chk_ids_prev = jQuery("#hid_survey_checked_ids").val();
		
		//add id and status
		var chk_ids_now = chk_ids_prev + "," + survey_id + "||A";
		
	}
	else if( chk_flag === false ) {
	
		//get the value of hidden field
		var chk_ids_prev = jQuery("#hid_survey_checked_ids").val();
		
		//add id and status
		var chk_ids_now = chk_ids_prev + "," + survey_id + "||I";
	
	}
	
	//set hidden field value null
	jQuery("#hid_survey_checked_ids").val('');
	
	//set the hidden vield value
	jQuery("#hid_survey_checked_ids").val(chk_ids_now);

}