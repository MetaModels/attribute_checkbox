<?php if (!defined('TL_ROOT')) die('You cannot access this file directly!');

/**
 * The MetaModels extension allows the creation of multiple collections of custom items,
 * each with its own unique set of selectable attributes, with attribute extendability.
 * The Front-End modules allow you to build powerful listing and filtering of the
 * data in each collection.
 *
 * PHP version 5
 * @package	   MetaModels
 * @subpackage AttributeText
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @copyright  CyberSpectrum, MEN AT WORK
 * @license    private
 * @filesource
 */

/**
 * This is the MetaModelAttribute class for handling text fields.
 * 
 * @package	   MetaModels
 * @subpackage AttributeCheckbox
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 */
class MetaModelAttributeCheckbox extends MetaModelAttributeSimple
{

	public function getSQLDataType()
	{
		return 'char(1) NOT NULL default \'\'';
	}

	public function getAttributeSettingNames()
	{
		return array_merge(parent::getAttributeSettingNames(), array(
			'parentCheckbox',
			'titleField',
			'width50',
			'insertBreak',
			'sortingField',
			'filteredField',
			'searchableField',
			'mandatory',
			'defValue',
			'uniqueItem',
			'formatPrePost',
			'format',
			'editGroups'
		));
	}

	public function getFieldDefinition()
	{
		$arrFieldDef = parent::getFieldDefinition();
		$arrFieldDef['inputType'] = 'checkbox';
		return $arrFieldDef;
	}

	public function parseValue($arrRowData, $strOutputFormat = 'html')
	{
		$arrResult = parent::parseValue($arrRowData, $strOutputFormat);
		$arrResult['html'] = $arrRowData[$this->getColName()];
		return $arrResult;
	}
}

?>