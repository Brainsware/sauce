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
		$this->interface_tests();
		$this->construct_tests();
		$this->iterator_tests();

		$this->slice_tests();
		$this->join_tests();
		$this->map_tests();
		$this->select_tests();

		$this->push_tests();
		$this->pop_tests();
		$this->shift_tests();
		$this->unshift_tests();
	}

	protected function interface_tests()
	{
		$this->should->implement('\Sauce\Vector should implement ArrayAccess', '', 'ArrayAccess', V());
		$this->should->implement('\Sauce\Vector should implement Countable', '', 'Countable', V());
		$this->should->implement('\Sauce\Vector should implement JsonSerializable', '', 'JsonSerializable', V());
		$this->should->implement('\Sauce\Vector should implement Iterator', '', 'Iterator', V());
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

	protected function iterator_tests ()
	{
		$this->should->assert(
			'In a fresh \Sauce\Vector instance, the method key() should return 0', '',
			function () { return V()->key() === 0; }
		);

		$this->should->assert(
			'In an empty \Sauce\Vector instance, the method current() should return null', '',
			function () { return V()->current() === null; }
		);

		$this->should->assert(
			'In a non-empty \Sauce\Vector instance, the method current() should return the stored value at given index', '',
			function () {
				$a = V([ 0, 1, 2 ]);

				return 0 === $a->current();
			}
		);

		$this->should->assert(
			'In a non-empty \Sauce\Vector instance, the method current() should return the stored value at given index', '',
			function () {
				$a = V([ 0, 1, 2 ]);
				$a->next();

				return 1 === $a->current();
			}
		);

		$this->should->assert(
			'In an empty \Sauce\Vector instance, the method next() should NOT increase the index', '',
			function () {
				$a = V();
				$key = $a->key();

				$a->next();

				return $key === $a->key();
			}
		);

		$this->should->assert(
			'In a non-empty \Sauce\Vector instance, the method next() should increase the index', '',
			function () {
				$a = V([ 0, 1, 2 ]);
				$a->next();

				return 1 === $a->key();
			}
		);

		$this->should->assert(
			'In a non-empty \Sauce\Vector instance where next() has been called at least once, rewind() should set the index to zero (0)', '',
			function () {
				$a = V([ 1, 2, 3 ]);
				$a->next();
				$a->next();
				$a->rewind();

				return 0 === $a->key();
			}
		);

		$this->should->assert(
			'In an empty \Sauce\Vector instance valid() should return false', '',
			function () { return false === V()->valid(); }
		);

		$this->should->assert(
			'In a non-empty (new) \Sauce\Vector instance valid() should return true', '',
			function () { return true === V([ 1, 2, 3 ])->valid(); }
		);
	}

	protected function slice_tests ()
	{
		$this->should->throw(
			'Calling slice() with non-numeric indexes should throw an \OutOfBoundsException', '',
			'OutOfBoundsException',
			function () {
				$a = V([ 0, 1, 2 ]);
				$a->slice('a', 'b');
			}
		);

		$this->should->throw(
			'Calling slice() with non-numeric indexes should throw an \OutOfBoundsException', '',
			'OutOfBoundsException',
			function () {
				$a = V([ 0, 1, 2 ]);
				$a->slice(0, A());
			}
		);

		$this->should->throw(
			'Calling slice() with non-numeric indexes should throw an \OutOfBoundsException', '',
			'OutOfBoundsException',
			function () {
				$a = V([ 0, 1, 2 ]);
				$a->slice(A(), 0);
			}
		);

		$this->should->assert(
			'Calling slice() with valid indexes should return an instance of \Sauce\Vector', '',
			function () {
				$a = V([ 0, 1, 2 ]);
				$b = $a->slice(0, 1);

				return $b instanceof \Sauce\Vector;
			}
		);

		$this->should->assert(
			'Calling slice() with valid indexes should return an instance of \Sauce\Vector holding the desired values', '',
			function () {
				$a = V([ 0, 1, 2 ]);
				$b = $a->slice(0, 2);

				return 
					$b->count() === 2 &&
					$b[0]       === 0 &&
					$b[1]       === 1;
			}
		);
	}

	protected function join_tests ()
	{
		$this->should->not_throw(
			'When calling join() without an argument, it  should not throw an exception', '',
			function () {
				$a = V([ 1, 2, 3 ]);
				$a->join();
			}
		);

		$this->should->throw(
			'When calling join() with non-string arguments, it should throw an InvalidArgumentException', '',
			'InvalidArgumentException',
			function () {
				$a = V([ 1, 2, 3 ]);
				$a->join(A());
			}
		);

		$this->should->assert(
			'join() should return an instance of \Sauce\String', '',
			function () {
				$a = V([ 1, 2, 3 ]);
				$b = $a->join();

				return $b instanceof \Sauce\String;
			}
		);

		$this->should->assert(
			'Calling join() without an argument should have a space as default delimiter', '',
			function () {
				$a = V([ 1, 2, 3 ]);
				$b = $a->join();

				return $b->equals('1 2 3');
			}
		);

		$this->should->assert(
			'Calling join() with a string as argument should have that string as delimiter', '',
			function () {
				$a = V([ 1, 2, 3 ]);
				$b = $a->join('aaa');

				return $b->equals('1aaa2aaa3');
			}
		);
	}

	protected function map_tests ()
	{
		$this->should->throw(
			'Calling map() with a non-callable argument should throw an InvalidArgumentException', '',
			'InvalidArgumentException',
			function () {
				$a = V([ 1, 2, 3 ]);
				$a->map(10);
			}
		);

		$this->should->assert(
			'Calling map() with a callable argument should return an instance of \Sauce\Vector', '',
			function () {
				$a = V([ 1, 2, 3 ]);
				$b = $a->map(function ($i) { return $i; });

				return $b instanceof \Sauce\Vector;
			}
		);

		$this->should->assert(
			'Calling map() with a callable argument should return an instance of \Sauce\Vector holding the values returned by the callback', '',
			function () {
				$a = V([ 1, 2, 3 ]);
				$b = $a->map(function ($i) { return $i + 1; });

				return
					$b->count() === 3 &&
					$b[0]       === 2 &&
					$b[1]       === 3 &&
					$b[2]       === 4;

			}
		);

		$this->should->assert(
			'Calling map() with a callable argument should return an instance of \Sauce\Vector holding the NON-NULL values returned by the callback', '',
			function () {
				$a = V([ 1, 2, 3, 4 ]);
				$b = $a->map(function ($i) { if (0 === ($i % 2)) return $i; });

				return
					$b->count() === 2 &&
					$b[0]       === 2 &&
					$b[1]       === 4;
			}
		);
	}

	protected function select_tests ()
	{
		$this->should->throw(
			'Calling select() with a non-callable argument should throw an InvalidArgumentException', '',
			'InvalidArgumentException',
			function () {
				$a = V([ 1, 2, 3 ]);
				$a->select(10);
			}
		);

		$this->should->assert(
			'Calling select() with a callable argument should return an instance of \Sauce\Vector', '',
			function () {
				$a = V([ 1, 2, 3 ]);
				$b = $a->select(function () { return true; });

				return $b instanceof \Sauce\Vector;
			}
		);

		$this->should->assert(
			'Calling select() with a callable argument should return an instance of \Sauce\Vector holding all values where the callback returns true', '',
			function () {
				$a = V([ 1, 2, 3, 4 ]);
				$b = $a->select(function ($i) { return 0 === ($i % 2); });

				return
					$b->count() === 2 &&
					$b[0]       === 2 &&
					$b[1]       === 4;
			}
		);
	}

	protected function exclude_tests ()
	{
		$this->should->throw(
			'Calling exclude() with a non-callable argument should throw an InvalidArgumentException', '',
			'InvalidArgumentException',
			function () {
				$a = V([ 1, 2, 3 ]);
				$a->exclude(10);
			}
		);

		$this->should->assert(
			'Calling exclude() with a callable argument should return an instance of \Sauce\Vector', '',
			function () {
				$a = V([ 1, 2, 3 ]);
				$b = $a->exclude(function () { return true; });

				return $b instanceof \Sauce\Vector;
			}
		);

		$this->should->assert(
			'Calling exclude() with a callable argument should return an instance of \Sauce\Vector holding all values where the callback returns true', '',
			function () {
				$a = V([ 1, 2, 3, 4 ]);
				$b = $a->exclude(function ($i) { return 0 === ($i % 2); });

				return
					$b->count() === 2 &&
					$b[0]       === 1 &&
					$b[1]       === 3;
			}
		);
	}

	protected function push_tests ()
	{
		$this->should->assert(
			'Calling push() should always return null', '',
			function () {
				$a = V([ 1, 2, 3 ]);

				return null === ($a->push(4));
			}
		);

		$this->should->assert(
			'Calling push() with an arbitrary value type as argument should add the value to the end of the internal storage array', '',
			function () {
				$a = V([ 1, 2, 3 ]);
				$a->push(4);

				return $a[3] === 4;
			}
		);

		$this->should->assert(
			'Calling push() with any array type (except \Sauce\Object) as argument should add the contained values to the end of the internal storage array', '',
			function () {
				$a = V([ 1, 2, 3, 4 ]);
				$b = V([ 5, 6, 7, 8 ]);

				$a->push($b);

				return
					$a->count() === 8 &&
					$a[4]       === 5 &&
					$a[5]       === 6 &&
					$a[6]       === 7 &&
					$a[7]       === 8;
			}
		);

		$this->should->assert(
			'Calling push() with a \Sauce\Object as argument should add that object to the end of the internal storage array', '',
			function () {
				$a = V([ 0, 1, 2 ]);
				$b = A([]);

				$a->push($b);

				return $a[3] instanceof \Sauce\Object;
			}
		);
	}

	protected function pop_tests ()
	{
		$this->should->assert(
			'Calling pop() should return null in case of an empty instance', '',
			function () {
				$a = V();

				return null === $a->pop();
			}
		);

		$this->should->assert(
			'Calling pop() should return the very last of the stored elements and remove it', '',
			function () {
				$a = V([ 0, 1, 2, 3 ]);
				$b = $a->pop();

				return
					$b          === 3 &&
					$a->count() === 3;
			}
		);
	}

	protected function shift_tests ()
	{
		$this->should->assert(
			'Calling shift() should return null in case of an empty instance', '',
			function () {
				$a = V();

				return null === $a->shift();
			}
		);

		$this->should->assert(
			'Calling shift() should return the very first of the stored elements and remove it', '',
			function () {
				$a = V([ 0, 1, 2, 3 ]);
				$b = $a->shift();

				return
					$b          === 0 &&
					$a->count() === 3;
			}
		);
	}

	protected function unshift_tests ()
	{
		$this->should->assert(
			'Calling unshift() should always return null', '',
			function () {
				$a = V([ 1, 2, 3 ]);

				return null === $a->unshift(4);
			}
		);

		$this->should->assert(
			'Calling unshift() with a single value should prepend given value to the storage', '',
			function () {
				$a = V([ 1, 2, 3 ]);
				$a->unshift(0);

				return
					4 === $a->count() &&
					0 === $a[0] &&
					1 === $a[1];
			}
		);

		$this->should->assert(
			'Calling unshift() with an array should prepend all elements of given array to the storage', '',
			function () {
				$a = V([ 3, 4, 5 ]);
				$b = V([ 0, 1, 2 ]);

				$a->unshift($b);

				dump($a);

				return
					6 === $a->count() &&
					0 === $a[0] &&
					1 === $a[1] &&
					2 === $a[2] &&
					3 === $a[3];
			}
		);
	}
}
