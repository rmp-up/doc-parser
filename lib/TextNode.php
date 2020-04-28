<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * TextNode.php
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

use DomainException;

/**
 * TextNode
 *
 * @method TextNode after(string $text)
 * @method TextNode before(string $text)
 * @method TextNode ignoreCase()
 * @method TextNode multiLine()
 * @method TextNode globalMatch()
 * @method TextNode pregMatchFlags($flags)
 * @method TextNode startOfInput()
 * @method TextNode startOfLine()
 * @method TextNode endOfInput()
 * @method TextNode endOfLine()
 * @method TextNode eitherFind($r)
 * @method TextNode orFind($r)
 * @method TextNode anyOf(array $r)
 * @method TextNode neither($r)
 * @method TextNode nor($r)
 * @method TextNode exactly($n)
 * @method TextNode min($n)
 * @method TextNode max($n)
 * @method TextNode of($s)
 * @method TextNode ofAny()
 * @method TextNode ofGroup($n)
 * @method TextNode from($s)
 * @method TextNode notFrom($s)
 * @method TextNode like($r)
 * @method TextNode reluctantly()
 * @method TextNode asGroup($name = null)
 * @method TextNode then($s)
 * @method TextNode find($s)
 * @method TextNode some($s)
 * @method TextNode maybeSome($s)
 * @method TextNode maybe($s)
 * @method TextNode anything()
 * @method TextNode anythingBut($s)
 * @method TextNode something()
 * @method TextNode any()
 * @method TextNode lineBreak()
 * @method TextNode lineBreaks()
 * @method TextNode whitespace()
 * @method TextNode notWhitespace()
 * @method TextNode tab()
 * @method TextNode tabs()
 * @method TextNode digit()
 * @method TextNode notDigit()
 * @method TextNode digits()
 * @method TextNode notDigits()
 * @method TextNode letter()
 * @method TextNode notLetter()
 * @method TextNode letters()
 * @method TextNode notLetters()
 * @method TextNode lowerCaseLetter()
 * @method TextNode lowerCaseLetters()
 * @method TextNode upperCaseLetter()
 * @method TextNode upperCaseLetters()
 *
 * @copyright 2020 Pretzlaw (https://rmp-up.de)
 */
class TextNode
{
    /**
     * @var null|RegExpBuilder
     */
    private $regexBuilder;
    /**
     * @var string
     */
    private $text;

    public function __construct(string $text = '', $regexBuilder = null)
    {
        $this->text = $text;
        $this->regexBuilder = $regexBuilder;
    }

    public function __call($name, $arguments): TextNode
    {
        $regex = $this->regex();

        if (!method_exists($regex, $name)) {
            throw new DomainException('Unknown method: ' . __CLASS__ . '::' . $name);
        }

        return new self($this->text, (clone $regex)->$name(...$arguments));
    }

    public function __toString()
    {
        if ($this->regex()->getLiteral()) {
            return implode(PHP_EOL, $this->findAll());
        }

        return $this->text;
    }

    public function resultSet(): ResultSet
    {
        return new ResultSet($this->regex()->getRegExp()->findIn($this->text));
    }

    protected function regex(): RegExpBuilder
    {
        if (null === $this->regexBuilder) {
            $this->regexBuilder = new RegExpBuilder();
        }

        return $this->regexBuilder;
    }
}