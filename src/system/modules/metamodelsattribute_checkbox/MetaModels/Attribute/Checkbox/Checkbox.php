<?php
/**
 * The MetaModels extension allows the creation of multiple collections of custom items,
 * each with its own unique set of selectable attributes, with attribute extendability.
 * The Front-End modules allow you to build powerful listing and filtering of the
 * data in each collection.
 *
 * PHP version 5
 * @package     MetaModels
 * @subpackage  AttributeCheckbox
 * @author      Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @copyright   The MetaModels team.
 * @license     LGPL.
 * @filesource
 */

namespace MetaModels\Attribute\Checkbox;

use MetaModels\Attribute\BaseSimple;

/**
 * This is the MetaModelAttribute class for handling checkbox fields.
 *
 * @package	   MetaModels
 * @subpackage AttributeCheckbox
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 */
class Checkbox extends BaseSimple
{
	/**
	 * Determine if the attribute is for publish usage.
	 *
	 * @return bool
	 */
	public function isPublishedField()
	{
		return $this->get('check_publish') == 1;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getSQLDataType()
	{
		return 'char(1) NOT NULL default \'\'';
	}

	/**
	 * {@inheritdoc}
	 */
	public function getAttributeSettingNames()
	{
		return array_merge(parent::getAttributeSettingNames(), array(
			'check_publish',
            'check_listview',
            'check_listviewicon',
            'check_listviewicondisabled',
			'filterable',
			'searchable',
			'sortable',
			'submitOnChange'
		));
	}

	/**
	 * {@inheritdoc}
	 */
	public function getFieldDefinition($arrOverrides = array())
	{
		$arrFieldDef              = parent::getFieldDefinition($arrOverrides);
		$arrFieldDef['inputType'] = 'checkbox';
		return $arrFieldDef;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getFilterOptions($arrIds, $usedOnly, &$arrCount = null)
	{
		if (!($arrIds || $usedOnly))
		{
			return array(
				'0' => $GLOBALS['TL_LANG']['MSC']['metamodelattribute_checkbox']['value_0'],
				'1' => $GLOBALS['TL_LANG']['MSC']['metamodelattribute_checkbox']['value_1']
			);
		}

		return parent::getFilterOptions($arrIds, $usedOnly, $arrCount);
	}
}
