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
			'String instance stored internally',
			'Passing a String instance to __construct copies over the stored string',
			function () {
				$string = 'abc';

				$s1 = new \Sauce\String($string);
				$s2 = new \sauce\String($s1);

				return $s2->to_s() === $string;
			}
		);

	}
}

?>
