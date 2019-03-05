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
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2012-2019 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_checkbox/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

namespace MetaModels\AttributeCheckboxBundle\EventListener;

use ContaoCommunityAlliance\DcGeneral\Contao\RequestScopeDeterminator;
use ContaoCommunityAlliance\DcGeneral\DataDefinition\Palette\Condition\Property\PropertyFalseCondition;
use ContaoCommunityAlliance\DcGeneral\DataDefinition\Palette\Condition\Property\PropertyTrueCondition;
use MetaModels\AttributeCheckboxBundle\Attribute\Checkbox;
use MetaModels\Events\CreatePropertyConditionEvent;

/**
 * Listener adds property condition for the checkbox.
 *
 * @package MetaModels\AttributeCheckboxBundle\EventListener
 */
class CreatePropertyConditionListener
{
    /**
     * Request scope determinator.
     *
     * @var RequestScopeDeterminator
     */
    private $scopeMatcher;

    /**
     * CreatePropertyConditionListener constructor.
     *
     * @param RequestScopeDeterminator $scopeMatcher Request scope determinator.
     */
    public function __construct(RequestScopeDeterminator $scopeMatcher)
    {
        $this->scopeMatcher = $scopeMatcher;
    }

    /**
     * Handle the CreatePropertyConditionEvent event.
     *
     * @param CreatePropertyConditionEvent $event The event.
     *
     * @return void
     */
    public function onCreatePropertyCondition(CreatePropertyConditionEvent $event)
    {
        if (!$this->scopeMatcher->currentScopeIsFrontend()) {
            return;
        }

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
    }
}
