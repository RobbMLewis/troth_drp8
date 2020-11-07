<?php

/**
 * @file
 * Lists available colors and color schemes for the Troth theme.
 */

$info = [
  // Available colors and color labels used in theme.
  'fields' => [
    'base' => t('Text color'),
    'link' => t('Headings/Link'),
    'headingshadow' => t('Headings shadow'),
    'headertop' => t('Header top'),
    'headerbottom' => t('Header bottom'),
    'headermenu' => t('Main menu strip'),
    'headermenulink' => t('Main menu link'),
    'headermenuborder' => t('Main menu/Footer menu border'),
    'headermenubghover' => t('Main menu background hover'),
    'bannertop' => t('Banner top'),
    'bannerbottom' => t('Banner bottom'),
    'bannerborder' => t('Banner border'),
    'contenttop' => t('Content top'),
    'contentbottom' => t('Content bottom'),
    'blockbg' => t('Block background'),
    'blockshadow' => t('Block shadow'),
    'buttontop' => t('Button top'),
    'buttonbottom' => t('Button bottom'),
    'buttontext' => t('Button text'),
    'buttontextshadow' => t('Button text shadow'),
    'buttonboxshadow' => t('Button box shadow'),
    'buttonbghover' => t('Button background hover'),
    'footer' => t('Footer 1'),
    'footerlink' => t('Footer link'),
    'footerbottomtop' => t('Footer 2 top'),
    'footerbottombottom' => t('Footer 2 bottom'),
  ],
  // Pre-defined color schemes.
  'schemes' => [
    'default' => [
      'title' => t('Default'),
      'colors' => [
        'base' => '#7a7a7a',
        'link' => '#1487d4',
        'headingshadow' => '#eaf1ea',
        'headertop' => '#2f2f2f',
        'headerbottom' => '#1b1a1a',
        'headermenu' => '#222222',
        'headermenulink' => '#ffffff',
        'headermenuborder' => '#353535',
        'headermenubghover' => '#1b1b1b',
        'bannertop' => '#efeeee',
        'bannerbottom' => '#dadada',
        'bannerborder' => '#fcfcfc',
        'contenttop' => '#e8e8e8',
        'contentbottom' => '#efefef',
        'blockbg' => '#fdfdfd',
        'buttontop' => '#0093c5',
        'buttonbottom' => '#0079a2',
        'buttontext' => '#fefefe',
        'buttontextshadow' => '#003547',
        'buttonboxshadow' => '#939da2',
        'buttonbghover' => '#008ab8',
        'footer' => '#181818',
        'footerlink' => '#e4e4e4',
        'footerbottomtop' => '#262626',
        'footerbottombottom' => '#1a1a1a',
      ],
    ],
    'Blue' => [
      'title' => t('Blue'),
      'colors' => [
        'base' => '#000000',
        'link' => '#2b66a9',
        'headingshadow' => '#eaf1ea',
        'headertop' => '#2f2f2f',
        'headerbottom' => '#1b1a1a',
        'headermenu' => '#222222',
        'headermenulink' => '#ffffff',
        'headermenuborder' => '#353535',
        'headermenubghover' => '#dfdfdf',
        'bannertop' => '#efeeee',
        'bannerbottom' => '#dadada',
        'bannerborder' => '#fcfcfc',
        'contenttop' => '#e8e8e8',
        'contentbottom' => '#efefef',
        'blockbg' => '#fdfdfd',
        'buttontop' => '#003547',
        'buttonbottom' => '#2a7fcb',
        'buttontext' => '#fefefe',
        'buttontextshadow' => '#003547',
        'buttonboxshadow' => '#2a6ee5',
        'buttonbghover' => '#2a6ee5',
        'footer' => '#181818',
        'footerlink' => '#e4e4e4',
        'footerbottomtop' => '#262626',
        'footerbottombottom' => '#1a1a1a',
      ],
    ],
  ],

  // CSS files (excluding @import) to rewrite with new color scheme.
  'css' => [
    'css/colors.css',
  ],

  // Files to copy.
  'copy' => [
    'logo.svg',
  ],

  // Gradient definitions.
  'gradients' => [
    [
      // (x, y, width, height).
      'dimension' => [0, 0, 0, 0],
      // Direction of gradient ('vertical' or 'horizontal').
      'direction' => 'vertical',
      // Keys of colors to use for the gradient.
      'colors' => ['top', 'bottom'],
    ],
  ],

  // Preview files.
  'preview_library' => 'troth/color.preview',
  'preview_html' => 'color/preview.html',

];
