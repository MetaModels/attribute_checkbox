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
 * @author     Christopher Boelter <c.boelter@cogizz.de>
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2012-2019 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_checkbox/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

namespace MetaModels\AttributeCheckboxBundle\EventListener;

use Contao\FilesModel;
use ContaoCommunityAlliance\DcGeneral\Contao\DataDefinition\Definition\Contao2BackendViewDefinition;
use ContaoCommunityAlliance\DcGeneral\Contao\DataDefinition\Definition\Contao2BackendViewDefinitionInterface;
use ContaoCommunityAlliance\DcGeneral\Contao\RequestScopeDeterminator;
use ContaoCommunityAlliance\DcGeneral\DataDefinition\ContainerInterface;
use ContaoCommunityAlliance\DcGeneral\DataDefinition\Definition\View\ToggleCommand;
use ContaoCommunityAlliance\DcGeneral\DataDefinition\Definition\View\ToggleCommandInterface;
use MetaModels\Attribute\IAttribute;
use MetaModels\AttributeCheckboxBundle\Attribute\Checkbox;
use MetaModels\DcGeneral\Events\MetaModel\BuildMetaModelOperationsEvent;

/**
 * This class creates the default instances for property conditions when generating input screens.
 */
class BuildMetaModelOperationsListener
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
     * Build a single toggle operation.
     *
     * @param Checkbox $attribute    The checkbox attribute.
     * @param array    $propertyData The property date from the input screen property.
     *
     * @return ToggleCommandInterface
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     * @SuppressWarnings(PHPMD.CamelCaseVariableName)
     */
    private function buildCommand($attribute, array $propertyData)
    {
        if ($attribute->get('check_listview') == 1) {
            $commandName = 'listviewtoggle_' . $attribute->getColName();
        } else {
            $commandName = 'publishtoggle_' . $attribute->getColName();
        }
        $toggle = new ToggleCommand();
        $toggle->setName($commandName);
        $toggle->setLabel($GLOBALS['TL_LANG']['MSC']['metamodelattribute_checkbox']['toggle'][0]);
        $toggle->setDescription(
            \sprintf(
                $GLOBALS['TL_LANG']['MSC']['metamodelattribute_checkbox']['toggle'][1],
                $attribute->getName()
            )
        );
        $extra           = $toggle->getExtra();
        $extra['icon']   = 'visible.svg';
        $objIconEnabled  = FilesModel::findByUuid($attribute->get('check_listviewicon'));
        $objIconDisabled = FilesModel::findByUuid($attribute->get('check_listviewicondisabled'));

        if ($attribute->get('check_listview') == 1 && $objIconEnabled->path && $objIconDisabled->path) {
            $extra['icon']          = $objIconEnabled->path;
            $extra['icon_disabled'] = $objIconDisabled->path;
        } else {
            $extra['icon'] = 'visible.svg';
        }

        $toggle->setToggleProperty($attribute->getColName());

        if ($attribute->get('check_inverse') == 1) {
            $toggle->setInverse(true);
        }

        if (!empty($propertyData['eval']['readonly'])) {
            $toggle->setDisabled(true);
        }

        return $toggle;
    }

    /**
     * Create the backend view definition.
     *
     * @param ContainerInterface $container The container.
     *
     * @return Contao2BackendViewDefinition
     */
    protected function createBackendViewDefinition($container)
    {
        if ($container->hasDefinition(Contao2BackendViewDefinitionInterface::NAME)) {
            $view = $container->getDefinition(Contao2BackendViewDefinitionInterface::NAME);
        } else {
            $view = new Contao2BackendViewDefinition();
            $container->setDefinition(Contao2BackendViewDefinitionInterface::NAME, $view);
        }

        return $view;
    }

    /**
     * Create the property conditions.
     *
     * @param BuildMetaModelOperationsEvent $event The event.
     *
     * @return void
     *
     * @throws \RuntimeException When no MetaModel is attached to the event or any other important information could
     *                           not be retrieved.
     */
    public function handle(BuildMetaModelOperationsEvent $event)
    {
        if (!$this->scopeMatcher->currentScopeIsBackend()) {
            return;
        }
        $allProps   = $event->getScreen()['properties'];
        $properties = \array_map(function ($property) {
            return $property['col_name'];
        }, $allProps);
        foreach ($event->getMetaModel()->getAttributes() as $attribute) {
            if (!$this->wantToAdd($attribute, $properties)) {
                continue;
            }
            $info = [];
            foreach ($allProps as $prop) {
                if ($prop['col_name'] === $attribute->getColName()) {
                    $info = $prop;
                }
            }

            $toggle    = $this->buildCommand($attribute, $info);
            $container = $event->getContainer();
            $view      = $this->createBackendViewDefinition($container);

            $commands = $view->getModelCommands();

            if (!$commands->hasCommandNamed($toggle->getName())) {
                if ($commands->hasCommandNamed('show')) {
                    $info = $commands->getCommandNamed('show');
                } else {
                    $info = null;
                }
                $commands->addCommand($toggle, $info);
            }
        }
    }

    /**
     * Test if we want to add an operation for the attribute.
     *
     * @param IAttribute $attribute  The attribute to test.
     * @param array      $properties The property names in the input screen.
     *
     * @return bool
     */
    private function wantToAdd($attribute, array $properties): bool
    {
        return ($attribute instanceof Checkbox)
            && (($attribute->get('check_publish') === '1') || ($attribute->get('check_listview') === '1'))
            && (\in_array($attribute->getColName(), $properties, true));
    }
}
