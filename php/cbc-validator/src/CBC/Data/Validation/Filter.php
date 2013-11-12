<?php

namespace CBC\Data\Validation;

class Filter {

	/**
	 * Transforms to lowercase before constraints validate.
	 * 
	 * @return callable
	 */
	public static function lower() {
		return function ($data) {
			if ($data) {
				return strtolower($data);
			}
		};
	}

	/**
	 * Transforms with trim before constraints validate.
	 * 
	 * @return callable
	 */
	public static function trim() {
		return function ($data) {
			if ($data) {
				return trim($data);
			}
		};
	}

	/**
	 * Transforms to uppercase before constraints validate.
	 * 
	 * @return callable
	 */
	public static function upper() {
		return function ($data) {
			if ($data) {
				return strtoupper($data);
			}
		};
	}
}