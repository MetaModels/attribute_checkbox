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
 * @subpackage Core
 * @author     Stefan Heimes <stefan_heimes@hotmail.com>
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2012-2017 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_checkbox/blob/master/LICENSE LGPL-3.0
 * @filesource
 */

namespace MetaModels\AttributeCheckboxBundle\Events;

use ContaoCommunityAlliance\DcGeneral\Contao\View\Contao2BackendView\Event\GetPropertyOptionsEvent;
use MetaModels\AttributeCheckboxBundle\Checkbox;
use MetaModels\DcGeneral\Data\Model;

/**
 * This class retrieves the options of an attribute within a MetaModel unless someone else already provided them.
 */
class CheckboxOptionsProvider
{
    /**
     * Retrieve the property options.
     *
     * @param GetPropertyOptionsEvent $event The event.
     *
     * @return void
     */
    public static function getPropertyOptions(GetPropertyOptionsEvent $event)
    {
        $model = $event->getModel();
        if (!($model instanceof Model)) {
            return;
        }

        $attribute = $model->getItem()->getAttribute($event->getPropertyName());

        // Check if we have a checkbox.
        if (!($attribute instanceof Checkbox)) {
            return;
        }

        // Reset the options we didn't need them for checkbox.
        $event->setOptions(null);
        $event->stopPropagation();
    }
}
