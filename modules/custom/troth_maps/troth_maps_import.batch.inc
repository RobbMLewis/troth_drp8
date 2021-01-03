<?php

/**
 * @file
 * Batch processing.
 */

use Shapefile\ShapefileReader;
use Drupal\troth_maps\Entity\TrothMapsRegions;
use Drupal\troth_maps\Entity\TrothMapsCountry;
use Drupal\troth_maps\Entity\TrothMapsState;
use Drupal\troth_maps\Entity\TrothMapsCounty;
use Drupal\troth_maps\Entity\TrothMapsZipcodes;
use Drupal\troth_maps\Entity\TrothMapsDownloads;
use Drupal\geofield\GeoPHP\GeoPHPWrapper;
use Drupal\user\Entity\User;

/**
 * Import the shape files into country entities.
 */
function import_country($term, $fname, &$context, $limit = 1) {
  $context['message'] = t('Importing Countries');
  // Prepare the sandbox.
  if (empty($context['sandbox'])) {
    $context['sandbox']['processed'] = 0;

  }
  $tname = $term->name->value;
  $mapping = get_mapping($term);
  $countryfield = '';
  if (isset($mapping['country_abr'])) {
    $countryfield = 'country_abr';
  }
  elseif (isset($mapping['country'])) {
    $countryfield = 'country';
  }

  $countrystorage = \Drupal::entityTypeManager()
    ->getStorage('troth_maps_country');

  $shapefile = new ShapefileReader($fname);
  if (!isset($context['sandbox']['current_id'])) {
    $context['sandbox']['current_id'] = $shapefile->getCurrentRecord() - 1;
    $context['sandbox']['max'] = $shapefile->getTotRecords();
  }

  $currec = $context['sandbox']['current_id'] + 1;
  $maxrec = $currec + $limit - 1;
  if ($maxrec > $context['sandbox']['max']) {
    $maxrec = $context['sandbox']['max'];
  }

  for ($i = $currec; $i <= $maxrec; $i++) {
    $shapefile->setCurrentRecord($i);
    $rec = $shapefile->fetchRecord();
    $fielddata = $rec->getDataArray();
    $fielddata[$mapping[$countryfield]] = str_ireplace(' and ', ' & ', $fielddata[$mapping[$countryfield]]);
    $country = $fielddata[$mapping[$countryfield]];
    $results = \Drupal::entityQuery('troth_maps_country')
      ->condition($countryfield, $country, 'like')
      ->execute();
    if (count($results) == 0) {
      $create = [];
      $create['country'] = $country;
      foreach ($mapping as $tfield => $rfield) {
        $create[$tfield] = $fielddata[$rfield];
      }
      $entity = $countrystorage->create($create);
      $wkt = $rec->getWKT();
      $entity->setGeom($wkt);
      $entity->save();
      $context['sandbox']['processed']++;
      $context['sandbox']['current_id'] = $i;
    }
    else {
      $entities = $countrystorage->loadMultiple($results);
      foreach ($entities as $entity) {
        $wkt = $rec->getWKT();
        $entity->setGeom($wkt);
        foreach ($mapping as $tfield => $rfield) {
          if ($tfield == 'country') {
            $entity->setCountry($fielddata[$rfield]);
          }
          elseif ($tfield == 'country_abr') {
            $entity->setCountryAbr($fielddata[$rfield]);
          }
        }
        $entity->save();
        $context['sandbox']['processed']++;
        $context['sandbox']['current_id'] = $i;
        // We only want the first entity, so break out of loop.
        break;
      }
    }
  }
  if ($context['sandbox']['processed'] != $context['sandbox']['max']) {
    $context['finished'] = $context['sandbox']['processed'] / $context['sandbox']['max'];
  }
}

/**
 * Import the shape files into state entities.
 */
function import_state($term, $fname, &$context, $limit = 1) {
  // Prepare the sandbox.
  if (empty($context['sandbox'])) {
    $context['sandbox']['processed'] = 0;
  }
  $tname = $term->name->value;
  $country = $term->field_country->value;
  $mapping = get_mapping($term);
  $statefield = '';
  $context['message'] = t('Importing States for @country', ['@country' => $country]);
  if (isset($mapping['state_abr'])) {
    $statefield = 'state_abr';
  }
  elseif (isset($mapping['state_code'])) {
    $statefield = 'state_code';
  }
  elseif (isset($mapping['state'])) {
    $statefield = 'state';
  }

  $statestorage = \Drupal::entityTypeManager()
    ->getStorage('troth_maps_state');

  $shapefile = new ShapefileReader($fname);
  if (!isset($context['sandbox']['current_id'])) {
    $context['sandbox']['current_id'] = $shapefile->getCurrentRecord() - 1;
    $context['sandbox']['max'] = $shapefile->getTotRecords();
  }

  $currec = $context['sandbox']['current_id'] + 1;
  $maxrec = $currec + $limit - 1;
  if ($maxrec > $context['sandbox']['max']) {
    $maxrec = $context['sandbox']['max'];
  }

  for ($i = $currec; $i <= $maxrec; $i++) {
    $shapefile->setCurrentRecord($i);
    $rec = $shapefile->fetchRecord();
    $fielddata = $rec->getDataArray();
    $state = $fielddata[$mapping[$statefield]];
    $results = \Drupal::entityQuery('troth_maps_state')
      ->condition('country', $country, 'Like')
      ->condition($statefield, $state, 'like')
      ->execute();
    if (count($results) == 0) {
      $create = [];
      $create['country'] = $country;
      foreach ($mapping as $tfield => $rfield) {
        $create[$tfield] = $fielddata[$rfield];
      }
      $entity = $statestorage->create($create);
      $wkt = $rec->getWKT();
      $entity->setGeom($wkt);
      $entity->save();
      $context['sandbox']['processed']++;
      $context['sandbox']['current_id'] = $i;
    }
    else {
      $entities = $statestorage->loadMultiple($results);
      foreach ($entities as $entity) {
        $wkt = $rec->getWKT();
        $entity->setGeom($wkt);
        foreach ($mapping as $tfield => $rfield) {
          if ($tfield == 'state') {
            $entity->setState($fielddata[$rfield]);
          }
          elseif ($tfield == 'state_abr') {
            $entity->setStateAbr($fielddata[$rfield]);
          }
          elseif ($tfield == 'state_code') {
            $entity->setStateAbr($fielddata[$rfield]);
          }
          elseif ($tfield == 'state_no') {
            $entity->setStateNo($fielddata[$rfield]);
          }
        }
        $entity->save();
        $context['sandbox']['processed']++;
        $context['sandbox']['current_id'] = $i;
        // We only want the first entity, so break out of loop.
        break;
      }
    }
  }
  if ($context['sandbox']['processed'] != $context['sandbox']['max']) {
    $context['finished'] = $context['sandbox']['processed'] / $context['sandbox']['max'];
  }
}

/**
 * Import the shape files into county entities.
 */
function import_county($term, $fname, &$context, $limit = 50) {
  // Prepare the sandbox.
  if (empty($context['sandbox'])) {
    $context['sandbox']['processed'] = 0;
  }
  $tname = $term->name->value;
  $country = $term->field_country->value;
  $mapping = get_mapping($term);
  $statefield = '';
  if (isset($mapping['state_abr'])) {
    $statefield = 'state_abr';
  }
  elseif (isset($mapping['state_code'])) {
    $statefield = 'state_code';
  }
  elseif (isset($mapping['state_no'])) {
    $statefield = 'state_no';
  }
  elseif (isset($mapping['state'])) {
    $statefield = 'state';
  }

  $countyfield = '';
  if (isset($mapping['county_full'])) {
    $countyfield = 'county_full';
  }
  elseif (isset($mapping['county'])) {
    $countyfield = 'county';
  }

  $countystorage = \Drupal::entityTypeManager()
    ->getStorage('troth_maps_county');
  $statestorage = \Drupal::entityTypeManager()
    ->getStorage('troth_maps_state');

  $shapefile = new ShapefileReader($fname);
  if (!isset($context['sandbox']['current_id'])) {
    $context['sandbox']['current_id'] = $shapefile->getCurrentRecord() - 1;
    $context['sandbox']['max'] = $shapefile->getTotRecords();
  }

  $currec = $context['sandbox']['current_id'] + 1;
  $maxrec = $currec + $limit - 1;
  if ($maxrec > $context['sandbox']['max']) {
    $maxrec = $context['sandbox']['max'];
  }
  $context['message'] = t('Importing Counties for @country #@start of @max', [
    '@country' => $country,
    '@start' => $currec,
    '@max' => $context['sandbox']['max'],
  ]);

  for ($i = $currec; $i <= $maxrec; $i++) {
    $shapefile->setCurrentRecord($i);
    $rec = $shapefile->fetchRecord();
    $fielddata = $rec->getDataArray();

    // Have to query to get state abr.
    $state = $fielddata[$mapping[$statefield]];
    $results = \Drupal::entityQuery('troth_maps_state')
      ->condition('country', $country, 'Like')
      ->condition($statefield, $state, 'like')
      ->execute();
    if (count($results) == 0) {
      $context['sandbox']['processed']++;
      $context['sandbox']['current_id'] = $i;
    }
    else {
      $entities = $statestorage->loadMultiple($results);
      $state = '';
      foreach ($entities as $entity) {
        $state = $entity->getStateAbr();
        break;
      }

      $county = $fielddata[$mapping[$countyfield]];
      $results = \Drupal::entityQuery('troth_maps_county')
        ->condition('country', $country, 'Like')
        ->condition('state', $state, 'like')
        ->condition($countyfield, $county, 'like')
        ->execute();
      if (count($results) == 0) {
        $create = [];
        $create['country'] = $country;
        $create['state'] = $state;
        foreach ($mapping as $tfield => $rfield) {
          $create[$tfield] = $fielddata[$rfield];
        }
        if (!isset($create['county_full'])) {
          $create['county_full'] = $create['county'] . " County";
        }
        $entity = $countystorage->create($create);
        $wkt = $rec->getWKT();
        $entity->setGeom($wkt);
        $entity->save();
        $context['sandbox']['processed']++;
        $context['sandbox']['current_id'] = $i;
      }
      else {
        $entities = $countystorage->loadMultiple($results);
        foreach ($entities as $entity) {
          $wkt = $rec->getWKT();
          $entity->setGeom($wkt);
          foreach ($mapping as $tfield => $rfield) {
            if ($tfield == 'county') {
              $entity->setCounty($fielddata[$rfield]);
            }
            elseif ($tfield == 'county_full') {
              $entity->setCountyFull($fielddata[$rfield]);
            }
          }
          $entity->save();
          $context['sandbox']['processed']++;
          $context['sandbox']['current_id'] = $i;
          // We only want the first entity, so break out of loop.
          break;
        }
      }
    }
  }
  if ($context['sandbox']['processed'] != $context['sandbox']['max']) {
    $context['finished'] = $context['sandbox']['processed'] / $context['sandbox']['max'];
  }
}

/**
 * Import the shape files into zipcode entities.
 */
function import_zipcode($term, $fname, $country, &$context, $limit = 100) {
  // Prepare the sandbox.
  if (empty($context['sandbox'])) {
    $context['sandbox']['processed'] = 0;
  }
  $tname = $term->name->value;
  $country = $term->field_country->value;
  $mapping = get_mapping($term);

  $zipcodestorage = \Drupal::entityTypeManager()
    ->getStorage('troth_maps_zipcodes');

  $shapefile = new ShapefileReader($fname);
  if (!isset($context['sandbox']['current_id'])) {
    $context['sandbox']['current_id'] = $shapefile->getCurrentRecord() - 1;
    $context['sandbox']['max'] = $shapefile->getTotRecords();
  }

  $currec = $context['sandbox']['current_id'] + 1;
  $maxrec = $currec + $limit - 1;
  if ($maxrec > $context['sandbox']['max']) {
    $maxrec = $context['sandbox']['max'];
  }
  $context['message'] = t('Importing Zipcodes Shapes for @country record @start of @end', [
    '@country' => $country,
    '@start' => $context['sandbox']['progress'],
    '@end' => $context['sandbox']['max'],
  ]);
  for ($i = $currec; $i <= $maxrec; $i++) {
    $shapefile->setCurrentRecord($i);
    $rec = $shapefile->fetchRecord();
    $fielddata = $rec->getDataArray();

    // Have to query to get state abr.
    $zipcode = $fielddata[$mapping['zipcode']];
    $results = \Drupal::entityQuery('troth_maps_zipcodes')
      ->condition('zipcode', $zipcode, 'like')
      ->condition('country', $country, 'like')
      ->execute();

    if (count($results) == 0) {
      if (isset($mapping['city'])) {
        $create = ['zipcode' => $zipcode, 'country' => $country];
        foreach ($mapping as $field => $rowid) {
          $create[$field] = $row[$rowid];
        }
        $entity = $zipcodestorage->create($create);
        $wkt = $rec->getWKT();
        $entity->setGeom($wkt);
        $entity->save();
      }
      $context['sandbox']['processed']++;
      $context['sandbox']['current_id'] = $i;
    }
    else {
      $entities = $zipcodestorage->loadMultiple($results);
      foreach ($entities as $entity) {
        $wkt = $rec->getWKT();
        $entity->setGeom($wkt);
        $entity->save();
        $context['sandbox']['processed']++;
        $context['sandbox']['current_id'] = $i;
        // We only want the first entity, so break out of loop.
        break;
      }
    }
  }
  if ($context['sandbox']['processed'] != $context['sandbox']['max']) {
    $context['finished'] = $context['sandbox']['processed'] / $context['sandbox']['max'];
  }
}

/**
 * Import the txt files into zipcode entities.
 */
function import_zipcode_txt($term, $fname, $country, &$context) {
  $limit = 100;
  $tname = $term->name->value;
  $mapping = get_mapping($term);
  if (!file_exists($fname)) {
    $context['finished'] = 1;
    $context['message'] = t('Zipcodes Data for @country was not found', [
      '@country' => $country,
    ]);
  }
  else {
    $file = new SplFileObject($fname, 'r');
    // Prepare the sandbox.
    if (empty($context['sandbox'])) {
      $context['sandbox']['file_pointer_position'] = $file->ftell();
      // Go to last line to set max.
      $file->seek(PHP_INT_MAX);
      $context['sandbox']['max'] = $file->key() + 1;
      $context['sandbox']['progress'] = 0;
    }
    $context['message'] = t('Importing Zipcodes Data for @country record @start of @end', [
      '@country' => $country,
      '@start' => $context['sandbox']['progress'],
      '@end' => $context['sandbox']['max'],
    ]);
    $zipcodestorage = \Drupal::entityTypeManager()
      ->getStorage('troth_maps_zipcodes');
    $start = $context['sandbox']['file_pointer_position'];
    $file->fseek($start);
    for ($i = 0; $i < $limit; $i++) {
      $row = $file->fgetcsv("\t");
      if ($file->eof()) {
        $i = $limit + 1;
      }
      $zipcode = $row[$mapping['zipcode']];
      if ($zipcode != '' && $zipcode != NULL) {
        $country = $row[$mapping['country']];
        $results = \Drupal::entityQuery('troth_maps_zipcodes')
          ->condition('zipcode', $zipcode, 'like')
          ->condition('country', $country, 'like')
          ->execute();
        if (count($results) == 0) {
          $create = ['zipcode' => $zipcode, 'country' => $country];
          foreach ($mapping as $field => $rowid) {
            $create[$field] = $row[$rowid];
          }
          if (strpos($create['city'], '(')) {
            // We have a long name.... remove from ( on.
            $city = explode('(', $create['city']);
            $create['city'] = $city[0];
          }
          if (isset($create['latitude'])) {
            $lat = $create['latitude'];
            unset($create['latitude']);
          }
          if (isset($create['longitude'])) {
            $lon = $create['longitude'];
            unset($create['longitude']);
          }
          if (isset($create['city']) && $create['city'] != NULL && $create['city'] != '') {
            $entity = $zipcodestorage->create($create);
            if (isset($lat) && isset($lon)) {
              $entity->setLatLong($lat, $lon);
            }
            $entity->save();
          }
        }
        else {
          $entities = $zipcodestorage->loadMultiple($results);
          foreach ($entities as $entity) {
            $latitude = '';
            $longitude = '';
            foreach ($mapping as $field => $rowid) {
              if ($field == 'city') {
                $entity->setCity($row[$rowid]);
              }
              if ($field == 'state') {
                $entity->setState($row[$rowid]);
              }
              if ($field == 'state_code') {
                $entity->setStateCode($row[$rowid]);
              }
              if ($field == 'county') {
                $entity->setCounty($row[$rowid]);
              }
              if ($field == 'county_code') {
                $entity->setCountyCode($row[$rowid]);
              }
              if ($field == 'community') {
                $entity->setCommunity($row[$rowid]);
              }
              if ($field == 'community_code') {
                $entity->setCommunityCode($row[$rowid]);
              }
              if ($field == 'latitude') {
                $latitude = ($row[$rowid]);
              }
              if ($field == 'longitude') {
                $longitude = ($row[$rowid]);
              }
            }
            if ($latitude != '' && $longitude != '') {
              $entity->setLatLong($latitude, $longitude);
            }
            $entity->save();
            break;
          }
        }
        $context['sandbox']['progress']++;
      }
    }
    $context['sandbox']['file_pointer_position'] = $file->ftell();
    $context['finished'] = $context['sandbox']['progress'] / $context['sandbox']['max'];
    if ($file->eof()) {
      $context['finished'] = 1;
    }
  }
  if ($context['finished'] >= 1) {
    $download = \Drupal::entityQuery('troth_maps_downloads')
      ->condition('country', $country, '=')->execute();
    $downloads = TrothMapsDownloads::loadMultiple($download);
    foreach ($downloads as $download) {
      $download->setLastUpdate(time());
      $download->save();
    }
  }
}

/**
 * Clean up the files.
 */
function delete_files($files) {
  foreach ($files as $file) {
    if (is_file($file)) {
      unlink($file);
    }
  }
}

/**
 * Remove region in zipcode entity.
 */
function unset_regions($field, $unset, &$context) {
  $context['message'] = t('Unsetting Regions');
  $limit = 100;
  // Prepare the sandbox.
  if (empty($context['sandbox'])) {
    $context['sandbox']['max'] = count($unset) - 1;
    $context['sandbox']['current'] = 0;
  }

  $start = $context['sandbox']['current'];
  $max = $start + $limit - 1;
  if ($max > $context['sandbox']['max']) {
    $max = $context['sandbox']['max'];
  }
  for ($i = $start; $i <= $max; $i++) {
    $entity = TrothMapsZipcodes::load($unset[$i]);
    if ($field == 'locregid') {
      $entity->setLocRegid(NULL);
    }
    else {
      $entity->setRegid(NULL);
    }
    $entity->save();
    $context['sandbox']['current']++;
  }
  $context['finished'] = $context['sandbox']['current'] / $context['sandbox']['max'];

}

/**
 * Set region in zipcode entity.
 */
function set_regions($field, $set, $regid, &$context) {
  $context['message'] = t('Setting Regions');
  $limit = 100;
  // Prepare the sandbox.
  if (empty($context['sandbox'])) {
    $context['sandbox']['max'] = count($set) - 1;
    $context['sandbox']['current'] = 0;
  }

  $start = $context['sandbox']['current'];
  $max = $start + $limit - 1;
  if ($max > $context['sandbox']['max']) {
    $max = $context['sandbox']['max'];
  }
  for ($i = $start; $i <= $max; $i++) {
    $entity = TrothMapsZipcodes::load($set[$i]);
    if ($field == 'locregid') {
      $entity->setLocRegid($regid);
    }
    else {
      $entity->setRegid($regid);
    }
    $entity->save();
    $context['sandbox']['current']++;
  }
  $context['finished'] = $context['sandbox']['current'] / $context['sandbox']['max'];

}

/**
 * Count the number of zipcodes in a region.
 */
function count_regions($field, $regid, $region_name, &$context) {
  $context['message'] = t('Counting Regions');
  $count = \Drupal::entityQuery('troth_maps_zipcodes')
    ->condition($field, $regid, '=')
    ->count()->execute();
  \Drupal::messenger()->addStatus(t('There are @count Zip Codes in @name', [
    '@count' => $count,
    '@name' => $region_name,
  ]));
  $context['finished'] = 1;
}

/**
 * Create the region shape files.
 */
function create_region_shape($regid, &$context) {
  $context['message'] = t('Creating Region Shape for regid # @regid', ['@regid' => $regid]);
  $limit = 10;
  // Prepare the sandbox.
  if (empty($context['sandbox'])) {
    $context['sandbox']['country'] = [];
    $context['sandbox']['state'] = [];
    $context['sandbox']['county'] = [];
    $context['sandbox']['zipcode'] = [];
    $region = TrothMapsRegions::load($regid);
    $context['sandbox']['region'] = $region;
    $build_values = $region->getBuildValues();
    $unset_states = [];

    if ($build_values['zipcode'] != '') {
      // Get all zipcode entities.
      $country = $build_values['country'][0];
      $zipcode_list = explode(',', $build_values['zipcode']);
      $query = \Drupal::entityQuery('troth_maps_zipcodes')
        ->condition('country', $country, 'LIKE');
      $dbor = $query->orConditionGroup();
      foreach ($zipcode_list as $zipcode_range) {
        $zips = explode('-', $zipcode_range);
        $dbor->condition('zipcode', $zips, 'BETWEEN');
        $query->condition($dbor);
      }
      $context['sandbox']['zipcodes'] = $query->execute();
    }
    if ($build_values['county'] != '') {
      // Get all county entities.
      // Get some state entities, if listed as all counties.
      $country = $build_values['country'][0];
      $county_query = \Drupal::entityQuery('troth_maps_county')
        ->condition('country', $country, 'LIKE');
      $state_query = \Drupal::entityQuery('troth_maps_state')
        ->condition('country', $country, 'LIKE');
      $stateor = $state_query->orConditionGroup();
      $usestate = 0;
      $counties = explode("\n", $build_values['county']);
      $dbor = $county_query->orConditionGroup();
      foreach ($counties as $row) {
        [$state, $countylist] = explode(':', $row);
        if (trim($countylist) == 'all') {
          $usestate = 1;
          $stateor->condition('state_abr', $state, '=');
        }
        else {
          // We need to keep track of partial states to remove later.
          if (!isset($unset_states[$country])) {
            $unset_states[$country] = [];
          }
          $unset_states[$country][] = $state;
          $countylist = explode(',', $countylist);
          $countyor = [];
          foreach ($countylist as $county) {
            $countyor[] = trim($county);
          }
          $dband = $county_query->andConditionGroup();
          $dband->condition('state', $state, '=');
          $dbor2 = $county_query->orConditionGroup();
          $dbor2->condition('county', $countyor, 'in');
          $dbor2->condition('county_full', $countyor, 'in');
          $dband->condition($dbor2);
          $dbor->condition($dband);
        }
      }
      $county_query->condition($dbor);
      $state_query->condition($stateor);
      $context['sandbox']['county'] = $county_query->execute();
      if ($usestate == 1) {
        $context['sandbox']['state'] = $state_query->execute();
      }
    }
    if ($build_values['state'] != '') {
      // Get all state entities.
      $country = $build_values['country'][0];
      $query = \Drupal::entityQuery('troth_maps_state')
        ->condition('country', $country, 'LIKE');
      $statelist = explode(',', $build_values['state']);
      $states = [];
      foreach ($statelist as $state) {
        $states[] = trim($state);
      }
      $query->condition('state_abr', $states, 'in');
      $entities = $query->execute();
      // We merge because we may have data from county.
      $context['sandbox']['state'] = array_merge($context['sandbox']['state'], $entities);
    }
    if ($build_values['country'] != '') {
      // Get all country entities.
      $context['sandbox']['country'] = \Drupal::entityQuery('troth_maps_country')
        ->condition('country_abr', $build_values['country'], 'in')
        ->execute();
    }
    // Uniquify the results.
    $context['sandbox']['country'] = array_unique($context['sandbox']['country']);
    $context['sandbox']['state'] = array_unique($context['sandbox']['state']);
    $context['sandbox']['county'] = array_unique($context['sandbox']['county']);
    $context['sandbox']['zipcode'] = array_unique($context['sandbox']['zipcode']);

    // Remove any states that should be partial.
    foreach ($unset_states as $country => $states) {
      $entities = \Drupal::entityQuery('troth_maps_state')
        ->condition('country', $country, 'LIKE')
        ->condition('state_abr', $states, 'in')
        ->execute();
      foreach ($entities as $eid) {
        if (($key = array_search($eid, $context['sandbox']['state'])) !== FALSE) {
          unset($context['sandbox']['state'][$key]);
        }
      }
    }

    // Set the max baxed off of entities.
    $context['sandbox']['max'] = 0;
    $context['sandbox']['max'] += count($context['sandbox']['zipcode']);
    $context['sandbox']['max'] += count($context['sandbox']['county']);
    $context['sandbox']['max'] += count($context['sandbox']['state']);
    // We only add country in the event that we don't have a smaller zone.
    if ($context['sandbox']['max'] == 0) {
      $context['sandbox']['max'] += count($context['sandbox']['country']);
    }
    else {
      $context['sandbox']['country'] = [];
    }
    $context['sandbox']['current'] = 0;
  }

  $geophp = new GeoPHPWrapper();
  if (isset($context['sandbox']['wkt'])) {
    // We've been through once, load the geom.
    // Geom for some reason doesn't stay in sandbox.
    $context['sandbox']['geom'] = $geophp->load($context['sandbox']['wkt'], 'wkt');
  }

  // Go through the entities.
  for ($i = 0; $i < $limit; $i++) {
    if (count($context['sandbox']['country']) > 0) {
      // Do country.
      foreach ($context['sandbox']['country'] as $key => $country) {
        $country = TrothMapsCountry::load($country);
        $cgeom = $country->getGeom();
        if ($cgeom != '') {
          $cgeom = $geophp->load($cgeom, 'wkt');
          if (!isset($context['sandbox']['geom'])) {
            $context['sandbox']['geom'] = $cgeom;
          }
          else {
            $geom = $context['sandbox']['geom'];
            if ($geom->geos() != FALSE) {
              $context['sandbox']['geom'] = $geom->union($cgeom);
            }
            else {
              $context['sandbox']['geom'] = troth_maps_mysql_union($geom, $cgeom);
            }
          }
          unset($context['sandbox']['country'][$key]);
          break;
        }
      }
    }
    elseif (count($context['sandbox']['state']) > 0) {
      // Do state.
      foreach ($context['sandbox']['state'] as $key => $state) {
        $state = TrothMapsState::load($state);
        $sgeom = $state->getGeom();
        if ($sgeom != '') {
          $sgeom = $geophp->load($sgeom, 'wkt');
          if (!isset($context['sandbox']['geom'])) {
            $context['sandbox']['geom'] = $sgeom;
          }
          else {
            $geom = $context['sandbox']['geom'];
            if ($geom->geos() != FALSE) {
              $context['sandbox']['geom'] = $geom->union($sgeom);
            }
            else {
              $context['sandbox']['geom'] = troth_maps_mysql_union($geom, $sgeom);
            }
          }
          unset($context['sandbox']['state'][$key]);
          break;
        }
      }
    }
    elseif (count($context['sandbox']['county']) > 0) {
      // Do county.
      foreach ($context['sandbox']['county'] as $key => $county) {
        $county = TrothMapsCounty::load($county);
        $cgeom = $county->getGeom();
        if ($cgeom != '') {
          $cgeom = $geophp->load($cgeom, 'wkt');
          if (!isset($context['sandbox']['geom'])) {
            $context['sandbox']['geom'] = $cgeom;
          }
          else {
            $geom = $context['sandbox']['geom'];
            if ($geom->geos() != FALSE) {
              $context['sandbox']['geom'] = $geom->union($cgeom);
            }
            else {
              $context['sandbox']['geom'] = troth_maps_mysql_union($geom, $cgeom);
            }
          }
          unset($context['sandbox']['county'][$key]);
          break;
        }
      }
    }
    elseif (count($context['sandbox']['zipcode']) > 0) {
      // Do Zipcode.
      foreach ($context['sandbox']['zipcode'] as $key => $zipcode) {
        $zipcode = TrothMapsZipcodes::load($zipcode);
        $zgeom = $zipcode->getGeom();
        if ($zgeom != '') {
          $zgeom = $geophp->load($zgeom, 'wkt');
          if (!isset($context['sandbox']['geom'])) {
            $context['sandbox']['geom'] = $zgeom;
          }
          else {
            $geom = $context['sandbox']['geom'];
            if ($geom->geos() != FALSE) {
              $context['sandbox']['geom'] = $geom->union($zgeom);
            }
            else {
              $context['sandbox']['geom'] = troth_maps_mysql_union($geom, $zgeom);
            }
          }
          unset($context['sandbox']['zipcode'][$key]);
          break;
        }
      }
    }
    else {
      $context['sandbox']['current'] = $context['sandbox']['max'] - 1;
    }
    $context['sandbox']['current']++;
  }
  // Save the wkt for next run.
  $context['sandbox']['wkt'] = $context['sandbox']['geom']->out('wkt');
  $context['finished'] = $context['sandbox']['current'] / $context['sandbox']['max'];
  if ($context['finished'] >= 1) {
    // Add the geom to the region entity.
    $geom = $context['sandbox']['geom'];
    $region = $context['sandbox']['region'];
    $region->setGeom($geom->out('wkt'), 'wkt');
    $region->save();
  }
}

/**
 * Update user region.
 */
function update_user_region(&$context) {
  $limit = 10;
  // Prepare the sandbox.
  if (empty($context['sandbox'])) {
    $count = \Drupal::entityQuery('user')
      ->condition('uid', 10, '>=')
      ->count()->execute();
    $context['sandbox']['max'] = $count - 1;
    $context['sandbox']['current'] = 0;
  }
  $context['message'] = t("Update User's region, update # @current of @max.", [
    '@current' => $context['sandbox']['current'],
    '@max' => $context['sandbox']['max'],
  ]);
  $current = $context['sandbox']['current'];
  $uids = \Drupal::entityQuery('user')
    ->condition('uid', 10, '>=')
    ->range($current, $limit)
    ->execute();
  foreach ($uids as $uid) {
    $account = User::load($uid);
    $account->save();
    $context['sandbox']['current']++;
  }

  $context['finished'] = $context['sandbox']['current'] / $context['sandbox']['max'];
}

/**
 * Finishes the batch processing.
 */
function batch_finish_callback($success, $results, $operations) {
  // The 'success' parameter means no fatal PHP errors were detected. All
  // other error management should be handled using 'results'.
  if ($success) {
    $message = \Drupal::translation()
      ->formatPlural(count($results), 'One post processed.', '@count posts processed.');
    $message = implode(',', $results);
  }
  else {
    $message = t('Finished with an error.');
  }
  drupal_set_message($message);
}

/**
 * Get field mapping from term.
 */
function get_mapping($term) {
  $mapping = explode("\n", $term->field_mapping->value);
  $stored = [];
  foreach ($mapping as $map) {
    $map = explode('=>', $map);
    if (count($map) == 2 && trim($map[1]) != '') {
      $stored[trim($map[0])] = trim($map[1]);
    }
  }
  return $stored;
}
