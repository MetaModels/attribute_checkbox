<?php
/**
 * The MetaModels extension allows the creation of multiple collections of custom items,
 * each with its own unique set of selectable attributes, with attribute extendability.
 * The Front-End modules allow you to build powerful listing and filtering of the
 * data in each collection.
 *
 * PHP version 5
 *
 * @package     MetaModels
 * @subpackage  AttributeCheckbox
 * @author      Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @author      Sven Baumann <baumann.sv@gmail.com>
 * @copyright   The MetaModels team.
 * @license     LGPL.
 * @filesource
 */

// Fields.
$GLOBALS['TL_LANG']['tl_metamodel_attribute']['typeOptions']['checkbox']       = 'Checkbox';
$GLOBALS['TL_LANG']['tl_metamodel_attribute']['check_publish'][0]              = 'Publishing checkbox';
$GLOBALS['TL_LANG']['tl_metamodel_attribute']['check_publish'][1]              =
    'If this is selected, the items will appear in lists in the frontend (you will get the green "eye" for free.';
$GLOBALS['TL_LANG']['tl_metamodel_attribute']['check_inverse'][0]              = 'Inverse';
$GLOBALS['TL_LANG']['tl_metamodel_attribute']['check_inverse'][1]              =
    'If this is selected, this inverse the published mode (for example content elements).';
$GLOBALS['TL_LANG']['tl_metamodel_attribute']['check_listview'][0]             = 'Listview checkbox';
$GLOBALS['TL_LANG']['tl_metamodel_attribute']['check_listview'][1]             =
    'If this is selected, you will get an additional icon in the backend listview.';
$GLOBALS['TL_LANG']['tl_metamodel_attribute']['check_listviewicon'][0]         = 'Listview icon';
$GLOBALS['TL_LANG']['tl_metamodel_attribute']['check_listviewicon'][1]         =
    'The icon which is shown in the backend listview';
$GLOBALS['TL_LANG']['tl_metamodel_attribute']['check_listviewicondisabled'][0] = 'Listview icon disabled';
$GLOBALS['TL_LANG']['tl_metamodel_attribute']['check_listviewicondisabled'][1] = 'Listview icon disabled';
