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
 * @author     Christopher Boelter <c.boelter@cogizz.de>
 * @author     David Maack <maack@men-at-work.de>
 * @author     Stefan Heimes <stefan_heimes@hotmail.com>
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2012-2017 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_checkbox/blob/master/LICENSE LGPL-3.0
 * @filesource
 */

$GLOBALS['TL_DCA']['tl_metamodel_attribute']['metapalettes']['checkbox extends _simpleattribute_'] = array
(
    '+advanced' => array('check_publish', 'check_listview')
);

$GLOBALS['TL_DCA']['tl_metamodel_attribute']['metasubpalettes'] = array
(
    'check_listview' => array('check_inverse', 'check_listviewicon', 'check_listviewicondisabled')
);

$GLOBALS['TL_DCA']['tl_metamodel_attribute']['fields']['check_publish'] = array
(
    'label'     => &$GLOBALS['TL_LANG']['tl_metamodel_attribute']['check_publish'],
    'exclude'   => true,
    'inputType' => 'checkbox',
    'eval'      => array
    (
        'tl_class' => 'w50'
    ),
);

$GLOBALS['TL_DCA']['tl_metamodel_attribute']['fields']['check_listview'] = array
(
    'label'     => &$GLOBALS['TL_LANG']['tl_metamodel_attribute']['check_listview'],
    'exclude'   => true,
    'inputType' => 'checkbox',
    'eval'      => array
    (
        'tl_class'       => 'w50',
        'submitOnChange' => true
    ),
);

$GLOBALS['TL_DCA']['tl_metamodel_attribute']['fields']['check_inverse'] = array
(
    'label'     => &$GLOBALS['TL_LANG']['tl_metamodel_attribute']['check_inverse'],
    'exclude'   => true,
    'inputType' => 'checkbox',
    'eval'      => array
    (
        'tl_class'       => 'w50 cbx'
    ),
);

$GLOBALS['TL_DCA']['tl_metamodel_attribute']['fields']['check_listviewicon'] = array
(
    'label'     => &$GLOBALS['TL_LANG']['tl_metamodel_attribute']['check_listviewicon'],
    'exclude'   => true,
    'inputType' => 'fileTree',
    'eval'      => array
    (
        'fieldType'  => 'radio',
        'files'      => true,
        'filesOnly'  => true,
        'extensions' => 'jpg,jpeg,gif,png,tif,tiff,svg',
        'tl_class'   => 'clr'
    )
);

$GLOBALS['TL_DCA']['tl_metamodel_attribute']['fields']['check_listviewicondisabled'] = array
(
    'label'     => &$GLOBALS['TL_LANG']['tl_metamodel_attribute']['check_listviewicondisabled'],
    'exclude'   => true,
    'inputType' => 'fileTree',
    'eval'      => array
    (
        'fieldType'  => 'radio',
        'files'      => true,
        'filesOnly'  => true,
        'extensions' => 'jpg,jpeg,gif,png,tif,tiff,svg',
        'tl_class'   => 'clr'
    )
);
