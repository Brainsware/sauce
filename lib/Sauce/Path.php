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

class Path
{
	const delimiter = '/';

	/* Join any number of given path fragments with the default directory
	 * delimiter.
	 *
	 * Example:
	 *
	 * 	$path = \Sauce\Path::join(APP_DIR, 'htdocs/scripts', $some_name);
	 * 	# => '/srv/web/app/htdocs/scripts/some_name'
	 */
	public static function join ()
	{
		$args = func_get_args();

		if (empty($args)) { return ''; }

		$paths = new Vector();

		foreach ($args as $arg) {
			if (is_string($arg)) {
				$arg = explode(self::delimiter, $arg);
			}

			if (is_an_array($arg)) {
				foreach ($arg as $fragment) {
					$paths->push($fragment);
				}
				continue;
			}

			$paths->push($arg);
		}

		$paths = $paths->select(function ($path) {
			$str = strval($path);

			return !empty($str);
		});

		$joined_path = $paths->join(self::delimiter);

		if ($args[0][0] === self::delimiter) {
			$joined_path->prependF(self::delimiter);
		}

		return $joined_path;
	}

    /**
     * Checks a file or directory existence and readability
     *
     * @param string $path /path/to/dir/or/file
     * @param string $fd Either 'f' for file or 'd' for directory.
     * @param string $rw Either 'w' for a check on writability or 'r' for a check on readability.
     *
     * @return bool
     */
    public static function check ($path, $fd, $rw)
    {
        if ($fd == 'f') {
            if (is_file($path) != true) {
                return false;
            }
        } elseif ($fd == 'd') {
            if (is_dir($path) != true) {
                return false;
            }
        } else {
            return false;
        }

        if ($rw == 'r') {
            if (!is_readable($path)) {
                return false;
            }
        } elseif ($rw == 'w') {
            if (!is_writable($path)) {
                return false;
            }
        } elseif ($rw == 'rw') {
            if (!is_readable($path) || !is_writable($path)) {
                return false;
            }
        } else {
            return false;
        }

        return true;
    }

    /* Delete all contents of a given directory.
     *
     * @param string $dir Directory.
     * @return bool
     */
    public static function truncate_directory ($dir)
    {
        if (substr($dir, strlen($dir)-1, 1) != '/') {
            $dir .= '/';
        }

        if (!$fileList = scandir ($dir)) {
            return false;
        }

        foreach ($fileList as $fileInList) {
            if (is_dir($dir . $fileInList) && $fileInList != '.' && $fileInList != '..') {
                // Some more directory. Recurse!
                if (!self::rmrf($dir . $fileInList . '/')) {
                    // If false, Error message was already given by rmrf.
                    return false;
                }
            } elseif (is_file($dir . $fileInList)) {
                if (!unlink ($dir . $fileInList)) {
                    return false;
                }
            }
        }

        return true;
    }

    /* Completely removes a directory and all its contents.
     *
     * @param string $dir Directory.
     * @return bool
     */
    public static function rmrf ($dir)
    {
        self::truncate_directory($dir);

        return @rmdir($dir);
    }

	/* List and filter contents given absolute path.
	 * 
	 * Takes an absolute path and an optional filter method.
	 * By default, all non-hidden entries are returned.
	 */

	public static function ls ($path, $fn = null)
	{
		if (!static::is_absolute($path)) {
			throw new \Exception('Supplied path is not absolute.');
		}

		if ($fn === null) {
			$fn = function ($entry) {
				return substr($entry, 0, 1) != '.';
			};
		}

		$entries = scandir($path);

		return V($entries)->select($fn);
	}

	/* Returns whether or not the given path is absolute.
	 *
	 * Works on Unix/Linux and Windows platforms.
	 */
	public static function is_absolute ($path)
	{
		return preg_match("/^(?:\/|\\|\w\:\\\).*$/", $path) === 1;
	}

	/* Proxy method for PHP's pathinfo function.
	 *
	 * This was added to have an Object instance as return value.
	 * There might also be more info added in the future.
	 */
	public static function info ($path)
	{
		return Ar(pathinfo($path));
	}
}

?>
