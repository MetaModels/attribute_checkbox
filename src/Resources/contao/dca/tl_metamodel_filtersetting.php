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
 * @author     David Maack <maack@men-at-work.de>
 * @author     Stefan Heimes <stefan_heimes@hotmail.com>
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @author     Ingolf Steinhardt <info@e-spin.de>
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @copyright  2012-2019 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_checkbox/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

$GLOBALS['TL_DCA']['tl_metamodel_filtersetting']['metapalettes']['checkbox_published extends _attribute_']['+config'] =
    [
        'check_ignorepublished',
        'check_allowpreview'
    ];

$GLOBALS['TL_DCA']['tl_metamodel_filtersetting']['fields']['check_ignorepublished'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_metamodel_filtersetting']['check_ignorepublished'],
    'exclude'   => true,
    'inputType' => 'checkbox',
    'sql'       => 'char(1) NOT NULL default \'\'',
    'eval'      => [
        'alwaysSave' => true,
        'tl_class'   => 'w50 m12',
    ],
];

$GLOBALS['TL_DCA']['tl_metamodel_filtersetting']['fields']['check_allowpreview'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_metamodel_filtersetting']['check_allowpreview'],
    'exclude'   => true,
    'inputType' => 'checkbox',
    'sql'       => 'char(1) NOT NULL default \'\'',
    'eval'      => [
        'alwaysSave' => true,
        'tl_class'   => 'w50 m12',
    ],
];
