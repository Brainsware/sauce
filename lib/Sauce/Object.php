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

/**
 * ## Bacon Objects
 *
 * `Object` is a class full of *magic*.
 *
 * For one, it doesn't only just provide standalone objects which you can
 * throw data at in almost any way, you can also access this data in any way.
 *
 * `Object` implements two interfaces: `ArrayAccess`, `Countable` and `JsonSerializable`. Those are
 * defined in the PHP standard library:
 *
 * <http://us.php.net/manual/en/class.arrayaccess.php>
 * <http://us.php.net/manual/en/class.countable.php>
 * <http://us.php.net/manual/en/jsonserializable.jsonserialize.php>
 *
 * That means you can use any array functions with an instance of this class,
 * including `count($arr)`.
 *
 * NOTE: The above is true as soon as PHP actually implements it. Some
 *       functions actually don't take objects as parameters (yet).
 *
 * As any other objects, properties can be set arbitrarily. The key difference
 * here is: you can also access them using the index operator.
 *
 * Example:
 *
 * 		> $a = new Object();
 * 		> $a->foo = 'foo';
 * 		> dump($a['foo']);
 * 		# => string(3) "foo"
 *
 * 	Of course this works the other way round, too:
 *
 * 		> $a['bar'] = 'bar'
 * 		> dump($a->bar);
 * 		# => string(3) "bar"
 *
 * 	> **Note:** all keys are automatically converted to lowercase.
 *
 * Additionally, `Object` uses the trait `CallableProperty`. It gives you the
 * power to add a closure/anonymous function as property of an instance and
 * call it immediately. Without this trait, you would have to store the
 * function in a seperate variable or use call_user_func. `CallableProperty`
 * also binds the function to the `Object` instance.
 */
class Object implements \ArrayAccess, \Countable, \JsonSerializable
{
	use CallableProperty;

	protected $storage;

	/* Creates a new Object instance, taking any kind of data.
	 *
	 * Additionally, the second parameter $recursive can be used to transform
	 * nested PHP built-in arrays into nested Object instances.
	 */
	public function __construct ($data = [], $recursive = false)
	{
		$this->storage = [];

		if (!is_an_array($data)) {
			$this->storage[0] = $data;
			return;
		}
		
		if ($data instanceof self) {
		    $data = $data->storage;
		}

		foreach ($data as $key => $value) {
			if (is_numeric($key)) {
				$key = strval($key);
			}

			if (is_string($key)) {
				$key = strtolower($key);
			}

			if ($recursive && is_an_array($value)) {
				$this->storage[$key] = new Object($value, true);
			} else {
				$this->storage[$key] = $value;
			}
		}
	}

	/* Return whether given key exists. */
	public function offsetExists ($key)
	{
		return array_key_exists($key, $this->storage);
	}

	/* Magic method to mimic array index access, also an alias for
	 * #offsetExists.
	 */
	public function __isset ($key) { return $this->offsetExists($key); }

	/* Alias for #offsetExists */
	public function has_key ($key) { return $this->offsetExists($key); }

	/* Implementation of ArrayAccess#offsetGet; returns the value of a given
	 * key.
	 */
	public function offsetGet ($key)
	{
		if ($this->offsetExists($key)) {
			return $this->storage[$key];
		}

		return null;
	}

	/* Magic method to mimic array index access, also an alias for #offsetGet */
	public function __get ($key) { return $this->offsetGet($key); }

	
	/* Implementation of ArrayAccess#offsetSet; sets a value for a given key. */
	public function offsetSet ($key, $value)
	{
		$this->storage[$key] = $value;
	}

	/* Magic method to mimic left-hand-side index access, also an alias for
	 * #offsetSet
	 */
	public function __set ($key, $value) { return $this->offsetSet($key, $value); }
	
	/* Remove a given key and its associated value. */
	public function offsetUnset ($key)
	{
		if ($this->offsetExists($key)) {
			unset($this->storage[$key]);
		}
	}

	/* Magic method to mimic array index access, also an alias for #offsetUnset */
	public function __unset ($key) { return $this->offsetUnset($key); }

	/* Returns the number of stored key-value pairs. */
	public function count ()
	{
		return count($this->storage);
	}

	/* Returns whether this object holds any data. */
	public function is_empty ()
	{
		return empty($this->storage);
	}

	/* Takes a function, iterates over all keys, calling the given function on
	 * each item. The function may return false or true to indicate whether to
	 * include the given key in the result. Returns a \Sauce\Vector object.
	 *
	 * If no function is given, all values are returned.
	 */
	public function keys ($fn = null)
	{
		if ($fn === null) {
			$fn = function ($key) { return true; };
		}

		$keys = V([]);

		foreach($this->storage as $key => $value) {
			if ($fn($key)) {
				$keys->push($key);
			}
		}

		return $keys;
	}

	/* Takes a function, iterates over all values, calling the given function on
	 * each item. The function may return false or true to indicate whether to
	 * include the given value in the result. Returns a \Sauce\Vector object.
	 *
	 * If no function is given, all values are returned.
	 */
	public function values ($fn = null)
	{
		if ($fn === null) {
			$fn = function ($key, $value) { return true; };
		}

		$values = V([]);

		foreach($this->storage as $key => $value) {
			if ($fn($key, $value)) {
				$values->push($value);
			}
		}

		return $values;
	}

	/* Takes a function, iterates over all items, calling the given function on
	 * each item. The individual results are collected and returned in a
	 * \Sauce\Vector object.
	 *
	 * If no function is given, it returns all values in a \Sauce\Vector object.
	 */
	public function collect ($fn = null)
	{
		if ($fn === null) {
			$fn = function ($key, $value) { return $value; };
		}

		$values = V([]);

		foreach($this->storage as $key => $value) {
			$values->push($fn($value));
		}

		return $values;
	}

	/* Takes a function or an array of keys
	 *
	 * Returns all key-value pairs the function returns true for or where the
	 * keys are in the given array.
	 */
	public function select ($fn)
	{
		if (!is_callable($fn) && is_an_array($fn)) {
			$keys = V($fn);

			$fn = function ($key, $value) use ($keys) {
				return $keys->includes($key);
			};
		}

		$result = new self();

		foreach ($this->storage as $key => $value) {
			if ($fn($key, $value)) {
				$result[$key] = $value;
			}
		}

		return $result;
	}

	/**
	 * TODO: document parameters
	 */
	public function merge ()
	{
		$args = func_get_args();
		$a    = new Object();

		// TODO: fix for actual Object

		foreach ($args as $arg) {
			if (is_an_array($arg)) {
				if ($arg instanceof self) {
					$arg = $arg->to_array();
				}

				foreach ($arg as $key => $value) {
					$key = strtolower($key);

					if (!$a->offsetExists($key)) {
						$a->offsetSet($key, $value);
					}
				}
			} else {
				$a->storage []= $arg;
			}
		}

		return $a;
	}

	/**
	 * TODO: document parameters
	 */
	public function mergeF ()
	{
		$args = func_get_args();

		foreach ($args as $arg) {
			if (is_an_array($arg)) {
				if ($arg instanceof self) {
					$arg = $arg->to_array();
				}

				foreach ($arg as $key => $value) {
					$this->offsetSet(strtolower($key), $value);
				}
			} else {
				$this->storage []= $arg;
			}
		}

		return $this;
	}
	
	/* Return an actual PHP array.
	 *
	 * NOTE: This method has to be used for foreach loops.
	 */
	public function to_array()
	{
		return $this->storage;
	}

	/* Alias for #to_array to implement the ArrayAccess */
	public function getArrayCopy () { return $this->to_array(); }

	/* Implementation of JsonSerializable; returns the internal storage - a PHP
	 * built-in array.
	 */
	public function jsonSerialize ()
	{
		return $this->storage;
	}
}

?>
