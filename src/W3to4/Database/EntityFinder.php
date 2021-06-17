<?php

/**
 * Part of datavideo project.
 *
 * @copyright  Copyright (C) 2021 .
 * @license    __LICENSE__
 */

namespace W3to4\Database;

use W3to4\Ioc;
use Windwalker\Attributes\AttributesAccessor;
use Windwalker\Core\Utilities\ClassFinder;
use Windwalker\ORM\Attributes\Table;

/**
 * The EntityFinder class.
 *
 * @since  __DEPLOY_VERSION__
 */
class EntityFinder
{
    public static function find(string $table, string $ns = 'App\\Entity'): ?string
    {
        /** @var ClassFinder $classFinder */
        $classFinder = Ioc::getRootApp()->service(ClassFinder::class);

        foreach ($classFinder->findClasses($ns) as $class) {
            $attr = AttributesAccessor::getFirstAttributeInstance(
                $class,
                Table::class,
                \ReflectionAttribute::IS_INSTANCEOF
            );

            if ($attr && $attr->getName() === $table) {
                return $class;
            }
        }

        return null;
    }
}
