<?php

namespace Drupal\troth_maps\Commands;

use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drush\Commands\DrushCommands;
use Drupal\taxonomy\Entity\Term;

/**
 * A Drush commandfile.
 *
 * In addition to this file, you need a drush.services.yml
 * in root of your module, and a composer.json file that provides the name
 * of the services file to use.
 *
 * See these files for an example of injecting Drupal services:
 *   - http://cgit.drupalcode.org/devel/tree/src/Commands/DevelCommands.php
 *   - http://cgit.drupalcode.org/devel/tree/drush.services.yml
 */
class DrushBatchProcessingCommands extends DrushCommands {

  /**
   * Logger service.
   *
   * @var \Drupal\Core\Logger\LoggerChannelFactoryInterface
   */
  private $loggerChannelFactory;

  /**
   * Constructs a new DrushBatchProcessingCommands object.
   *
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $loggerChannelFactory
   *   Logger service.
   */
  public function __construct(LoggerChannelFactoryInterface $loggerChannelFactory) {
    $this->loggerChannelFactory = $loggerChannelFactory;
  }

  /**
   * Update Maps.
   *
   * @command troth_maps:update
   * @aliases troth-maps-update
   */
  public function updateMaps() {
    // 1. Log the start of the script.
    $this->loggerChannelFactory->get('troth_maps')->info('Update maps batch operations start');

    // Get the zipfiles.
    $zipfiles = troth_maps_download_zips();
    $batch = [
      'title' => t('Importing Shapes'),
      'init_message' => t('The shape files are being imported'),
      'operations' => [],
      'file' => drupal_get_path('module', 'troth_maps') . '/troth_maps_import.batch.inc',
      'finished' => 'batch_finish_callback',
    ];

    // Get CSV file TID's and SHP file TID's.
    $csv = \Drupal::entityQuery('taxonomy_term')
      ->condition('vid', 'troth_maps', '=')
      ->condition('field_file_type', 'csv', '=')
      ->execute();
    $txt = \Drupal::entityQuery('taxonomy_term')
      ->condition('vid', 'troth_maps', '=')
      ->condition('field_file_type', 'txt', '=')
      ->execute();
    $shp = \Drupal::entityQuery('taxonomy_term')
      ->condition('vid', 'troth_maps', '=')
      ->condition('field_file_type', 'shp', '=')
      ->execute();

    $order = ['country', 'state', 'county', 'zipcode'];
    $skip = [];
    $delfiles = [];
    foreach ($order as $table) {
      $csvfiles = [];
      $txtfiles = [];
      $shpfiles = [];
      // re-order the files so CSV comes first.
      foreach ($zipfiles[$table] as $tid => $path) {
        $ntid = $tid;
        if (strpos($tid, ':') != NULL) {
          [$ntid, $country] = explode(':', $tid);
        }

        if (in_array($ntid, $csv)) {
          $csvfiles[$tid] = $path;
        }
        elseif (in_array($ntid, $txt)) {
          $txtfiles[$tid] = $path;
        }
        elseif (in_array($ntid, $shp)) {
          $shpfiles[$tid] = $path;
        }
      }
      $data = $csvfiles + $txtfiles + $shpfiles;

      // Go through each file and add to batch.
      foreach ($data as $tid => $path) {
        $country = '';
        if (strpos($tid, ':') != NULL) {
          [$tid, $country] = explode(':', $tid);
        }
        $term = Term::load($tid);
        if (!in_array($term->name->value, $skip)) {
          $filetype = $term->field_file_type->value;
          $table = $term->field_entity->value;
          $extfiles = troth_maps_extract_zip($path);
          $use = preg_grep('/' . $filetype . '$/', array_keys($extfiles));
          while ($fname = $extfiles[array_shift($use)]) {
            $delfiles = array_merge($delfiles, $extfiles);
            if ($filetype == 'shp') {
              if ($table == 'zipcode') {
                $ops = [$term, $fname, $country];
                $batch['operations'][] = ['import_zipcode', $ops];
              }
              elseif ($table == 'country') {
                $batch['operations'][] = ['import_country', [$term, $fname]];
              }
              elseif ($table == 'state') {
                $batch['operations'][] = ['import_state', [$term, $fname]];
              }
              elseif ($table == 'county') {
                $batch['operations'][] = ['import_county', [$term, $fname]];
              }
              else {
                break;
              }
            }
            elseif ($filetype == 'csv') {
              break;
            }
            elseif ($filetype == 'txt') {
              if ($table == 'zipcode') {
                $searchname = basename($fname);
                if (preg_match("/$country/i", $searchname)) {
                  $ops = [$term, $fname, $country];
                  $batch['operations'][] = ['import_zipcode_txt', $ops];
                }
              }
              else {
                break;
              }
            }
          }
        }
      }
    }
    $batch['operations'][] = ['delete_files', [$delfiles]];
    $regids = \Drupal::entityQuery('troth_maps_regions')
      ->condition('region_type', 'special', '!=')
      ->condition('archived', 1, '!=')
      ->execute();
    foreach ($regids as $regid) {
      $batch['operations'][] = ['create_region_shape', [$regid]];
    }
    // 5. Add batch operations as new batch sets.
    batch_set($batch);
    // 6. Process the batch sets.
    drush_backend_batch_process();
    // 6. Show some information.
    $this->logger()->notice("Batch operations end.");
    // 7. Log some information.
    $this->loggerChannelFactory->get('troth_maps')->info('Update batch operations end.');
  }
  /**
   * Update User Region.
   *
   * @command troth_maps:update-region
   * @aliases troth-maps-update-region
   */
  public function updateUserRegion() {
    // 1. Log the start of the script.
    $this->loggerChannelFactory->get('troth_maps')->info('Update maps batch operations start');

    $batch = [
      'title' => t('Updateing User Region'),
      'init_message' => t('User regions are being updated.'),
      'operations' => [],
      'file' => drupal_get_path('module', 'troth_maps') . '/troth_maps_import.batch.inc',
      'finished' => 'batch_finish_callback',
    ];
    $batch['operations'][] = ['update_user_region', []];
    // 5. Add batch operations as new batch sets.
    batch_set($batch);
    // 6. Process the batch sets.
    drush_backend_batch_process();
    // 6. Show some information.
    $this->logger()->notice("Batch operations end.");
    // 7. Log some information.
    $this->loggerChannelFactory->get('troth_maps')->info('Update batch operations end.');
  }
}
