<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Comment.php
 *
 * LICENSE: This source file is created by the company around Pretzlaw
 * located in Germany also known as rmp-up. All its contents are proprietary
 * and under german copyright law. Consider this file as closed source and/or
 * without the permission to reuse or modify its contents.
 * This license is available through the world-wide-web at the following URI:
 * https://rmp-up.de/license-generic.txt . If you did not receive a copy
 * of the license and are unable to obtain it through the web, please send a
 * note to mail@rmp-up.de so we can mail you a copy.
 *
 * @package    phpunit-docgen
 * @copyright  2020 Pretzlaw
 * @license    https://rmp-up.de/license-generic.txt
 * @link       https://project.rmp-up.de/phpunit-docgen
 */

declare(strict_types=1);

namespace RmpUp\Doc;

use Michelf\MarkdownExtra;
use phpDocumentor\Reflection\DocBlock;
use RuntimeException;
use SimpleXMLElement;

/**
 * Comment
 *
 * @copyright  2020 Pretzlaw (https://rmp-up.de)
 */
class Comment extends HtmlNode
{
    /**
     * @var DocBlock
     */
    private $docBlock;

    public function __construct(DocBlock $docBlock)
    {
        $this->docBlock = $docBlock;

        parent::__construct(
            simplexml_load_string('<html lang="en">' . MarkdownExtra::defaultTransform($this->markdown()) . '</html>')
        );
    }

    public function docBlock(): DocBlock
    {
        return $this->docBlock;
    }

    public function execute($xpathOrIndex)
    {
        $index = 0;
        if (is_int($xpathOrIndex)) {
            $index = $xpathOrIndex;
            $xpathOrIndex = '//pre/code';
        }

        $content = $this->xpath($xpathOrIndex, $index);

        if (is_array($content) && count($content) === 1) {
            // Seems like the only one here.
            $content = reset($content);
        }

        if (!$content || !$content instanceof SimpleXMLElement) {
            throw new RuntimeException('Code not found or empty: ' . $xpathOrIndex);
        }

        $tempFile = tempnam(sys_get_temp_dir(), 'rmpup_dc_');

        $isSaved = file_put_contents($tempFile, $content);

        if (false === $isSaved) {
            throw new RuntimeException('Could not create tempfile for code: ' . $xpathOrIndex);
        }

        ob_start();
        $return = require $tempFile;
        $content = ob_get_clean();
        unlink($tempFile);

        if (1 !== $return) {
            return $return;
        }

        return $content;
    }

    public function markdown(): string
    {
        $markdown = $this->docBlock()->getSummary();

        if (false === strpos($markdown, "\n")) {
            $markdown = '# ' . trim($markdown);
        }

        $markdown .= PHP_EOL . PHP_EOL;

        $description = $this->docBlock()->getDescription();
        // Unescape possibly escaped comment blocks
        $description = preg_replace('@(\s*)\*\\\/(\s*)$@m', '\1*/', (string) $description);

        $markdown .= $description;

        return trim($markdown);
    }

    /**
     * @param string   $xpath
     * @param int|null $index
     *
     * @return SimpleXMLElement[]|SimpleXMLElement|bool
     */
    public function xpath(string $xpath, int $index = null)
    {
        $elements = $this->xml()->xpath($xpath);

        if (null === $index) {
            return $elements;
        }

        if (count($elements) < $index + 1) {
            return false;
        }

        return $elements[$index];
    }
}
