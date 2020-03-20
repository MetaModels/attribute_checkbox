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
 * @author     Andreas Isaak <info@andreas-isaak.de>
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @author     Richard Henkenjohann <richardhenkenjohann@googlemail.com>
 * @copyright  2012-2019 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_checkbox/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

namespace MetaModels\AttributeCheckboxBundle\FilterSetting;

use Contao\CoreBundle\Security\Authentication\Token\TokenChecker;
use Contao\System;
use Doctrine\DBAL\Connection;
use MetaModels\Filter\FilterUrlBuilder;
use MetaModels\Filter\Setting\ICollection;
use MetaModels\Filter\Setting\Simple;
use MetaModels\Filter\IFilter;
use MetaModels\Filter\Rules\SimpleQuery;
use MetaModels\Filter\Rules\StaticIdList;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Published setting handler for checkboxes.
 */
class Published extends Simple
{
    /**
     * Database connection.
     *
     * @var Connection
     */
    private $connection;

    /**
     * The token checker.
     *
     * @var TokenChecker
     */
    private $tokenChecker;

    /**
     * Constructor - initialize the object and store the parameters.
     *
     * @param ICollection                   $collection       The parenting filter settings object.
     * @param array                         $data             The attributes for this filter setting.
     * @param Connection                    $connection       The database connection.
     * @param EventDispatcherInterface|null $dispatcher       The event dispatcher.
     * @param FilterUrlBuilder|null         $filterUrlBuilder The filter URL builder.
     * @param TokenChecker|null             $tokenChecker     The token checker.
     */
    public function __construct(
        ICollection $collection,
        array $data,
        Connection $connection = null,
        EventDispatcherInterface $dispatcher = null,
        FilterUrlBuilder $filterUrlBuilder = null,
        TokenChecker $tokenChecker = null
    ) {
        parent::__construct($collection, $data, $dispatcher, $filterUrlBuilder);

        if (null === $connection) {
            // @codingStandardsIgnoreStart Silencing errors is discouraged
            @\trigger_error(
                'You should pass a doctrine database connection to "' . __METHOD__ . '".',
                E_USER_DEPRECATED
            );
            // @codingStandardsIgnoreEnd

            $connection = System::getContainer()->get('database_connection');
        }

        $this->connection = $connection;
        $this->tokenChecker = $tokenChecker;
    }

    /**
     * {@inheritdoc}
     */
    public function prepareRules(IFilter $objFilter, $arrFilterUrl)
    {
        if ($this->get('check_ignorepublished') && $arrFilterUrl['ignore_published' . $this->get('id')]) {
            return;
        }

        // Skip filter when in front end preview.
        if ($this->get('check_allowpreview') && ($this->tokenChecker && $this->tokenChecker->isPreviewMode())) {
            return;
        }

        $objAttribute = $this->getMetaModel()->getAttributeById($this->get('attr_id'));

        $publishedValue = 1;
        if ((int) $objAttribute->get('check_publish') === 1
            && (int) $objAttribute->get('check_inverse') === 1
        ) {
            $publishedValue = '';
        }

        if ($objAttribute) {
            $objFilter->addFilterRule(SimpleQuery::createFromQueryBuilder(
                $this
                    ->connection
                    ->createQueryBuilder()
                    ->select('id')
                    ->from($this->getMetaModel()->getTableName())
                    ->where($objAttribute->getColName() . '=:value')
                    ->setParameter('value', $publishedValue)
            ));

            return;
        }
        // No attribute found, do not return anyting.
        $objFilter->addFilterRule(new StaticIdList([]));
    }

    /**
     * {@inheritdoc}
     */
    public function getParameters()
    {
        return ($this->get('check_ignorepublished')) ? ['ignore_published' . $this->get('id')] : [];
    }

    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     * @SuppressWarnings(PHPMD.CamelCaseVariableName)
     */
    public function getParameterDCA()
    {
        if (!$this->get('check_ignorepublished')) {
            return [];
        }

        $objAttribute = $this->getMetaModel()->getAttributeById($this->get('attr_id'));

        $arrLabel = [];
        foreach ($GLOBALS['TL_LANG']['MSC']['metamodel_filtersetting']['ignore_published'] as $strLabel) {
            $arrLabel[] = \sprintf($strLabel, $objAttribute->getName());
        }

        return [
            'ignore_published' . $this->get('id') => [
                'label'   => $arrLabel,
                'inputType'    => 'checkbox',
            ]
        ];
    }
}
