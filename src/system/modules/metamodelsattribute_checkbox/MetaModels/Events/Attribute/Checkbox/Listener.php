<?php
/**
 * The MetaModels extension allows the creation of multiple collections of custom items,
 * each with its own unique set of selectable attributes, with attribute extendability.
 * The Front-End modules allow you to build powerful listing and filtering of the
 * data in each collection.
 *
 * PHP version 5
 * @package    MetaModels
 * @subpackage Frontend
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @copyright  The MetaModels team.
 * @license    LGPL.
 * @filesource
 */

namespace MetaModels\Events\Attribute\Checkbox;

use ContaoCommunityAlliance\DcGeneral\Contao\DataDefinition\Definition\Contao2BackendViewDefinition;
use ContaoCommunityAlliance\DcGeneral\Contao\DataDefinition\Definition\Contao2BackendViewDefinitionInterface;
use ContaoCommunityAlliance\DcGeneral\DataDefinition\Definition\View\ToggleCommand;
use MetaModels\Attribute\Checkbox\Checkbox;
use MetaModels\Events\BuildAttributeEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * This class creates the default instances for property conditions when generating input screens.
 */
class Listener
	implements EventSubscriberInterface
{
	/**
	 * {@inheritDoc}
	 */
	public static function getSubscribedEvents()
	{
		return array(
			BuildAttributeEvent::NAME => __CLASS__ . '::handle'
		);
	}

	/**
	 * Create the property conditions.
	 *
	 * @param BuildAttributeEvent $event The event.
	 *
	 * @return void
	 *
	 * @throws \RuntimeException When no MetaModel is attached to the event or any other important information could
	 *                           not be retrieved.
	 */
	public function handle(BuildAttributeEvent $event)
	{
		if (!(($event->getAttribute() instanceof Checkbox) && (($event->getAttribute()->get('check_publish') == 1) || ($event->getAttribute()->get('check_listview') == 1))))
		{
			return;
		}

		$container = $event->getContainer();

		if ($container->hasDefinition(Contao2BackendViewDefinitionInterface::NAME))
		{
			$view = $container->getDefinition(Contao2BackendViewDefinitionInterface::NAME);
		}
		else
		{
			$view = new Contao2BackendViewDefinition();
			$container->setDefinition(Contao2BackendViewDefinitionInterface::NAME, $view);
		}

		$commands    = $view->getModelCommands();
		$attribute   = $event->getAttribute();

		if($event->getAttribute()->get('check_listview') == 1)
		{
			$commandName = 'listviewtoggle_' . $attribute->getColName();
		}
		else
		{
			$commandName = 'publishtoggle_' . $attribute->getColName();
		}

		if (!$commands->hasCommandNamed($commandName))
		{
			$toggle = new ToggleCommand();
			$toggle->setName($commandName);
			$toggle->setLabel($GLOBALS['TL_LANG']['MSC']['metamodelattribute_checkbox']['toggle'][0]);
			$toggle->setDescription($GLOBALS['TL_LANG']['MSC']['metamodelattribute_checkbox']['toggle'][1]);

			$extra = $toggle->getExtra();

			$objIconEnabled = \FilesModel::findByUuid($event->getAttribute()->get('check_listviewicon'));
			$objIconDisabled = \FilesModel::findByUuid($event->getAttribute()->get('check_listviewicondisabled'));

			if($event->getAttribute()->get('check_listview') == 1 && $objIconEnabled->path && $objIconDisabled->path)
			{
				$extra['icon'] = $objIconEnabled->path;
				$extra['icon_disabled'] = $objIconDisabled->path;
			}
			else
			{
				$extra['icon'] = 'visible.gif';
			}

			$toggle->setToggleProperty($attribute->getColName());

			$commands->addCommand($toggle);
		}
	}
}

