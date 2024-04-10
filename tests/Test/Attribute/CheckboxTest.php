<?php

/**
 * This file is part of MetaModels/attribute_checkbox.
 *
 * (c) 2012-2024 The MetaModels team.
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
 * @copyright  2012-2024 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_checkbox/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

namespace MetaModels\AttributeCheckboxBundle\Test\Attribute;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Query\Expression\ExpressionBuilder;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Result;
use Doctrine\DBAL\Statement;
use MetaModels\AttributeCheckboxBundle\Attribute\Checkbox;
use MetaModels\Helper\TableManipulator;
use MetaModels\IMetaModel;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests to test class Checkbox.
 *
 * @covers \MetaModels\AttributeCheckboxBundle\Attribute\Checkbox
 */
class CheckboxTest extends TestCase
{
    /**
     * Mock a MetaModel.
     *
     * @param string $language         The language.
     * @param string $fallbackLanguage The fallback language.
     *
     * @return IMetaModel|MockObject
     */
    protected function mockMetaModel($language, $fallbackLanguage)
    {
        $metaModel = $this->getMockBuilder(IMetaModel::class)->getMock();

        $metaModel
            ->expects(self::any())
            ->method('getTableName')
            ->willReturn('mm_unittest');

        $metaModel
            ->expects(self::any())
            ->method('getActiveLanguage')
            ->willReturn($language);

        $metaModel
            ->expects(self::any())
            ->method('getFallbackLanguage')
            ->willReturn($fallbackLanguage);

        return $metaModel;
    }

    /**
     * Mock the Contao database.
     *
     * @param array $methods The method names to mock.
     *
     * @return Connection&MockObject
     */
    private function mockConnection(array $methods = []): Connection&MockObject
    {
        $connection = $this->getMockBuilder(Connection::class)
            ->disableOriginalConstructor()
            ->onlyMethods(\array_merge($methods, ['getDatabasePlatform']))
            ->getMock();

        $platform = $this
            ->getMockBuilder(AbstractPlatform::class)
            ->disableOriginalConstructor()
            ->onlyMethods([])
            ->getMockForAbstractClass();
        $connection->method('getDatabasePlatform')->willReturn($platform);

        return $connection;
    }

    /**
     * Mock the table manipulator.
     *
     * @param Connection $connection The database connection mock.
     *
     * @return TableManipulator&MockObject
     */
    private function mockTableManipulator(Connection $connection): MockObject&TableManipulator
    {
        return $this->getMockBuilder(TableManipulator::class)
            ->setConstructorArgs([$connection, []])
            ->getMock();
    }

    /**
     * Test that the attribute can be instantiated.
     *
     * @return void
     */
    public function testInstantiation()
    {
        $connection  = $this->mockConnection();
        $manipulator = $this->mockTableManipulator($connection);

        $text = new Checkbox($this->mockMetaModel('en', 'en'), [], $connection, $manipulator);
        $this->assertInstanceOf(Checkbox::class, $text);
    }

    /**
     * Data provider for the testSearchFor() method.
     *
     * @return array
     */
    public function searchForProvider()
    {
        return [
            'search for \'\''       => ['', ''],
            'search for false'      => ['', false],
            'search for 0'          => ['', 0],
            'search for \'0\''      => ['', '0'],
            'search for \'string\'' => ['1', 'string'],
            'search for true'       => ['1', true],
            'search for 1'          => ['1', 1],
            'search for \'1\''      => ['1', '1'],
        ];
    }

    /**
     * Test the search for method.
     *
     * @param string $expectedParameter The expected search parameter for the query.
     * @param mixed  $searchValue       The search input value.
     *
     * @return void
     *
     * @dataProvider searchForProvider
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function testSearchFor($expectedParameter, $searchValue)
    {
        $metaModel = $this->mockMetaModel('en', 'en');
        $connection   = $this->mockConnection(['createQueryBuilder']);
        $manipulator = $this->mockTableManipulator($connection);

        $result = $this
            ->getMockBuilder(Result::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['fetchFirstColumn'])
            ->getMock();

        $result
            ->expects(self::once())
            ->method('fetchFirstColumn')
            ->willReturn(['success']);

        $builder = $this
            ->getMockBuilder(QueryBuilder::class)
            ->setConstructorArgs([$connection])
            ->getMock();

        $builder->expects(self::once())->method('select')->with('t.id')->willReturn($builder);
        $builder->expects(self::once())->method('from')->with('mm_unittest', 't')->willReturn($builder);
        $builder->expects(self::once())->method('where')->with('t.testcol=:value')->willReturn($builder);
        $builder->expects(self::once())->method('setParameter')
            ->with('value', $searchValue ? '1' : '')
            ->willReturn($builder);

        $builder->expects(self::once())->method('executeQuery')->willReturn($result);

        $connection->expects(self::once())->method('createQueryBuilder')->willReturn($builder);

        $checkbox = new Checkbox($metaModel, ['colname' => 'testcol'], $connection, $manipulator);

        $this->assertSame(['success'], $checkbox->searchFor($searchValue));
    }

    /**
     * Generate test data for testSerialize().
     *
     * @return array
     */
    public function serializeProvider()
    {
        return [
            'false is empty'        => ['', false],
            '0 is empty'            => ['', 0],
            'empty string is empty' => ['', ''],
            '\'0\' is empty'        => ['', '0'],
            'true is \'1\''         => ['1', true],
            '5 is \'1\''            => ['1', 5],
            '\'string\' is \'1\''   => ['1', 'string'],
        ];
    }

    /**
     * Test that the attribute can be instantiated.
     *
     * @param string $expected The expected value.
     * @param mixed  $value    The input value.
     *
     * @return void
     *
     * @dataProvider serializeProvider
     */
    public function testSerialize($expected, $value)
    {
        $connection  = $this->mockConnection();
        $manipulator = $this->mockTableManipulator($connection);

        $checkbox = new Checkbox($this->mockMetaModel('en', 'en'), [], $connection, $manipulator);
        $this->assertEquals($expected, $checkbox->serializeData($value));
        $this->assertSame($expected, $checkbox->serializeData($value));
    }

    /**
     * Generate test data for testSerialize().
     *
     * @return array
     */
    public function unserializeProvider()
    {
        return [
            'false is empty'        => ['', false],
            '0 is empty'            => ['', 0],
            'empty string is empty' => ['', ''],
            '\'0\' is empty'        => ['', '0'],
            'true is \'1\''         => ['1', true],
            '5 is \'1\''            => ['1', 5],
            '\'string\' is \'1\''   => ['1', 'string'],
        ];
    }

    /**
     * Test that the attribute can be instantiated.
     *
     * @param string $expected The expected value.
     * @param mixed  $value    The input value.
     *
     * @return void
     *
     * @dataProvider unserializeProvider
     */
    public function testUnserialize($expected, $value)
    {
        $connection  = $this->mockConnection();
        $manipulator = $this->mockTableManipulator($connection);

        $checkbox = new Checkbox($this->mockMetaModel('en', 'en'), [], $connection, $manipulator);
        $this->assertEquals($expected, $checkbox->unserializeData($value));
        $this->assertSame($expected, $checkbox->unserializeData($value));
    }
}
