<?php
declare(strict_types=1);

namespace MarcinOrlowski\ResponseBuilder;

/**
 * Laravel API Response Builder
 *
 * @package   MarcinOrlowski\ResponseBuilder
 *
 * @author    Marcin Orlowski <mail (#) marcinOrlowski (.) com>
 * @copyright 2016-2020 Marcin Orlowski
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      https://github.com/MarcinOrlowski/laravel-api-response-builder
 */
class Validator
{
    /** @var string */
    public const TYPE_STRING = 'string';

    /** @var string */
    public const TYPE_INTEGER = 'integer';

    /** @var string */
    public const TYPE_BOOL = 'boolean';

    /** @var string */
    public const TYPE_ARRAY = 'array';

    /** @var string */
    public const TYPE_OBJECT = 'object';

    /** @var string */
    public const TYPE_NULL = 'NULL';

    /**
     * Checks if given $val is of type boolean
     *
     * @param string $key Name of the key to be used if exception is thrown.
     * @param mixed  $var Variable to be asserted.
     *
     * @return void
     *
     * @throws \InvalidArgumentException
     */
    public static function assertIsBool(string $key, $var): void
    {
        self::assertIsType($key, $var, [self::TYPE_BOOL]);
    }

    /**
     * Checks if given $val is of type integer
     *
     * @param string $key Name of the key to be used if exception is thrown.
     * @param mixed  $var Variable to be asserted.
     *
     * @return void
     *
     * @throws \InvalidArgumentException
     */
    public static function assertIsInt(string $key, $var): void
    {
        self::assertIsType($key, $var, [self::TYPE_INTEGER]);
    }

    /**
     * Checks if given $val is of type array
     *
     * @param string $key Name of the key to be used if exception is thrown.
     * @param mixed  $var Variable to be asserted.
     *
     * @return void
     *
     * @throws \InvalidArgumentException
     */
    public static function assertIsArray(string $key, $var): void
    {
        self::assertIsType($key, $var, [self::TYPE_ARRAY]);
    }

    /**
     * Checks if given $val is an object
     *
     * @param string $key Name of the key to be used if exception is thrown.
     * @param mixed  $var Variable to be asserted.
     *
     * @return void
     *
     * @throws \InvalidArgumentException
     */
    public static function assertIsObject(string $key, $var): void
    {
        self::assertIsType($key, $var, [self::TYPE_OBJECT]);
    }

    /**
     * Checks if given $val is of type string
     *
     * @param string $name Label or name of the variable to be used in exception message (if thrown).
     * @param mixed  $var  Variable to be asserted.
     *
     * @return void
     *
     * @throws \InvalidArgumentException
     */
    public static function assertIsString(string $name, $var): void
    {
        self::assertIsType($name, $var, [self::TYPE_STRING]);
    }

    /**
     * @param string $name Label or name of the variable to be used in exception message (if thrown).
     * @param mixed  $var  Variable to be asserted.
     * @param int    $min  Min allowed value (inclusive)
     * @param int    $max  Max allowed value (inclusive)
     *
     * @return void
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public static function assertIsIntRange(string $name, $var, int $min, int $max): void
    {
        self::assertIsInt($name, $var);

        if ($min > $max) {
            throw new \RuntimeException(
                sprintf('%s: Invalid range for "%s". Ensure bound values are not swapped.', __FUNCTION__, $name));
        }

        if (($min > $var) || ($var > $max)) {
            throw new \InvalidArgumentException(
                sprintf('Invalid value of "%s" (%d). Must be between %d-%d inclusive.', $name, $var, $min, $max));
        }
    }

    /**
     * Checks if $item (of name $key) is of type that is include in $allowed_types.
     *
     * @param string $name          Label or name of the variable to be used in exception message (if thrown).
     * @param mixed  $var           Variable to be asserted.
     * @param array  $allowed_types Array of allowed types for $var, i.e. [Validator::TYPE_INTEGER]
     *
     * @return void
     *
     * @throws \InvalidArgumentException
     */
    public static function assertIsType(string $name, $var, array $allowed_types): void
    {
        $type = gettype($var);
        if (!in_array($type, $allowed_types)) {
            throw new \InvalidArgumentException(
                sprintf('"%s" must be one of allowed types: %s (%s given)',
                    $name, implode(', ', $allowed_types), gettype($var))
            );
        }
    }

    /**
     * Ensures given $http_code is valid code for error response.
     *
     * @param int $http_code
     */
    public static function assertErrorHttpCode(int $http_code): void
    {
        self::assertIsInt('http_code', $http_code);
        self::assertIsIntRange('http_code', $http_code,
            ResponseBuilder::ERROR_HTTP_CODE_MIN, ResponseBuilder::ERROR_HTTP_CODE_MAX);
    }

    /**
     * Ensures given $http_code is valid for response indicating sucessful operation.
     *
     * @param int $http_code
     */
    public static function assertOkHttpCode(int $http_code): void
    {
        self::assertIsInt('http_code', $http_code);
        self::assertIsIntRange('http_code', $http_code, 200, 299);
    }

    /**
     * Ensures $obj is instance of $cls.
     *
     * @param string $name
     * @param object $obj
     * @param string $cls
     */
    public static function assertInstanceOf(string $name, object $obj, string $cls): void
    {
        if (!($obj instanceof $cls)) {
            throw new \InvalidArgumentException(
                sprintf('"%s" must be instance of "%s".', $name, $cls)
            );
        }
    }
}
