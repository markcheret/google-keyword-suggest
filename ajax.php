<?php
/**
 * Created by Stefan Herndler.
 * User: Stefan
 * Date: 21.08.14 23:28
 * Version: 1.0.0
 * Since: 0.0.1
 */

// defines the callback function for the ajax call
add_action("wp_ajax_nopriv_google_ks_suggestion", "GoogleKS_getSuggestion");
add_action("wp_ajax_google_ks_suggestion", "GoogleKS_getSuggestion");

/**
 * ajax call to determine suggestions
 * @since 1.0.0
 */
function GoogleKS_getSuggestion() {
	// collect data from AJAX request via POST
	$l_str_Language = array_key_exists("lang", $_POST) ? $_POST["lang"] : "";
	$l_str_Country = array_key_exists("cr", $_POST) ? $_POST["cr"] : "";
	$l_str_Keyword = array_key_exists("keyword", $_POST) ? utf8_encode(urlencode(trim($_POST['keyword']))) : "";

	// abort if a requested value is empty, return an empty string to clear the current list
	if (empty($l_str_Language) || empty($l_str_Country) || empty($l_str_Keyword)) {
		echo '<li>' . __("Bad request. Please fill in all input fields.", GOOGLE_KS_INTERNAL_PLUGIN_NAME) . '</li>';
		exit;
	}
	// get response utf8 encoded
	$l_str_Response = getSuggestions($l_str_Keyword, $l_str_Language, $l_str_Country);

	// convert response to XML
	/** @var SimpleXMLElement $l_obj_xml */
	$l_obj_xml = simplexml_load_string($l_str_Response);

	if (empty($l_obj_xml)) {
		returnSuggestions('<li>' . sprintf(__("Error reading response from API.", GOOGLE_KS_INTERNAL_PLUGIN_NAME)) . '</li>');
	}

	// iterate through each suggestion
	$l_str_Result = "";
	foreach($l_obj_xml->CompleteSuggestion as $l_obj_Value) {
		// append suggestion to output list
		$l_str_Result .= '<li>'.$l_obj_Value->suggestion['data'].'</li>';
	}
	// alert suggestions
	returnSuggestions($l_str_Result);
}

/**
 * Collects the suggestions or stops the script on Failure.
 *
 * @since 1.0.2
 * @param string $p_str_Keyword
 * @param string $p_str_Language
 * @param string $p_str_Country
 * @return string
 */
function getSuggestions($p_str_Keyword, $p_str_Language, $p_str_Country) {
	// get url
	$l_str_Url = 'http://google.de/complete/search?output=toolbar&hl='.$p_str_Language.'&gl='.$p_str_Country.'&q='.$p_str_Keyword;
	// get suggestions as XML
	//$l_str_Response = file_get_contents($l_str_Url);

	$l_obj_Curl = curl_init();
	curl_setopt($l_obj_Curl, CURLOPT_URL, $l_str_Url);
	curl_setopt($l_obj_Curl, CURLOPT_HEADER, 0);
	curl_setopt($l_obj_Curl, CURLOPT_RETURNTRANSFER, 1);
	$l_str_Response = curl_exec($l_obj_Curl);
	$l_int_HttpStatus = curl_getinfo($l_obj_Curl, CURLINFO_HTTP_CODE);
	curl_close($l_obj_Curl);

	// either no response received or Failure in request
	if (empty($l_str_Response)) {
		returnSuggestions('<li>' . sprintf(__("Error reading response from API. Response status: %d", GOOGLE_KS_INTERNAL_PLUGIN_NAME), $l_int_HttpStatus) . '</li>');
	}
	return utf8_encode($l_str_Response);
}

/**
 * Alerts the suggestions and stops the script.
 *
 * @since 1.0.2
 * @param string $p_str_Suggestions
 */
function returnSuggestions($p_str_Suggestions) {
	echo $p_str_Suggestions;
	exit;
}