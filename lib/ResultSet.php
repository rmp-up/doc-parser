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

/**
 * ResultSet
 *
 * @copyright 2020 Pretzlaw (https://rmp-up.de)
 */
class ResultSet extends ArrayObject
{
    public function all(): ResultSet
    {
        return $this;
    }

    public function filter($callback = null): ResultSet
    {
        $data = $this->getArrayCopy();

        if (null !== $callback) {
            $data = array_filter($data, $callback);
        }

        return new self(array_filter($data));
    }

    public function map($callback): ResultSet
    {
        return new self(array_map($callback, $this->getArrayCopy()));
    }

    public function first(): ResultString
    {
        $all = $this->getArrayCopy();

        return new ResultString(reset($all) ?: '');
    }

    public function last(): ResultString
    {
        $all = $this->getArrayCopy();

        return new ResultString(end($all) ?: '');
    }
}