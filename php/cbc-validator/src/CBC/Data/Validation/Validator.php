<?php

namespace CBC\Data\Validation;

class Validator {

	/**
	 * @var string
	 */
	protected $rule = '';

	/**
	 * @var array
	 */
	protected $rules = [];

	/**
	 * @var string
	 */
	protected $message = 'This field is invalid.';

	/**
	 * @var boolean
	 */
	protected $is_valid = true;

	/**
	 * Validates the constraints on all rules against the request.
	 * 
	 * @param  array $request
	 * @return boolean
	 */
	public function is_valid($request) {
		$this->is_valid = true;

		foreach ($this->rules as $field => $rule) {
			$data = isset($request[$field]) ? $request[$field] : null;
			
			// apply any filters to the data before processing
			foreach ($rule['filters'] as $filter) {
				$data = call_user_func($filter, $data);
			}

			// test each constraint and exit on failed constraint
			foreach ($rule['constraints'] as $constraint) {
				if (false === call_user_func($constraint['test'], $data)) {
					$this->addMessage($field, $constraint['message']);
					break;
				}
			}
		}

		return $this->is_valid;
	}

	/**
	 * Adds a rule to the validator.
	 * 
	 * @param  string $rule
	 * @return Validator
	 */
	public function setRule($rule) {
		$this->rule = $rule;

		if (!isset($this->rules[$rule])) {
			$this->rules[$rule] = [
				'constraints' => [],
				'filters' => [],
				'is_valid' => true
			];
		}

		return $this;
	}

	/**
	 * Adds a constraint to a rule.
	 * 
	 * @param  callable  $constraint
	 * @param  string    $message
	 * @return Validator
	 */
	public function addConstraint($constraint, $message = null) {
		if (!is_callable($constraint)) {
			throw new \Exception('addConstraint must be callable');
		}

		$this->rules[$this->rule]['constraints'][] = [
			'test' => $constraint,
			'message' => $message
		];

		return $this;
	}

	/**
	 * Adds a filter to the rule that is executed before constraints.
	 * 
	 * @param  callable $filter
	 * @return Validator
	 */
	public function addFilter($filter) {
		if (!is_callable($filter)) {
			throw new \Exception('addFilter must be callable');
		}

		$this->rules[$this->rule]['filters'][] = $filter;

		return $this;
	}

	/**
	 * Retrieves the error from a rule.
	 * 
	 * @param  string $rule
	 * @return mixed
	 */
	public function getError($rule) {
		$error = null;

		if ($this->hasError($rule)) {
			$error = $this->rules[$rule]['message'];
		}

		return $error;
	}

	/**
	 * Tests if a rule has an error.
	 * 
	 * @param  string  $rule
	 * @return boolean
	 */
	public function hasError($rule) {
		return isset($this->rules[$rule]) ? !$this->rules[$rule]['is_valid'] : false;
	}

	/**
	 * Sets the default message for errors.
	 * 
	 * @param string $message
	 * @return Validator
	 */
	public function setMessage($message) {
		$this->message = $message;
		return $this;
	}

	/**
	 * Adds a message to a rule.
	 * 
	 * @param string $rule
	 * @param string $message
	 */
	protected function addMessage($rule, $message) {
		$this->is_valid = false;
		$this->rules[$rule]['is_valid'] = false;
		$this->rules[$rule]['message'] = (is_null($message)) ? $this->message : $message;
	}
}