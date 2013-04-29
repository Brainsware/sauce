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

require realpath(__DIR__) . '/../vendor/autoload.php';

/* Should is a class for general asserts and to check whether code throws or
 * does not throw exceptions of given type as expected. */
class Should
{
	protected $results;

	public function __construct ($name, $results)
	{
		$this->results = $results;
	}

	public function results ()
	{
		return $this->results;
	}

	/* #assert checks whether a given closure executes without throwing an
	 * exception and returns true.
	 *
	 * If the closure is not callable, the test does not pass.
	 *
	 * If the closure or any code called therein throws an exception, the test
	 * does not pass and the exception message and its trace are pushed
	 * onto the results vector.
	 *
	 * TODO: Examples
	 */
	public function assert ($name, $description, $fn)
	{
		$success = false;
		$message = '';
		$trace = V();

		if (!is_callable($fn)) {
			$success = false;
			$message = 'Passed test function is not callable';

		} else {
			try {
				$success = true === $fn();

			} catch (\Exception $e) {
				$success = false;
				$message = "An exception was thrown in file {$e->getFile()}:{$e->getLine()}:\n{$e->getMessage()}";
				$trace   = V($e->getTrace());
			}
		}

		$this->results->push(A([
			'name'        => $name,
			'description' => $description,
			'success'     => $success,
			'message'     => $message,
			'trace'       => $trace
		]));
	}

	/* #throw checks whether a given closure executes WITH throwing an
	 * exception of given type.
	 *
	 * If the closure is not callable, the test does not pass.
	 *
	 * If the closure or any code called therein throws an exception other
	 * than the expected, the test does not pass and the exception message
	 * and its trace are pushed onto the results vector.
	 *
	 * TODO: Examples
	 */
	public function _throw ($name, $description, $exception_type, $fn)
	{
		$success = false;
		$message = '';
		$trace   = V();

		if (!is_callable($fn)) {
			$success = false;
			$message = 'Passed test function is not callable';

		} else {
			try {
				$fn();

			} catch (\Exception $e) {
				if ($exception_type === get_class($e)) {
					$success = true;

				} else {
					$type_of_e = get_class($e);

					$message = "An exception other than the expected {$exception_type} was thrown in file {$e->getFile()}:{$e->getLine()}: \n({$type_of_e}) {$e->getMessage()}";
					$trace   = V($e->getTrace());
				}
			}
		}

		$this->results->push(A([
			'name'        => $name,
			'description' => $description,
			'success'     => $success,
			'message'     => $message,
			'trace'       => $trace
		]));
	}

	/* #not_throw checks whether a given closure executes WITHOUT throwing an
	 * exception of given type. This method does not check the closure's
	 * return value.
	 *
	 * If the closure is not callable, the test does not pass.
	 *
	 * If the closure or any code called therein throws an exception the test
	 * does not pass and the exception message and its trace are pushed onto
	 * the results vector.
	 *
	 * TODO: Examples
	 */
	public function not_throw ($name, $description, $fn, $exception_type)
	{
		$success = false;
		$message = '';
		$trace   = V();

		if (!is_callable($fn)) {
			$success = false;
			$message = 'Passed test function is not callable';

		} else {
			try {
				$fn();

				$success = true;

			} catch (\Exception $e) {
				$type_of_e = get_class($e);

				$message = "An exception other than the expected {$exception_type} was thrown in file {$e->getFile()}:{$e->getLine()}: \n({$type_of_e}) {$e->getMessage()}";
				$trace   = V($e->getTrace());
			}
		}

		$this->results->push(A([
			'name'        => $name,
			'description' => $description,
			'success'     => $success,
			'message'     => $message,
			'trace'       => $trace
		]));
	}

	/* Since we may not define a method called #throw by hand, we have to walk
	 * the extra mile and catch it via #__call. */
	public function __call ($name, $arguments)
	{
		switch ($name) {
			case 'throw': return call_user_func_array([ $this, '_throw' ], $arguments); break;

			default: trigger_error("Call to undefined method ".__CLASS__."::$func()", E_USER_ERROR); break;
		}
	}
}

$results = V();

$should = new Should('String class', $results);

$should->throw('Argument contract __construct', 'An InvalidArgumentException should be thrown',
	'InvalidArgumentException',
	function () { new \Sauce\String(0); }
);

$should->assert('String stored internally', 'Passing a string stores that string internally in the object',
	function () {
		$string = 'abc';
		$s = new \Sauce\String($string);

		return $s->to_s() === $string;
	}
);

$should->assert('Should fail!', '...', function () { return false; });

$paint = new \Sauce\CliColors();

foreach ($should->results()->to_array() as $i => $result) {
	$success = $result->success ? $paint::green('PASSED') : $paint::red('FAILED');

	printf("(%d) %-50s %s\n", ($i + 1), $result->name, $success);
}


?>
