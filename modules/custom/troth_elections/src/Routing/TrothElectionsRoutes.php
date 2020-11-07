<?php

namespace Drupal\troth_elections\Routing;

use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Drupal\Core\Datetime\DrupalDateTime;

/**
 * Defines dynamic routes.
 */
class TrothElectionsRoutes {

  /**
   * {@inheritdoc}
   */
  public function routes() {
    $route_collection = new RouteCollection();
    $language = \Drupal::languageManager()->getCurrentLanguage()->getId();
    $votingStartDate = new DrupalDateTime(\Drupal::config('troth_elections.adminsettings')->get('troth_elections_voting_open_date'), 'America/Los_Angeles');
    $year = $votingStartDate->format('Y');

    // Year Page.
    if (\Drupal::service('path.alias_storage')->aliasExists('/members/elections/' . $year, $language) == FALSE) {
      $route = new Route(
      // Path to attach this route to:
      '/members/elections/' . $year,
      // Route defaults:
      [
        '_controller' => '\Drupal\troth_elections\Controller\TrothElectionsController::summaryPage',
        '_title' => $year . " Elections",
      ],
      // Route requirements:
      [
        '_role'  => 'member+administrator',
      ]
      );
      // Add the route under the name 'example.content'.
      $collection = 'troth_elections.year_summary_' . $year;
      $route_collection->add($collection, $route);
    }

    // Nomination Page.
    if (\Drupal::service('path.alias_storage')->aliasExists('/members/elections/' . $year . '/nominations', $language) == FALSE) {
      $route = new Route(
      // Path to attach this route to:
      '/members/elections/' . $year . '/nominations',
      // Route defaults:
      [
        '_controller' => '\Drupal\troth_elections\Controller\TrothElectionsController::nominationDisplay',
        '_title' => $year . " Nominations",
      ],
      // Route requirements:
      [
        '_role'  => 'member+administrator',
      ]
      );
      // Add the route under the name 'example.content'.
      $collection = 'troth_elections.year_nominations_' . $year;
      $route_collection->add($collection, $route);
    }

    // Candidate Statemsnts Page.
    if (\Drupal::service('path.alias_storage')->aliasExists('/members/elections/' . $year . '/candidates', $language) == FALSE) {
      $route = new Route(
      // Path to attach this route to:
      '/members/elections/' . $year . '/candidates',
      // Route defaults:
      [
        '_controller' => '\Drupal\troth_elections\Controller\TrothElectionsController::candidateStatements',
        '_title' => $year . " Candidate Statements",
      ],
      // Route requirements:
      [
        '_role'  => 'member+administrator',
      ]
      );
      // Add the route under the name 'example.content'.
      $collection = 'troth_elections.year_candidates_' . $year;
      $route_collection->add($collection, $route);
    }

    // Voters Page.
    if (\Drupal::service('path.alias_storage')->aliasExists('/members/elections/' . $year . '/voters', $language) == FALSE) {
      $route = new Route(
      // Path to attach this route to:
      '/members/elections/' . $year . '/voters',
      // Route defaults:
      [
        '_controller' => '\Drupal\troth_elections\Controller\TrothElectionsController::voters',
        '_title' => $year . " Voters",
      ],
      // Route requirements:
      [
        '_role'  => 'member+administrator',
      ]
      );
      // Add the route under the name 'example.content'.
      $collection = 'troth_elections.year_voters_' . $year;
      $route_collection->add($collection, $route);
    }

    return $route_collection;
  }

}
