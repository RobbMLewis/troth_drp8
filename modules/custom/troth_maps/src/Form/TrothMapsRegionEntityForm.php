<?php

namespace Drupal\troth_maps\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\troth_maps\Entity\TrothMapsRegions;
use Drupal\troth_maps\Entity\TrothMapsZipcodes;
use CommerceGuys\Addressing\Country\CountryRepository;

/**
 * Class TrothOfficerEntityForm.
 */
class TrothMapsRegionEntityForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);
    if (!$this->entity->getRegionType()) {
      $regiontype = \Drupal::routeMatch()->getParameter('regiontype');
    }
    else {
      $regiontype = $this->entity->getRegionType();
    }
    
    $form['prefix'] = [
      '#type' => 'item',
      '#markup' => t('<p>Modify or update various map and region related parts of the website</p>'),
    ];
    $form['region_type'] = [
      '#type' => 'hidden',
      '#value' => $regiontype,
    ];
    $form['regid'] = [
      '#type' => 'hidden',
      '#value' => $this->entity->getRegid(),
    ];
    $form['region_name'] = [
      '#title' => t('Name of the Region'),
      '#type' => 'textfield',
      '#required' => TRUE,
      '#default_value' => $this->entity->getRegionName() ?: $this->entity->getRegionName(),
    ];
    $form['archived'] = [
      '#title' => t('Archive the region?'),
      '#type' => 'checkbox',
      '#required' => FALSE,
      '#default_value' => $this->entity->getArchived() ?: $this->entity->getArchived(),
    ];
    $form['region_email'] = [
      '#title' => t('Region Email Address'),
      '#type' => 'email',
      '#required' => TRUE,
      '#default_value' => $this->entity->getRegionEmail() ?: $this->entity->getRegionEmail(),
      '#pattern' => '(.+)@thetroth.org',
    ];
    if ($regiontype != 'special') {
      // Get countries from address module and create entities.
      if (\Drupal::moduleHandler()->moduleExists('jquery_colorpicker') && $regiontype == 'region') {
        $form['kml_color'] = [
          '#type' => 'jquery_colorpicker',
          '#title' => t('Map Color'),
          '#default_value' => $this->entity->getKmlColor() ? $this->entity->getKmlColor() : '#FF0000',
        ];
      }

      $build_values = $this->entity->getBuildValues();

      $rep = new CountryRepository();
      $countries = $rep->getList();
      $form['multi_country'] = [
        '#title' => t('Multi-Country Region'),
        '#type' => 'checkbox',
      ];

      $form['country'] = [
        '#title' => t('Country'),
        '#type' => 'select',
        '#options' => $countries,
        '#default_value' => 'US',
        '#required' => TRUE,
        '#states' => [
          'visible' => [
            ':input[name="multi_country"]' => ['checked' => FALSE],
          ],
          'invisible' => [
            ':input[name="multi_country"]' => ['checked' => TRUE],
          ],
        ],
      ];
      $form['country_multi'] = [
        '#title' => t('Country'),
        '#type' => 'select',
        '#multiple' => TRUE,
        '#description' => t('Select one or more countries'),
        '#options' => $countries,
        '#default_value' => 'US',
        '#required' => TRUE,
        '#states' => [
          'visible' => [
            ':input[name="multi_country"]' => ['checked' => TRUE],
          ],
          'invisible' => [
            ':input[name="multi_country"]' => ['checked' => FALSE],
          ],
        ],
      ];

      if (!isset($build_values['country']) || count($build_values['country']) == 0) {
        $form['multi_country']['#default_value'] = 0;
        $form['country_multi']['#default_value'] = 'US';
      }
      elseif (count($build_values['country']) > 1) {
        $form['multi_country']['#default_value'] = 1;
        $form['country_multi']['#default_value'] = $build_values['country'];
      }
      else {
        $form['multi_country']['#default_value'] = 0;
        $form['country']['#default_value'] = $build_values['country'];
      }

      $form['state'] = [
        '#title' => t('States'),
        '#description' => t('Enter a comma separated list of states.'),
        '#type' => 'textfield',
        '#size' => '100',
        '#default_value' => $build_values['state'] ?: $build_values['state'],
      ];
      $form['county'] = [
        '#title' => t('Counties'),
        '#description' => t('Enter a comma separated list of counties.  If you have multiple staes, please use the following format: State abbreviation followed by a colon then comma separated list of counties.  Each state seperated by a new line.  You can use "all" if you need to select all counties in a state. As example:<br/><hr/>STATE:county,county,county<br/>STATE:all'),
        '#type' => 'textarea',
        '#default_value' => $build_values['county'] ?: $build_values['county'],
      ];
      $form['zipcode'] = [
        '#title' => t('Zip/Postal Codes'),
        '#description' => t('Enter #####-##### to designate a range.<br/>If you have multiple ranges, separate with a comma.'),
        '#type' => 'textarea',
        '#default_value' => $build_values['zipcode'] ?: $build_values['zipcode'],
      ];
    }

    return $form;

  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $type = $form_state->getValue('region_type');
    $regid = $form_state->getValue('regid');
    if (!$form_state->getValue('archived')) {
      if ($type != 'special') {
        if ($form_state->getValue('multi_country') == 0) {
          $country = [$form_state->getValue('country')];
        }
        else {
          $country = $form_state->getValue('country_multi');
        }
        $state = trim($form_state->getValue('state'));
        $county = trim($form_state->getValue('county'));
        $zipcode = trim($form_state->getValue('zipcode'));

        if ($country == 'US' && $state == '' && $county == '' && $zipcode == '') {
          $form_state->setErrorByName('country', t('There must be conditions set in at least one of the following fields: State, County, Zip/Postal Code'));
        }

        if ($county != '' && !strpos($county, ':')) {
          $form_state->setErrorByName('county', t('The county format is wrong. It must be STATE:County,County...'));
        }

        // Check to see if the definition overlaps.
        // Gather all the information we need for searching.
        $zipsearch = [];
        $countysearch = [];
        $statesearch = [];
        $skipstate = [];
        $countrysearch = [];

        if ($zipcode != '') {
          // We have zipcodes, make array by explode on comma.
          $zipsearch = explode(',', $zipcode);
        }

        if ($county != '') {
          // We have counties.
          $counties = explode("\n", $county);
          foreach ($counties as $row) {
            [$cstate, $countylist] = explode(':', $row);
            $skipstate[] = trim($cstate);
            if (!isset($countysearch[$cstate])) {
              $countysearch[$cstate] = [];
            }
            $carr = explode(',', $countylist);
            foreach ($carr as $cty) {
              $countysearch[$cstate][] = trim($cty);
            }
          }
        }

        if ($state != '') {
          // We have states.
          $states = explode(',', $state);
          foreach ($states as $st) {
            $st = trim($st);
            // We skip those found in county.
            if (!in_array($st, $skipstate)) {
              $statesearch[] = $st;
            }
          }
        }

        if (count($zipsearch) == 0 && count($countysearch) == 0 && count($statesearch) == 0) {
          // We need to search countries.
          $countrysearch = $country;
        }

        // Get archived regions.
        $archived = \Drupal::entityQuery('troth_maps_regions')
          ->condition('archived', 1, '=')
          ->execute();

        if (count($countrysearch) > 0) {
          $query = \Drupal::entityQuery('troth_maps_zipcodes')
            ->condition('country', $countrysearch, 'in');
        }
        else {
          $query = \Drupal::entityQuery('troth_maps_zipcodes')
            ->condition('country', $country[0], 'like');
          $dbor = $query->orConditionGroup();
          if (count($statesearch) > 0) {
            foreach ($statesearch as $st) {
              $dbor->condition('state_code', $st, 'like');
            }
          }
          if (count($countysearch) > 0) {
            foreach ($countysearch as $st => $counties) {
              $dband = $query->andConditionGroup();
              $dband->condition('state_code', $st, 'like');
              $dband->condition('county', $counties, 'in');
              $dbor->condition($dband);
            }
          }
          if (count($zipsearch) > 0) {
            foreach ($zipsearch as $ziprange) {
              $zips = explode('-', $ziprange);
              $dbor->condition('zipcode', $zips, 'BETWEEN');
            }
          }
          $query->condition($dbor);
        }
        if ($type == 'local') {
          $query->condition('locregid', $regid, '!=')
            ->condition('locregid', NULL, 'IS NOT NULL');
          if (count($archived) > 0) {
            $query->condition('locregid', $archived, 'Not in');
          }
        }
        else {
          $query->condition('regid', $regid, '!=')
            ->condition('regid', NULL, 'IS NOT NULL');
          if (count($archived) > 0) {
            $query->condition('regid', $archived, 'Not in');
          }
        }
        $results = $query->execute();
        $count = count($results);
        if ($count > 0) {
          $region = [];
          $states = [];
          $regids = [];
          foreach ($results as $zipid) {
            $zipcode = TrothMapsZipcodes::load($zipid);
            $state = $zipcode->getStateCode();
            if ($type == 'local') {
              $regid = $zipcode->getLocRegid();
            }
            else {
              $regid = $zipcode->getRegid();
            }
            if (!isset($states[$state])) {
              $states[$state] = [];
            }
            if (!isset($states[$state][$regid])) {
              $states[$state][$regid] = 0;
            }
            $states[$state][$regid]++;
          }
          foreach ($states as $state => $regids) {
            foreach ($regids as $regid => $count) {
              $region = TrothMapsRegions::load($regid);
              $form_state->setErrorByName('', t('@region found in @state, count:@count'), [
                '@region' => $region->getName(),
                '@state' => $state,
                '@count' => $count,
              ]);
            }
          }
        }
      }
    }

    // Check to see if name is already in use.
    $results = \Drupal::entityQuery('troth_maps_regions')
      ->condition('region_name', $form_state->getValue('region_name'), '=')
      ->execute();

    if (count($results) == 1) {
      if (!in_array($regid, $results)) {
        $form_state->setErrorByName('region_name', t('The region name is already in use.'));
      }
    }
    return parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity = &$this->entity;
    $type = $form_state->getValue('region_type');
    $regid = $form_state->getValue('regid');
    $region_name = $form_state->getValue('region_name');
    $archived = $form_state->getValue('archived');

    $region_email = $form_state->getValue('region_email');
    if ($type == 'special') {
      $entity->setRegionName($region_name);
      $entity->setRegionEmail($region_email);
      $entity->setRegionType($type);
      $entity->setArchived($archived);
      $entity->setBuildValues('');
    }
    else {
      if ($form_state->getValue('multi_country') == 0) {
        $country = [$form_state->getValue('country')];
      }
      else {
        $country = $form_state->getValue('country_multi');
      }
      $state = trim($form_state->getValue('state'));
      $county = trim($form_state->getValue('county'));
      $zipcode = trim($form_state->getValue('zipcode'));
      if (\Drupal::moduleHandler()->moduleExists('jquery_colorpicker') && $type == 'region') {
        $kml_color = strtoupper($form_state->getValue('kml_color'));
      }
      else {
        $kml_color = NULL;
      }
      if ($type == 'local') {
        $border_color = '#000000';
        $transparency = '0';
        $create_shape = 1;
      }
      elseif ($type == 'region') {
        $border_color = NULL;
        $transparency = '0.3';
        $create_shape = 1;
      }
      else {
        $border_color = NULL;
        $transparency = NULL;
        $create_shape = 0;
      }

      $entity->setRegionName($region_name);
      $entity->setRegionEmail($region_email);
      $entity->setRegionType($type);
      $entity->setBuildValues(['placeholder'=>'']);
      $entity->setKmlColor($kml_color);
      $entity->setBorderColor($border_color);
      $entity->setTransparency($transparency);
      $entity->setCreateShape($create_shape);
      $entity->setArchived($archived);
      $entity->save();
      $regid = $entity->getRegid();
      $data = [
        'regid' => $regid,
        'region_name' => $region_name,
        'region_type' => $type,
        'country' => $country,
        'state' => $state,
        'county' => $county,
        'zipcode' => $zipcode,
      ];
      $entity->setBuildValues($data);
      $entity->save();


      // Update zipcode entity.
      if ($type == 'local') {
        $fieldname = 'locregid';
      }
      else {
        $fieldname = 'regid';
      }

      // Get entites that were in region.
      $query = \Drupal::entityQuery('troth_maps_zipcodes')
        ->condition($fieldname, $regid, '=');
      $current = $query->execute();
      $new = [];
      if (!$archived) {
        // Gather all the information we need for searching.
        $zipsearch = [];
        $countysearch = [];
        $statesearch = [];
        $skipstate = [];
        $countrysearch = [];

        if ($zipcode != '') {
          // We have zipcodes, make array by explode on comma.
          $zipsearch = explode(',', $zipcode);
        }

        if ($county != '') {
          // We have counties.
          $counties = explode("\n", $county);
          foreach ($counties as $row) {
            [$cstate, $countylist] = explode(':', $row);
            $skipstate[] = trim($cstate);
            if (!isset($countysearch[$cstate])) {
              $countysearch[$cstate] = [];
            }
            $carr = explode(',', $countylist);
            foreach ($carr as $cty) {
              $countysearch[$cstate][] = trim($cty);
            }
          }
        }

        if ($state != '') {
          // We have states.
          $states = explode(',', $state);
          foreach ($states as $st) {
            $st = trim($st);
            // We skip those found in county.
            if (!in_array($st, $skipstate)) {
              $statesearch[] = $st;
            }
          }
        }

        if (count($zipsearch) == 0 && count($countysearch) == 0 && count($statesearch) == 0) {
          // We need to search countries.
          $countrysearch = $country;
        }

        if (count($countrysearch) > 0) {
          $query = \Drupal::entityQuery('troth_maps_zipcodes')
            ->condition('country', $countrysearch, 'in');
        }
        else {
          $query = \Drupal::entityQuery('troth_maps_zipcodes')
            ->condition('country', $country[0], 'like');
          $dbor = $query->orConditionGroup();
          if (count($statesearch) > 0) {
            foreach ($statesearch as $st) {
              $dbor->condition('state_code', $st, 'like');
            }
          }
          if (count($countysearch) > 0) {
            foreach ($countysearch as $st => $counties) {
              $dband = $query->andConditionGroup();
              $dband->condition('state_code', $st, 'like');
              $dband->condition('county', $counties, 'in');
              $dbor->condition($dband);
            }
          }
          if (count($zipsearch) > 0) {
            foreach ($zipsearch as $ziprange) {
              $zips = explode('-', $ziprange);
              $dbor->condition('zipcode', $zips, 'BETWEEN');
            }
          }
          $query->condition($dbor);
        }
        $new = $query->execute();
      }
      $unset = array_values(array_diff($current, $new));
      $set = array_values(array_diff($new, $current));

      $batch = [
        'title' => t('Updating Tables'),
        'init_message' => t('The tables are being updated with new region information'),
        'operations' => [],
        'file' => drupal_get_path('module', 'troth_maps') . '/troth_maps_import.batch.inc',
        'finished' => 'batch_finish_callback',
      ];
      if (count($unset) > 0) {
        $batch['operations'][] = ['unset_regions', [$fieldname, $unset]];
      }
      if (count($set) > 0) {
        $batch['operations'][] = [
          'set_regions',
        [
          $fieldname,
          $set,
          $regid,
        ],
        ];
      }
      $batch['operations'][] = ['count_regions', [
        $fieldname,
        $regid,
        $region_name,
      ],
      ];
      $batch['operations'][] = ['create_region_shape', [$regid]];

      batch_set($batch);
    }
    $status = parent::save($form, $form_state);
  }

}
