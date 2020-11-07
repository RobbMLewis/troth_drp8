<?php

namespace Drupal\troth_user;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceProviderBase;
use Drupal\Core\DependencyInjection\ServiceProviderInterface;

/**
 * Custom ServiceProvider.
 */
class TrothUserServiceProvider extends ServiceProviderBase implements ServiceProviderInterface {

  /**
   * {@inheritdoc}
   */
  public function alter(ContainerBuilder $container) {
    $definition = $container->getDefinition('entity.autocomplete_matcher');
    $definition->setClass('Drupal\troth_user\TrothUserAutocompleteMatcher');
  }

}
