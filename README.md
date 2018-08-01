Sauce is a general purpose library providing containers and functions
that ease development in PHP.

Currently Sauce is written and tested for PHP 5.4.x.

Provided classes/traits:

* `Sauce\SObject` - A simple key-value store
* `Sauce\Vector` - A classic array with ordered integer keys
* `Sauce\String` - A simple string representation with utility methods
* `Sauce\Immutable` - An abstract class implementing `ArrayAccess` but denying access to any set methods
* `Sauce\ImmutableObject` - A version of `Object` implemening `Immutable`
* `Sauce\CallableProperty` - A trait implementing `__call` which allows calling a property holding a function - if callable
* `Sauce\AwareObject` -  A version of `Object` storing all keys that have been changed
* `Sauce\Path` - A simple path representation with utility methods 
* `Sauce\DateTime` - A class extending the original `DateTime` class with sane default formats
* `Sauce\CliColors` - A class for making colored CLI output.

Provided global functions:

* `dump()` - Replacement for `var_dump()` that can take arbitrary data; when not on the command line, this function will wrap the result in a `<pre>` tag.
* `sdump()` - Replacement for `var_export($value, true)`
* `V()` - Create a new `Vector` from given data
* `A()` - Create a new `Object` from given data
* `Ar()` - Recursively create `Object` instances from given data (arrays in arrays will be also instantiated as `Object`)
* `S()` - Create a `String` instance from given string
* `Vs()` - Creates a `Vector` instance with given strings, pushing each argument as `String` instance onto the Vector.
* `ensure()` - Define a function/method contract, throws an InvalidArgumentException when contract is not fulfilled. See comments on that function for documentation.
* `is_not_null()` - Check whether given data is not null
* `is_an_array()` - Check whether given data is an array or an instance of any class extending `Object` or implementing `ArrayAccess` 
* `is_a_string()` - Check whether given data is a string or an instance of `String`
* `is_cli()` - Check whether the current environment is the command line interface or CGI/mod\_php
* `is_cli_server()` - Check whether the application server running PHP is the built-in server.
* `split_uri()` - Split a string by `/` and remove empty leading/trailing strings
* `path_info()` - Gather PHP's `PATH_INFO` or build it from `SCRIPT_NAME` and `REQUEST_URI`, removes `GET` parameters if present.
* `http_method()` - Returns method reported by the server (`REQUEST_METHOD`), but overrides it if a `_method` parameters was sent with the request. (Useful for resource/CRUD controllers)
* `or_equals()` - Check whether given variable is set and not null, basically mimicking Ruby's or-equals operator (`||=`)
* `has_method($object, $method)` - Check whether or not given object or class has a given method defined.
