Sauce is a general purpose library providing containers and functions
that ease development in PHP.

Currently Sauce is written and tested for PHP 5.4.x.

Provided classes:

* `Sauce\Object`
* `Sauce\Vector`
* `Sauce\Immutable`
* `Sauce\ImmutableObject`
* `Sauce\CallableProperty`
* `Sauce\AwareObject`
* `Sauce\Path`
* `Sauce\DateTime`

Provided functions:

* `dump()` - Replacement for `var_dump()` that can take arbitrary data; when not on the command line, this function will wrap the result in a `<pre>` tag.
* `V()` - Create a new `Vector` from given data
* `A()` - Create a new `Object` from given data
* `Ar()` - Recursively create `Object` instances from given data (arrays in arrays will be also instantiated as `Object`)
* `is_an_array()` - Check whether given data is an array or an instance of any class extending `Obect` or implementing `ArrayAccess` 
* `is_cli()` - Check whether the current environment is the command line interface or CGI/mod\_php
* `split_uri()` - Split a string by `/` and remove empty leading/trailing strings
* `path_info()` - Gather PHP's `PATH_INFO` or build it from `SCRIPT_NAME` and `REQUEST_URI`, removes `GET` parameters if present.
* `http_method()` - Returns method reported by the server (`REQUEST_METHOD`), but overrides it if a `_method` parameters was sent with the request. (Useful for resource/CRUD controllers)
