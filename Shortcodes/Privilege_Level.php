<?php 
/* The function that creates the HTML on the front-end, based on the parameters
* supplied in the product-catalog shortcode */
function Privilege_Level($atts, $content = null) {
		// Include the required global variables, and create a few new ones
		global $wpdb;
		global $ewd_feup_user_table_name, $ewd_feup_levels_table_name, $ewd_feup_user_fields_table_name;
		
		$UserCookie = CheckLoginCookie();
		
		$User = $wpdb->get_row($wpdb->prepare("SELECT * FROM $ewd_feup_user_table_name WHERE Username='%s'", $UserCookie['Username']));
		$PrivilegeLevel = $wpdb->get_row($wpdb->prepare("SELECT Level_Privilege FROM $ewd_feup_levels_table_name WHERE Level_ID='%d'", $User->Level_ID));
		$User_Data = $wpdb->get_results($wpdb->prepare("SELECT * FROM $ewd_feup_user_fields_table_name WHERE User_ID='%d'", $User->User_ID));
		
		if (!$UserCookie) {$ReturnString .= __("Please log in to access this content.", 'EWD_FEUP'); return $ReturnString;}
		
		// Get the attributes passed by the shortcode, and store them in new variables for processing
		extract( shortcode_atts( array(
						 								 		'minimum_level' => '',
																'maximum_level' => '',
																'level' => '',
																'field_name' => '',
																'field_value' => ''),
																$atts
														)
												);
		
		$ReturnString = $content;
		
		if ($minimum_level != '' and $PrivilegeLevel->Level_Privilege < $minimum_level) {$ReturnString = "<div class='ewd-feup-error'>" . __("Sorry, your account isn't a high enough level to access this content.", 'EWD_FEUP') . "</div>";}
		if ($maximum_level != '' and $PrivilegeLevel->Level_Privilege > $maximum_level) {$ReturnString = "<div class='ewd-feup-error'>" . __("Sorry, your account level is too high to access this content.", 'EWD_FEUP') . "</div>";}
		if ($level != '' and $PrivilegeLevel->Level_Privilege != $level) {$ReturnString = "<div class='ewd-feup-error'>" . __("Sorry, your account isn't the correct level to acces this content.", 'EWD_FEUP') . "</div>";}
		if ($field_name != '') {
			  foreach ($User_Data as $Field) {
						if ($Field->Field_Name == $field_name and $Field->Field_Value == $field_value) {$Validate = "Yes";}
				}
				if ($Validate != "Yes") {$ReturnString = "<div class='ewd-feup-error'>" . __("Sorry, this content is only for those whose " . $field_name . " is " . $field_value . ".", 'EWD_FEUP') . "</div>";}
		}
		
		return $ReturnString;
}
add_shortcode("restricted", "Privilege_Level");
