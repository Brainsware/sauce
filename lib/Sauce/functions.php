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

/* This file is a collection of rather specialized functions that are useful
 * enough to be available everywhere. They try not to infer with any standard
 * library or basic extension functions.
 */

/* Shortcut for creating a new Vector object.
 *
 * Example:
 * 	V([]) 
 *  # is the same as calling
 *  new \Sauce\Vector([])
 */
function V()
{
	$data = func_get_args();

	if (count($data) == 1) {
		$data = $data[0];
	}

	return new \Sauce\Vector($data);
}

/* ## A(...)
 *
 * `A` takes all given arguments, no matter what type they are, and stuffs them
 * into a new `Sauce\Object`.
 *
 * This is basically a shortcut function for `new \Sauce\Object(array $args)`.
 *
 * Examples:
 *
 *     > A(1, 2, 3, 4);
 *     # => object(Sauce\Object)#1 (4) {
 *              ["0"]=> int(1)
 *              ["1"]=> int(2)
 *              ["2"]=> int(3)
 *              ["3"]=> int(4)
 *     }
 *
 *     > A(1, array('Hello!' => 'World?'), 'foo')
 *     # => object(Sauce\Object)#1 (3) {
 *           ["0"]=> int(1)
 *           ["1"]=> array(1) {
 *               ["Hello!"]=> string(6) "World?"
 *           }
 *           ["2"]=> string(3) "foo"
 *     }
 *
 *     > A(array(1 => 2, 2 => 3));
 *     # => object(Sauce\Object)#1 (2) {
 *           ["1"]=> int(2)
 *           ["3"]=> int(4)
 *     }
 */
function A ()
{
	$data = func_get_args();

	if (count($data) == 1) {
		$data = $data[0];
	}

	return new \Sauce\Object($data);
}

/* ## Ar(...)
 *
 * `Ar` works exactly the same as `A` but lets the `Sauce\Object` constructor
 * build its data structure in a recursive way. This means every nested array
 * is converted to a `Sauce\Object`.
 *
 * Examples:
 *
 *     > Ar(1, array('Hello' => 'world!'), 'foo');
 *     # => object(Sauce\Object)#2 (3) {
 *         ["0"]=> int(1)
 *         ["1"]=> object(Sauce\Object)#1 (1) {
 *             ["Hello"]=> string(6) "world!"
 *         }
 *         ["2"]=> string(3) "foo"
 *     }
 */
function Ar ()
{
	$data = func_get_args();

	if (count($data) == 1) {
		$data = $data[0];
	}

	return new \Sauce\Object($data, true);
}

/* S creates a \Sauce\String instance, appends all given arguments and returns
 * the result.
 *
 * TODO: Examples
 */
function S ()
{
	$data = func_get_args();
	$string = '';

	if (count($data) > 0) {
		$string = $data[0];
	}

	$object = new \Sauce\String($string);

	if (count($data) > 1) {
		for ($i = 1; $i++; $i < count($data)) {
			$object->appendF($data[$i]);
		}
	}

	return $object;
}

/* Vs creates a Vector instance, pushes all given arguments as new String
 * instances and returns it.
 *
 * TODO: Examples
 */
function Vs ()
{
	$strings = func_get_args();
	$vector = V();

	foreach ($strings as $string) {
		$vector->push(S($string));
	}

	return $vector;
}

/* ## is\_an\_array($var)
 *
 * Check whether a given variable either really is an array, a `Sauce\Object`
 * instance, or any other object that implements `ArrayAccess`.
 *
 * Examples:
 *
 *     > is_an_array(array());
 *     # => bool(true)
 *
 *     > is_an_array('foo');
 *     # => bool(false)
 *
 *     > is_an_array(A('a', 'b', 'c'));
 *     # => bool(true)
 *
 *     > is_an_array(1);
 *     # => bool(false)
 *
 *     > is_an_array(new ArrayObject());
 *     # => bool(true)
 *
 */
function is_an_array ($var)
{
	return is_array($var) || $var instanceof \Sauce\Object || $var instanceof \ArrayAccess;
}

/* Check whether a given variable is a string or an instance of String
 *
 * TODO: Examples
 */ 
function is_a_string ($string)
{
	return is_string($string) || $string instanceof \Sauce\String;
}

/* Check whether given variable is not null. This is just a proxy method so one
 * can use !is_null as callback in #ensure. */
function is_not_null ($value)
{
	return !is_null($value);
}

/* Check whether given variable is set and not null; basically mimicking Ruby's
 * 'or equals' operator (||=).
 *
 * TODO: Examples
 */
function or_equals ($var, $value)
{
	return (isset($var) && $var !== null) ? $var : $value;
}

/* ## dump(...)
 *
 * Takes arbitrary arguments and outputs them via `var_dump()`. If not run in
 * a CLI, the result is wrapped with '<pre></pre>' tags.
 */
function dump ()
{
	$args = func_get_args();

	if (!is_cli()) echo '<pre>';

	foreach ($args as $arg) {
		var_dump($arg);
	}

	if (!is_cli()) echo '</pre>';
}

/* This is a shorthand function for var_export($value, true) and returns a string */
function sdump ($value)
{
	return var_export($value, true);
}

/* Check whether PHP is run from an application server or the command line. */
function is_cli()
{
	return php_sapi_name() == 'cli';
}

/* Check whether the application server running PHP is the built-in server. */
function is_cli_server () {
	return (php_sapi_name() == 'cli-server');
}

/* Splits a URI into chunks seperated by '/' and returns them as array. */
function split_uri ($uri) {
	ensure('URI', $uri, is_a_string, __FUNCTION__);

	$splitted_uri = explode('/', $uri);

	// Remove empty strings from beginning and end
	if (count($splitted_uri) > 1) {
		$last = end($splitted_uri);
		$first = reset($splitted_uri);

		if (empty($last)) {
			array_pop($splitted_uri);
		}

		if (empty($first)) {
			array_shift($splitted_uri);
		}
	}

	return $splitted_uri;
}

/* ## path\_info()
 *
 * `path_info()` returns the canonalized path info of the request.
 *
 * If the path info contains /index.php this will be left out.
 * GET parameters are not returned.
 */
function path_info () {
	if (array_key_exists('PATH_INFO', $_SERVER)) {
		$path_info = $_SERVER['PATH_INFO'];
	} else {
		$path_info = S($_SERVER['REQUEST_URI']);

		/* REQUEST_URI may hold GET parameters that need to be stripped away.
		 * Otherwise the condition REQUEST_URI !== SCRIPT_NAME will be true even though
		 * the URI is the same. */
		if ($path_info->includes('?')) {
			$path_info = S($path_info->split('?')[0]);
		}

		if (!$path_info->equals($_SERVER['SCRIPT_NAME'])) {
			/* Find out whether the script is located in some other directory than / and
			 * replace the prefix in the request URI.
			 */
			$prefix = join('/', array_filter(explode('/', $_SERVER['SCRIPT_NAME']), function ($item) {
				return !empty($item) && 'index.php' != $item;
			}));

			if (!empty($prefix)) {
				$path_info->replaceF($prefix, '');
			}
		}

		$path_info = $path_info->to_s();
	}

	// In some cases path info does include the GET parameters
	// (passed in the URI), so we need to remove those.
	$question_mark = strpos($path_info, '?');

	if ($question_mark !== false) {
		$path_info = substr($path_info, 0, $question_mark);
	}

	return $path_info;
}

/* ## http\_method()
 *
 * `http_method()` returns the lowercased Request Method. This can be either
 * the HTTP verb itself, or otherwise, if a POST parameter named `_method`
 * exists this will be returned.
 *
 * @return string
 * 
 */
function http_method () {
	$method = $_SERVER['REQUEST_METHOD'];

	if (array_key_exists('_method', $_REQUEST)) {
		$method = $_REQUEST['_method'];
	} 

	if (array_key_exists('HTTP_X_HTTP_METHOD_OVERRIDE', $_SERVER)) {
		$method = $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'];
	}

	return strtolower($method);
}

/* Return whether or not given object or class has a method defined.
 *
 * If an object is given, #method_exists is called. If a class is given,
 * #get_class_methods is used.
 */
function has_method ($object, $method) {
	if (!is_object($object)) {
		return V(get_class_methods($object))->include($method);
	}

	return method_exists($object, $method);
}


/* Utility function for method contracts: throws an InvalidArgumentException
 * in case given callback returns false on given value.
 *
 * The parameters name, value and fn are mandatory, class and method names do
 * not have to be supplied. In case the fourth argument is given but the last
 * is not, it is assumed as method/function name.
 *
 * This is practically a shorthand for:
 *
 * if (!is_something($value) {
 *   throw new InvalidArgumentException(...);
 * }
 *
 * ensure('Argument', $value, is_something);
 */
function ensure ($name, $value, $fn, $class = null, $method = null)
{
	if (!is_callable($fn)) {
		throw new InvalidArgumentException("#ensure: Given callback is not callable: " . sdump($fn));
	}

	if (!is_a_string($name)) {
		throw new InvalidArgumentException("#ensure: Given argument name is not a string: " . sdump($fn));
	}

	// TODO: Contract for $value

	if (is_not_null($class) && is_null($method)) {
		$method = $class;
		$class = null;
	}

	if (!$fn($value)) {
		throw new InvalidArgumentException("{$class}#{$method}: {$name} does not comply argument contract " . sdump($fn) . ": " . sdump($value) . ')');
	}
}

?>
