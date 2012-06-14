<?php

/**
   Copyright 2012 Brainsware

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

namespace Sauce\Traits;

/**
 * TODO: Document what this trait does.
 */

trait Immutable
{
	public function offsetSet   ($key, $value) { $this->__deny_access(); }
	public function offsetUnset ($key)         { $this->__deny_access(); }
	public function __set       ($key, $value) { $this->__deny_access(); }
	public function __unset     ($key)         { $this->__deny_access(); }
	
	private function __deny_access ()
	{
		throw new \LogicException('This object is immutable.');
	}
}

?>
