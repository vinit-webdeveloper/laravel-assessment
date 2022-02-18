<?php

namespace MarcinOrlowski\ResponseBuilder\Tests\Models;

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

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Facades\Request;

/**
 * Class TestModel to verify auto-conversion feature
 */
class TestModelArrayable implements Arrayable
{
    /** @var string|null */
    protected $val;

    /**
     * TestModel constructor.
     *
     * @param string $val
     */
    public function __construct(string $val)
    {
        $this->val = $val;
    }

    /**
     * @return string|null
     */
    public function getVal(): ?string
    {
        return $this->val;
    }

    /**
     * Converts model to array. Signature must match JsonResource::toArray()
     *
     * @param Request $request
     *
     * @return array
     */
    public function toArray($request = null): array
    {
        return [
            'val' => $this->val,
        ];
    }
}
