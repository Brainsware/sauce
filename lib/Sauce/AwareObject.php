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

/*	AwareObject keeps track of changed properties and stores the keys of all
 *	changed properties in a Vector object. That means any changes to the
 *	data passed in to the constructor (even if you don't pass any data) is
 *	recorded.
 *
 *	This has been mainly implemented to be used in ORM implementations, so only
 *	the changed data is actually written back to the database.
 *
 *	To retrieve the list of changed properties, call #changed.
 */
class AwareObject extends Object
{
	protected $changed_properties;

	public function __construct ($data = [], $recursive = false)
	{
		$this->changed_properties = new Vector();

		parent::__construct($data, $recursive);
	}

	public function offsetSet ($key, $value)
	{
		$this->changed_properties->push($key);

		return parent::offsetSet($key, $value);
	}

	public function offsetUnset ($key)
	{
		$this->changed_properties->push($key);

		return parent::offsetUnset($key);
	}

	/* Retrieve the list of changed properties. */
	public function changed ()
	{
		return new Vector($this->changed_properties);
	}
}

?>
