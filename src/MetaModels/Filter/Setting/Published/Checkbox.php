<?php

/**
 * This file is part of MetaModels/attribute_checkbox.
 *
 * (c) 2012-2018 The MetaModels team.
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
 * @copyright  2012-2018 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_checkbox/blob/master/LICENSE LGPL-3.0
 * @filesource
 */

namespace MetaModels\Filter\Setting\Published;

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
            $objFilterRule = new SimpleQuery(\sprintf(
                'SELECT id FROM %s WHERE %s=?',
                $this->getMetaModel()->getTableName(),
                $objAttribute->getColName()
            ), [$publishedValue]);
            $objFilter->addFilterRule($objFilterRule);

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
