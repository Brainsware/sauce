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
		ensure('Argument', $string, is_a_string, __CLASS__, __METHOD__);

		if ($string instanceof self) {
			$string = $string->to_s();
		}

		$this->string = $string;
	}

	/* Actually act like a string */
	public function __toString ()
	{
		return $this->string;
	}

	/* Returns the length of the stored string */
	public function length ()
	{
		return strlen($this->string);
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
		ensure('Argument', $needle, is_a_string, __CLASS__, __METHOD__);

		if ($needle instanceof self) {
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
		ensure('Argument', $needle, is_a_string, __CLASS__, __METHOD__);

		if ($needle instanceof self) {
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
		ensure('Argument', $needle, is_a_string, __CLASS__, __METHOD__);

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
		ensure('Argument', $other, is_a_string, __CLASS__, __METHOD__);

		if ($other instanceof self) {
			return $other->to_s() === $this->string;
		}

		return $other === $this->string;
	}

	/* Returns a new String instance replacing the search string with the
	 * replacement string.
	 *
	 * If either of the arguments is not a string or an instance of String,
	 * an InvalidArgumentException is thrown.
	 */
	function replace ($search, $replace)
	{
		ensure('Search argument',  $search,  is_a_string, __CLASS__, __METHOD__);
		ensure('Replace argument', $replace, is_a_string, __CLASS__, __METHOD__);

		$result = str_replace($search, $replace, $this);

		return S($result);
	}

	/* Returns a new string instance replacing the search string with the
	 * replacement string but case insentively.
	 *
	 * If either of the arguments is not a string or an instance of String,
	 * an InvalidArgumentException is thrown.
	 */
	function ireplace ($search, $replace)
	{
		ensure('Search argument',  $search,  is_a_string, __CLASS__, __METHOD__);
		ensure('Replace argument', $replace, is_a_string, __CLASS__, __METHOD__);

		$result = str_ireplace($search, $replace, $this);

		return S($result);
	}

	/* Replaces the search string with the replacement and stores the result.
	 *
	 * If either of the arguments is not a string or an instance of String,
	 * an InvalidArgumentException is thrown.
	 */
	function replaceF ($search, $replace)
	{
		ensure('Search argument',  $search,  is_a_string, __CLASS__, __METHOD__);
		ensure('Replace argument', $replace, is_a_string, __CLASS__, __METHOD__);

		$this->string = str_replace($search, $replace, $this);

		return $this;
	}

	/* Replaces the search string with the replacement case insensitively and
	 * stores the result.
	 *
	 * If either of the arguments is not a string or an instance of String,
	 * an InvalidArgumentException is thrown.
	 */
	function ireplaceF ($search, $replace)
	{
		ensure('Search argument',  $search,  is_a_string, __CLASS__, __METHOD__);
		ensure('Replace argument', $replace, is_a_string, __CLASS__, __METHOD__);

		$this->string = str_ireplace($search, $replace, $this);

		return $this;
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
		ensure('Start', $start, is_numeric, __CLASS__, __METHOD__);
		ensure('End',   $end,   is_numeric, __CLASS__, __METHOD__);

		return new self(substr($this->string, $start, $end));
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
		ensure('Start', $start, is_numeric, __CLASS__, __METHOD__);
		ensure('End',   $end,   is_numeric, __CLASS__, __METHOD__);

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
		ensure('Argument', $string, is_a_string, __CLASS__, __METHOD__);

		if ($string instanceof self) {
			$string = $string->to_s();
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
		ensure('Argument', $string, is_a_string, __CLASS__, __METHOD__);

		if ($string instanceof self) {
			$string = $string->to_s();
		}

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
		ensure('Argument', $string, is_a_string, __CLASS__, __METHOD__);

		if ($string instanceof self) {
			$string = $string->to_s();
		}

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
		ensure('Argument', $string, is_a_string, __CLASS__, __METHOD__);

		if ($string instanceof self) {
			$string = $string->to_s();
		}

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
		if (is_not_null($characters)) {
			ensure('Argument', $characters, is_a_string, __CLASS__, __METHOD__);
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
		if (is_not_null($characters)) {
			ensure('Argument', $characters, is_a_string, __CLASS__, __METHOD__);
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
		ensure('Argument', $by, is_a_string, __CLASS__, __METHOD__);

		return V(explode($by, $this->string));
	}

	/* Splits the stored string by newline characters and returns the
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
