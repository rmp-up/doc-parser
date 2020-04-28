<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * ResultSet.php
 *
 * LICENSE: This source file is created by the company around M. Pretzlaw
 * located in Germany also known as rmp-up. All its contents are proprietary
 * and under german copyright law. Consider this file as closed source and/or
 * without the permission to reuse or modify its contents.
 * This license is available through the world-wide-web at the following URI:
 * https://rmp-up.de/license-generic.txt . If you did not receive a copy
 * of the license and are unable to obtain it through the web, please send a
 * note to mail@rmp-up.de so we can mail you a copy.
 *
 * @package   doc
 * @copyright 2020 Pretzlaw
 * @license   https://rmp-up.de/license-generic.txt
 */

declare(strict_types=1);

namespace RmpUp\Doc;

use ArrayObject;
use Exception;
use IteratorAggregate;
use Traversable;

/**
 * ResultSet
 *
 * @copyright 2020 Pretzlaw (https://rmp-up.de)
 */
class ResultString
{
    /**
     * @var string
     */
    private $result;

    public function __construct(string $result)
    {
        $this->result = $result;
    }

    public function __toString()
    {
        return $this->result;
    }

    public function filter($callback, ...$arguments): ResultString
    {
        return new ResultString($callback($this->result, ...$arguments));
    }

    public function split(...$segmentation): ResultSet
    {
        $segmentation = array_map(
            function ($value) {
                return preg_quote($value, '/'); // RegExp class is using "/" as delimiter as well
            },
            $segmentation
        );
        return new ResultSet(preg_split('/(' . implode('|', $segmentation) . ')/u', $this->result));
    }
}