![REST API Response Builder for Laravel](img/logo.png)

# Configuration file #
 If you want to change `ResponseBuilder` default configuration you need to use config file. Use package provided configuration
 template and publish `response_builder.php` configuration template file to your `config/` folder:

    php artisan vendor:publish

 If you are fine with the defaults, this step can safely be omitted. You can also remove published `config/response_builder.php`
 file if exists.

# Configuration options #

 Available configuration options and its current default values listed in alphabetical order. Please note, that in majority
 of use cases it should be perfectly sufficient to just use defaults and only tune the config when needed.
 
 * [converter](#converter)
 * [debug](#debug)
 * [encoding_options](#encoding_options)
 * [exception_handler](#exception_handler)
 * [map](#map)
 * [min_code](#min_code)
 * [max_code](#max_code)

## classes ##
 
`Response Builder` can auto-convert to be used as response `data`. The following classes are supported out of the
box:

 * `\Illuminate\Database\Eloquent\Model`          
 * `\Illuminate\Support\Collection`               
 * `\Illuminate\Database\Eloquent\Collection`     
 * `\Illuminate\Http\Resources\Json\JsonResource` 

Create new entry for each class you want to have supported. The entry key is a full class name (including namespace):

```php
'converter' => [
    Namespace\Classname::class => [
        'handler' => \MarcinOrlowski\ResponseBuilder\Converters\ToArrayConverter::class,
        'key'     => 'items',
        
        // Optional paramters
        'pri'    => 0, 
        ],
],
```
The `handler` is full name of the class that implements `ConverterContract`. Object of that class will be instantiated
and conversion method will be invked. The `key` is a string that will be used as the JSON response as key to array representation.

All configuration entries are assigned priority `0` which can be changed using `pri` key (integer). This value is used to
sort the entries to ensure that matching order is preserved. Entries with higher priority are matched first etc. This is
very useful when you want to indirect configuration for two classes where additionally second extends first one. 
So if you have class `A` and `B` that extends `A` and you want different handling for `B` than you have set for `A` 
then `B` related configuration must be set with higher priority.

See [Data Conversion](docs.md#data-conversion) docs for closer details wih examples.
 
## debug ##

```php
'debug' => [
    'debug_key' => 'debug',

    'exception_handler' => [
           'trace_key' => 'trace',
           'trace_enabled' => env('APP_DEBUG', false),
    ],
],
```

`debug_key` - name of the JSON key trace data should be put under when in `debug` node.

	/**
	 * When ExceptionHandler kicks in and this is set to @true,
	 * then returned JSON structure will contain additional debug data
	 * with information about class name, file name and line number.
	 */

```json
{
    "success": false,
    "code": 0,
    "locale": "en",
    "message": "Uncaught Exception",
    "data": null,
    "debug": {
        "trace": {
            "class": "<EXCEPTION CLASS NAME>",
            "file": "<FILE THAT CAUSED EXCEPTION>",
            "line": "<LINE NUMBER>"
        }
    }
}
```
## encoding_options ##

 This option controls data JSON encoding. Since v3.1, encoding was relying on framework's defaults, however this
 caused valid UTF-8 characters (i.e. accents) to be returned escaped, which, while technically correct,
 and theoretically transparent) might not be desired.

 To prevent escaping, add JSON_UNESCAPED_UNICODE:
 
     JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_AMP|JSON_HEX_QUOT|JSON_UNESCAPED_UNICODE

 Laravel's default value:
 
    JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_AMP|JSON_HEX_QUOT

 See [json_encode() manual](http://php.net/manual/en/function.json-encode.php) for more details.

## exception_handler ##

 `ResponseBuilder`'s Exception handler helper is plug-and-play helper that will automatically handle
 any exception thrown by your code and expose valid JSON response to the client applications. But aside
 from error handling, some programmers use exceptions to quickly break the flow and return with additional
 information. In such case you may want to assign separate API code to each of these "special" exceptions
 and this is where `exception_handler` section comes in.
 
 Each configuration entry consits of exception class name as a key and parameters array with fields
 `api_code` and `http_code`. At runtime, exception handler will look for config entry for particualr
 exception class and if there's one, proper handler, dedicated to that exception class, kicks in
 and deals with the exception. If no such config exists, `default` handler will be used.

 **NOTE:** For now there's no option to specify custom converted as of yet (but that's next step anywya), 
 so adding own classes to the config same way we did for 
 `\Symfony\Component\HttpKernel\Exception\HttpException::class` won't work.

## map ##

`ResponseBuilder` can automatically use text error message associated with error code and return in the
response, once its configured to know which string to use for which code. `ResponseBuilder` uses standard
Laravel's `Lang` facade to process strings.

```php
'map' => [
	ApiCode::SOMETHING => 'api.something',
	...
],
```
	
See [Exception Handling with Response Builder](docs/exceptions.md) if you want to provide own messages for built-in codes.

## min_code ##

 This option defines lowest allowed (inclusive) code that can be used.

 NOTE ResponseBuilder reserves first 19 codes for its own needs. First code you can use is 20th code in your pool.

```php
'min_code' => 100,
```

## max_code ##

 Min api code in assigned for this module (inclusive)
 This option defines highest allowed (inclusive) code that can be used.

```php
'max_code' => 1024,
```
