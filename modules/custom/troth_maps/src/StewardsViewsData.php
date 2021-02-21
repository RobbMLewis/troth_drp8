<?php

namespace Drupal\troth_maps;

use Drupal\views\EntityViewsData;

/**
 * Provides views data for order items.
 */
class StewardsViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();
    // Additional information for Views integration, such as table joins, can be
    // put here.
    $data['troth_steward']['table'][] = [];
    $data['troth_steward']['table']['group'] = t('Stewards');
    $data['troth_steward']['table']['provider'] = 'stewards';
    $data['troth_steward']['table']['base'] = [
     // Identifier (primary) field in this table for Views.
      'field' => 'region_id',
    // Label in the UI.
      'title' => t('Stewards'),
    // Longer description in the UI. Required.
      'help' => t('troth_steward table .'),
      'weight' => -10,
    ];

    $data['troth_maps_regions']['troth_steward_data'] = [
      'title' => t('Stewards'),
      'help' => t('Relations with troth_steward table'),
      'relationship' => [
      // Views name of the table being joined to from foo.
        'base' => 'troth_steward',
      // Database field name in example_table for the join.
        'base field' => 'region_id',
      // Real database field name in foo for the join, to override
      // 'unique_dummy_name'.
        'field' => 'id',
      // ID of relationship handler plugin to use.
        'id' => 'standard',
        'label' => t('Stewards'),
      ],
    ];
    return $data;
  }

}
