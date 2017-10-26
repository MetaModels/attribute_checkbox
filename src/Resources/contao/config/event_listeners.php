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
 * @author     Stefan Heimes <stefan_heimes@hotmail.com>
 * @copyright  2012-2017 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_checkbox/blob/master/LICENSE LGPL-3.0
 * @filesource
 */

use ContaoCommunityAlliance\DcGeneral\Contao\View\Contao2BackendView\Event\GetPropertyOptionsEvent;
use ContaoCommunityAlliance\DcGeneral\DataDefinition\Palette\Condition\Property\PropertyFalseCondition;
use ContaoCommunityAlliance\DcGeneral\DataDefinition\Palette\Condition\Property\PropertyTrueCondition;
use MetaModels\Attribute\Checkbox\AttributeTypeFactory;
use MetaModels\Attribute\Checkbox\Checkbox;
use MetaModels\Attribute\Events\CreateAttributeFactoryEvent;
use MetaModels\Events\Attribute\Checkbox\Listener;
use MetaModels\Events\Attribute\Checkbox\PublishedFilterSettingTypeRenderer;
use MetaModels\Events\CreatePropertyConditionEvent;
use MetaModels\Events\MetaModelsBootEvent;
use MetaModels\Filter\Setting\Events\CreateFilterSettingFactoryEvent;
use MetaModels\Filter\Setting\Published\FilterSettingTypeFactory;
use MetaModels\MetaModelsEvents;

return [
    MetaModelsEvents::SUBSYSTEM_BOOT_BACKEND => [
        function (MetaModelsBootEvent $event) {
            new Listener($event->getServiceContainer());
            new PublishedFilterSettingTypeRenderer($event->getServiceContainer());
        }
    ],
    MetaModelsEvents::ATTRIBUTE_FACTORY_CREATE => [
        function (CreateAttributeFactoryEvent $event) {
            $factory = $event->getFactory();
            $factory->addTypeFactory(new AttributeTypeFactory());
        }
    ],
    MetaModelsEvents::FILTER_SETTING_FACTORY_CREATE => [
        function (CreateFilterSettingFactoryEvent $event) {
            $factory = $event->getFactory();
            $factory->addTypeFactory(new FilterSettingTypeFactory());
        }
    ],
    CreatePropertyConditionEvent::NAME => [[
        function (CreatePropertyConditionEvent $event) {
            $meta = $event->getData();

            if ('conditionpropertyvalueis' !== $meta['type']) {
                return;
            }

            $metaModel = $event->getMetaModel();
            $attribute = $metaModel->getAttributeById($meta['attr_id']);
            if (!($attribute instanceof Checkbox)) {
                return;
            }

            if ((bool) $meta['value']) {
                $event->setInstance(new PropertyTrueCondition($attribute->getColName()));
                return;
            }
            $event->setInstance(new PropertyFalseCondition($attribute->getColName()));
        },
        -10
        ]
    ],
    MetaModelsEvents::SUBSYSTEM_BOOT_FRONTEND => [
        function (MetaModelsBootEvent $event) {
            $dispatcher = $event->getServiceContainer()->getEventDispatcher();
            $dispatcher->addListener(
                GetPropertyOptionsEvent::NAME,
                'MetaModels\Events\Attribute\Checkbox\CheckboxOptionsProvider::getPropertyOptions',
                200);
        }
    ]
];
