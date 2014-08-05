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

/**
 * Table tl_metamodel_attribute 
 */

$GLOBALS['TL_DCA']['tl_metamodel_attribute']['metapalettes']['checkbox extends _simpleattribute_'] = array
(
	'+advanced' => array('check_publish', 'check_listview')
);

$GLOBALS['TL_DCA']['tl_metamodel_attribute']['metasubpalettes'] = array
(
    'check_listview' => array('check_listviewicon', 'check_listviewicondisabled')
);

$GLOBALS['TL_DCA']['tl_metamodel_attribute']['fields']['check_publish'] = array
(
	'label'                 => &$GLOBALS['TL_LANG']['tl_metamodel_attribute']['check_publish'],
	'exclude'               => true,
	'inputType'             => 'checkbox',
	'eval'                  => array('tl_class' => 'w50'),
);

$GLOBALS['TL_DCA']['tl_metamodel_attribute']['fields']['check_listview'] = array
(
    'label'                 => &$GLOBALS['TL_LANG']['tl_metamodel_attribute']['check_listview'],
    'exclude'               => true,
    'inputType'             => 'checkbox',
    'eval'                  => array('tl_class' => 'w50', 'submitOnChange' => true),
);

$GLOBALS['TL_DCA']['tl_metamodel_attribute']['fields']['check_listviewicon'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_metamodel_attribute']['check_listviewicon'],
    'exclude'                 => true,
    'inputType'               => 'fileTree',
    'eval'                    => array
    (
        'fieldType'           => 'radio',
        'files'               => true,
        'filesOnly'           => true,
        'extensions'          => 'jpg,jpeg,gif,png,tif,tiff',
        'tl_class'            => 'clr'
    )
);

$GLOBALS['TL_DCA']['tl_metamodel_attribute']['fields']['check_listviewicondisabled'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_metamodel_attribute']['check_listviewicondisabled'],
    'exclude'                 => true,
    'inputType'               => 'fileTree',
    'eval'                    => array
    (
        'fieldType'           => 'radio',
        'files'               => true,
        'filesOnly'           => true,
        'extensions'          => 'jpg,jpeg,gif,png,tif,tiff',
        'tl_class'            => 'clr'
    )
);
