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

/* This class adds two convenient methods to the original DateTime class:
 * #now and #db_format.
 */
class DateTime extends \DateTime
{
	/* Returns the current time/date (NOW) as new DateTime object.
	 *
	 * If no format is given, the default format is used: 'Y-m-d H:i:s'.
	 */
	public static function now($format = '')
	{
		$now = new self();

		return $now->format(empty($format) ? 'Y-m-d H:i:s' : $format);
	}

	/* Returns the stored time in a (default) format usable for databases.
	 *
	 * If format is given, the default format is used: 'Y-m-d H:i:s'
	 */
	public function db_format ($format = 'Y-m-d H:i:s')
	{
		return $this->format($format);
	}
}

?>
