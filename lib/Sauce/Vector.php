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
class Vector implements \ArrayAccess, \Countable, \JsonSerializable, \Iterator
{
	protected $storage;
	protected $current_index = 0;

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

		if ($data instanceof \Sauce\SObject) $data = [ $data ];
		if (empty($data))                   return;

		if (!is_an_array($data)) {
			$this->storage []= $data;

			return;
		}

		foreach ($data as $value) {
			$this->storage []= $value;
		}
	}

	/* Iterator methods */
	public function current ()
	{
		if (!$this->valid()) return null;

		return $this->storage[$this->current_index];
	}

	public function key ()
	{
		return $this->current_index;
	}

	public function next ()
	{
		if ($this->valid()) {
			$this->current_index++;
		}
	}

	public function rewind ()
	{
		$this->current_index = 0;
	}

	public function valid ()
	{
		return $this->current_index < $this->count();
	}


	/* Return an actual PHP array.
	 *
	 * NOTE: This method has to be used for foreach loops.
	 */
	public function to_array ()
	{
		return $this->storage;
	}
	/* Alias for #to_array to implement the ArrayAccess */
	public function getArrayCopy() { return $this->to_array(); }

	/* Slice the array. Takes numeric start and end indices.
	 * If a non-numeric index is passed an exception is thrown. */
	public function slice ($start, $end)
	{
		if (!is_numeric($start)) {
			$start = var_export($start, true);

			throw new \OutOfBoundsException("Invalid start index {$start}");
		}

		if (!is_numeric($end)) {
			$end = var_export($end, true);

			throw new \OutOfBoundsException("Invalid end index {$end}");
		}

		return V(array_slice($this->storage, $start, ($end - $start)));
	}

	/* Join the strval() of each element of the array with a specified delimiter.
	 *
	 * Example:
	 *
	 * 	$a = V([ 'A', 'B', 'C' ]);
	 * 	$a->join(', ');
	 * 	# => "A, B, C"
	 */
	public function join ($delimiter = ' ')
	{
		if (!is_a_string($delimiter)) {
			$delimiter = var_export($delimiter, true);

			throw new \InvalidArgumentException("Invalid delimiter given: {$delimiter}");
		}

		$strings = $this->map(function ($v) use ($delimiter) {
			if (is_array($v)) {
				// return implode($delimiter, $v);
			}
			return strval($v);
		});

		return S(join($delimiter, $strings->to_array()));
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
		if(!is_callable($callback)) {
			throw new \InvalidArgumentException('Invalid (not callable) callback given');
		}

		$result = new self();

		for ($i = 0; $i < $this->count(); $i++) {
			$value = $callback($this->storage[$i]);

			if (null !== $value) {
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
		if(!is_callable($callback)) {
			throw new \InvalidArgumentException('Invalid (not callable) callback given');
		}

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
		if(!is_callable($callback)) {
			throw new \InvalidArgumentException('Invalid (not callable) callback given');
		}

		return $this->map(function ($v) use ($callback) {
			if (!$callback($v)) {
				return $v;
			}
		});
	}

	/* Takes any type of data and pushes it onto the vector.
	 *
	 * Given an array, (PHP built-in or Vector), it will push its contents.
	 * Any object or Object instance is pushed as-is.
	 */
	public function push ($value)
	{
		if (is_an_array($value) && !($value instanceof \Sauce\SObject)) {
			foreach ($value as $v) {
				$this->push($v);
			}

			return null;
		}

		$this->storage []= $value;

		return null;
	}

	/* Removes the very last element and returns it. */
	public function pop ()
	{
		if ($this->count() > 0) {
			return array_pop($this->storage);
		}

		return null;
	}

	/* Removes the very first element and returns it. */
	public function shift ()
	{
		if ($this->count() > 0) {
			return $this->offsetUnset(0);
		}

		return null;
	}

	/* Prepend given value(s) at the beginning. */
	public function unshift ($values = [])
	{
		if (!is_an_array($values) || $values instanceof \Sauce\SObject) {
			$values = [ $values ];
		}

		$values = V($values);
		$storage = $this->storage;

		for ($i = ($values->count() - 1); $i >= 0; $i--) {
			array_unshift($storage, $values[$i]);
		}

		$this->storage = $storage;

		return null;
	}
	/* Alias for #unshift */
	public function prepend ($values = []) { return $this->unshift($values); }

	/* Takes any value and compares it to the stored values. Returns true if it
	 * is found, false otherwise.
	 *
	 * NOTE: The comparison is made with '===', so the type does matter here.
	 *       If a less restrictive comparison is needed, use #map, #select or
	 *       #exclude.
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

	/* Implementation of ArrayAccess#offsetGet; returns the value at a given
	 * numeric index.
	 *
	 * NOTE: If given any non-numeric index, an OutOfBounds exception is thrown.
	 *
	 * NOTE: If given an index greater or equal to the number of elements, an
	 *       OutOfBoundsException is thrown.
	 */
	public function offsetGet ($index)
	{
		if (!is_numeric($index)) {
			throw new \OutOfBoundsException('You are trying to access a non-numeric index.');
		}

		if ($index >= $this->count() || $index < 0) {
			throw new \OutOfBoundsException("Invalid index {$index}");
		}

		return $this->storage[$index];
	}

	/* Implementation of ArrayAccess#offsetSet; sets a value at a given
	 * numeric index.
	 *
	 * NOTE: If given any non-numeric index, an OutOfBoundsException is thrown.
	 *
	 * NOTE: If given any index greater than the number of elements, an
	 *       OutOfBoundsException is thrown.
	 */
	public function offsetSet ($index, $value)
	{
		if (!is_numeric($index)) {
			throw new \OutOfBoundsException('Out of bounds: you are trying to set a non-numeric index.');
		}

		if ($index > $this->count() || $index < 0) {
			throw new \OutOfBoundsException("Invalid index {$index}");
		}

		$this->storage[$index] = $value;

		return null;
	}

	/* Remove an element at a given numeric index and return it.
	 *
	 * NOTE: If given any non-numeric index, an OutOfBoundsException is thrown.
	 *
	 * NOTE: If given any index greater than the number of elements, an
	 *       OutOfBoundsException is thrown.
	 */
	public function offsetUnset ($index)
	{
		$value = $this->offsetGet($index);

		if ($index == 0) {
			return array_shift($this->storage);
		}

		if ($index == $this->count() - 1) {
			return array_pop($this->storage);
		}

		$new = $this->slice(0, $index - 1);
		$new->push($this->slice($index, $this->count()));

		$this->storage = $new->to_array();

		return $value;
	}

	/* Returns whether a given numeric index exists.
	 *
	 * NOTE: This method DOES NOT throw any exceptions.
	 */
	public function offsetExists ($index)
	{
		return is_numeric($index) && $index < $this->count() && $index >= 0;
	}

	/* Returns the number of elements stored. */
	public function count ()
	{
		return count($this->storage);
	}

	/* Returns whether or not the Vector is empty. */
	public function is_empty ()
	{
		return empty($this->storage);
	}

	/* Implementation of JsonSerializable; returns the internal storage - a PHP
	 * built-in array.
	 */
	public function jsonSerialize ()
	{
		return $this->storage;
	}
}

?>
