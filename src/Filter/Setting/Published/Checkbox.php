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
 * @author     Andreas Isaak <info@andreas-isaak.de>
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2012-2017 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_checkbox/blob/master/LICENSE LGPL-3.0
 * @filesource
 */

namespace MetaModels\Attribute\Checkbox\Filter\Setting\Published;

use Contao\System;
use Doctrine\DBAL\Connection;
use MetaModels\Filter\Setting\ICollection;
use MetaModels\Filter\Setting\Simple;
use MetaModels\Filter\IFilter;
use MetaModels\Filter\Rules\SimpleQuery;
use MetaModels\Filter\Rules\StaticIdList;

/**
 * Published setting handler for checkboxes.
 */
class Checkbox extends Simple
{
    /**
     * Database connection.
     *
     * @var Connection
     */
    private $connection;

    /**
     * Constructor - initialize the object and store the parameters.
     *
     * @param ICollection $collection The parenting filter settings object.
     *
     * @param array       $data       The attributes for this filter setting.
     *
     * @param Connection  $connection The database connection.
     */
    public function __construct(ICollection $collection, array $data, Connection $connection = null)
    {
        parent::__construct($collection, $data);

        if (null === $connection) {
            @trigger_error(
                'You should pass a doctrine database connection to "' . __METHOD__ . '".',
                E_USER_DEPRECATED
            );

            $connection = System::getContainer()->get('database_connection');
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

        // Skip filter when in front end preview.
        if ($this->get('check_allowpreview') && BE_USER_LOGGED_IN) {
            return;
        }

        $objAttribute = $this->getMetaModel()->getAttributeById($this->get('attr_id'));

        $publishedValue = 1;
        if (intval($objAttribute->get('check_publish')) === 1
            && intval($objAttribute->get('check_inverse')) === 1
        ) {
            $publishedValue = '';
        }

        if ($objAttribute) {
            $objFilterRule = new SimpleQuery(sprintf(
                'SELECT id FROM %s WHERE %s=?',
                $this->getMetaModel()->getTableName(),
                $objAttribute->getColName(),
                $this->connection
            ), array($publishedValue));
            $objFilter->addFilterRule($objFilterRule);

            return;
        }
        // No attribute found, do not return anyting.
        $objFilter->addFilterRule(new StaticIdList(array()));
    }

    /**
     * {@inheritdoc}
     */
    public function getParameters()
    {
        return ($this->get('check_ignorepublished')) ? array('ignore_published' . $this->get('id')) : array();
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
            return array();
        }

        $objAttribute = $this->getMetaModel()->getAttributeById($this->get('attr_id'));

        $arrLabel = array();
        foreach ($GLOBALS['TL_LANG']['MSC']['metamodel_filtersetting']['ignore_published'] as $strLabel) {
            $arrLabel[] = sprintf($strLabel, $objAttribute->getName());
        }

        return array(
            'ignore_published' . $this->get('id') => array
            (
                'label'   => $arrLabel,
                'inputType'    => 'checkbox',
            )
        );
    }
}
