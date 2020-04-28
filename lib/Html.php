<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Html.php
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

use SimpleXMLElement;

/**
 * Html
 *
 * @copyright 2020 Pretzlaw (https://rmp-up.de)
 */
class Html extends HtmlNode
{
    public function __construct(string $html)
    {
        parent::__construct(simplexml_load_string($html));
    }

    public function enumeration($index = 0)
    {
        return $this->listings($index, 'ol');
    }

    public function itemization($index = 0)
    {
        return $this->listings($index, 'ul');
    }

    public function listings($index = 0, $type = null)
    {
        $query = './/ul[li] | //ol[li]';
        if ($type !== null) {
            $query = './/' . $type . '[li]';
        }
        $lists = $this->xml()->xpath($query); // All lists with children

        if (count($lists) < $index) {
            return null;
        }

        return $lists[$index];
    }
}