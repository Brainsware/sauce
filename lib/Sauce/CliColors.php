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

/* Source: https://gist.github.com/donatj/1315354 */

namespace Sauce;
 
class CliColors
{
	public static $foreground_colors = [
		'black'        => '0;30',
		'dark_gray'    => '1;30',
		'blue'         => '0;34',
		'light_blue'   => '1;34',
		'green'        => '0;32',
		'light_green'  => '1;32',
		'cyan'         => '0;36',
		'light_cyan'   => '1;36',
		'red'          => '0;31',
		'light_red'    => '1;31',
		'purple'       => '0;35',
		'light_purple' => '1;35',
		'brown'        => '0;33',
		'yellow'       => '1;33',
		'light_gray'   => '0;37',
		'white'        => '1;37'
	];

	public static $background_colors = [
		'black'      => '40',
		'red'        => '41',
		'green'      => '42',
		'yellow'     => '43',
		'blue'       => '44',
		'magenta'    => '45',
		'cyan'       => '46',
		'light_gray' => '47'
	];

	public static $options = [
		'bold'      => '1',
		'underline' => '4',
		'blink'     => '5',
		'hidden'    => '8'
	];

	/* https://gist.github.com/donatj/1315354 */
	public static function __callStatic ($foreground_color, $args)
	{
		$string = $args[0];		
		$colored_string = "";
 
		// Check if given foreground color found
		if (isset(self::$foreground_colors[$foreground_color])) {
			$colored_string .= "\033[" . self::$foreground_colors[$foreground_color] . "m";
		} else {
			throw new \Exception ('"' . $foreground_color . '" is not a valid color');
		}
		
		array_shift($args);

		foreach ($args as $option){
			// Check if given background color found
			if (isset(self::$background_colors[$option])) {
				$colored_string .= "\033[" . self::$background_colors[$option] . "m";
			} elseif (isset(self::$options[$option])) {
				$colored_string .= "\033[" . self::$options[$option] . "m";
			}
		}
		
		// Add string and end coloring
		$colored_string .= $string . "\033[0m";
		
		return $colored_string;
	}
}
 
?>
