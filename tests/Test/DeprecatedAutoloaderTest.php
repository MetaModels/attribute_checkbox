<?php

/**
 * This file is part of MetaModels/attribute_checkbox.
 *
 * (c) 2012-2022 The MetaModels team.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * This project is provided in good faith and hope to be usable by anyone.
 *
 * @package    MetaModels/attribute_checkbox
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @author     Ingolf Steinhardt <info@e-spin.de>
 * @copyright  2012-2022 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_checkbox/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

namespace MetaModels\AttributeCheckboxBundle\Test;

use MetaModels\AttributeCheckboxBundle\Attribute\AttributeTypeFactory;
use MetaModels\AttributeCheckboxBundle\Attribute\Checkbox;
use MetaModels\AttributeCheckboxBundle\FilterSetting\FilterSettingTypeFactory;
use MetaModels\AttributeCheckboxBundle\FilterSetting\Published;
use PHPUnit\Framework\TestCase;

/**
 * This class tests if the deprecated autoloader works.
 *
 * @coversNothing
 */
class DeprecatedAutoloaderTest extends TestCase
{
    /**
     * Aliases of old classes to the new one.
     *
     * @var array
     */
    private static $classes = [
        'MetaModels\Attribute\Checkbox\Checkbox'                       => Checkbox::class,
        'MetaModels\Attribute\Checkbox\AttributeTypeFactory'           => AttributeTypeFactory::class,
        'MetaModels\Filter\Setting\Published\Checkbox'                 => Published::class,
        'MetaModels\Filter\Setting\Published\FilterSettingTypeFactory' => FilterSettingTypeFactory::class,
    ];

    /**
     * Provide the alias class map.
     *
     * @return array
     */
    public function provideAliasClassMap()
    {
        $values = [];

        foreach (static::$classes as $alias => $class) {
            $values[] = [$alias, $class];
        }

        return $values;
    }

    /**
     * Test if the deprecated classes are aliased to the new one.
     *
     * @param string $oldClass Old class name.
     * @param string $newClass New class name.
     *
     * @dataProvider provideAliasClassMap
     */
    public function testDeprecatedClassesAreAliased($oldClass, $newClass)
    {
        $this->assertTrue(\class_exists($oldClass), \sprintf('Class alias "%s" is not found.', $oldClass));

        $oldClassReflection = new \ReflectionClass($oldClass);
        $newClassReflection = new \ReflectionClass($newClass);

        $this->assertSame($newClassReflection->getFileName(), $oldClassReflection->getFileName());
    }
}
