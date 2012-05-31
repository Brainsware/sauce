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

/**
 * ## Sauce Objects
 *
 * `Object` is a class full of *magic*.
 *
 * For one, it doesn't only just provide standalone objects which you can
 * throw data at in almost any way, you can also access this data in any way.
 *
 * `Object` implements two interfaces: `ArrayAccess` and `Countable`. Those are
 * defined in the PHP standard library:
 *
 * <http://us.php.net/manual/en/class.arrayaccess.php>
 * <http://us.php.net/manual/en/class.countable.php>
 *
 * That means you can use any array functions with an instance of this class,
 * including `count($arr)`.
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
 * Additionally, `Object` implements the abstract class `CallableProperty`. It gives
 * you the power to add a closure/anonymous function as property of an instance
 * and call it immediately. Without this base class, you would have to store
 * the function in a seperate variable or use call_user_func. `CallableProperty`
 * also binds the function to the `Object` instance.
 *
 */
class Object extends CallableProperty implements \ArrayAccess, \Countable
{
	protected $storage;

	public function __construct ($data = array(), $recursive = false)
	{
		$this->storage = array();

		if (!is_an_array($data)) {
			$this->storage[0] = $data;
			return;
		}
		
		if (is_a($data, '\Sauce\Object')) {
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

	public function offsetExists ($key)
	{
		return array_key_exists($key, $this->storage);
	}
	public function __isset ($key) { return $this->offsetExists($key); }
	public function has_key ($key) { return $this->offsetExists($key); }

	public function offsetGet ($key)
	{
		if ($this->offsetExists($key)) {
			return $this->storage[$key];
		}

		return null;
	}
	public function __get ($key) { return $this->offsetGet($key); }

	public function offsetSet ($key, $value)
	{
		$this->storage[$key] = $value;
	}
	public function __set ($key, $value) { return $this->offsetSet($key, $value); }

	public function offsetUnset ($key)
	{
		if ($this->offsetExists($key)) {
			unset($this->storage[$key]);
		}
	}
	public function __unset ($key) { return $this->offsetUnset($key); }

	public function count ()
	{
		return count($this->storage);
	}

	public function is_empty ()
	{
		return empty($this->storage);
	}

	public function merge ()
	{
		$args = func_get_args();

		foreach ($args as $arg) {
			if (is_an_array($arg)) {
				foreach ($arg as $key => $value) {
					$key = strtolower($key);

					if (!$this->offsetExists($key)) {
						$this->offsetSet($key, $value);
					}
				}
			} else {
				$this->storage []= $arg;
			}
		}
	}

	public function mergeF ()
	{
		$args = func_get_args();

		foreach ($args as $arg) {
			if (is_an_array($arg)) {
				foreach ($arg as $key => $value) {
					$this->offsetSet(strtolower($key), $value);
				}
			} else {
				$this->storage []= $arg;
			}
		}
	}
	
	public function getArrayCopy ()
	{
		return $this->storage;
	}
}

?>
