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
 * @author     Andreas Isaak <info@andreas-isaak.de>
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @copyright  The MetaModels team.
 * @license    LGPL.
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
            $objFilterRule = new SimpleQuery(sprintf(
                'SELECT id FROM %s WHERE %s=?',
                $this->getMetaModel()->getTableName(),
                $objAttribute->getColName()
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
