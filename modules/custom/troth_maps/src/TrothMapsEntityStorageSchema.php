<?php

namespace Drupal\troth_maps;

use Drupal\Core\Entity\ContentEntityTypeInterface;
use Drupal\Core\Entity\Sql\SqlContentEntityStorageSchema;

/**
 * Defines the troth maps schema handler.
 */
class TrothMapsEntityStorageSchema extends SqlContentEntityStorageSchema {

  /**
   * {@inheritdoc}
   */
  protected function getEntitySchema(ContentEntityTypeInterface $entity_type, $reset = FALSE) {
    $schema = parent::getEntitySchema($entity_type, $reset);

    // Then target your annoying field and set the 'not null' key to FALSE!
    if (!empty($schema['troth_maps_zipcodes']['fields']['state'])) {
      $schema['troth_maps_zipcodes']['fields']['state']['not null'] = FALSE;
    }
    // Then target your annoying field and set the 'not null' key to FALSE!
    if (!empty($schema['troth_maps_zipcodes']['fields']['state_code'])) {
      $schema['troth_maps_zipcodes']['fields']['state_code']['not null'] = FALSE;
    }
    // Then target your annoying field and set the 'not null' key to FALSE!
    if (!empty($schema['troth_maps_zipcodes']['fields']['county'])) {
      $schema['troth_maps_zipcodes']['fields']['county']['not null'] = FALSE;
    }
    // Then target your annoying field and set the 'not null' key to FALSE!
    if (!empty($schema['troth_maps_zipcodes']['fields']['county_code'])) {
      $schema['troth_maps_zipcodes']['fields']['county_code']['not null'] = FALSE;
    }
    // Then target your annoying field and set the 'not null' key to FALSE!
    if (!empty($schema['troth_maps_zipcodes']['fields']['community'])) {
      $schema['troth_maps_zipcodes']['fields']['community']['not null'] = FALSE;
    }
    // Then target your annoying field and set the 'not null' key to FALSE!
    if (!empty($schema['troth_maps_zipcodes']['fields']['community_code'])) {
      $schema['troth_maps_zipcodes']['fields']['community_code']['not null'] = FALSE;
    }
    // Then target your annoying field and set the 'not null' key to FALSE!
    if (!empty($schema['troth_maps_zipcodes']['fields']['regid'])) {
      $schema['troth_maps_zipcodes']['fields']['regid']['not null'] = FALSE;
    }
    // Then target your annoying field and set the 'not null' key to FALSE!
    if (!empty($schema['troth_maps_zipcodes']['fields']['locregid'])) {
      $schema['troth_maps_zipcodes']['fields']['locregid']['not null'] = FALSE;
    }

    // Then target your annoying field and set the 'not null' key to FALSE!
    if (!empty($schema['troth_maps_zipcodes']['fields']['latlong__value'])) {
      $schema['troth_maps_zipcodes']['fields']['latlong__value']['not null'] = FALSE;
    }
    // Then target your annoying field and set the 'not null' key to FALSE!
    if (!empty($schema['troth_maps_zipcodes']['fields']['latlong__geo_type'])) {
      $schema['troth_maps_zipcodes']['fields']['latlong__geo_type']['not null'] = FALSE;
    }
    // Then target your annoying field and set the 'not null' key to FALSE!
    if (!empty($schema['troth_maps_zipcodes']['fields']['latlong__lat'])) {
      $schema['troth_maps_zipcodes']['fields']['latlong__lat']['not null'] = FALSE;
    }
    // Then target your annoying field and set the 'not null' key to FALSE!
    if (!empty($schema['troth_maps_zipcodes']['fields']['latlong__lon'])) {
      $schema['troth_maps_zipcodes']['fields']['latlong__lon']['not null'] = FALSE;
    }
    // Then target your annoying field and set the 'not null' key to FALSE!
    if (!empty($schema['troth_maps_zipcodes']['fields']['latlong__left'])) {
      $schema['troth_maps_zipcodes']['fields']['latlong__left']['not null'] = FALSE;
    }
    // Then target your annoying field and set the 'not null' key to FALSE!
    if (!empty($schema['troth_maps_zipcodes']['fields']['latlong__top'])) {
      $schema['troth_maps_zipcodes']['fields']['latlong__top']['not null'] = FALSE;
    }
    // Then target your annoying field and set the 'not null' key to FALSE!
    if (!empty($schema['troth_maps_zipcodes']['fields']['latlong__right'])) {
      $schema['troth_maps_zipcodes']['fields']['latlong__right']['not null'] = FALSE;
    }
    // Then target your annoying field and set the 'not null' key to FALSE!
    if (!empty($schema['troth_maps_zipcodes']['fields']['latlong__bottom'])) {
      $schema['troth_maps_zipcodes']['fields']['latlong__bottom']['not null'] = FALSE;
    }
    // Then target your annoying field and set the 'not null' key to FALSE!
    if (!empty($schema['troth_maps_zipcodes']['fields']['latlong__geohash'])) {
      $schema['troth_maps_zipcodes']['fields']['latlong__geohash']['not null'] = FALSE;
    }
    // Then target your annoying field and set the 'not null' key to FALSE!
    if (!empty($schema['troth_maps_zipcodes']['fields']['geom__value'])) {
      $schema['troth_maps_zipcodes']['fields']['geom__value']['not null'] = FALSE;
    }
    // Then target your annoying field and set the 'not null' key to FALSE!
    if (!empty($schema['troth_maps_zipcodes']['fields']['geom__geo_type'])) {
      $schema['troth_maps_zipcodes']['fields']['geom__geo_type']['not null'] = FALSE;
    }
    // Then target your annoying field and set the 'not null' key to FALSE!
    if (!empty($schema['troth_maps_zipcodes']['fields']['geom__lat'])) {
      $schema['troth_maps_zipcodes']['fields']['geom__lat']['not null'] = FALSE;
    }
    // Then target your annoying field and set the 'not null' key to FALSE!
    if (!empty($schema['troth_maps_zipcodes']['fields']['geom__lon'])) {
      $schema['troth_maps_zipcodes']['fields']['geom__lon']['not null'] = FALSE;
    }
    // Then target your annoying field and set the 'not null' key to FALSE!
    if (!empty($schema['troth_maps_zipcodes']['fields']['geom__left'])) {
      $schema['troth_maps_zipcodes']['fields']['geom__left']['not null'] = FALSE;
    }
    // Then target your annoying field and set the 'not null' key to FALSE!
    if (!empty($schema['troth_maps_zipcodes']['fields']['geom__top'])) {
      $schema['troth_maps_zipcodes']['fields']['geom__top']['not null'] = FALSE;
    }
    // Then target your annoying field and set the 'not null' key to FALSE!
    if (!empty($schema['troth_maps_zipcodes']['fields']['geom__right'])) {
      $schema['troth_maps_zipcodes']['fields']['geom__right']['not null'] = FALSE;
    }
    // Then target your annoying field and set the 'not null' key to FALSE!
    if (!empty($schema['troth_maps_zipcodes']['fields']['geom__bottom'])) {
      $schema['troth_maps_zipcodes']['fields']['geom__bottom']['not null'] = FALSE;
    }
    // Then target your annoying field and set the 'not null' key to FALSE!
    if (!empty($schema['troth_maps_zipcodes']['fields']['geom__geohash'])) {
      $schema['troth_maps_zipcodes']['fields']['geom__geohash']['not null'] = FALSE;
    }
    // Then target your annoying field and set the 'not null' key to FALSE!
    if (!empty($schema['troth_maps_country']['fields']['geom__value'])) {
      $schema['troth_maps_country']['fields']['geom__value']['not null'] = FALSE;
    }
    // Then target your annoying field and set the 'not null' key to FALSE!
    if (!empty($schema['troth_maps_country']['fields']['geom__geo_type'])) {
      $schema['troth_maps_country']['fields']['geom__geo_type']['not null'] = FALSE;
    }
    // Then target your annoying field and set the 'not null' key to FALSE!
    if (!empty($schema['troth_maps_country']['fields']['geom__lat'])) {
      $schema['troth_maps_country']['fields']['geom__lat']['not null'] = FALSE;
    }
    // Then target your annoying field and set the 'not null' key to FALSE!
    if (!empty($schema['troth_maps_country']['fields']['geom__lon'])) {
      $schema['troth_maps_country']['fields']['geom__lon']['not null'] = FALSE;
    }
    // Then target your annoying field and set the 'not null' key to FALSE!
    if (!empty($schema['troth_maps_country']['fields']['geom__left'])) {
      $schema['troth_maps_country']['fields']['geom__left']['not null'] = FALSE;
    }
    // Then target your annoying field and set the 'not null' key to FALSE!
    if (!empty($schema['troth_maps_country']['fields']['geom__top'])) {
      $schema['troth_maps_country']['fields']['geom__top']['not null'] = FALSE;
    }
    // Then target your annoying field and set the 'not null' key to FALSE!
    if (!empty($schema['troth_maps_country']['fields']['geom__right'])) {
      $schema['troth_maps_country']['fields']['geom__right']['not null'] = FALSE;
    }
    // Then target your annoying field and set the 'not null' key to FALSE!
    if (!empty($schema['troth_maps_country']['fields']['geom__bottom'])) {
      $schema['troth_maps_country']['fields']['geom__bottom']['not null'] = FALSE;
    }
    // Then target your annoying field and set the 'not null' key to FALSE!
    if (!empty($schema['troth_maps_country']['fields']['geom__geohash'])) {
      $schema['troth_maps_country']['fields']['geom__geohash']['not null'] = FALSE;
    }
    // Then target your annoying field and set the 'not null' key to FALSE!
    if (!empty($schema['troth_maps_state']['fields']['geom__value'])) {
      $schema['troth_maps_state']['fields']['state_no']['not null'] = FALSE;
    }
    // Then target your annoying field and set the 'not null' key to FALSE!
    if (!empty($schema['troth_maps_state']['fields']['geom__value'])) {
      $schema['troth_maps_state']['fields']['geom__value']['not null'] = FALSE;
    }
    // Then target your annoying field and set the 'not null' key to FALSE!
    if (!empty($schema['troth_maps_state']['fields']['geom__geo_type'])) {
      $schema['troth_maps_state']['fields']['geom__geo_type']['not null'] = FALSE;
    }
    // Then target your annoying field and set the 'not null' key to FALSE!
    if (!empty($schema['troth_maps_state']['fields']['geom__lat'])) {
      $schema['troth_maps_state']['fields']['geom__lat']['not null'] = FALSE;
    }
    // Then target your annoying field and set the 'not null' key to FALSE!
    if (!empty($schema['troth_maps_state']['fields']['geom__lon'])) {
      $schema['troth_maps_state']['fields']['geom__lon']['not null'] = FALSE;
    }
    // Then target your annoying field and set the 'not null' key to FALSE!
    if (!empty($schema['troth_maps_state']['fields']['geom__left'])) {
      $schema['troth_maps_state']['fields']['geom__left']['not null'] = FALSE;
    }
    // Then target your annoying field and set the 'not null' key to FALSE!
    if (!empty($schema['troth_maps_state']['fields']['geom__top'])) {
      $schema['troth_maps_state']['fields']['geom__top']['not null'] = FALSE;
    }
    // Then target your annoying field and set the 'not null' key to FALSE!
    if (!empty($schema['troth_maps_state']['fields']['geom__right'])) {
      $schema['troth_maps_state']['fields']['geom__right']['not null'] = FALSE;
    }
    // Then target your annoying field and set the 'not null' key to FALSE!
    if (!empty($schema['troth_maps_state']['fields']['geom__bottom'])) {
      $schema['troth_maps_state']['fields']['geom__bottom']['not null'] = FALSE;
    }
    // Then target your annoying field and set the 'not null' key to FALSE!
    if (!empty($schema['troth_maps_state']['fields']['geom__geohash'])) {
      $schema['troth_maps_state']['fields']['geom__geohash']['not null'] = FALSE;
    }
    // Then target your annoying field and set the 'not null' key to FALSE!
    if (!empty($schema['troth_maps_county']['fields']['geom__value'])) {
      $schema['troth_maps_county']['fields']['geom__value']['not null'] = FALSE;
    }
    // Then target your annoying field and set the 'not null' key to FALSE!
    if (!empty($schema['troth_maps_county']['fields']['geom__geo_type'])) {
      $schema['troth_maps_county']['fields']['geom__geo_type']['not null'] = FALSE;
    }
    // Then target your annoying field and set the 'not null' key to FALSE!
    if (!empty($schema['troth_maps_county']['fields']['geom__lat'])) {
      $schema['troth_maps_county']['fields']['geom__lat']['not null'] = FALSE;
    }
    // Then target your annoying field and set the 'not null' key to FALSE!
    if (!empty($schema['troth_maps_county']['fields']['geom__lon'])) {
      $schema['troth_maps_county']['fields']['geom__lon']['not null'] = FALSE;
    }
    // Then target your annoying field and set the 'not null' key to FALSE!
    if (!empty($schema['troth_maps_county']['fields']['geom__left'])) {
      $schema['troth_maps_county']['fields']['geom__left']['not null'] = FALSE;
    }
    // Then target your annoying field and set the 'not null' key to FALSE!
    if (!empty($schema['troth_maps_county']['fields']['geom__top'])) {
      $schema['troth_maps_county']['fields']['geom__top']['not null'] = FALSE;
    }
    // Then target your annoying field and set the 'not null' key to FALSE!
    if (!empty($schema['troth_maps_county']['fields']['geom__right'])) {
      $schema['troth_maps_county']['fields']['geom__right']['not null'] = FALSE;
    }
    // Then target your annoying field and set the 'not null' key to FALSE!
    if (!empty($schema['troth_maps_county']['fields']['geom__bottom'])) {
      $schema['troth_maps_county']['fields']['geom__bottom']['not null'] = FALSE;
    }
    // Then target your annoying field and set the 'not null' key to FALSE!
    if (!empty($schema['troth_maps_county']['fields']['geom__geohash'])) {
      $schema['troth_maps_county']['fields']['geom__geohash']['not null'] = FALSE;
    }
    // Then target your annoying field and set the 'not null' key to FALSE!
    if (!empty($schema['troth_maps_regions']['fields']['geom__value'])) {
      $schema['troth_maps_regions']['fields']['geom__value']['not null'] = FALSE;
    }
    // Then target your annoying field and set the 'not null' key to FALSE!
    if (!empty($schema['troth_maps_regions']['fields']['geom__geo_type'])) {
      $schema['troth_maps_regions']['fields']['geom__geo_type']['not null'] = FALSE;
    }
    // Then target your annoying field and set the 'not null' key to FALSE!
    if (!empty($schema['troth_maps_regions']['fields']['geom__lat'])) {
      $schema['troth_maps_regions']['fields']['geom__lat']['not null'] = FALSE;
    }
    // Then target your annoying field and set the 'not null' key to FALSE!
    if (!empty($schema['troth_maps_regions']['fields']['geom__lon'])) {
      $schema['troth_maps_regions']['fields']['geom__lon']['not null'] = FALSE;
    }
    // Then target your annoying field and set the 'not null' key to FALSE!
    if (!empty($schema['troth_maps_regions']['fields']['geom__left'])) {
      $schema['troth_maps_regions']['fields']['geom__left']['not null'] = FALSE;
    }
    // Then target your annoying field and set the 'not null' key to FALSE!
    if (!empty($schema['troth_maps_regions']['fields']['geom__top'])) {
      $schema['troth_maps_regions']['fields']['geom__top']['not null'] = FALSE;
    }
    // Then target your annoying field and set the 'not null' key to FALSE!
    if (!empty($schema['troth_maps_regions']['fields']['geom__right'])) {
      $schema['troth_maps_regions']['fields']['geom__right']['not null'] = FALSE;
    }
    // Then target your annoying field and set the 'not null' key to FALSE!
    if (!empty($schema['troth_maps_regions']['fields']['geom__bottom'])) {
      $schema['troth_maps_regions']['fields']['geom__bottom']['not null'] = FALSE;
    }
    // Then target your annoying field and set the 'not null' key to FALSE!
    if (!empty($schema['troth_maps_regions']['fields']['geom__geohash'])) {
      $schema['troth_maps_regions']['fields']['geom__geohash']['not null'] = FALSE;
    }
    // Then target your annoying field and set the 'not null' key to FALSE!
    if (!empty($schema['troth_maps_regions']['fields']['kml_color'])) {
      $schema['troth_maps_regions']['fields']['kml_color']['not null'] = FALSE;
    }
    // Then target your annoying field and set the 'not null' key to FALSE!
    if (!empty($schema['troth_maps_regions']['fields']['border_color'])) {
      $schema['troth_maps_regions']['fields']['border_color']['not null'] = FALSE;
    }
    // Then target your annoying field and set the 'not null' key to FALSE!
    if (!empty($schema['troth_maps_regions']['fields']['transparency'])) {
      $schema['troth_maps_regions']['fields']['transparency']['not null'] = FALSE;
    }
    return $schema;
  }

}
