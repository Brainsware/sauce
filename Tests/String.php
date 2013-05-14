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

namespace Tests;

class String
{
	use \Grease\Test;

	public function tests ()
	{
		$this->construct_tests();
		$this->shortcut_tests();
		$this->string_check_tests();
		$this->equals_tests();
	}

	public function equals_tests ()
	{
		$this->should->throw(
			'Argument contract #equals',
			'When passing an argument of type other than string or an instance of \Sauce\String, an InvalidArgumentException should be thrown.',
			'InvalidArgumentException',
			function () {
				$a = S('abc');
				$a->equals(0);
			}
		);

		$this->should->assert(
			'String#equals should return true on passing an identical string', '',
			function () {
				$s = S('abc');

				return $s->equals('abc');
			}
		);

		$this->should->assert(
			'String#equals should return true on passing \Sauce\String instance with an identical string stored', '',
			function () {
				$a = S('abc');
				$b = S('abc');

				return $a->equals($b);
			}
		);
	}

	public function construct_tests ()
	{
		$this->should->throw(
			'Argument contract __construct',
			'An InvalidArgumentException should be thrown',
			'InvalidArgumentException',
			function () { new \Sauce\String(0); }
		);

		$this->should->assert(
			'String stored internally',
			'Passing a string to __construct stores that string internally in the object',
			function () {
				$string = 'abc';
				$s = new \Sauce\String($string);

				return $s->to_s() === $string;
			}
		);

		$this->should->assert(
			'String instance should store the passed instance\'s value',
			'Passing a String instance to __construct copies over the stored string',
			function () {
				$string = 'abc';

				$s1 = new \Sauce\String($string);
				$s2 = new \sauce\String($s1);

				return $s2->to_s() === $string;
			}
		);
	}

	protected function shortcut_tests ()
	{
		$this->should->assert(
			'S() should return a String instance',
			'When using the shortcut function S(), it should return a \Sauce\String instance',
			function () {
				$s = S('abc');

				return $s instanceof \Sauce\String;
			}
		);

		$this->should->assert(
			'S() should return a String instance with the correct value',
			'When using the shortcut function S(), it should return a \Sauce\String instance holding the passed value',
			function () {
				$s = S('abc');

				return $s->to_s() === 'abc';
			}
		);

		$this->should->assert(
			'Vs() should return a Vector instance', '',
			function () {
				return Vs() instanceof \Sauce\Vector;
			}
		);

		$this->should->assert(
			'Vs() should return a Vector instance holding the passed strings', '',
			function () {
				$a = Vs('abc', 'def');

				return $a[0]->to_s() === 'abc' && $a[1]->to_s() === 'def';
			}
		);
	}

	protected function string_check_tests ()
	{
		$this->should->assert(
			'is_a_string() should return false on passing an integer', '',
			function () { return false === is_a_string(0); }
		);

		$this->should->assert(
			'is_a_string() should return false on passing an array', '',
			function () { return false === is_a_string([]); }
		);

		$this->should->assert(
			'is_a_string() should return false on passing an object of type other than \Sauce\String', '',
			function () { return false === is_a_string(A()); }
		);

		$this->should->assert(
			'is_a_string() should return true on passing an object of type \Sauce\String', '',
			function () { return true === is_a_string(S()); }
		);

		$this->should->assert(
			'is_a_string() should return true on passing a string', '',
			function () { return true === is_a_string('abc'); }
		);
	}
}

?>
