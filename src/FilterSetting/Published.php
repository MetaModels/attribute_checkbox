<?php

/**
 * This file is part of MetaModels/attribute_checkbox.
 *
 * (c) 2012-2024 The MetaModels team.
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
 * @author     Ingolf Steinhardt <info@e-spin.de>
 * @copyright  2012-2024 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_checkbox/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

namespace MetaModels\AttributeCheckboxBundle\FilterSetting;

use Contao\CoreBundle\Security\Authentication\Token\TokenChecker;
use Contao\System;
use Doctrine\DBAL\Connection;
use MetaModels\Attribute\IAttribute;
use MetaModels\Filter\FilterUrlBuilder;
use MetaModels\Filter\Setting\ICollection;
use MetaModels\Filter\Setting\Simple;
use MetaModels\Filter\IFilter;
use MetaModels\Filter\Rules\SimpleQuery;
use MetaModels\Filter\Rules\StaticIdList;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Published setting handler for checkboxes.
 */
class Published extends Simple
{
    /**
     * Database connection.
     *
     * @var Connection|null
     */
    private ?Connection $connection;

    /**
     * Constructor - initialize the object and store the parameters.
     *
     * @param ICollection                   $collection       The parenting filter settings object.
     * @param array                         $data             The attributes for this filter setting.
     * @param Connection|null               $connection       The database connection.
     * @param EventDispatcherInterface|null $dispatcher       The event dispatcher.
     * @param FilterUrlBuilder|null         $filterUrlBuilder The filter URL builder.
     */
    public function __construct(
        ICollection $collection,
        array $data,
        Connection $connection = null,
        EventDispatcherInterface $dispatcher = null,
        FilterUrlBuilder $filterUrlBuilder = null
    ) {
        parent::__construct($collection, $data, $dispatcher, $filterUrlBuilder);

        if (null === $connection) {
            // @codingStandardsIgnoreStart
            @trigger_error(
                'Connection is missing. It has to be passed in the constructor. Fallback will be dropped.',
                E_USER_DEPRECATED
            );
            // @codingStandardsIgnoreEnd
            $connection = System::getContainer()->get('database_connection');
            assert($connection instanceof Connection);
        }
        $this->connection = $connection;
    }

    /**
     * {@inheritdoc}
     */
    public function prepareRules(IFilter $objFilter, $arrFilterUrl)
    {
        if ($this->get('check_ignorepublished') && $arrFilterUrl['ignore_published' . $this->get('id')]) {
            return;
        }

        if (null === $this->connection) {
            return;
        }

        // Skip filter when in front end preview.
        $tokenChecker = System::getContainer()->get('contao.security.token_checker');
        assert($tokenChecker instanceof TokenChecker);

        if (
            $this->get('check_allowpreview')
            && $tokenChecker->isPreviewMode()
        ) {
            return;
        }

        $objAttribute = $this->getMetaModel()->getAttributeById((int) $this->get('attr_id'));

        // No attribute found, do not return anything.
        if (null === $objAttribute) {
            $objFilter->addFilterRule(new StaticIdList([]));

            return;
        }

        $publishedValue = 1;
        if ((int) $objAttribute->get('check_inverse') === 1) {
            $publishedValue = '';
        }

        $objFilter->addFilterRule(
            SimpleQuery::createFromQueryBuilder(
                $this
                    ->connection
                    ->createQueryBuilder()
                    ->select('id')
                    ->from($this->getMetaModel()->getTableName(), 't')
                    ->where('t.' . $objAttribute->getColName() . '=:value')
                    ->setParameter('value', $publishedValue)
            )
        );
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

        $attribute = $this->getMetaModel()->getAttributeById((int) $this->get('attr_id'));
        assert($attribute instanceof IAttribute);

        $translator = System::getContainer()->get('translator');
        assert($translator instanceof TranslatorInterface);

        return [
            'ignore_published' . $this->get('id') => [
                'label'       => $translator->trans(
                    'ignore_published.label',
                    ['name' => $attribute->getName()],
                    'tl_metamodel_filtersetting'
                ),
                'description' => $translator->trans(
                    'ignore_published.description',
                    ['name' => $attribute->getName()],
                    'tl_metamodel_filtersetting'
                ),
                'inputType'   => 'checkbox',
            ]
        ];
    }
}
