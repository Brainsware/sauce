<?php

/**
   Copyright 2012-2013 Brainsware

   Licensed under the Apache License, Version 2.0 (the "License");
   you may not use this file except in compliance with the License.
   You may obtain a copy of the License at

       http://www.apache.org/licenses/LICENSE-2.0

   Unless required by applicable law or agreed to in writing, software
   distributed under the License is distributed on an "AS IS" BASIS,
   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
   See the License for the specific language governing permissions and
   limitations under the License.

*/

namespace Sauce;

/* The String class is an extensive wrapper for PHP's most common string
 * functions.
 *
 * The main motivation is to provide methods with readable names, consistent
 * parameter order and predictable outcome. Additionally this class also
 * interfaces with the Vector class to integrate smoothly with the rest of
 * Sauce.
 *
 * TODO: Examples
 */
class String
{
	// Internal storage
	private $string = '';

	/* Constructor; if given argument is not a string or a String instance, an
	 * InvalidArgumentException is thrown.
	 *
	 * Given a String instance, a copy is made.
	 */
	public function __construct ($string = '')
	{
		if (!is_a_string($string)) {
			throw new \InvalidArgumentException('Argument is not a string (' . gettype($string) . ')');
		}

		if ($string instanceof \Sauce\String) {
			$string = $string->to_s();
		}

		$this->string = $string;
	}

	/* Checks whether the stored string starts with given string.
	 *
	 * If given argument is not a string or a String instance, an
	 * InvalidArgumentException is thrown.
	 *
	 * Returns boolean.
	 *
	 * TODO: Examples
	 */
	public function starts_with ($needle)
	{
		if (!is_a_string($needle)) {
			throw new \InvalidArgumentException('Argument is not a string');
		}

		if ($needle instanceof \Sauce\String) {
			$needle = $needle->to_s();
		}

		return 0 === strcmp($this->slice(0, strlen($needle))->to_s(), $needle);
	}

	/* Checks whether the stored string ends with given string.
	 *
	 * If given argument is not a string or a String instance, an
	 * InvalidArgumentException is thrown.
	 *
	 * Returns boolean.
	 * 
	 * TODO: Examples
	 */
	public function ends_with ($needle)
	{
		if (!is_a_string($needle)) {
			throw new \InvalidArgumentException('Argument is not a string');
		}

		if ($needle instanceof \Sauce\String) {
			$needle = $needle->to_s();
		}

		$len = strlen($needle);

		return 0 === strcmp($this->slice(-$len, $len)->to_s(), $needle);
	}

	/* Checks whether the stored string includes given string.
	 *
	 * If given argument is not a string or a String instance, an
	 * InvalidArgumentException is thrown.
	 *
	 * Returns boolean.
	 *
	 * TODO: Examples
	 */
	public function includes ($needle)
	{
		if (!is_a_string($needle)) {
			throw new \InvalidArgumentException('Argument is not a string');
		}

		return false !== strpos($this->string, $needle);
	}
	
	/* Returns whether given string (or String instance) is exactly the same
	 * string.
	 *
	 * If given argument is not a string or String instance, an
	 * InvalidArgumentException is thrown.
	 *
	 * Returns boolean.
	 *
	 * TODO: Examples
	 */
	public function equals ($other)
	{
		if (!is_a_string($other)) {
			throw new \InvalidArgumentException('Argument is not a string');
		}

		if ($other instanceof self) {
			return $other->to_s() === $this->string;
		}

		return $other === $this->string;
	}

	/* Returns a slice of the stored string as new String instance.
	 * 
	 * The start and end parameters are passed to the PHP function #substr
	 * directly after they are both verified to be numeric.
	 * 
	 * If either of the arguments is not numeric, an InvalidArgumentException
	 * is thrown.
	 *
	 * Returns a new String instance with the sliced string.
	 *
	 * TODO: Examples
	 */
	public function slice ($start, $end)
	{
		if (!is_numeric($start)) {
			$start = var_export($start, true);

			throw new \InvalidArgumentException("Invalid start index {$start}");
		}

		if (!is_numeric($end)) {
			$end = var_export($end, true);

			throw new \InvalidArgumentException("Invalid end index {$end}");
		}

		return new String(substr($this->string, $start, $end));
	}

	/* Slices the stored string.
	 *
	 * The start and end parameters are passed to the PHP function #substr
	 * directly after they are both verified to be numeric.
	 * 
	 * If either of the arguments is not numeric, an InvalidArgumentException
	 * is thrown.
	 *
	 * Returns this instance.
	 *
	 * TODO: Examples
	 */
	public function sliceF ($start, $end)
	{
		if (!is_numeric($start)) {
			throw new \InvalidArgumentException("Invalid start index {$start}");
		}

		if (!is_numeric($end)) {
			throw new \InvalidArgumentException("Invalid end index {$end}");
		}

		$this->string = substr($this->string, $start, $end);

		return $this;
	}

	/* Appends given string to the stored string and returns the result as
	 * new String instance.
	 *
	 * If given argument is not a string or a String instance, an
	 * InvalidArgumentException is thrown.
	 *
	 * Returns a new String instance with the full string.
	 *
	 * TODO: Examples
	 */
	public function append ($string)
	{
		if (!is_a_string($needle)) {
			throw new \InvalidArgumentException('Argument is not a string');
		}

		return new String($this->string . $string);
	}

	/* Appends given string to the stored string.
	 *
	 * If given argument is not a string or a String instance, an
	 * InvalidArgumentException is thrown.
	 *
	 * Returns this instance.
	 *
	 * TODO: Examples
	 */
	public function appendF ($string)
	{
		$this->string .= $string;

		return $this;
	}

	/* Prepends given string to the stored string and returns the result as
	 * new String instance.
	 *
	 * If given argument is not a string or a String instance, an
	 * InvalidArgumentException is thrown.
	 *
	 * Returns a new String instance with the full string.
	 *
	 * TODO: Examples
	 */
	public function prepend ($string)
	{
		return new String($string . $this->string);
	}

	/* Prepends given string to the stored string.
	 *
	 * If given argument is not a string or a String instance, an
	 * InvalidArgumentException is thrown.
	 *
	 * Returns this instance.
	 *
	 * TODO: Examples
	 */
	public function prependF ($string)
	{
		$this->string = $string . $this->string;

		return $this;
	}

	/* Trims whitespaces from the beginning and end of the stored string and
	 * returns the result as new String instance.
	 *
	 * Optionally, a string containing all characters (instead of whitespaces)
	 * to strip away can be passed. If given argument is not a string or a
	 * String instance, an InvalidArgumentException is thrown.
	 *
	 * Returns a new String instance with the trimmed string.
	 * 
	 * TODO: Examples
	 */
	public function trim ($characters = null)
	{
		if (!is_a_string($characters)) {
			throw new \InvalidArgumentException('Argument is not a string');
		}

		$trimmed = '';

		if (null !== $characters) {
			$trimmed = trim($this->string, $characters);
		} else {
			$trimmed = trim($this->string);
		}

		return new String($trimmed);
	}

	/* Trims whitespaces from the beginning and end of the stored string.
	 *
	 * Optionally, a string containing all characters (instead of whitespaces)
	 * to strip away can be passed. If given argument is not a string or a
	 * String instance, an InvalidArgumentException is thrown.
	 *
	 * Returns this instance.
	 * 
	 * TODO: Examples
	 */
	public function trimF ($characters = null)
	{
		if (!is_a_string($characters)) {
			throw new \InvalidArgumentException('Argument is not a string');
		}

		if (null !== $characters) {
			$this->string = trim($this->string, $characters);
		} else {
			$this->string = trim($this->string);
		}

		return $this;
	}

	/* Splits the stored string by given string and returns the result as new
	 * Vector instance.
	 *
	 * If given argument is not a string or a String instance, an
	 * InvalidArgumentException is thrown.
	 *
	 * Returns a new Vector instance.
	 *
	 * TODO: Examples
	 */
	public function split ($by = ' ')
	{
		if (!is_a_string($by)) {
			throw new \InvalidArgumentException('Argument is not a string');
		}

		return V(explode($by, $this->string));
	}

	/* Splits the stored string by any newline characters and returns the
	 * result as new Vector instance.
	 *
	 * Returns a new Vector instance.
	 *
	 * TODO: Examples
	 */
	public function to_lines ()
	{
		return V(preg_split('/$\R?^/m', $this->string));
	}

	/* Returns the stored string as string. */
	public function to_s ()
	{
		return $this->string;
	}
}

?>
