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

namespace MetaModels\AttributeCheckboxBundle\Attribute;

use Doctrine\DBAL\Connection;
use MetaModels\Attribute\IAttributeTypeFactory;
use MetaModels\Helper\TableManipulator;

/**
 * Attribute type factory for checkbox attributes.
 */
class AttributeTypeFactory implements IAttributeTypeFactory
{
    /**
     * The database connection.
     *
     * @var Connection
     */
    private $connection;

    /**
     * The table manipulator.
     *
     * @var TableManipulator
     */
    private $tableManipulator;

    /**
     * {@inheritDoc}
     */
    public function __construct(Connection $connection, TableManipulator $tableManipulator)
    {
        $this->connection       = $connection;
        $this->tableManipulator = $tableManipulator;
    }

    /**
     * {@inheritDoc}
     */
    public function getTypeName()
    {
        return 'checkbox';
    }

    /**
     * {@inheritDoc}
     */
    public function getTypeIcon()
    {
        return 'bundles/metamodelsattributecheckbox/checkbox.png';
    }

    /**
     * {@inheritDoc}
     */
    public function createInstance($information, $metaModel)
    {
        return new Checkbox($metaModel, $information, $this->connection, $this->tableManipulator);
    }

    /**
     * {@inheritDoc}
     */
    public function isTranslatedType()
    {
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function isSimpleType()
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function isComplexType()
    {
        return false;
    }
}
