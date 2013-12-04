<?php

namespace CBC\Data\Validation;

class Constraint {

	/**
	 * Validates email constraint.
	 *
	 * @return callable
	 */
	public static function email() {
		return function ($data) {
			if ($data) {
				return filter_var($data, FILTER_VALIDATE_EMAIL);
			}
		};
	}

	/**
	 * Validates number constraint.
	 *
	 * @return callable
	 */
	public static function numeric() {
		return function ($data) {
			if ($data) {
				return is_numeric($data);
			}
		};
	}

	/**
	 * Validates regex constraint.
	 *
	 * @return callable
	 */
	public static function regex() {
		return function ($data) use($regex) {
			if ($data) {
				return (bool) preg_match($regex, $data);
			}
		};
	}

	/**
	 * Validates required constraint.
	 *
	 * @return callable
	 */
	public static function required() {
		return function ($data) {
			return strlen($data) > 0;
		};
	}
}