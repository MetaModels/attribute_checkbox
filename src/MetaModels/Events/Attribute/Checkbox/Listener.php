<?php
/**
 * The MetaModels extension allows the creation of multiple collections of custom items,
 * each with its own unique set of selectable attributes, with attribute extendability.
 * The Front-End modules allow you to build powerful listing and filtering of the
 * data in each collection.
 *
 * PHP version 5
 *
 * @package    MetaModels
 * @subpackage AttributeCheckbox
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @author     Christopher Boelter <c.boelter@cogizz.de>
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @copyright  The MetaModels team.
 * @license    LGPL.
 * @filesource
 */

namespace MetaModels\Events\Attribute\Checkbox;

use ContaoCommunityAlliance\DcGeneral\Contao\DataDefinition\Definition\Contao2BackendViewDefinition;
use ContaoCommunityAlliance\DcGeneral\Contao\DataDefinition\Definition\Contao2BackendViewDefinitionInterface;
use ContaoCommunityAlliance\DcGeneral\DataDefinition\Definition\View\ToggleCommand;
use ContaoCommunityAlliance\DcGeneral\DataDefinition\Definition\View\ToggleCommandInterface;
use MetaModels\Attribute\Checkbox\Checkbox;
use MetaModels\DcGeneral\Events\BaseSubscriber;
use MetaModels\DcGeneral\Events\MetaModel\BuildMetaModelOperationsEvent;

/**
 * This class creates the default instances for property conditions when generating input screens.
 */
class Listener extends BaseSubscriber
{
    /**
     * {@inheritDoc}
     */
    public function registerEventsInDispatcher()
    {
        $this
            ->addListener(
                BuildMetaModelOperationsEvent::NAME,
                array($this, 'handle')
            );
    }

    /**
     * Build a single toggle operation.
     *
     * @param Checkbox $attribute The checkbox attribute.
     *
     * @return ToggleCommandInterface
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     * @SuppressWarnings(PHPMD.CamelCaseVariableName)
     */
    protected function buildCommand($attribute)
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
            sprintf(
                $GLOBALS['TL_LANG']['MSC']['metamodelattribute_checkbox']['toggle'][1],
                $attribute->getName()
            )
        );
        $extra           = $toggle->getExtra();
        $extra['icon']   = 'visible.gif';
        $objIconEnabled  = \FilesModel::findByUuid($attribute->get('check_listviewicon'));
        $objIconDisabled = \FilesModel::findByUuid($attribute->get('check_listviewicondisabled'));

        if ($attribute->get('check_listview') == 1 && $objIconEnabled->path && $objIconDisabled->path) {
            $extra['icon']          = $objIconEnabled->path;
            $extra['icon_disabled'] = $objIconDisabled->path;
        } else {
            $extra['icon'] = 'visible.gif';
        }

        $toggle->setToggleProperty($attribute->getColName());

        if (intval($attribute->get('check_publish')) === 1
            && intval($attribute->get('check_inverse')) === 1
        ) {
            $toggle->setInverse(true);
        }

        return $toggle;
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
        foreach ($event->getMetaModel()->getAttributes() as $attribute) {
            if (($attribute instanceof Checkbox)
                && (($attribute->get('check_publish') == 1)
                    || ($attribute->get('check_listview') == 1))
            ) {
                $toggle    = $this->buildCommand($attribute);
                $container = $event->getContainer();
                if ($container->hasDefinition(Contao2BackendViewDefinitionInterface::NAME)) {
                    $view = $container->getDefinition(Contao2BackendViewDefinitionInterface::NAME);
                } else {
                    $view = new Contao2BackendViewDefinition();
                    $container->setDefinition(Contao2BackendViewDefinitionInterface::NAME, $view);
                }

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
    }
}
