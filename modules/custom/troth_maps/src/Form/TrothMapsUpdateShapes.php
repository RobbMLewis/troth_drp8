<?php

namespace Drupal\troth_maps\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\taxonomy\Entity\Term;

/**
 * Update the shape files.
 */
class TrothMapsUpdateShapes extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'troth_maps_update_shapes';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $termpage = Url::fromUserInput("/admin/structure/taxonomy/manage/troth_maps/overview");
    $form['header'] = [
      '#type' => 'item',
      '#markup' => t('<p><b>This page will launch a batch process that will take a while to run.  Please have a stable internet connection and plan to leave this page open for up to an hour before clicking the button.</b>  To change where the files are downloaded from, go to the <a href=":link">troth maps taxonomy page</a></p>', [
        ':link' => $termpage->toString(),
      ]),
    ];
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Update Shape Files'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
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
    batch_set($batch);
  }

}
