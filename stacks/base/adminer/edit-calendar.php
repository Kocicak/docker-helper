<?php

/** Display jQuery UI Timepicker for each date and datetime field
* @link https://www.adminer.org/plugins/#use
* @uses jQuery-Timepicker, http://trentrichardson.com/examples/timepicker/
* @uses jQuery UI: core, widget, mouse, slider, datepicker
* @author Jakub Vrana, https://www.vrana.cz/
* @license https://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
* @license https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2 (one or other)
*/
class AdminerEditCalendar {
	/** @access protected */
	var $prepend, $langPath;

	/**
	* @param string text to append before first calendar usage
	* @param string path to language file, %s stands for language code
	*/
	function __construct($prepend = null, $langPath = "plugins/jquery/jquery.ui.datepicker-%s.js") {
		if ($prepend === null) {
			$prepend = "<link rel='stylesheet' type='text/css' href='plugins/jquery/jquery-ui.css'>\n"
                . "<link rel='stylesheet' type='text/css' href='plugins/jquery/jquery.datetimepicker.css'>\n"
				. script_src("plugins/jquery/jquery.js")
				. script_src("plugins/jquery/jquery-ui.js")
				. script_src("plugins/jquery/php-date-formatter.js")
				. script_src("plugins/jquery/jquery.datetimepicker.js")
			;
		}
		$this->prepend = $prepend;
		$this->langPath = $langPath;
	}

	function head() {
		echo $this->prepend;
		if ($this->langPath && function_exists('get_lang')) { // since Adminer 3.2.0
			$lang = get_lang();
			$lang = ($lang == "zh" ? "zh-CN" : ($lang == "zh-tw" ? "zh-TW" : $lang));
			if ($lang != "en" && file_exists(sprintf($this->langPath, $lang))) {
				echo script_src(sprintf($this->langPath, $lang));
				echo script("jQuery(function () { jQuery.timepicker.setDefaults(jQuery.datepicker.regional['$lang']); });");
			}
		}
	}

	function editInput($table, $field, $attrs, $value) {
		if (preg_match("~date|time~", $field["type"])) {
            $format = "format: 'Y-m-d H:i:s'";
			return "<input id='fields-" . h($field["field"]) . "' value='" . h($value) . "'" . (@+$field["length"] ? " data-maxlength='" . (+$field["length"]) . "'" : "") . "$attrs>" . script(
				"jQuery('#fields-" . js_escape($field["field"]) . "').datetimepicker({ $format });");
		}
	}

}
