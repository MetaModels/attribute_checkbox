<?php

/**
 * This file is part of MetaModels/attribute_checkbox.
 *
 * (c) 2012-2017 The MetaModels team.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * This project is provided in good faith and hope to be usable by anyone.
 *
 * @package    MetaModels
 * @subpackage AttributeCheckbox
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2012-2017 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_checkbox/blob/master/LICENSE LGPL-3.0
 * @filesource
 */

namespace MetaModels\AttributeCheckboxBundle\Test\Attribute;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\Statement;
use MetaModels\AttributeCheckboxBundle\Attribute\Checkbox;
use MetaModels\Helper\TableManipulator;
use MetaModels\IMetaModel;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests to test class Checkbox.
 */
class CheckboxTest extends TestCase
{
    /**
     * Mock a MetaModel.
     *
     * @param string $language         The language.
     * @param string $fallbackLanguage The fallback language.
     *
     * @return IMetaModel|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function mockMetaModel($language, $fallbackLanguage)
    {
        $metaModel = $this->getMockForAbstractClass('MetaModels\IMetaModel');

        $metaModel
            ->expects($this->any())
            ->method('getTableName')
            ->will($this->returnValue('mm_unittest'));

        $metaModel
            ->expects($this->any())
            ->method('getActiveLanguage')
            ->will($this->returnValue($language));

        $metaModel
            ->expects($this->any())
            ->method('getFallbackLanguage')
            ->will($this->returnValue($fallbackLanguage));

        return $metaModel;
    }

    /**
     * Mock the Contao database.
     *
     * @param string|null   expectedQuery The query to expect.
     *
     * @param callable|null $callback     Callback which gets mocked statement passed.
     *
     * @return Connection|\PHPUnit_Framework_MockObject_MockObject
     */
    private function mockConnection(callable $callback = null, $expectedQuery = null, $queryMethod = 'prepare')
    {
        $mockDb = $this
            ->getMockBuilder(Connection::class)
            ->disableOriginalConstructor()
            ->getMock();

        $statement = $this
            ->getMockBuilder(Statement::class)
            ->getMock();

        $mockDb->method('prepare')->willReturn($statement);
        $mockDb->method('query')->willReturn($statement);

        if ($callback) {
            call_user_func($callback, $statement);
        }

        if (!$expectedQuery || $expectedQuery === 'prepare') {
            $mockDb->expects($this->never())->method('query');
        }

        if (!$expectedQuery || $expectedQuery === 'query') {
            $mockDb->expects($this->never())->method('prepare');
        }

        if (!$expectedQuery) {
            return $mockDb;
        }

        $mockDb
            ->expects($this->once())
            ->method($queryMethod)
            ->with($expectedQuery);

        if ($queryMethod === 'prepare') {
            $statement
                ->expects($this->once())
                ->method('execute')
                ->willReturn(true);
        }

        return $mockDb;
    }

    /**
     * Mock the table manipulator.
     *
     * @param Connection $connection The database connection mock.
     *
     * @return TableManipulator|\PHPUnit_Framework_MockObject_MockObject
     */
    private function mockTableManipulator(Connection $connection)
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
     */
    public function testSearchFor($expectedParameter, $searchValue)
    {
        $metaModel = $this->mockMetaModel('en', 'en');

        $connection  = $this->mockConnection(
            function ($statement) {
                $statement
                    ->expects($this->once())
                    ->method('fetchAll')
                    ->with(\PDO::FETCH_COLUMN, 'id')
                    ->willReturn(['success']);
            },
            'SELECT id FROM mm_unittest WHERE testcol = :value'
        );
        $manipulator = $this->mockTableManipulator($connection);

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
