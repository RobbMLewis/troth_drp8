<?php

namespace Drupal\troth_elections\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

/**
 * Listens to the dynamic route events.
 */
class TrothElectionsRouteSubscriber extends RouteSubscriberBase {

  /**
   * {@inheritdoc}
   */
  protected function alterRoutes(RouteCollection $collection) {

    if ($route = $collection->get('troth_elections.base_form')) {
      $path = \Drupal::config('troth_elections.adminsettings')->get('troth_elections_url');
      if (substr($path, 0, 1) != '/') {
        $path = '/' . $path;
      }
      $route->setPath($path);
    }

    if ($route = $collection->get('troth_elections.make_nom_form')) {
      $path = \Drupal::config('troth_elections.adminsettings')->get('troth_elections_nom_url');
      if (substr($path, 0, 1) != '/') {
        $path = '/' . $path;
      }
      $route->setPath($path);
    }

    if ($route = $collection->get('troth_elections.accept_nom_form')) {
      $path = \Drupal::config('troth_elections.adminsettings')->get('troth_elections_nom_accept_url');
      if (substr($path, 0, 1) != '/') {
        $path = '/' . $path;
      }
      $route->setPath($path);
    }

    if ($route = $collection->get('troth_elections.nom_bio_form')) {
      $path = \Drupal::config('troth_elections.adminsettings')->get('troth_elections_nom_bio_url');
      if (substr($path, 0, 1) != '/') {
        $path = '/' . $path;
      }
      $route->setPath($path);
    }

    if ($route = $collection->get('troth_elections.nom_ballot_form')) {
      $path = \Drupal::config('troth_elections.adminsettings')->get('troth_elections_ballot_url');
      if (substr($path, 0, 1) != '/') {
        $path = '/' . $path;
      }
      $route->setPath($path);
    }

  }

}
