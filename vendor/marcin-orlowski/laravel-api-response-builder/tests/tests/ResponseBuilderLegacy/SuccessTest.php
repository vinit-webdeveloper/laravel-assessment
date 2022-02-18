<?php

namespace MarcinOrlowski\ResponseBuilder\Tests\Legacy;

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

use MarcinOrlowski\ResponseBuilder\BaseApiCodes;
use MarcinOrlowski\ResponseBuilder\ResponseBuilderLegacy;
use MarcinOrlowski\ResponseBuilder\Tests\TestCase;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class SuccessTest extends TestCase
{
    /**
     * Check plain success() invocation
     *
     * @return void
     */
    public function testSuccess(): void
    {
        $this->response = ResponseBuilderLegacy::success();
        $j = $this->getResponseSuccessObject(BaseApiCodes::OK());

        $this->assertNull($j->data);
        $this->assertEquals(\Lang::get(BaseApiCodes::getCodeMessageKey(BaseApiCodes::OK())), $j->message);
    }

    /**
     * Tests success() behavior with different JSON encoding options used
     *
     * @return void
     */
    public function testSuccessEncodingOptions(): void
    {
        $test_string = 'ąćę';
        $test_string_escaped = $this->escape8($test_string);

        // source data
        $data = ['test' => $test_string];

        // Checks if it gets returned in escaped form.
        // Ensure config is different from what we want.
        \Config::set('encoding_options', JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE);

        $encoding_options = JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT;
        $resp = ResponseBuilderLegacy::success($data, BaseApiCodes::OK(), null, null, $encoding_options);

        $matches = [];
        $this->assertNotEquals(0, preg_match('/^.*"test":"(.*)".*$/', $resp->getContent(), $matches));
        $result_escaped = $matches[1];
        $this->assertEquals($test_string_escaped, $result_escaped);


        // check if it returns unescaped
        // ensure config is different from what we want
        \Config::set('encoding_options', JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);

        $encoding_options = JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE;
        $resp = ResponseBuilderLegacy::success($data, BaseApiCodes::OK(), null, null, $encoding_options);

        $matches = [];
        $this->assertNotEquals(0, preg_match('/^.*"test":"(.*)".*$/', $resp->getContent(), $matches));
        $result_unescaped = $matches[1];
        $this->assertEquals($test_string, $result_unescaped);

        // this one is just in case...
        $this->assertNotEquals($result_escaped, $result_unescaped);
    }

    /**
     * Tests success() with custom API code no custom message
     *
     * @return void
     */
    public function testSuccessApiCodeNoCustomMessage(): void
    {
        \Config::set(ResponseBuilderLegacy::CONF_KEY_MAP, []);
        $this->response = ResponseBuilderLegacy::success(null, $this->random_api_code);
        $j = $this->getResponseSuccessObject($this->random_api_code);

        $this->assertNull($j->data);
    }

    /**
     * Tests success() with custom API code and no custom message mapping
     *
     * @return void
     */
    public function testSuccessApiCodeCustomMessage(): void
    {
        $this->response = ResponseBuilderLegacy::success(null, $this->random_api_code);
        $j = $this->getResponseSuccessObject($this->random_api_code);

        $this->assertNull($j->data);
    }


    /**
     * Tests success() with custom API code and custom message
     *
     * @return void
     */
    public function testSuccessApiCodeCustomMessageLang(): void
    {
        // for simplicity let's reuse existing message that is using placeholder
        \Config::set(ResponseBuilderLegacy::CONF_KEY_MAP, [
            $this->random_api_code => BaseApiCodes::getCodeMessageKey(BaseApiCodes::NO_ERROR_MESSAGE()),
        ]);

        $lang_args = [
            'api_code' => $this->getRandomString('foo'),
        ];

        $this->response = ResponseBuilderLegacy::success(null, $this->random_api_code, $lang_args);
        $expected_message = \Lang::get(BaseApiCodes::getCodeMessageKey($this->random_api_code), $lang_args);
        $j = $this->getResponseSuccessObject($this->random_api_code, null, $expected_message);

        $this->assertNull($j->data);
    }


    /**
     * Tests successWithCode() with custom API code and custom message
     *
     * @return void
     */
    public function testSuccessWithCodeApiCodeCustomMessageLang(): void
    {
        // for simplicity let's reuse existing message that is using placeholder
        /** @noinspection PhpUndefinedClassInspection */
        \Config::set(ResponseBuilderLegacy::CONF_KEY_MAP, [
            $this->random_api_code => BaseApiCodes::getCodeMessageKey(BaseApiCodes::NO_ERROR_MESSAGE()),
        ]);

        $lang_args = [
            'api_code' => $this->getRandomString('foo'),
        ];

        $this->response = ResponseBuilderLegacy::successWithCode($this->random_api_code, $lang_args);
        $expected_message = \Lang::get(BaseApiCodes::getCodeMessageKey($this->random_api_code), $lang_args);
        $j = $this->getResponseSuccessObject($this->random_api_code, null, $expected_message);

        $this->assertNull($j->data);
    }

    /**
     * Checks success() with valid payload types and HTTP code
     *
     * @return void
     */
    public function testSuccessDataAndHttpCode(): void
    {
        $payloads = [
            null,
            [$this->getRandomString() => $this->getRandomString()],
        ];
        $http_codes = [
            [HttpResponse::HTTP_OK => null],
            [HttpResponse::HTTP_ACCEPTED => HttpResponse::HTTP_ACCEPTED],
            [HttpResponse::HTTP_OK => HttpResponse::HTTP_OK],
        ];

        /** @var \MarcinOrlowski\ResponseBuilder\BaseApiCodes $api_codes_class_name */
        $api_codes_class_name = $this->getApiCodesClassName();

        foreach ($payloads as $payload) {
            foreach ($http_codes as $http_code) {
                foreach ($http_code as $http_code_expect => $http_code_send) {
                    $this->response = ResponseBuilderLegacy::success($payload, null, [], $http_code_send);

                    $j = $this->getResponseSuccessObject($api_codes_class_name::OK(), $http_code_expect);

                    $expected_payload = is_array($payload) ? (object)$payload : $payload;
                    $this->assertEquals($expected_payload, $j->data);
                }
            }
        }
    }

    /**
     * Tests successWithHttpCode()
     *
     * @return void
     */
    public function testSuccessHttpCode(): void
    {
        $http_codes = [
            HttpResponse::HTTP_ACCEPTED,
            HttpResponse::HTTP_OK,
        ];
        foreach ($http_codes as $http_code) {
            $this->response = ResponseBuilderLegacy::successWithHttpCode($http_code);
            $j = $this->getResponseSuccessObject(null, $http_code);
            $this->assertNull($j->data);
        }
    }

    /**
     * Tests that passing null as argument to successWithHttpCode() it will fall back to defaults.
     *
     * @return void
     */
    public function testSuccessWithNullAsHttpCode(): void
    {
        $response = ResponseBuilderLegacy::successWithHttpCode(null);
        $this->assertEquals(ResponseBuilderLegacy::DEFAULT_HTTP_CODE_OK, $response->getStatusCode());
    }

    /**
     * Tests if successXX() with too high http code would throw expected exception.
     *
     * @return void
     */
    public function testSuccessWithTooBigHttpCode(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        ResponseBuilderLegacy::successWithHttpCode(666);
    }

    /**
     * Tests if successXX() with too low http code would throw expected exception.
     *
     * @return void
     */
    public function testSuccessWithTooLowHttpCode(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        ResponseBuilderLegacy::successWithHttpCode(0);
    }

    /**
     * Tests if successWithMessage() returns our custom message in the response object.
     */
    public function testSuccessWithMessage(): void
    {
        $msg = $this->getRandomString('msg_');
        $this->response = ResponseBuilderLegacy::successWithMessage($msg);
        $j = $this->getResponseSuccessObject(BaseApiCodes::OK(), null, $msg);
        $this->assertNull($j->data);
    }

}
