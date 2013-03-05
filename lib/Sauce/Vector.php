<?php

/**
   Copyright 2012 Brainsware

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

/* `Vector` is a simple value store. It can be used as two-dimensional array or
 * stack, taking any type of values.
 *
 * `Vector` implements two important interfaces: `ArrayAccess` and `Countable`. Those are
 * defined in the PHP standard library:
 *
 * <http://us.php.net/manual/en/class.arrayaccess.php>
 * <http://us.php.net/manual/en/class.countable.php>
 *
 * That means you can use any array functions with an instance of this class,
 * including `count($arr)`.
 *
 * NOTE: The above is true as soon as PHP actually implements it. Some
 *       functions actually don't take objects as parameters (yet).
 *
 * NOTE: Also foreach($obj as $foo) does not work yet. You have to give foreach
 *       $obj->to_array() in order to make it work properly.
 *
 * The elements can be accessed with a numeric index, but only in the
 * boundaries of the stored values. If you try to access or set an index higher
 * or equal to the the current amount of stored values, an exception is thrown.
 */
class Vector implements \ArrayAccess, \Countable, \JsonSerializable
{
	protected $storage;

	/* Creates a new Vector object taking any kind of data.
	 *
	 * Given another Vector object, it will copy all its data.
	 * Given an array, it will copy all its content into the internal storage.
	 *
	 * For now, Vector only accepts actual array data.
	 */
	public function __construct ($data = [])
	{
		$this->storage = [];

		if (is_a($data, 'Sauce\Vector')) {
			$data = $data->to_array();
		}

		foreach ($data as $value) {
			$this->storage []= $value;
		}
	}

	/* Return an actual PHP array.
	 * This method has to be used for foreach loops.
	 */
	public function to_array ()
	{
		return $this->storage;
	}
	/* Alias for #to_array */
	public function getArrayCopy() { return $this->to_array(); }

	/* Slice the array. Takes numeric start and end indices.
	 * If a non-numeric index is passed an exception is thrown. */
	public function slice ($start, $end)
	{
		if (!is_numeric($start)) {
			throw new \OutOfBoundsException("Invalid start index {$index}");
		}

		if (!is_numeric($end)) {
			throw new \OutOfBoundsException("Invalid end index {$index}");
		}

		return array_slice($this->storage, $start, ($end - $start));
	}

	/* Join the strval() of each element of the array with a specified delimiter.
	 *
	 * Example:
	 *
	 * 	$a = V([ 'A', 'B', 'C' ]);
	 * 	$a->join(', ');
	 * 	# => "A, B, C"
	 */
	public function join ($delimiter)
	{
		$strings = $this->map(function ($v) {
			return strval($v);
		});

		return join($delimiter, $strings->to_array());
	}

	/* Iterate over all elements given a callback and return the results of
	 * the callback's return values as new Vector.
	 *
	 * Example:
	 *
	 * 	$a = V([ 1, 2, 3 ]);
	 * 	$a->map(function($i) { return $i + 1; });
	 * 	# => [ 2, 3, 4 ] (Vector)
	 */
	public function map ($callback)
	{
		$result = new self();

		for ($i = 0; $i < $this->count(); $i++) {
			$value = $callback($this->storage[$i]);

			if ($value) {
				$result->push($value);
			}
		}

		return $result;
	}

	/* Iterate over all elements given a callback and only return the elements
	 * where the callback returns boolean *true*.
	 *
	 * Example:
	 *
	 * 	$a = V([ 1, 2, 3 ]);
	 * 	$a->select(function ($i) { return $i > 2; });
	 * 	# => [ 3 ] (Vector)
	 */
	public function select ($callback)
	{
		return $this->map(function ($v) use ($callback) {
			if ($callback($v)) {
				return $v;
			}
		});
	}

	/* Iterate over all elements given a callback and only return the elements
	 * where the callback returns boolean *false*.
	 *
	 * Example:
	 *
	 * 	$a = V([ 1, 2, 3 ]);
	 * 	$a->exclude(function ($i) { return $i > 2; });
	 * 	# => [ 1, 2 ] (Vector)
	 */
	public function exclude ($callback)
	{
		return $this->map(function ($v) use ($callback) {
			if (!$callback($v)) {
				return $v;
			}
		});
	}

	/* Takes any type of data and pushes it onto the vector. Given an array,
	 * (PHP built-in or Vector), it will push its contents. Any object or
	 * Object instance is pushed as-is.
	 */
	public function push ($value)
	{
		if (is_an_array($value) && !is_a($value, '\Sauce\Object')) {
			foreach ($value as $v) {
				$this->push($v);
			}

			return;
		}

		$this->storage []= $value;
	}

	public function pop ()
	{
		return array_pop($this->storage);
	}

	public function shift ()
	{
		return array_shift($this->storage);
	}

	/* Takes any value and compares it to the stored values. Returns true if it is found, false otherwise.
	 *
	 */
	public function includes ($value)
	{
		for ($i = 0; $i < $this->count(); $i++) {
			if ($value === $this->storage[$i]) {
				return true;
			}
		}

		return false;
	}

	public function offsetGet ($index)
	{
		if (!is_numeric($index)) {
			throw new \OutOfBoundsException('You are trying to access a non-numeric index.');
		}
		
		if ($index >= $this->count()) {
			throw new \OutOfBoundsException("Invalid index {$index}");
		}

		return $this->storage[$index];
	}

	public function offsetSet ($index, $value)
	{
		if (!is_numeric($index)) {
			throw new \OutOfBoundsException('Out of bounds: you are trying to set a non-numeric index.');
		}

		if ($index > $this->count()) {
			throw new \OutOfBoundsException("Invalid index {$index}");
		}

		$this->storage[$index] = $value;
	}

	public function offsetUnset ($index)
	{
		if ($index == 0) {
			return array_shift($this->storage);
		}

		if ($index == $this->count() - 1) {
			return array_pop($this->storage);
		}

		$left = array_slice($this->storage, 0, $index);

		for ($i = $index; $i++; ($i + 1) < $this->count()) {
			$left []= $this->storage[$i + 1];
		}

		$this->storage = $left;
	}

	public function offsetExists ($index)
	{
		return is_numeric($index) && $index < $this->count();
	}

	public function count ()
	{
		return count($this->storage);
	}

	public function is_empty ()
	{
		return empty($this->storage);
	}

	public function jsonSerialize ()
	{
		return $this->storage;
	}
}

?>
