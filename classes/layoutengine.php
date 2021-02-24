<?php
/**
 * Created by Stefan Herndler.
 * User: she
 * Date: 12.08.14 15:55
 * Version: 1.0.0
 * Since: 0.0.1
 */

// entry point has to be the index.php file
if (!defined("GOOGLE_KS_INTERNAL_PLUGIN_NAME")) {
	return;
}

// define class only once
if (!class_exists("GoogleKS_LayoutEngine")) :

	/**
	 * Class GoogleKS_LayoutEngine
	 */
	class GoogleKS_LayoutEngine {

		// string, public plugin name
		// @since 1.0.0
		private $a_str_PublicPluginName = "Google Keyword Suggest";

		/**
		 * @constructor
		 * @since 1.0.0
		 */
		public function __construct() {

		}

		/**
		 * executes the layout engine and renders the settings menu
		 * @since: 1.0.0
		 * @return void
		 */
		public function Run() {
			// add action hook to register my meta boxes for specific pages
			add_action("admin_menu", array($this, "RegisterMetaBoxes"));
		}

		/**
		 * register all meta boxes
		 * @since: 1.0.0
		 * @return void
		 */
		public function RegisterMetaBoxes() {
			// add a new meta box to the "edit post"
			add_meta_box( // http://codex.wordpress.org/Function_Reference/add_meta_box
				GOOGLE_KS_INTERNAL_PLUGIN_NAME . "_post", // unique meta box id
				$this->a_str_PublicPluginName, // meta box title
				array($this, "handleMetaBox"), // callback function
				'post', // screen
				'side', // content
				'high' // priority
			);

			// add a new meta box to the "edit page"
			add_meta_box( // http://codex.wordpress.org/Function_Reference/add_meta_box
				GOOGLE_KS_INTERNAL_PLUGIN_NAME . "_page", // unique meta box id
				$this->a_str_PublicPluginName, // meta box title
				array($this, "handleMetaBox"), // callback function
				'page', // screen
				'side', // content
				'high' // priority
			);
		}

		/**
		 * callback function for all meta boxes
		 * @since 1.0.0
		 */
		public function handleMetaBox() {
			// get language and country code
			$l_arr_Locale = explode("_", strtolower(get_locale()));
			$l_str_Language = $l_arr_Locale[0];
			$l_str_Country = count($l_arr_Locale) > 1 ? $l_arr_Locale[1] : "us";

			// change country code for specific countries
			switch($l_str_Country) {
				case "gb": // change "Great Britain" to "United Kingdom"
					$l_str_Country = "uk";
					break;
			}
			?>

			<table>
				<tbody>
				<tr>
					<td><?php echo $this->Label(__("Language:",GOOGLE_KS_INTERNAL_PLUGIN_NAME),"lang"); ?></td>
					<td><?php echo $this->AddSelect("lang", array("en" => "English", "fr" => "French", "de" => "German", "it" => "Italian", "es" => "Spanish", "sv" => "Swedish"), $l_str_Language); ?></td>
				</tr>
				<tr>
					<td><?php echo $this->Label(__("Country:",GOOGLE_KS_INTERNAL_PLUGIN_NAME),"cr"); ?></td>
					<td><?php echo $this->AddSelect("cr", array("au" => "Australia", "at" => "Austria", "fr" => "France", "de" => "Germany", "it" => "Italy", "mx" => "Mexico", "es" => "Spain", "se" => "Sweden", "ch" => "Switzerland", "uk" => "United Kingdom", "us" => "United States"), $l_str_Country); ?></td>
				</tr>
				<tr>
					<td><?php echo $this->Label(__("Keyword:",GOOGLE_KS_INTERNAL_PLUGIN_NAME),"keyword"); ?></td>
					<td><?php echo $this->Text_box("keyword", ""); ?></td>
				</tr>
				<tr>
					<td colspan="100%">
						<ul id="suggest" style="list-style:square; padding-left:10px;"></ul>
					</td>
				</tr>
				<tr>
					<td colspan="100%">
						<div style="text-align:right; padding-right:8px;">
							<input type="button" class="button button-primary" id="google_ks_find" value="<?php echo __('Find suggestions',GOOGLE_KS_INTERNAL_PLUGIN_NAME); ?>" />
						</div>
					</td>
				</tr>
				<tr>
					<td colspan="100%">
						<div style="text-align:right; font-size:10px;  padding-right:4px;">
							<span style="font-style:italic;">
								<?php echo $this->toItalic($this->addText(__("proudly crafted by",GOOGLE_KS_INTERNAL_PLUGIN_NAME))); ?>
								<a href="http://www.herndler.org" target="_blank">herndler.org</a> &
								<a href="http://www.seomotion.org" target="_blank">seomotion.org</a>
							</span>
						</div>
					</td>
				</tr>
				</tbody>
			</table>
			<script type="text/javascript">
				jQuery("#google_ks_find").on("click", function() {
					var l_obj_Button = jQuery(this);
					l_obj_Button.prop("disabled", true);
					jQuery.ajax({
						type: 'POST',
						url: '/wp-admin/admin-ajax.php',
						data: {
							action: 'google_ks_suggestion',
							lang: jQuery("#lang").val(),
							cr: jQuery("#cr").val(),
							keyword: jQuery("#keyword").val()
						},
						success: function(data, textStatus, XMLHttpRequest){
							jQuery("#suggest").html(data);
							l_obj_Button.prop("disabled", false);
						},
						error: function(MLHttpRequest, textStatus, errorThrown){
							console.log(textStatus);
							l_obj_Button.prop("disabled", false);
						}
					});
				});
			</script>
		<?php
		}

		/**
		 * returns the html code to append a single line break
		 * @since: 1.0.0
		 * @return string
		 */
		public function LineBreak() {
			return "<br/>";
		}

		/**
		 * returns the html code to append a newline
		 * @since: 1.0.0
		 * @return string
		 */
		public function Newline() {
			return "<br/><br/>";
		}

		/**
		 * add a space
		 * @since 1.0.0
		 * @return string
		 */
		public function addSpace() {
			return "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
		}

		/**
		 * return a html formatted string
		 * @since 1.0.0
		 * @param string $p_str_Text
		 * @return string
		 */
		public function addText($p_str_Text) {
			return '<span>' . $p_str_Text . '</span>';
		}

		/**
		 * returns the html code to change the font-style to be bold
		 * @since: 1.0.0
		 * @param string $p_str_Text
		 * @return string
		 */
		public function toBold($p_str_Text) {
			return "<b>" . $p_str_Text . "</b>";
		}

		/**
		 * returns the html code to change the font-style to be italic
		 * @since: 1.0.0
		 * @param string $p_str_Text
		 * @return string
		 */
		public function toItalic($p_str_Text) {
			return "<i>" . $p_str_Text . "</i>";
		}

		/**
		 * returns the html code to change the font-style to be underlined
		 * @since: 1.0.0
		 * @param string $p_str_Text
		 * @return string
		 */
		public function toUnderline($p_str_Text) {
			return "<u>" . $p_str_Text . "</u>";
		}

		/**
		 * returns the html code to change the font-color to a specific HEX value
		 * @since: 1.0.0
		 * @param string $p_str_Text
		 * @param string $p_str_Color [hex value]
		 * @return string
		 */
		public function ChangeForeColor($p_str_Text, $p_str_Color) {
			return '<font color="#' . $p_str_Color . '">' . $p_str_Text . '</font>';
		}

		/**
		 * returns the html code to be indended
		 * @since: 1.0.0
		 * @param string $p_str_Text
		 * @return string
		 */
		public function Indended($p_str_Text) {
			return "<blockquote>" . $p_str_Text . "</blockquote>";
		}

		/**
		 * returns the html code for a formatted description text
		 * @since: 1.0.0
		 * @param string $p_str_Text
		 * @return string
		 */
		public function Description($p_str_Text) {
			return $this->Indended($this->toItalic($p_str_Text));
		}

		/**
		 * returns the html code to append a label-tag
		 * @since: 1.0.0
		 * @param string $p_str_Caption
		 * @param string $p_str_For
		 * @return string
		 */
		public function Label($p_str_Caption, $p_str_For) {
			return '<label for="' . $p_str_For . '">' . $this->toBold($p_str_Caption) . '</label>';
		}

		/**
		 * returns the html code to append a single line text box
		 * @since: 1.0.0
		 * @param string $p_str_Setting   [setting name to be loaded]
		 * @param string $p_str_Class     [optional css class name]
		 * @param int    $p_int_Width     [optional, css width of input]
		 * @param bool   $p_bool_Readonly [optional readonly]
		 * @param bool   $p_bool_hidden   [optional hidden input]
		 * @param int    $p_int_MaxLength [optional max length]
		 * @return string
		 */
		public function Text_box($p_str_Setting, $p_str_Class = "", $p_int_Width = -1, $p_bool_Readonly = false, $p_bool_hidden = false, $p_int_MaxLength = -1) {
			// start to build the html tag
			$l_str_OutputStream = '<input type="text"';
			// append class name to output stream
			if (!empty($p_str_Class)) {
				$l_str_OutputStream .= ' class="' . $p_str_Class . '"';
			}
			// append width to output stream
			if (!empty($p_int_Width) && is_int($p_int_Width) && $p_int_Width > 0) {
				$l_str_OutputStream .= ' style="width: ' . $p_int_Width . '% !important;"';
			}
			// append readonly attribute to output stream
			if ($p_bool_Readonly) {
				$l_str_OutputStream .= ' readonly="readonly"';
			}
			// append hidden attribute to output stream
			if ($p_bool_hidden) {
				$l_str_OutputStream .= ' style="display:none;"';
			}
			// append max length to output stream
			if (!empty($p_int_MaxLength) && is_int($p_int_MaxLength) && $p_int_MaxLength > 0) {
				$l_str_OutputStream .= ' maxlength="' . $p_int_MaxLength . '"';
			}
			// append id, name and value
			$l_str_OutputStream .= ' id="' . $p_str_Setting . '"';
			$l_str_OutputStream .= ' name="' . $p_str_Setting . '"';
			$l_str_OutputStream .= ' value=""';
			// close html tag
			$l_str_OutputStream .= '/>';
			// return html tag
			return $l_str_OutputStream;
		}

		/**
		 * returns the html code to append a multi line text area
		 * @since: 1.0.0
		 * @param string $p_str_Setting [setting name to be loaded]
		 * @param string $p_str_Class   [optional css class name]
		 * @param int    $p_int_Width   [optional, css width of input]
		 * @param int    $p_int_Rows    [optional number of rows]
		 * @return string
		 */
		public function Text_area($p_str_Setting, $p_str_Class = "", $p_int_Width = -1, $p_int_Rows = 8) {
			// start to build the html tag
			$l_str_OutputStream = '<textarea';
			// append class name to output stream
			if (!empty($p_str_Class)) {
				$l_str_OutputStream .= ' class="' . $p_str_Class . '"';
			}
			// append width to output stream
			if (!empty($p_int_Width) && is_int($p_int_Width) && $p_int_Width > 0) {
				$l_str_OutputStream .= ' style="width: ' . $p_int_Width . '% !important;"';
			}
			// append max length to output stream
			if (!empty($p_int_Rows) && is_int($p_int_Rows) && $p_int_Rows > 0) {
				$l_str_OutputStream .= ' rows="' . $p_int_Rows . '"';
			}
			// append id, name and value
			$l_str_OutputStream .= ' id="' . $p_str_Setting . '"';
			$l_str_OutputStream .= ' name="' . $p_str_Setting . '"';
			$l_str_OutputStream .= '/>';
			$l_str_OutputStream .= $p_str_Setting;
			// close html tag
			$l_str_OutputStream .= '</textarea>';
			// return html tag
			return $l_str_OutputStream;
		}

		/**
		 * returns a select box
		 * @since 1.0.0
		 * @param string $p_str_SettingsID
		 * @param array $p_arr_Options
		 * @param string $p_str_Default
		 * @param string $p_str_ClassName
		 * @return string
		 */
		public function AddSelect($p_str_SettingsID, $p_arr_Options, $p_str_Default, $p_str_ClassName = "") {
			// if input shall have a css class, add the style tag for it
			if (!empty($p_str_ClassName)) {
				$p_str_ClassName = 'class="' . $p_str_ClassName . '"';
			}
			// select starting tag
			$l_str_OutputStream = '<select ' . $p_str_ClassName . ' name="' . $p_str_SettingsID . '" id="' . $p_str_SettingsID . '">';
			// loop through all array keys
			foreach ($p_arr_Options as $l_str_Value => $l_str_Caption) {
				// add key as option value
				$l_str_OutputStream .= '<option value="' . $l_str_Value . '"';
				if ($p_str_Default == $l_str_Value) {
					$l_str_OutputStream .= ' selected';
				}
				$l_str_OutputStream .= '>' . $l_str_Caption . '</option>';
			}
			// close select
			$l_str_OutputStream .= '</select>';
			// outputs the SELECT field
			return $l_str_OutputStream;
		}
	} // end of class

endif;