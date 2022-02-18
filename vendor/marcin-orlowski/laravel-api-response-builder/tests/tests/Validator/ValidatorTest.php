<?php

namespace MarcinOrlowski\ResponseBuilder\Tests;

/**
 * Laravel API Response Builder
 *
 * @package   MarcinOrlowski\ResponseBuilder
 *
 * @author    Marcin Orlowski <mail (#) marcinOrlowski (.) com>
 * @copyright 2016-2020 Marcin Orlowskis
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      https://github.com/MarcinOrlowski/laravel-api-response-builder
 */

use MarcinOrlowski\ResponseBuilder\Validator;

class ValidatorTest extends TestCase
{
    /**
     * Tests if assertIsInt() pass if given valid data.
     *
     * @return void
     */
    public function testAssertIsIntCorrectType(): void
    {
        Validator::assertIsInt(__FUNCTION__, 666);
        // This assert won't be called if exception is thrown
        $this->assertTrue(true);
    }

    /**
     * Tests if assertIsInt() throws exception when feed with invalid type argument.
     *
     * @return void
     */
    public function testAssertIsIntWrongType(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Validator::assertIsInt(__FUNCTION__, 'chicken');
    }

    // -----------------------------------------------------------------------------------------------------------

    /**
     * Tests if assertIsObject() pass if given valid data.
     *
     * @return void
     */
    public function testAssertIsObjectCorrectType(): void
    {
        $obj = new \StdClass();
        Validator::assertIsObject(__FUNCTION__, $obj);
        // This assert won't be called if exception is thrown
        $this->assertTrue(true);
    }

    /**
     * Tests if assertIsObject() throws exception when feed with invalid type argument.
     *
     * @return void
     */
    public function testAssertIsObjectWrongType(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Validator::assertIsObject(__FUNCTION__, 'chicken');
    }

    // -----------------------------------------------------------------------------------------------------------

    /**
     * Checks behavior of assertIsArray() with valid data
     */
    public function testAssertIsArrayWithValidData(): void
    {
        Validator::assertIsArray(__FUNCTION__, []);
        // This assert won't be called if exception is thrown
        $this->assertTrue(true);
    }

    /**
     * Checks behavior of assertIsArray() with invalid data
     */
    public function testAssertIsArrayWithInvalidData(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Validator::assertIsArray(__FUNCTION__, false);
    }

    // -----------------------------------------------------------------------------------------------------------

    /**
     * Tests if assertIsString() pass with valid data type
     *
     * @return void
     */
    public function testAssertIsStringCorrectType(): void
    {
        Validator::assertIsString(__FUNCTION__, 'string');
        // This assert won't be called if exception is thrown
        $this->assertTrue(true);
    }

    /**
     * Tests if assertIsString() throws exception when feed with invalid type argument.
     *
     * @return void
     */
    public function testAssertIsStringWrongType(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Validator::assertIsString(__FUNCTION__, 666);
    }

    // -----------------------------------------------------------------------------------------------------------

    /**
     * Tests if assertIsBool() pass with valid data type
     *
     * @return void
     */
    public function testAssertIsBoolCorrectType(): void
    {
        Validator::assertIsBool(__FUNCTION__, false);
        // This assert won't be called if exception is thrown
        $this->assertTrue(true);
    }

    /**
     * Tests if assertIsBool() throws exception when feed with invalid type argument.
     *
     * @return void
     */
    public function testAssertIsBoolWrongType(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Validator::assertIsBool(__FUNCTION__, 666);
    }

    // -----------------------------------------------------------------------------------------------------------

    public function testAssertIsIntRangeWithValidData(): void
    {
        Validator::assertIsIntRange(__FUNCTION__, 300, 100, 500);
        // This assert won't be called if exception is thrown
        $this->assertTrue(true);
    }

    /**
     * Check if assertIntRange() main variable type is ensured to be integer.
     *
     * @return void
     */
    public function testAssertIsIntRangeVarType(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Validator::assertIsIntRange(__FUNCTION__, 'string', 100, 200);
    }

    /**
     * Check if assertIntRange() range $min and $max are in right order.
     *
     * @return void
     */
    public function testAssertIsIntRangeMinMaxOrder(): void
    {
        $this->expectException(\RuntimeException::class);
        Validator::assertIsIntRange(__FUNCTION__, 300, 500, 200);
    }

    /**
     * Check if assertIntRange() to ensure we check $var is in range nd $max bounds only
     *
     * @return void
     */
    public function testAssertIsIntRangeVarInMinMaxRangeWithDataOutOfRange(): void
    {
        // ensure main variable is an integer
        $this->expectException(\InvalidArgumentException::class);
        Validator::assertIsIntRange(__FUNCTION__, 100, 300, 500);
    }

    // -----------------------------------------------------------------------------------------------------------

    /**
     * Checks if assertInstanceOf() would throw exception if obj is not instance of given class
     */
    public function testAssertInstanceOfInvalidClass(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $obj = new \StdClass();
        Validator::assertInstanceOf('obj', $obj, \JsonSerializable::class);
    }

    /**
     * Checks if assertInstanceOf() would pass if obj is instance of given class
     */
    public function testAssertInstanceOfValidClass(): void
    {
        $obj = new \StdClass();
        Validator::assertInstanceOf('obj', $obj, \StdClass::class);
        // This assert won't be called if exception is thrown
        $this->assertTrue(true);
    }

    // -----------------------------------------------------------------------------------------------------------

    /**
     * Tests assertIsType() helper.
     */
    public function testAssertTypeWithVariousData(): void
    {
        /**
         * Test data. Each entry is an array with a following keys:
         *   item    : value to be tested or array of values from which one value will be randomly picked for testing.
         *   types   : array of allowed `Validator::TYPE_xxx` types
         *   expected: @false if test is expected to fail (type of `item` is not in `types`), @true if it should pass.
         */
        $test_data = [
            [
                'item'     => false,
                'types'    => [Validator::TYPE_STRING],
                'expected' => false,
            ],
            [
                'item'     => false,
                'types'    => [Validator::TYPE_BOOL],
                'expected' => true,
            ],
            [
                'item'     => 'foo',
                'types'    => [Validator::TYPE_STRING],
                'expected' => true,
            ],
            [
                'item'     => 23,
                'types'    => [Validator::TYPE_STRING],
                'expected' => false,
            ],
            [
                'item'     => 666,
                'types'    => [Validator::TYPE_INTEGER],
                'expected' => true,
            ],
            [
                'item'     => 'fail',
                'types'    => [Validator::TYPE_INTEGER,
                               Validator::TYPE_BOOL],
                'expected' => false,
            ],

        ];

        foreach ($test_data as $key => $data) {
            $label = sprintf('test_data[%d]', $key);

            $test_passed = true;
            try {
                Validator::assertIsType($label, $data['item'], $data['types']);
            } catch (\Exception $ex) {
                $test_passed = false;
            }

            $msg = sprintf('Entry #%d: testing if "%s" (%s) is one of these: %s.',
                $key, $data['item'], gettype($data['item']), implode(', ', $data['types']));
            $this->assertEquals($test_passed, $data['expected'], $msg);
        }
    }

}
