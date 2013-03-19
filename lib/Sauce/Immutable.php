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

namespace Sauce;

/* Immutable is an abstract class implementing ArrayAccess' methods each
 * raising a LogicException. This ensures any instance of a class extending
 * Immutable is not changable via array indexing or property setting.
 */
abstract class Immutable implements \ArrayAccess
{
	public function offsetSet   () { $this->deny_access(); }
	public function offsetUnset () { $this->deny_access(); }
	public function __set       ($name, $value) { $this->deny_access(); }
	
	private function deny_access ()
	{
		throw new \LogicException('This object is immutable.');
	}
}

?>
