<?php

class Widget {
	/**
	 * Returns a textual id based on a form's name.
	 *
	 * @param string $name
	 */
	private static function id($name) {
		return preg_replace("/^(.*?)_*$/", "\\1", str_replace(array ('[', ']'), array('_', ''), $name));
	}

	private static function isRequired($name, $override = false) {
		// We can force an override, or if this doesn't appear to be a class 
		// attribute deal, we can also skip it.
		if($override || strpos($name, "[") === false) {
			return $override;
		} else {
			list($class, $column) = explode("[", $name);
			$class = Inflector::classify($class);
			$column = str_replace("]", "", $column);

			if(class_exists($class, false)) {
				$object = new $class();
				if(method_exists($object, "isRequired")) {
					return call_user_func(array($object, "isRequired"), $column);
				}
			}
		}
	}

	/**
	 * Returns a label tag for a form element.
	 *
	 * The label handed can either be a string or an array. In the case of a
	 * string, it simply becomes the label text. However, if its an array, the
	 * first element becomes the label text, and the rest of the elements
	 * become their own description paragrahs.
	 *
	 * If an id is present, we do an actual label tag, and link it to the given
	 * id. However, if no id is present, we simply generate a div tag that's
	 * styled in the same manner as a label.
	 *
	 * @param mixed $label
	 * @param string $id
	 */
	public static function label($label, $id = '', $required = false) {
		$html = '';
		if(!empty($label)) {
			if(is_array($label)) {
				$real_label = array_shift($label);
				$description = $label;
				$label = $real_label;
			}

			$class = ($required) ? " required" : "";

			if($id) {
				$html .= "<label for=\"$id\" class=\"field$class\">$label</label>\n";
			} else {
				$html .= "<label class=\"field$class\">$label</label>\n";
			}

			if(isset($description)) {
				foreach($description as $i => $text) {
					$class = ($i == 0) ? ' top' : '';
					$html .= "<p class=\"description$class\">$text</p>\n";
				}
			}
		}

		return $html;
	}

	/**
	 * Returns an input tag
	 *
	 * @param string $label
	 * @param string $name
	 * @param string $value
	 * @param int $size
	 * @param int $maxlength
	 * @see label
	 */
	public static function input($label, $name, $value = '', $size = 30, $maxlength = 255, $required = false) {
		$id = Widget::id($name);
		$class[] = ($size == 30) ? "expand" : "";
		$class = implode(" ", $class);
		return Widget::label($label, $id, Widget::isRequired($name, $required)) . "<input type=\"text\" name=\"$name\" id=\"$id\" value=\"$value\" size=\"$size\" maxlength=\"$maxlength\" class=\"$class\" />";
	}

	/**
	 * Returns a textarea tag
	 *
	 * @param string $label
	 * @param string $name
	 * @param string $value
	 * @param int $rows
	 * @param int $cols
	 * @see label
	 */
	public static function textarea($label, $name, $value = '', $rows = 10, $cols = 30, $required = false) {
		$id = Widget::id($name);
		$class[] = ($cols == 30) ? "expand" : "";
		$class = implode(" ", $class);
		return Widget::label($label, $id, Widget::isRequired($name, $required)) . "<textarea name=\"$name\" id=\"$id\" rows=\"$rows\" cols=\"$cols\" class=\"$class\">$value</textarea>";
	}

	public static function file($label, $name, $required = false) {
		$id = Widget::id($name);
		return Widget::label($label, $id, Widget::isRequired($name, $required)) . "<input type=\"file\" name=\"$name\" id=\"$id\" />";
	}

	/**
	 * Returns a select list
	 *
	 * The supplied list should be an array of objects. The getId() method will
	 * be used for the option's value, and the array's string value
	 * (__toString()) will be used for the textual representation.
	 *
	 * @param string $label
	 * @param string $name
	 * @param array $list 
	 * @param int $value
	 * @see label
	 */
	public static function select($label, $name, $list, $value = 0, $required = false) {
		$id = Widget::id($name);
		$html = Widget::label($label, $id, Widget::isRequired($name, $required)) . "<select name=\"$name\" id=\"$id\">";
		$html .= '<option value="0">Select one</option><option value="0"></option>';
		foreach($list as $object) {
			$selected = ($object->getId() == $value) ? ' selected="selected"' : '';
			$html .= "<option value=\"{$object->getId()}\"$selected>{$object->__toString()}</option>";
		}
		$html .= "</select>";
		return $html;
	}

	/**
	 * Returns a list of radio buttons.
	 *
	 * List given as an array of objects similar to select().
	 *
	 * @param string $label
	 * @param string $name
	 * @param array $list
	 * @param int $value
	 * @see label
	 * @see select
	 */
	public static function radios($label, $name, $list, $value = 0, $required = false, $disabledFields = null) {
		$html = Widget::label($label, "", Widget::isRequired($name, $required));

		// Append a 0 value hidden element so we the radios behave more
		// normally.
		$html .= "<input type=\"hidden\" name=\"$name\" value=\"0\" />";

		foreach($list as $object) {
			$disabled = "";
			if ($disabledFields != null) {
	
				if ( in_array($object->getId(), $disabledFields) ) {
					$disabled = " disabled = \"true\"";
				}
			}
			$id = Widget::id($name) .'_'. $object->getId();
			$checked = ($object->getId() == $value) ? ' checked="checked"' : '';
			$html .= "<input type=\"radio\" name=\"$name\"$disabled id=\"$id\" value=\"{$object->getId()}\"$checked><label for=\"$id\">{$object->__toString()}</label><br />";
		}
		return $html;

	}

	/**
	 * Returns a list of checkboxes.
	 *
	 * List given as an array of objects similar to select().
	 *
	 * @param string $label
	 * @param string $name
	 * @param array $list
	 * @param array $values
	 * @see label
	 * @see select
	 */
	public static function checkboxes($label, $name, $list, $values = array(), $required = false) {
		$html = Widget::label($label, "", Widget::isRequired($name, $required));

		// Append a 0 value hidden element so we the checkboxes behave more
		// normally.
		$html .= "<input type=\"hidden\" name=\"$name\" value=\"0\" />";

		foreach($list as $object) {
			$id = Widget::id($name) .'_'. $object->getId();
			$checked = (in_array($object->getId(), $values)) ? ' checked="checked"' : '';
			$html .= "<input type=\"checkbox\" name=\"$name\" id=\"$id\" value=\"{$object->getId()}\"$checked><label for=\"$id\">{$object->__toString()}</label><br />\n";
		}
		return $html;
	}

	public static function date($name, $time = 0, $start = 2004, $end = 0) {
		// If we're given an "empty" time, fill it in with the current time.
		if($time == 0) {
			$time = mktime(0, 0, 0);
		}

		if($end == 0) {
			$end = date('Y') + 1;
		}

		$year_name = "{$name}[year]";
		$year_id = Widget::id($year_name);
		$year = "<select name=\"$year_name\" id=\"$year_id\">";
		for($i = $start; $i <= $end; $i++) {
			$selected = (date('Y', $time) == $i) ? ' selected="selected"' : '';
			$year .= "<option value=\"$i\"$selected>$i</option>";
		}
		$year .= '</select>';

		$month_name = "{$name}[month]";
		$month_id = Widget::id($month_name);
		$month = "<select name=\"$month_name\" id=\"$month_id\">";
		for($i = 1; $i <= 12; $i++) {
			$selected = (date('n', $time) == $i) ? ' selected="selected"' : '';
			$month .= sprintf('<option value="%d"%s>%02d</option>', $i, $selected, $i);
		}
		$month .= '</select>';

		$day_name = "{$name}[day]";
		$day_id = Widget::id($day_name);
		$day = "<select name=\"$day_name\" id=\"$day_id\">";
		for($i = 1; $i <= 31; $i++) {
			$selected = (date('j', $time) == $i) ? ' selected="selected"' : '';
			$day .= sprintf('<option value="%d"%s>%02d</option>', $i, $selected, $i);
		}
		$day .= '</select>';

		return "$month / $day / $year";
	}

	public static function time($name, $time = 0) {
		// If we're given an "empty" time, fill it in with the current time.
		if($time == 0) {
			$time = mktime(0, 0, 0);
		}

		$hour_name = "{$name}[hour]";
		$hour_id = Widget::id($hour_name);
		$hour = "<select name=\"$hour_name\" id=\"$hour_id\">";
		for($i = 1; $i <= 12; $i++) {
			$selected = (date('g', $time) == $i) ? ' selected="selected"' : '';
			$hour .= "<option value=\"$i\"$selected>$i</option>";
		}
		$hour .= '</select>';

		$minute_name = "{$name}[minute]";
		$minute_id = Widget::id($minute_name);
		$minute = "<select name=\"$minute_name\" id=\"$minute_id\">";
		for($i = 0; $i < 60; $i += 5) {
			$selected = (date('i', $time) == $i) ? ' selected="selected"' : '';
			$minute .= sprintf('<option value="%d"%s>%02d</option>', $i, $selected, $i);
		}
		$minute .= '</select>';

		$ampm_name = "{$name}[ampm]";
		$ampm_id = Widget::id($ampm_name);
		$am_selected = (date('a', $time) == 'am') ? ' selected="selected"' : '';
		$pm_selected = (date('a', $time) == 'pm') ? ' selected="selected"' : '';
		$ampm = '<select name="'. $ampm_name .'" id="'. $ampm_id .'">
				<option value="am"'. $am_selected .'>AM</option>
				<option value="pm"'. $pm_selected .'>PM</option>
			</select>';

		return "$hour : $minute $ampm";
	}

	public static function dateTime($name, $time = 0) {
		return self::date($name, $time) .'&nbsp;&nbsp;<strong>at</strong>&nbsp;&nbsp;'. self::time($name, $time);
	}

	public static function ratingScale($name, $value = 0) {
		$html = '<div style="float: left; width: 100%;">';
		for($i = 1; $i <= 5; $i++) {
			$id = Widget::id($name) ."_$i";
			$checked = ($i == $value) ? ' checked="checked"' : '';
			$html .= "<span style=\"text-align: center; float: left; margin-bottom: 1em;\"><input type=\"radio\" name=\"$name\" id=\"$id\" value=\"$i\"$checked /><label for=\"$id\" style=\"float: left; width: 30px; font-weight: bold; margin-left: -2px;\">$i</label></span>";
		}
		$html .= '</div>';

		return $html;
	}


	/**
	 * Returns a radio input
	 *
	 * @param string $label
	 * @param string $name
	 * @param string $form_value
	 * @param bool $value
	 */
	public static function radio($label, $name, $form_value, $value) {
		$id = Widget::id($name) .'_'. $form_value;
		$checked = ($value) ? ' checked="checked"' : '';
		return "<input type=\"radio\" name=\"$name\" id=\"$id\" value=\"$form_value\"$checked /><label for=\"$id\">$label</label><br />";
	}

	/**
	 * Returns a yes/no toggle.
	 *
	 * @param string $label
	 * @param string $name
	 * @param bool  $value
	 * @see label
	 * @see radio
	 * @see noYes
	 */
	public static function yesNo($label, $name, $value = true, $required = false) {
		$false = ($value === false) || ($value === "0");
		return Widget::label($label, "", Widget::isRequired($name, $required)) . Widget::radio('Yes', $name, '1', $value) . Widget::radio('No', $name, '0', $false);
	}

	/**
	 * Returns a yes/no toggle.
	 *
	 * @param string $label
	 * @param string $name
	 * @param bool  $value
	 * @see label
	 * @see radio
	 * @see yesNo
	 */
	public static function noYes($label, $name, $value = false, $required = false) {
		$false = ($value === false) || ($value === "0");
		return Widget::label($label, "", Widget::isRequired($name, $required)) . Widget::radio('No', $name, '0', $false) . Widget::radio('Yes', $name, '1', $value);
	}

	public static function checkbox($label, $name, $value = false) {
		$id = Widget::id($name);
		$checked = ($value) ? ' checked="checked"' : '';
		return "<input type=\"hidden\" name=\"$name\" value=\"0\" /><input type=\"checkbox\" name=\"$name\" id=\"$id\" value=\"1\"$checked /><label for=\"$id\">$label</label><br />";
	}

}

?>
