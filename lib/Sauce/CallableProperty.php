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

namespace Sauce;

/* CallableProperty implements the magic method __call to catch all calls to
 * not actually implemented methods. It then searches for a callable property
 * (a closure stored in a property) with the same name. If it finds one, it
 * returns the result of the call.
 *
 * If the property is not callable or not found, a BadMethodCallException is
 * thrown.
 */
trait CallableProperty
{
	public function __call ($method, $args)
	{
		if (isset($this->$method) && is_callable($this->$method)) {
			$closure = \Closure::bind($this->$method, $this, get_class());

			return call_user_func_array(
				$closure,
				$args
			);
		}

		$args = V($args);

		throw new \BadMethodCallException("Method not found: $method (" . $args->join(', ') . ")");
	}
}

?>
