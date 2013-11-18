/**
 * This validates edit-page.php
 *
 * @package		WordPress
 * @subpackage	mfs-survey
 * @filename	edit-page-script.js
 */

/**
 * @method : delete_question()
 * @param : ajax_edit_question_url string
 * @param : survey_id integer
 * @param : page_id integer
 * @return : void
 * @desc : Delete question_details for this "page_id" through ajax call
 */
function delete_question( ajaxcal, page_id, question_id, current_page_url ) {

	// To remove conflict
	jQuery.noConflict();
	
	// Displays text 'Are you sure to delete this Question?' on Delete click
	jQuery('<div></div>').appendTo('body')
	.html('<div style="text-align:center;"><h4>'+edit_page_object.delete_question+'</h4></div>')
	.dialog({
		modal: true, title: 'Delete Question', zIndex: 10000, autoOpen: true,
		
		buttons: {
			No: function () {
				jQuery(this).dialog("close");
			},
			Yes: function () {
				//window.location.href = page_delete_url;
				delete_question_confirm(ajaxcal, page_id, question_id, current_page_url);
				jQuery(this).dialog("close");
			}
		},
		close: function (event, ui) {
			jQuery(this).remove();
		}
	});
	
	return false;

}

function delete_question_confirm(ajaxcal, page_id, question_id, current_page_url) {

	// To remove conflict
	jQuery.noConflict();
	
	var page_id = page_id;
	
	//validation for number	
	if (isNaN(page_id)) {
	
		// alerts 'This is not a valid Page'
		jQuery('<div></div>').appendTo('body')
		.html('<div style="text-align:center;"><h4>'+edit_page_object.page_not_valid+'</h4></div>')
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
	
		var data = {
			action: 'edit_page_form',
			data_page_id: page_id
			
		};

		jQuery.post(ajaxparameter.ajax_url, data, function(response) {
		
			window.location.href = current_page_url;
		
		});
		
	
	
}

/**
 * @method : question_checkbox_status()
 * @param : question_id integer
 * @param : id_no integer
 * @return : void
 * @desc : This function sets checkbox id and status in hidden field
 */
function question_checkbox_status( question_id, id_no ) {

	// To remove conflict
	jQuery.noConflict();
	
	var chkbox_id = "chk_question" + id_no;
	var chk_flag = jQuery('#' + chkbox_id).is(":checked"); 
	
	if( chk_flag === true ) {
		
		//get the value of hidden field
		var chk_ids_prev = jQuery("#hid_question_checked_ids").val();
		
		//add id and status
		var chk_ids_now = chk_ids_prev + "," + question_id + "||A";
		
	}
	else if( chk_flag === false ) {
	
		//get the value of hidden field
		var chk_ids_prev = jQuery("#hid_question_checked_ids").val();
		
		//add id and status
		var chk_ids_now = chk_ids_prev + "," + question_id + "||I";
	
	}
	
	//set hidden field value null
	jQuery("#hid_question_checked_ids").val('');
	
	//set the hidden vield value
	jQuery("#hid_question_checked_ids").val(chk_ids_now);

}

/**
 * @method : validate_txt_page_title()
 * @return : void
 * @desc : This function checks if page name is empty on keyup
 */
function validate_txt_page_title() {

	// To remove conflict
	jQuery.noConflict();
	
	// checks if page name is empty
	// (If) empty, change textbox border color to red
	// (Else) default textbox border color
	if(jQuery("#txt_page_title").val() == "") {
	
		jQuery("#txt_page_title").css("border","1px solid red");
	
	} else {
	
		jQuery("#txt_page_title").css("border","1px solid #CCCCCC");
	
	}

}

/**
 * @method : validate_edit_page()
 * @return : boolean
 * @desc : This function validates form_edit_page from inserting empty values
 */
function validate_edit_page() {
	
	// To remove conflict
	jQuery.noConflict();
	
	var empty_flag = 1;
	
	if(jQuery("#txt_page_title").val() === "") {
	
		jQuery("#txt_page_title").css("border","1px solid red");
		jQuery("#txt_page_title").focus();
		empty_flag = 0;
		
	} else {
	
		jQuery("#txt_page_title").css("border","1px solid #CCCCCC");
	
	}

	if(empty_flag === 0) {
	
		return false;			
	
	}
	
}