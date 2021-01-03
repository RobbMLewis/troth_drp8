<?php

namespace Drupal\troth_maps\Plugin\views\argument_default;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Cache\CacheableDependencyInterface;
use Drupal\views\Plugin\views\argument_default\ArgumentDefaultPluginBase;

/**
 * Default argument plugin to extract the current user's region.
 *
 * This plugin actually has no options so it does not need to do a great deal.
 *
 * @ViewsArgumentDefault(
 *   id = "troth_maps_region_id",
 *   title = @Translation("Region ID from logged in user")
 * )
 */
class CurrentUserRid extends ArgumentDefaultPluginBase implements CacheableDependencyInterface {

  /**
   * {@inheritdoc}
   */
  public function getArgument() {
    $account = user_load(\Drupal::currentUser()->id());
    return $account->field_region->getValue()[0]['target_id'];
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheMaxAge() {
    return Cache::PERMANENT;
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheContexts() {
    return ['user'];
  }

}
