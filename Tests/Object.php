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

class Object
{
	use \Grease\Test;

	public function tests ()
	{
		$this->interface_tests();
	}

	protected function interface_tests ()
	{
		$this->should->implement('Object should implement ArrayAccess', '', 'ArrayAccess', A());
		$this->should->implement('Object should implement Countable', '', 'Countable', A());
		$this->should->implement('Object should implement JsonSerializable', '', 'JsonSerializable', A());

		// NOTE: Not yet implemented, but should be implemented at some point.
		$this->should->implement('Object should implement Iterator', '', 'Iterator', A());

		$this->should->assert(
			'Calling count() should always return a numeric value', '',
			function () {
				$a = A([
					'a' => 0,
					'b' => 1,
					'c' => 2,
					'd' => 3
				]);

				return is_numeric($a->count());
			}
		);

		$this->should->assert(
			'Calling count() should always return a numeric value', '',
			function () { return is_numeric(A()->count()); }
		);

		$this->should->assert(
			'Calling empty() should always return a boolean value', '',
			function () { return true === A()->is_empty(); }
		);

		$this->should->assert(
			'Calling empty() should always return a boolean value', '',
			function () { return false === A([ 'a' => 1, 'b' => 2, 'c' => 3 ])->is_empty(); }
		);

		$this->should->assert(
			'Calling jsonSerialize() should always return an array (the internal storage)', '',
			function () {
				$a = A([ 'a' => 1, 'b' => 2, 'c' => 3 ]);

				return is_array($a->jsonSerialize());
			}
		);

		$this->should->assert(
			'Calling jsonSerialize() should always return an array (the internal storage)', '',
			function () {
				$a = V();

				return is_array($a->jsonSerialize());
			}
		);
	}

	protected function construct_tests ()
	{
		$this->should->assert(
			'The constructor should take an argument of arbitrary type that is not an array or an instance of \Sauce\Vector and store it in an internal array', '',
			function () {
				$a = A(10);

				return $a[0] === 10;
			}
		);

		$this->should->assert(
			'The constructor should take an argument of arbitrary type that is not an array or an instance of \Sauce\Vector and store it in an internal array', '',
			function () {
				$a = A('abc');

				return $a[0] === 'abc';
			}
		);

	}
}
