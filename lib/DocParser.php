<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DocQuery.php
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

use phpDocumentor\Reflection\DocBlockFactory;
use ReflectionClass;

/**
 * DocQuery
 *
 * @copyright  2020 Pretzlaw (https://rmp-up.de)
 */
trait DocParser
{
    private $docBlockFactory;

    public function classComment($className = null): Comment
    {
        if (null === $className) {
            $className = get_class($this);
        }

        $reflection = new ReflectionClass($className);

        $docComment = (string) $reflection->getDocComment();

        return new Comment($this->docBlockFactory()->create($docComment));
    }

    /**
     * @return Comment
     *
     * @deprecated 0.3.0 Use ::classComment() or ::methodComment() instead.
     */
    public function comment(): Comment
    {
        return $this->methodComment(get_class($this), $this->getName());
    }

    public function methodComment($classOrMethodName = null, $method = null): Comment
    {
        if (null === $classOrMethodName) {
            $classOrMethodName = get_class($this);
        }

        if (false !== strpos($classOrMethodName, '::')) {
            $parts = explode('::', $classOrMethodName);

            $classOrMethodName = get_class($this);
            $method = array_pop($parts);

            if ($parts) {
                $classOrMethodName = array_pop($parts);
            }
        }

        $method = (new ReflectionClass($classOrMethodName))->getMethod($method);

        return new Comment($this->docBlockFactory()->create((string)$method->getDocComment()));
    }


    private function docBlockFactory(): DocBlockFactory
    {
        if (null === $this->docBlockFactory) {
            $this->docBlockFactory = DocBlockFactory::createInstance();
        }

        return $this->docBlockFactory;
    }
}