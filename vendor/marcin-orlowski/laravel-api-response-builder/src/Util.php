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
final class Util
{
    /**
     * Merges the configs together and takes multi-dimensional arrays into account.
     * Support for multi-dimensional config array. Built-in config merge only supports flat arrays.
     * Throws \RuntimeException if arrays stucture causes type conflics (i.e. you want to merge
     * array with int).
     *
     * @param array $original Array to merge other array into. Usually default values to overwrite.
     * @param array $merging  Array with items to be merged into $original, overriding (primitives) or merging
     *                        (arrays) entries in destination array.
     *
     * @return array
     *
     * @throws \RuntimeException
     */
    public static function mergeConfig(array $original, array $merging): array
    {
        $array = $original;
        foreach ($merging as $m_key => $m_val) {
            if (array_key_exists($m_key, $original)) {
                $orig_type = gettype($original[ $m_key ]);
                $m_type = gettype($m_val);
                if ($orig_type !== $m_type) {
                    throw new \RuntimeException(
                        "Incompatible types. Cannot merge {$m_type} into {$orig_type} (key '{$m_key}').");
                }

                if (is_array($merging[ $m_key ])) {
                    $array[ $m_key ] = static::mergeConfig($original[ $m_key ], $m_val);
                } else {
                    $array[ $m_key ] = $m_val;
                }
            } else {
                $array[ $m_key ] = $m_val;
            }
        }

        return $array;
    }

    /**
     * Sorts array by value, assuming value is an array and contains `pri` key with integer (positive/negative)
     * value which is used for sorting higher -> lower priority.
     *
     * @param array &$array
     */
    public static function sortArrayByPri(array &$array): void
    {
        // we now need to sort 'converter' node by priority
        uasort($array, function($array_a, $array_b) {
            $pri_a = $array_a['pri'] ?? 0;
            $pri_b = $array_b['pri'] ?? 0;

            return $pri_b <=> $pri_a;
        });
    }
}
