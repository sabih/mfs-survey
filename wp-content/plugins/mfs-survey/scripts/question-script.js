/**
 * This validates question-form.php
 *
 * @package		WordPress
 * @subpackage	mfs-survey
 * @filename	question-script.js
 */

/**
 * @method : get_page_by_survey()
 * @param : ajax_survey_url string
 * @return : void
 * @desc : Get page by survey_id through ajax call
 */
function get_page_by_survey( ajax_survey_url ) {
	
	// To remove conflict
	jQuery.noConflict();
	
	//Validation starts//
	
	// checks if survey name is empty
	// (If) empty change select border color to red
	// (Else) default select border color
	if(jQuery("#sel_survey").val() === "") {
	
		jQuery("#sel_survey").css("border","1px solid red");
	
	} else {
	
		jQuery("#sel_survey").css("border","1px solid #CCCCCC");
	
	}
	//Validation ends//

	var survey_id = jQuery("#sel_survey").val();
	
	//validation for number
	if (isNaN(survey_id)) {
	
		// alerts 'This is not a valid Survey'
		jQuery('<div></div>').appendTo('body')
		.html('<div style="text-align:center;"><h4>'+question_object.survey_not_valid+'</h4></div>')
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
	
	jQuery(document).ready(function($) {

		var data = {
		
			data_survey_id: survey_id		
			
		};

		jQuery.post(ajax_survey_url, data, function(response) {			
			
			jQuery('#sel_page').html(response);
			jQuery('#sel_next_page').html(response);
			
			// If no page added for a particular survey then
			// display alert message
			var count = jQuery("#sel_page option").length;
			var survey_value = jQuery("#sel_survey").val();
			
			if( count === 1 && survey_value != "" ) {
			
				// alerts 'Please add Page for this Survey'
				jQuery('<div></div>').appendTo('body')
				.html('<div style="text-align:center;"><h4>'+question_object.add_page+'</h4></div>')
				.dialog({
					modal: true, title: 'Add Page', zIndex: 10000, autoOpen: true,
					
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
			
		
		});
		
	});

}

/**
 * @method : validate_sel_question_type()
 * @return : void
 * @desc : This function shows the hidden table for adding question and option
 * 			depending on the question type choosen
 */
function validate_sel_question_type() {
	
	// To remove conflict
	jQuery.noConflict();	
		
	//Validation starts//
	
	// checks if survey name is empty
	// (If) empty change select border color to red
	// (Else) default select border color
	if(jQuery("#sel_question_type").val() === "") {
	
		jQuery("#sel_question_type").css("border","1px solid red");
	
	} else {
	
		jQuery("#sel_question_type").css("border","1px solid #CCCCCC");
	
	}
	//Validation ends//
	
	// To remove the red border color from "txt_question" on change of "sel_question_type"
	jQuery("#txt_question").css("border","1px solid #CCCCCC");
	
	var question_type = jQuery("#sel_question_type").val();
	
	if(question_type === "") {
	
		jQuery("#tbl_question").hide();
		
	} else {
		
		jQuery("#tbl_question").show();
		
		// Call function remove_row() with @param all_row to remove all options except 1st one
		remove_row('all_row');
		
		if(question_type === "Textbox") {
		
			// Displays text 'Please add your question'
			jQuery("#lbl_question_header").text(question_object.add_question);
		
		} else {
		
			// Displays text 'Please add your question and option'
			jQuery("#lbl_question_header").text(question_object.add_question_option);
			
			// Call function add_row() to add a new row for option
			add_row();
		
		}
	
	}
	
}

/**
 * @method : add_row()
 * @return : void
 * @desc : This function adds new rows for option on button click
 *			and onchange of "sel_question_type"
 */
function add_row() {

	var table = document.getElementById('tbl_question');
	
	// Get the number of rows in table (On 1st click row_count = 3)
	var row_count = table.rows.length;
	
	// Making row_count_input = 1, which will be used to generate id for option textbox
	var row_count_input = parseInt(row_count) - 2;
	
	// Insert row in table 
	//(insertRow(0) takes 1st value as 0, So, insertRow(row_count) means insertRow(3) ie, 4th row)
	var row = table.insertRow(row_count);
	
	var cell1 = row.insertCell(0);
	var cell2 = row.insertCell(1);
	
	// The button "+" and "-" gets stored in "str_btn_add" and "str_btn_remove" respectively
	var str_btn_add = "";
	var str_btn_remove = "";
	if(row_count === 2) {
	
		str_btn_add = '<input type="button" name="btn_add_option[]" value="+" onclick="add_row();" class="button" id="btn_add_option" />';
		str_btn_remove = '<input type="button" name="btn_remove_option[]" value="-" onclick="remove_row(\'one_row\');" class="button" style="display:none" id="btn_remove_option" />';
	
	}
	
	var txt_option_id = "txt_option" + row_count_input;
	
	// Insert text "Option" in Cell1
	cell1.innerHTML = question_object.option;
	
	// Insert textbox in Cell2
	// The button "+" and "-" gets added in 3rd row (ie, row_count = 2) with 1st option textbox
	cell2.innerHTML = "<input type='text' name='txt_option[]' id='" + txt_option_id + "' >" + str_btn_add + str_btn_remove;
	
	// Show Remove button when row_count > 2
	if( row_count > 2 ) {
	
		jQuery("#btn_remove_option").show();
	
	}

}

/**
 * @method : remove_row()
 * @param : delete_row string
 * @return : void
 * @desc : This function removes rows for option on "-" Remove button click
 *			and onchange of "sel_question_type"
 */
function remove_row( delete_row ) {

	var table = document.getElementById('tbl_question');
	
	// Get the number of rows in table (On 1st click, row_count = n)
	var row_count = table.rows.length;
		
	if(delete_row === 'one_row') {
	
		// Delete row from table 
		//(deleteRow(0) takes 1st value as 0, So, deleteRow(row_count-1) means deleteRow(n-1) ie, last row)
		table.deleteRow( row_count - 1 );
	
	} else {
	
		for(var total_row = row_count; total_row > 2; total_row--) {
	
			table.deleteRow( total_row - 1 );
		
		}
	
	}
	
	// Hide Remove button when row_count < 5
	if( row_count < 5 ) {
	
		jQuery("#btn_remove_option").hide();
		
	}

}

/**
 * @method : validate_sel_next_page()
 * @return : void
 * @desc : This function checks if next page name is empty on change
 */
function validate_sel_next_page() {

	// To remove conflict
	jQuery.noConflict();
	
	// checks if sel_next_page is empty
	// (If) empty change select border color to red
	// (Else) default select border color
	if(jQuery("#sel_next_page").val() === "") {
	
		jQuery("#sel_next_page").css("border","1px solid red");
	
	} else {
	
		jQuery("#sel_next_page").css("border","1px solid #CCCCCC");
	
	}

}

/**
 * @method : validate_sel_page()
 * @return : void
 * @desc : This function checks if page name is empty on change
 */
function validate_sel_page() {

	// To remove conflict
	jQuery.noConflict();
	
	// checks if page name is empty
	// (If) empty change select border color to red
	// (Else) default select border color
	if(jQuery("#sel_page").val() === "") {
	
		jQuery("#sel_page").css("border","1px solid red");
	
	} else {
	
		jQuery("#sel_page").css("border","1px solid #CCCCCC");
	
	}

}

/**
 * @method : validate_txt_question()
 * @return : void
 * @desc : This function checks if question is empty on keyup
 */
function validate_txt_question() {

	// To remove conflict
	jQuery.noConflict();
	
	// checks if question is empty
	// (If) empty, change textbox border color to red
	// (Else) default textbox border color
	if(jQuery("#txt_question").val() === "") {
	
		jQuery("#txt_question").css("border","1px solid red");
	
	} else {
	
		jQuery("#txt_question").css("border","1px solid #CCCCCC");
	
	}

}

/**
 * @method : diplay_options()
 * @return : void
 * @desc : This function stores all options values in "hid_option_ids" field onclick of "btn_submit_question"
 */
function diplay_options() {

	var table = document.getElementById('tbl_question');
	
	// Get the number of rows in table (On 1st click row_count = n)
	var row_count = table.rows.length;
	row_count = row_count - 2;
	jQuery("#hid_option_ids").val('');
	
	for(var id = 0; id < row_count; id++) {

		var option = document.getElementById('txt_option'+id).value;
		
		//get the value of hidden field
		var prev_option_ids = jQuery("#hid_option_ids").val();
		
		//add id and status
		var current_option_ids = prev_option_ids + "|" + option;
		
		//set hidden field value null
		jQuery("#hid_option_ids").val('');
		
		//set the hidden vield value
		jQuery("#hid_option_ids").val(current_option_ids);			
		
	}
	
}

/**
 * @method : validate_add_question()
 * @return : boolean
 * @desc : This function validates question-form.php from inserting empty values
 */
function validate_add_question() {
	
	// To remove conflict
	jQuery.noConflict();
	
	var empty_flag = 1;
	
	if(jQuery("#sel_question_type").val() != "") {
	
		// checks if question is empty
		// (If) empty change textbox border color to red
		// (Else) default textbox border color	
		if(jQuery("#txt_question").val() === "") {
		
			jQuery("#txt_question").css("border","1px solid red");
			jQuery("#txt_question").focus();
			empty_flag = 0;
			
		} else {
		
			jQuery("#txt_question").css("border","1px solid #CCCCCC");
		
		}		
		
		// checks if any option is empty
		// (If) empty change textbox border color to red
		// (Else) default textbox border color	
		var table = document.getElementById('tbl_question');
	
		var row_count = table.rows.length;
		var row_count_input = parseInt(row_count) - 3;
		
		for(var total_row = row_count_input; total_row  >= 0; total_row--) {
			
			var txt_option_id = "#txt_option" + total_row;
			
			// checks if option is empty
			// (If) empty change textbox border color to red
			// (Else) default textbox border color	
			if(jQuery(txt_option_id).val() === "") {
			
				jQuery(txt_option_id).css("border","1px solid red");
				jQuery(txt_option_id).focus();
				empty_flag = 0;
				
			} else {
			
				jQuery(txt_option_id).css("border","1px solid #CCCCCC");
			
			}
			
		}
		
	}
	
	// checks if question type is empty
	// (If) empty change droplist border color to red
	// (Else) default droplist border color
	if(jQuery("#sel_question_type").val() === "") {
	
		jQuery("#sel_question_type").css("border","1px solid red");
		jQuery("#sel_question_type").focus();
		empty_flag = 0;
	
	} else {
	
		jQuery("#sel_question_type").css("border","1px solid #CCCCCC");			
	
	}
	
	// checks if page name is empty
	// (If) empty change droplist border color to red
	// (Else) default droplist border color
	if(jQuery("#sel_page").val() === "") {
	
		jQuery("#sel_page").css("border","1px solid red");
		jQuery("#sel_page").focus();
		empty_flag = 0;
	
	} else {
	
		jQuery("#sel_page").css("border","1px solid #CCCCCC");			
	
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
	
	var page_id = jQuery("#sel_page").val();
	var next_page_id = jQuery("#sel_next_page").val();
	
	//validation for number
	if (page_id && isNaN(page_id)) {
	
		// alerts 'This is not a valid Page'
		jQuery('<div></div>').appendTo('body')
		.html('<div style="text-align:center;"><h4>'+question_object.page_not_valid+'</h4></div>')
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
		empty_flag = 0;
		
	}
	
	//validation for number
	if (next_page_id && isNaN(next_page_id)) {
	
		// alerts 'Next Page is not valid'
		jQuery('<div></div>').appendTo('body')
		.html('<div style="text-align:center;"><h4>'+question_object.next_page_not_valid+'</h4></div>')
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
		empty_flag = 0;
		
	}
	
	page_id = parseInt(page_id);
	next_page_id = parseInt(next_page_id);
	
	// Validation for question type
	var question_type = jQuery("#sel_question_type").val();
	if( question_type != "" && question_type != "Textbox" && question_type != "Radiobutton" && question_type != "Checkbox" ) {
	
		// alerts 'This is not valid Question Type'
		jQuery('<div></div>').appendTo('body')
		.html('<div style="text-align:center;"><h4>'+question_object.question_type_not_valid+'</h4></div>')
		.dialog({
			modal: true, title: 'Question Type Not Valid', zIndex: 10000, autoOpen: true,
			
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
	
	if(empty_flag === 0) {
	
		return false;			
	
	}
	
}