<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Node.php
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

use InvalidArgumentException;
use SimpleXMLElement;

/**
 * Node
 *
 * @copyright 2020 Pretzlaw (https://rmp-up.de)
 *
 * @method HtmlNode|HtmlNode[]|ResultSet code(int $index = null)
 * @method HtmlNode|HtmlNode[]|ResultSet img(int $index = null)
 * @method HtmlNode|HtmlNode[]|ResultSet li(int $index = null)
 * @method HtmlNode|HtmlNode[]|ResultSet ol(int $index = null)
 * @method HtmlNode|HtmlNode[]|ResultSet p(int $index = null)
 * @method HtmlNode|HtmlNode[]|ResultSet ul(int $index = null)
 */
class HtmlNode
{
    /**
     * @var SimpleXMLElement
     */
    private $xml;

    public function __construct(SimpleXMLElement $element)
    {
        $this->xml = $element;
    }

    public function __call($name, $arguments)
    {
        $index = array_shift($arguments);

        $query = '//' . $name;
        if ($arguments) {
            $query .= array_shift($arguments); // append a suffix (e.g. '[@class="head"]')
        }

        $nodes = $this->xml()->xpath($query);

        if (null === $index) {
            // No index so we return all
            return new ResultSet(
                array_map(
                    function ($node) {
                        return new HtmlNode($node);
                    },
                    $nodes
                )
            );
        }

        if (empty($nodes[$index])) {
            throw new InvalidArgumentException('Node does not exist: ' . $index);
        }

        return new HtmlNode($nodes[$index]) ?? null;
    }

    public function __toString()
    {
        return (string) $this->text();
    }

    public function evaluate()
    {
        $filePath = tempnam(sys_get_temp_dir(), 'rmp-up_doc');
        file_put_contents($filePath, $this->text());
        $result = (require $filePath);
        unlink($filePath);

        return $result;
    }

    public function html(): string
    {
        return (string) $this->xml()->asXML();
    }

    public function text(): TextNode
    {
        return new TextNode(html_entity_decode(strip_tags($this->html())));
    }

    /**
     * @return SimpleXMLElement
     */
    public function xml(): SimpleXMLElement
    {
        return $this->xml;
    }
}