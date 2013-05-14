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

class Vector
{
	use \Grease\Test;

	public function tests ()
	{
		$this->construct_tests();
	}

	protected function construct_tests ()
	{
		$this->should->assert(
			'The constructor should take an argument of arbitrary type that is not an array or an instance of \Sauce\Vector and store it in an internal array', '',
			function () {
				$a = new \Sauce\Vector(10);

				return $a[0] === 10;
			}
		);

		$this->should->assert(
			'The constructor should take an argument of arbitrary type that is not an array or an instance of \Sauce\Vector and store it in an internal array', '',
			function () {
				$a = new \Sauce\Vector('abc');

				return $a[0] === 'abc';
			}
		);

		$this->should->assert(
			'The constructor should take an argument of arbitrary type that is not an array or an instance of \Sauce\Vector and store it in an internal array', '',
			function () {
				$a = new \Sauce\Vector(new \Sauce\Object());

				return $a[0] instanceof \Sauce\Object;
			}
		);

		$this->should->assert(
			'When given an array, all its contents should be copied over to the internal storage.', '',
			function () {
				$a = new \Sauce\Vector([ 1, 2, 3 ]);

				return
					$a[0] === 1 &&
					$a[1] === 2 &&
					$a[2] === 3;
			}
		);

		$this->should->assert(
			'When given an instance of \Sauce\Vector, all its contents should be copied over to the internal storage.', '',
			function () {
				$a = new \Sauce\Vector(new \Sauce\Vector([ 1, 2, 3 ]));

				return
					$a[0] === 1 &&
					$a[1] === 2 &&
					$a[2] === 3;
			}
		);

	}
}
