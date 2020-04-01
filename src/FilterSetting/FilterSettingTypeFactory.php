<?php

/**
 * This file is part of MetaModels/attribute_checkbox.
 *
 * (c) 2012-2020 The MetaModels team.
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
 * @author     Richard Henkenjohann <richardhenkenjohann@googlemail.com>
 * @copyright  2012-2020 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_checkbox/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

namespace MetaModels\AttributeCheckboxBundle\FilterSetting;

use Doctrine\DBAL\Connection;
use MetaModels\Filter\FilterUrlBuilder;
use MetaModels\Filter\Setting\AbstractFilterSettingTypeFactory;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

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
     * The event dispatcher.
     *
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * The filter URL builder.
     *
     * @var FilterUrlBuilder
     */
    private $filterUrlBuilder;

    /**
     * Create a new instance.
     *
     * @param Connection                    $connection       The database connection.
     * @param EventDispatcherInterface|null $dispatcher       The event dispatcher.
     * @param FilterUrlBuilder|null         $filterUrlBuilder The filter URL builder.
     */
    public function __construct(
        Connection $connection,
        EventDispatcherInterface $dispatcher = null,
        FilterUrlBuilder $filterUrlBuilder = null
    ) {
        parent::__construct();

        $this
            ->setTypeName('checkbox_published')
            ->setTypeIcon('bundles/metamodelscore/visible.png')
            ->setTypeClass(Published::class)
            ->allowAttributeTypes('checkbox');

        $this->connection       = $connection;
        $this->dispatcher       = $dispatcher;
        $this->filterUrlBuilder = $filterUrlBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function createInstance($information, $filterSettings)
    {
        return new Published(
            $filterSettings,
            $information,
            $this->connection,
            $this->dispatcher,
            $this->filterUrlBuilder
        );
    }
}
