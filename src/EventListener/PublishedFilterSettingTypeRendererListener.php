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

namespace MetaModels\AttributeCheckboxBundle\EventListener;

use ContaoCommunityAlliance\DcGeneral\Contao\RequestScopeDeterminator;
use ContaoCommunityAlliance\DcGeneral\Contao\View\Contao2BackendView\Event\ModelToLabelEvent;
use ContaoCommunityAlliance\DcGeneral\Data\ModelInterface;
use ContaoCommunityAlliance\DcGeneral\EnvironmentInterface;
use MetaModels\CoreBundle\Assets\IconBuilder;
use MetaModels\CoreBundle\EventListener\DcGeneral\Table\FilterSetting\AbstractFilterSettingTypeRenderer;
use MetaModels\Filter\Setting\IFilterSettingFactory;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Handles rendering of model from tl_metamodel_filtersetting.
 */
class PublishedFilterSettingTypeRendererListener extends AbstractFilterSettingTypeRenderer
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
     * @param IFilterSettingFactory    $filterSettingFactory The filter factory.
     * @param EventDispatcherInterface $dispatcher           The event dispatcher.
     * @param IconBuilder              $iconBuilder          The icon builder.
     * @param RequestScopeDeterminator $scopeMatcher         Request scope determinator.
     */
    public function __construct(
        IFilterSettingFactory $filterSettingFactory,
        EventDispatcherInterface $dispatcher,
        IconBuilder $iconBuilder,
        RequestScopeDeterminator $scopeMatcher
    ) {
        parent::__construct($filterSettingFactory, $dispatcher, $iconBuilder);

        $this->scopeMatcher = $scopeMatcher;
    }

    /**
     * {@inheritdoc}
     */
    protected function getTypes()
    {
        return array('checkbox_published');
    }

    /**
     * {@inheritdoc}
     */
    protected function getLabelParameters(EnvironmentInterface $environment, ModelInterface $model)
    {
        return $this->getLabelParametersWithAttributeAndUrlParam($environment, $model);
    }

    /**
     * {@inheritdoc}
     */
    public function modelToLabel(ModelToLabelEvent $event)
    {
        if (!$this->scopeMatcher->currentScopeIsBackend()) {
            return;
        }

        parent::modelToLabel($event);
    }
}
