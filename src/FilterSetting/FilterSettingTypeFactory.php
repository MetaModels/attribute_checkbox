<?php

/**
 * This file is part of MetaModels/attribute_checkbox.
 *
 * (c) 2012-2019 The MetaModels team.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * This project is provided in good faith and hope to be usable by anyone.
 *
 * @package    MetaModels/attribute_checkbox
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2012-2019 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_checkbox/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

namespace MetaModels\AttributeCheckboxBundle\FilterSetting;

use Doctrine\DBAL\Connection;
use MetaModels\Filter\Setting\AbstractFilterSettingTypeFactory;

/**
 * Attribute type factory for published filter settings.
 */
class FilterSettingTypeFactory extends AbstractFilterSettingTypeFactory
{
    /**
     * Database connection.
     *
     * @var Connection
     */
    private $connection;

    /**
     * Create a new instance.
     *
     * @param Connection $connection Databse connection.
     */
    public function __construct(Connection $connection)
    {
        parent::__construct();

        $this
            ->setTypeName('checkbox_published')
            ->setTypeIcon('bundles/metamodelscore/visible.png')
            ->setTypeClass(Published::class)
            ->allowAttributeTypes('checkbox');

        $this->connection = $connection;
    }

    /**
     * {@inheritdoc}
     */
    public function createInstance($information, $filterSettings)
    {
        // $this->typeClass is private and there is no getter...
        $typeClass = Published::class;

        return new $typeClass($filterSettings, $information, $this->connection);
    }
}
