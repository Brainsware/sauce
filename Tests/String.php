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

function should_throw ($exception_type, $fn = null) {
	if (null === $fn && is_callable($exception_type)) {
		$fn = $exception_type;
		$exception_type = null;
	}

	try {
		$fn();

	} catch (\Exception $e) {
		if (null !== $exception_type) {
			assert($e instanceof $exception_type, 'Exception thrown as expected');
		} else {
			assert($e !== null);
		}
	}
}

function should_not_throw ($exception_type, $fn = null) {
	if (null === $fn && is_callable($exception_type)) {
		$fn = $exception_type;
		$exception_type = null;
	}

	try {
		$fn();

	} catch (\Exception $e) {
		if (null !== $exception_type) {
			assert(!($e instanceof $exception_type));
		}
	}

	assert(true);
}

$tests = V();

$tests->push(A([
	'name'        => 'Test argument contract of __construct',
	'description' => 'Tests whether an InvalidArgumentException is thrown when a non-string argument is given',
	'fn'          => function () {
		$valid = true;

		should_throw('\\InvalidArgumentException', function () { new \Sauce\String(0); });
		should_throw('\\InvalidArgumentException', function () { new \Sauce\String([]); });

		should_not_throw('\\InvalidArgumentException', function () { new\Sauce\String(); });
		should_not_throw('\\InvalidArgumentException', function () { new\Sauce\String('abc'); });

		assert(false);
	}]));

foreach ($tests->to_array() as $i => $test) {
	echo "({$i}) {$test->name}\n";
	echo "\t{$test->description}\n";

	$test->fn();
}

?>
