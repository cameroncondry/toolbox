<?php

/**
 *    Truncates a string if the string hits the character limit
 *    @param   string   $string  string to truncate
 *    @param   integer  $limit   character limit
 *    @param   string   $pad     padding character
 *    @return  string            truncated string
 */
function truncate_string($string, $limit = 35, $pad = '…') {
	if (strlen($string) > $limit) {
		return substr($string, 0, $limit) . $pad;
	}

	return $string;
}

/**
 *    Returns an array of a string separated by ",", ";", "|", or "/"
 *    @param   string  $string  string to be converted
 *    @return  array            delimeter exploded array
 */
function trim_explode($string) {
	return array_map('trim', preg_split('/,|;|\|/', $string));
}

/**
 *    Returns the filesize in a human readable format
 *    @param   mixed    $bytes      bytes to convert
 *    @param   integer  $precision  expected precision
 *    @return  string               human readable filesize
 */
function human_filesize($bytes, $precision = 2) {
	$sizes = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB'];

	$total = count($sizes);
	$factor = 0;

	while (++$factor < $total && $bytes >= 1024) {
		$bytes /= 1024;
	}

	return sprintf("%.{$precision}f", $bytes) . ' ' . $sizes[$factor - 1];
}

/**
 *    Returns dates in a universal format.
 *    @param   mixed    $date       current timestamp or date string
 *    @param   boolean  $long_date  true for human readable date
 *    @return  string               formatted date
 */
function format_date($date, $long_date = true) {
	$timestamp = strtotime($date) ? strtotime($date) : $date;

	if ($long_date) {
		return $timestamp ? date('jS \o\f F, Y - g:ia', $timestamp) : 'Invalid Date: ' . $date;
	} else {
		return $timestamp ? date('Y-m-d H:i:s', $timestamp) : 'Invalid Date: ' . $date;
	}
}

/**
 *    Merge two arrays, using the first array's keys
 *    @param   array  $defaults  default keys and values
 *    @param   array  $updates   updated values
 *    @return  array             returns modified default array
 */
function update_array($defaults, $updates) {
	$results = [];

	foreach ($defaults as $key => $default) {
		$results[$key] = $default;

		if (isset($updates[$key])) {
			$results[$key] = is_array($updates[$key]) ? update_array($results[$key], $updates[$key]) : trim($updates[$key]);
		}
	}

	return $results;
}

/**
 *    Mimics XDebug's var_dump() altered functionality to better display data.
 *    @param   mixed  $data  data to display
 *    @return  none          displays results
 */
function dump($data) {

	// display headers for each data set
	if (is_object($data) || is_array($data)) {
		$arr_obj = new ArrayObject($data);

		echo '<pre style="display: inline; margin: 0;"><strong>' . gettype($data) . '</strong>';
		echo '(<em>' . (is_object($data) ? get_class($data) : '') . '</em>)';
		echo ' (' . $arr_obj->count() . ')</pre>';

		// format object properties
		if (is_object($data)) {
			$object_data = [];
			$reflection = new ReflectionClass($data);
			$properties = $reflection->getProperties();

			foreach ($properties as $property) {
				$property->setAccessible(true);

				$key  = '"' . $property->getName() . '"';
				$key .= $property->isProtected() ? ':protected' : '';
				$key .= $property->isPrivate() ? ':private' : '';
				$object_data[$key] = $property->getValue($data);
			}

			foreach (get_object_vars($data) as $key => $value) {
				$object_data['"' . $key . '"'] = $value;
			}

			$data = $object_data;
		} else {
			$array_data = [];

			// format array names
			foreach ($data as $key => $value) {
				$array_data['"' . $key . '"'] = $value;
			}

			$data = $array_data;
		}

		// normalize spacing
		$pad = ['key' => 0, 'type' => 0];
		foreach ($data as $key => $value) {
			$pad['key'] = (strlen($key) + 4 > $pad['key']) ? (strlen($key) + 4) : $pad['key'];
			$pad['type'] = (strlen(gettype($value)) + 6 > $pad['type']) ? (strlen(gettype($value)) + 6) : $pad['type'];
		}

		echo '<ul style="list-style: none; margin: 0 0 0 20px; padding: 0;">';
		foreach ($data as $key => $value) {
			if (is_object($value) || is_array($value)) {
				echo '<li style="margin: 4px 0;"><pre style="display: inline; margin: 0;">' . $key . ' </pre>';
				dump($value);
				echo '</li>';
				continue;
			}

			$color = !is_string($value) ? (is_float($value) ? 'f57900' : '4e9a06') : 'c00';
			echo '<li><pre style="margin: 0 0 0 0">' . str_pad($key, $pad['key']);
			echo '<span style="color: #000; font-size: 0.8em">' . str_pad(gettype($value) . ' ' . strlen($value), $pad['type']) . '</span>';
			echo '<span style="color: #' . $color . '"> ' . htmlspecialchars($value) . '</span></pre></li>';

		}
		echo '</ul>';
	} else {
		$color = !is_string($data) ? (is_float($data) ? 'f57900' : '4e9a06') : 'c00';
		echo '<pre>';
		echo '<span style="font-size: 0.8em">' . gettype($data) . '</span>';
		echo '<span style="white-space: pre-wrap; color: #' . $color . '"> ' . htmlspecialchars($data) . '</span></pre>';
	}
}