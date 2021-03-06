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
		$this->is_a_string_tests();
		$this->length_tests();
		$this->equals_tests();
		$this->starts_with_tests();
		$this->ends_with_tests();
		$this->includes_tests();
		$this->replace_tests();
		$this->ireplace_tests();
		$this->replaceF_tests();
		$this->ireplaceF_tests();
		$this->slice_tests();
		$this->sliceF_tests();
		$this->append_tests();
		$this->appendF_tests();
		$this->prepend_tests();
		$this->prependF_tests();
		$this->trim_tests();
		$this->trimF_tests();
		$this->split_tests();
		$this->to_lines_tests();
	}

	protected function construct_tests ()
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

	protected function is_a_string_tests ()
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

	protected function length_tests ()
	{
		$this->should->assert(
			'#length should return the length of the stored string', '',
			function () {
				$a = S('abc');

				return 3 === $a->length();
			}
		);
	}

	protected function starts_with_tests ()
	{
		$this->should->throw(
			'Argument contract #starts_with', '',
			'InvalidArgumentException',
			function () {
				S('abc')->starts_with(0);
			}
		);

		$this->should->assert(
			'#starts_with should return false on passing a string that is not included', '',
			function () {
				$s = S('Lorem ipsum lorem ipsum');

				return false === $s->starts_with('abc');
			}
		);

		$this->should->assert(
			'#starts_with should return true on passing a string that is included and at the beginning', '',
			function () {
				$s = S('Lorem ipsum lorem ipsum');

				return true === $s->starts_with('Lorem');
			}
		);
	}

	protected function ends_with_tests ()
	{
		$this->should->throw(
			'Argument contract #ends_with', '',
			'InvalidArgumentException',
			function () {
				S('abc')->ends_with(0);
			}
		);

		$this->should->assert(
			'#ends_with should return false on passing a string that is not included', '',
			function () {
				$s = S('Lorem ipsum lorem ipsum');

				return false === $s->ends_with('abc');
			}
		);

		$this->should->assert(
			'#ends_with should return true on passing a string that is included and at the end', '',
			function () {
				$s = S('Lorem ipsum lorem ipsum');

				return true === $s->ends_with('ipsum');
			}
		);
	}

	protected function includes_tests ()
	{
		$this->should->throw(
			'Argument contract #includes', '',
			'InvalidArgumentException',
			function () {
				S('abc')->includes(0);
			}
		);

		$this->should->assert(
			'#includes should return false on passing a string that is not included', '',
			function () {
				$s = S('Lorem ipsum lorem ipsum');

				return false === $s->includes('abc');
			}
		);

		$this->should->assert(
			'#includes should return true on passing a string that is included', '',
			function () {
				$s = S('Lorem ipsum lorem ipsum');

				return true === $s->includes('ipsum');
			}
		);
	}

	protected function replace_tests ()
	{
		$this->should->throw(
			'Argument contract #replace',
			'When passing an argument of type other than string or an instance of \Sauce\String, an InvalidArgumentException should be thrown.',
			'InvalidArgumentException',
			function () {
				$a = S('abcdef123');
				$a->replace(0, 0);
			}
		);

		$this->should->throw(
			'Argument contract #replace',
			'When passing an argument of type other than string or an instance of \Sauce\String, an InvalidArgumentException should be thrown.',
			'InvalidArgumentException',
			function () {
				$a = S('abcdef123');
				$a->replace(0, 'abc');
			}
		);

		$this->should->throw(
			'Argument contract #replace',
			'When passing an argument of type other than string or an instance of \Sauce\String, an InvalidArgumentException should be thrown.',
			'InvalidArgumentException',
			function () {
				$a = S('abcdef123');
				$a->replace(S('abc'), 0);
			}
		);

		$this->should->assert(
			'Calling #replace with valid arguments should return a new instance of \Sauce\String', '',
			function () {
				$a = S('abcdef123');
				$b = $a->replace(S('abc'), 'def');

				return $b instanceof \Sauce\String &&
					   $b !== $a;
			}
		);

		$this->should->assert(
			'Calling #replace with valid arguments and a non-matching search string should return an instance of \Sauce\String that is equal to the original string', '',
			function () {
				$a = S('abcdef123');
				$b = $a->replace('456', 'def');

				return $b->equals('abcdef123');
			}
		);

		$this->should->assert(
			'Calling #replace with valid arguments and a matching search string should return an instance of \Sauce\String with the search string replaced', '',
			function () {
				$a = S('abcdef123');
				$b = $a->replace(S('abc'), 'def');

				return $b->equals('defdef123');
			}
		);
	}

	protected function ireplace_tests ()
	{
		$this->should->throw(
			'Argument contract #ireplace',
			'When passing an argument of type other than string or an instance of \Sauce\String, an InvalidArgumentException should be thrown.',
			'InvalidArgumentException',
			function () {
				$a = S('abcdef123');
				$a->ireplace(0, 0);
			}
		);

		$this->should->throw(
			'Argument contract #ireplace',
			'When passing an argument of type other than string or an instance of \Sauce\String, an InvalidArgumentException should be thrown.',
			'InvalidArgumentException',
			function () {
				$a = S('abcdef123');
				$a->ireplace(0, 'abc');
			}
		);

		$this->should->throw(
			'Argument contract #ireplace',
			'When passing an argument of type other than string or an instance of \Sauce\String, an InvalidArgumentException should be thrown.',
			'InvalidArgumentException',
			function () {
				$a = S('abcdef123');
				$a->ireplace(S('abc'), 0);
			}
		);

		$this->should->assert(
			'Calling #ireplace with valid arguments should return a new instance of \Sauce\String', '',
			function () {
				$a = S('abcdef123');
				$b = $a->ireplace(S('abc'), 'def');

				return $b instanceof \Sauce\String &&
					   $b !== $a;
			}
		);

		$this->should->assert(
			'Calling #ireplace with valid arguments and a non-matching search string should return an instance of \Sauce\String that is equal to the original string', '',
			function () {
				$a = S('abcdef123');
				$b = $a->ireplace('456', 'def');

				return $b->equals('abcdef123');
			}
		);

		$this->should->assert(
			'Calling #ireplace with valid arguments and a matching search string should return an instance of \Sauce\String with the search string replaced', '',
			function () {
				$a = S('abcdef123');
				$b = $a->ireplace(S('abc'), 'def');

				return $b->equals('defdef123');
			}
		);
	}

	protected function replaceF_tests ()
	{
		$this->should->throw(
			'Argument contract #replaceF',
			'When passing an argument of type other than string or an instance of \Sauce\String, an InvalidArgumentException should be thrown.',
			'InvalidArgumentException',
			function () {
				$a = S('abcdef123');
				$a->replaceF(0, 0);
			}
		);

		$this->should->throw(
			'Argument contract #replaceF',
			'When passing an argument of type other than string or an instance of \Sauce\String, an InvalidArgumentException should be thrown.',
			'InvalidArgumentException',
			function () {
				$a = S('abcdef123');
				$a->replaceF(0, 'abc');
			}
		);

		$this->should->throw(
			'Argument contract #replaceF',
			'When passing an argument of type other than string or an instance of \Sauce\String, an InvalidArgumentException should be thrown.',
			'InvalidArgumentException',
			function () {
				$a = S('abcdef123');
				$a->replaceF(S('abc'), 0);
			}
		);

		$this->should->assert(
			'Calling #replaceF with valid arguments should return the same instance', '',
			function () {
				$a = S('abcdef123');
				$b = $a->replaceF(S('abc'), 'def');

				return $b instanceof \Sauce\String &&
					   $b === $a;
			}
		);

		$this->should->assert(
			'Calling #replaceF with valid arguments and a non-matching search string should return the same instance without a change', '',
			function () {
				$a = S('abcdef123');
				$a->replaceF('456', 'def');

				return $a->equals('abcdef123');
			}
		);

		$this->should->assert(
			'Calling #replaceF with valid arguments and a matching search string should return an instance of \Sauce\String with the search string replaceFd', '',
			function () {
				$a = S('abcdef123');
				$a->replaceF(S('abc'), 'def');

				return $a->equals('defdef123');
			}
		);
	}

	protected function ireplaceF_tests ()
	{
		$this->should->throw(
			'Argument contract #ireplaceF',
			'When passing an argument of type other than string or an instance of \Sauce\String, an InvalidArgumentException should be thrown.',
			'InvalidArgumentException',
			function () {
				$a = S('ABcdef123');
				$a->ireplaceF(0, 0);
			}
		);

		$this->should->throw(
			'Argument contract #ireplaceF',
			'When passing an argument of type other than string or an instance of \Sauce\String, an InvalidArgumentException should be thrown.',
			'InvalidArgumentException',
			function () {
				$a = S('ABcdef123');
				$a->ireplaceF(0, 'abc');
			}
		);

		$this->should->throw(
			'Argument contract #ireplaceF',
			'When passing an argument of type other than string or an instance of \Sauce\String, an InvalidArgumentException should be thrown.',
			'InvalidArgumentException',
			function () {
				$a = S('abCdef123');
				$a->ireplaceF(S('abc'), 0);
			}
		);

		$this->should->assert(
			'Calling #ireplaceF with valid arguments should return the same instance', '',
			function () {
				$a = S('abCdef123');
				$b = $a->ireplaceF(S('abc'), 'def');

				return $b instanceof \Sauce\String &&
					   $b === $a;
			}
		);

		$this->should->assert(
			'Calling #ireplaceF with valid arguments and a non-matching search string should return the same instance without a change', '',
			function () {
				$a = S('abcdef123');
				$a->ireplaceF('456', 'def');

				return $a->equals('abcdef123');
			}
		);

		$this->should->assert(
			'Calling #ireplaceF with valid arguments and a matching search string should return an instance of \Sauce\String with the search string replaceFd', '',
			function () {
				$a = S('abcdef123');
				$a->ireplaceF(S('ABC'), 'def');

				return $a->equals('defdef123');
			}
		);
	}

	protected function equals_tests ()
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

	protected function slice_tests ()
	{
		$this->should->throw(
			'Argument contract #slice',
			'When passing arguments of type other than integer, an InvalidArgumentException should be thrown.',
			'InvalidArgumentException',
			function () {
				$a = S('Lorem ipsum, lorem ipsum.');
				$a->slice('abc', 'def');
			}
		);

		$this->should->throw(
			'Argument contract #slice',
			'When passing arguments of type other than integer, an InvalidArgumentException should be thrown.',
			'InvalidArgumentException',
			function () {
				$a = S('Lorem ipsum, lorem ipsum.');
				$a->slice(0, 'def');
			}
		);

		$this->should->throw(
			'Argument contract #slice',
			'When passing arguments of type other than integer, an InvalidArgumentException should be thrown.',
			'InvalidArgumentException',
			function () {
				$a = S('Lorem ipsum, lorem ipsum.');
				$a->slice(A(), 10);
			}
		);

		$this->should->assert(
			'#slice should return an instance of \Sauce\String', '',
			function () {
				$a = S('Lorem ipsum, lorem ipsum.');
				$b = $a->slice(0, 10);

				return $b instanceof \Sauce\String;
			}
		);

		$this->should->assert(
			'#slice should return the correct value', '',
			function () {
				$a = S('Lorem ipsum, lorem ipsum.');
				$b = $a->slice(1, 10);

				return $b->equals('orem ipsum');
			}
		);
	}

	protected function sliceF_tests ()
	{
		$this->should->throw(
			'Argument contract #sliceF',
			'When passing arguments of type other than integer, an InvalidArgumentException should be thrown.',
			'InvalidArgumentException',
			function () {
				$a = S('Lorem ipsum, lorem ipsum.');
				$a->sliceF('abc', 'def');
			}
		);

		$this->should->throw(
			'Argument contract #sliceF',
			'When passing arguments of type other than integer, an InvalidArgumentException should be thrown.',
			'InvalidArgumentException',
			function () {
				$a = S('Lorem ipsum, lorem ipsum.');
				$a->sliceF(0, 'def');
			}
		);

		$this->should->throw(
			'Argument contract #sliceF',
			'When passing arguments of type other than integer, an InvalidArgumentException should be thrown.',
			'InvalidArgumentException',
			function () {
				$a = S('Lorem ipsum, lorem ipsum.');
				$a->sliceF(A(), 10);
			}
		);

		$this->should->assert(
			'#sliceF should return the same instance of \Sauce\String (this)', '',
			function () {
				$a = S('Lorem ipsum, lorem ipsum.');
				$b = $a->sliceF(0, 10);

				return $b === $a;
			}
		);

		$this->should->assert(
			'After calling #sliceF, the object should hold the correct value', '',
			function () {
				$a = S('Lorem ipsum, lorem ipsum.');
				$a->sliceF(1, 10);

				return $a->equals('orem ipsum');
			}
		);
	}

	protected function append_tests ()
	{
		$this->should->throw(
			'Argument contract #append',
			'When passing arguments of type other than string or an instance of \Sauce\String, an InvalidArgumentException should be thrown.',
			'InvalidArgumentException',
			function () {
				$a = S('Lorem ipsum, lorem ipsum.');
				$a->append(10);
			}
		);

		$this->should->throw(
			'Argument contract #append',
			'When passing arguments of type other than string or an instance of \Sauce\String, an InvalidArgumentException should be thrown.',
			'InvalidArgumentException',
			function () {
				$a = S('Lorem ipsum, lorem ipsum.');
				$a->append(A());
			}
		);

		$this->should->assert(
			'#append should return an instance of \Sauce\String', '',
			function () {
				$a = S('Lorem ipsum, lorem ipsum.');
				$b = $a->append(' Lorem; ipsum');

				return $b instanceof \Sauce\String;
			}
		);

		$this->should->assert(
			'#append should return an instance of \Sauce\String holding the former and latter string combined', '',
			function () {
				$a = S('Lorem ipsum, lorem ipsum.');
				$b = $a->append('ipsum ipsum');

				return $b->equals('Lorem ipsum, lorem ipsum.ipsum ipsum');
			}
		);

		$this->should->assert(
			'Given another instance of \Sauce\String, #append should combine those two and return a new instance of \Sauce\String holding the former and the latter strings combined.', '',
			function () {
				$a = S('Lorem ipsum');
				$b = S(', ipsum lorem.');
				$c = $a->append($b);

				return $c->equals('Lorem ipsum, ipsum lorem.');
			}
		);
	}

	protected function appendF_tests ()
	{
		$this->should->throw(
			'Argument contract #appendF',
			'When passing arguments of type other than string or an instance of \Sauce\String, an InvalidArgumentException should be thrown.',
			'InvalidArgumentException',
			function () {
				$a = S('Lorem ipsum, lorem ipsum.');
				$a->appendF(10);
			}
		);

		$this->should->throw(
			'Argument contract #appendF',
			'When passing arguments of type other than string or an instance of \Sauce\String, an InvalidArgumentException should be thrown.',
			'InvalidArgumentException',
			function () {
				$a = S('Lorem ipsum, lorem ipsum.');
				$a->appendF(A());
			}
		);

		$this->should->assert(
			'#appendF should return the same instance of \Sauce\String (this)', '',
			function () {
				$a = S('Lorem ipsum, lorem ipsum.');
				$b = $a->appendF(' Lorem; ipsum');

				return $b === $a;
			}
		);

		$this->should->assert(
			'#appendF should append the given string to the internally stored string', '',
			function () {
				$a = S('Lorem ipsum, lorem ipsum.');
				$a->appendF('ipsum ipsum');

				return $a->equals('Lorem ipsum, lorem ipsum.ipsum ipsum');
			}
		);

		$this->should->assert(
			'Given another instance of \Sauce\String, #appendF should append it to the internally stored string.', '',
			function () {
				$a = S('Lorem ipsum');
				$b = S(', ipsum lorem.');
				$a->appendF($b);

				return $a->equals('Lorem ipsum, ipsum lorem.');
			}
		);
	}

	protected function prepend_tests ()
	{
		$this->should->throw(
			'Argument contract #prepend',
			'When passing arguments of type other than string or an instance of \Sauce\String, an InvalidArgumentException should be thrown.',
			'InvalidArgumentException',
			function () {
				$a = S('Lorem ipsum, lorem ipsum.');
				$a->prepend(10);
			}
		);

		$this->should->throw(
			'Argument contract #prepend',
			'When passing arguments of type other than string or an instance of \Sauce\String, an InvalidArgumentException should be thrown.',
			'InvalidArgumentException',
			function () {
				$a = S('Lorem ipsum, lorem ipsum.');
				$a->prepend(A());
			}
		);

		$this->should->assert(
			'#prepend should return an instance of \Sauce\String', '',
			function () {
				$a = S('Lorem ipsum, lorem ipsum.');
				$b = $a->prepend(' Lorem; ipsum');

				return $b instanceof \Sauce\String;
			}
		);

		$this->should->assert(
			'#prepend should return an instance of \Sauce\String holding the former and latter string combined', '',
			function () {
				$a = S('Lorem ipsum, lorem ipsum.');
				$b = $a->prepend('ipsum ipsum');

				return $b->equals('ipsum ipsumLorem ipsum, lorem ipsum.');
			}
		);

		$this->should->assert(
			'Given another instance of \Sauce\String, #prepend should combine those two and return a new instance of \Sauce\String holding the former and the latter strings combined.', '',
			function () {
				$a = S('Lorem ipsum');
				$b = S(', ipsum lorem.');
				$c = $a->prepend($b);

				return $c->equals(', ipsum lorem.Lorem ipsum');
			}
		);
	}

	protected function prependF_tests ()
	{
		$this->should->throw(
			'Argument contract #prependF',
			'When passing arguments of type other than string or an instance of \Sauce\String, an InvalidArgumentException should be thrown.',
			'InvalidArgumentException',
			function () {
				$a = S('Lorem ipsum, lorem ipsum.');
				$a->prependF(10);
			}
		);

		$this->should->throw(
			'Argument contract #prependF',
			'When passing arguments of type other than string or an instance of \Sauce\String, an InvalidArgumentException should be thrown.',
			'InvalidArgumentException',
			function () {
				$a = S('Lorem ipsum, lorem ipsum.');
				$a->prependF(A());
			}
		);

		$this->should->assert(
			'#prependF should return the same instance of \Sauce\String (this)', '',
			function () {
				$a = S('Lorem ipsum, lorem ipsum.');
				$b = $a->prependF(' Lorem; ipsum');

				return $b === $a;
			}
		);

		$this->should->assert(
			'#prependF should prepend the given string to the internally stored string', '',
			function () {
				$a = S('Lorem ipsum, lorem ipsum.');
				$a->prependF('ipsum ipsum');

				return $a->equals('ipsum ipsumLorem ipsum, lorem ipsum.');
			}
		);

		$this->should->assert(
			'Given another instance of \Sauce\String, #prependF should prepend it to the internally stored string.', '',
			function () {
				$a = S('Lorem ipsum');
				$b = S(', ipsum lorem.');
				$a->prependF($b);

				return $a->equals(', ipsum lorem.Lorem ipsum');
			}
		);
	}

	protected function trim_tests ()
	{
		$this->should->throw(
			'Argument contract #trim',
			'When passing arguments of type other than string or an instance of \Sauce\String, an InvalidArgumentException should be thrown.',
			'InvalidArgumentException',
			function () {
				$a = S('Lorem ipsum, lorem ipsum.');
				$a->trim(10);
			}
		);

		$this->should->throw(
			'Argument contract #trim',
			'When passing arguments of type other than string or an instance of \Sauce\String, an InvalidArgumentException should be thrown.',
			'InvalidArgumentException',
			function () {
				$a = S('Lorem ipsum, lorem ipsum.');
				$a->trim(A());
			}
		);

		$this->should->assert(
			'#trim should return an instance of \Sauce\String', '',
			function () {
				$a = S('Lorem ipsum, lorem ipsum.');
				$b = $a->trim(' Lorem; ipsum');

				return $b instanceof \Sauce\String;
			}
		);

		$this->should->assert(
			'When given no argument, #trim should remove all whitespaces from the beginning and end of the stored string', '',
			function () {
				$a = S('  Lorem ipsum                      ');
				$b = $a->trim();

				return $b->equals('Lorem ipsum');
			}
		);

		$this->should->assert(
			'When given a string as argument, #trim should remove all those characters in the string from the beginning and end of the stored string.', '',
			function () {
				$a = S('!@#$%^&*()_-Lorem Ipsum    ');
				$b = $a->trim('!@#$%^&*()_- ');

				return $b->equals('Lorem Ipsum');
			}
		);
	}

	protected function trimF_tests ()
	{
		$this->should->throw(
			'Argument contract #trimF',
			'When passing arguments of type other than string or an instance of \Sauce\String, an InvalidArgumentException should be thrown.',
			'InvalidArgumentException',
			function () {
				$a = S('Lorem ipsum, lorem ipsum.');
				$a->trimF(10);
			}
		);

		$this->should->throw(
			'Argument contract #trimF',
			'When passing arguments of type other than string or an instance of \Sauce\String, an InvalidArgumentException should be thrown.',
			'InvalidArgumentException',
			function () {
				$a = S('Lorem ipsum, lorem ipsum.');
				$a->trimF(A());
			}
		);

		$this->should->assert(
			'#trimF should return the same instance of \Sauce\String (this)', '',
			function () {
				$a = S('Lorem ipsum, lorem ipsum.');
				$b = $a->trimF(' Lorem; ipsum');

				return $b === $a;
			}
		);

		$this->should->assert(
			'When given no argument, #trim should remove all whitespaces from the beginning and end of the stored string', '',
			function () {
				$a = S('  Lorem ipsum                      ');
				$a->trimF();

				return $a->equals('Lorem ipsum');
			}
		);

		$this->should->assert(
			'When given a string as argument, #trim should remove all those characters in the string from the beginning and end of the stored string.', '',
			function () {
				$a = S('!@#$%^&*()_-Lorem Ipsum    ');
				$a->trimF('!@#$%^&*()_- ');

				return $a->equals('Lorem Ipsum');
			}
		);
	}

	protected function split_tests ()
	{
		$this->should->throw(
			'Argument contract #split',
			'When passing arguments of type other than string or an instance of \Sauce\String, an InvalidArgumentException should be thrown.',
			'InvalidArgumentException',
			function () {
				$a = S('Lorem ipsum, lorem ipsum.');
				$a->split(10);
			}
		);

		$this->should->throw(
			'Argument contract #split',
			'When passing arguments of type other than string or an instance of \Sauce\String, an InvalidArgumentException should be thrown.',
			'InvalidArgumentException',
			function () {
				$a = S('Lorem ipsum, lorem ipsum.');
				$a->split(A());
			}
		);

		$this->should->assert(
			'#split should return an instance of \Sauce\Vector', '',
			function () {
				$a = S('Lorem ipsum, lorem ipsum.');
				$b = $a->split();

				return $b instanceof \Sauce\Vector;
			}
		);

		$this->should->assert(
			'#split should return an instance of \Sauce\Vector holding the seperate strings', '',
			function () {
				$a = S('Lorem ipsum, lorem ipsum.');
				$b = $a->split();

				return
					$b->count() === 4 &&
					$b[0] === 'Lorem' &&
					$b[1] === 'ipsum,' &&
					$b[2] === 'lorem' &&
					$b[3] === 'ipsum.';
			}
		);
	}

	protected function to_lines_tests ()
	{
		$this->should->assert(
			'#to_lines should return an instance of \Sauce\Vector', '',
			function () {
				$a = S("Lorem ipsum,\nlorem ipsum.");
				$b = $a->to_lines();

				return $b instanceof \Sauce\Vector;
			}
		);

		$this->should->assert(
			'#to_lines should return an instance of \Sauce\Vector holding the seperate lines', '',
			function () {
				$a = S("Lorem ipsum,\nlorem ipsum.\nLorem ipsum!");
				$b = $a->to_lines();

				return
					$b->count() === 3 &&
					$b[0] === 'Lorem ipsum,' &&
					$b[1] === 'lorem ipsum.' &&
					$b[2] === 'Lorem ipsum!';
			}
		);
	}
}

?>
