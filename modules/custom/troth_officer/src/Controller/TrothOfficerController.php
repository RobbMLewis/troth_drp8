<?php

namespace Drupal\troth_officer\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Drupal\Component\Datetime\DateTimePlus;
use Drupal\troth_officer\Entity\TrothOfficer;
use Drupal\troth_officer\Entity\TrothOfficerType;
use Drupal\Core\Url;

/**
 * Provides route responses for the Example module.
 */
class TrothOfficerController extends ControllerBase {

  /**
   * Returns a simple page.
   *
   * @return array
   *   A simple renderable array.
   */
  public function groupPage($group = NULL) {
    if ($group == NULL || $group == '') {
      throw new NotFoundHttpException();
    }

    $group = troth_officer_office_groups($group);
    if (count($group) == 0) {
      throw new NotFoundHttpException();
    }

    // We have a valid path, lets get the group information:
    $groups = unserialize(\Drupal::config('troth_officer.adminsettings')->get('groups'));
    $group = $groups[array_key_first($group)];
    if ($group['archive']) {
      throw new NotFoundHttpException();
    }
    $output = [];
    $output['#title'] = $group['name'];
    $output[] = [
      '#markup' => $group['description']['value'],
    ];

    $items = [];
    $types = \Drupal::service('entity_type.bundle.info')->getBundleInfo('troth_officer');
    $current_path = \Drupal::service('path.current')->getPath();
    $current_path = \Drupal::request()->getRequestUri();
    foreach ($types as $bundle => $data) {
      $type = TrothOfficerType::load($bundle);
      $now = new DateTimePlus();
      $query = \Drupal::entityQuery('troth_officer')
        ->condition('enddate', $now->format('U'), '>=')
        ->condition('bundle', $bundle, '=');
      $entids = $query->execute();
      if (count($entids) == 0) {
        $items[$bundle] = ['#markup' => "<a href=\"$current_path/$bundle\">" . $data['label'] . "</a>: Open"];
      }
      else {
        $names = [];
        foreach ($entids as $entid) {
          $entity = TrothOfficer::load($entid);
          $officer = $entity->getOfficer();
          $names[] = $officer->field_profile_first_name->value . " " . $officer->field_profile_last_name->value;
        }
        $name = implode(', ', $names);
        $items[$bundle] = ['#markup' => "<a href=\"$current_path/$bundle\">" . $data['label'] . "</a>: $name"];
      }
    }
    $output[] = [
      '#theme' => 'item_list',
      '#list_type' => 'ul',
      '#items' => $items,
    ];
    $output = $this->addEditLink($output);
    return $output;
  }

  /**
   * Returns a simple page.
   *
   * @return array
   *   A simple renderable array.
   */
  public function officePage($group = NULL, $office = NULL) {
    if ($group == NULL || $group == '' || $office == NULL || $office == '') {
      throw new NotFoundHttpException();
    }

    $group = troth_officer_office_groups($group);
    if (count($group) == 0) {
      throw new NotFoundHttpException();
    }
    $types = \Drupal::service('entity_type.bundle.info')->getBundleInfo('troth_officer');
    if (!in_array($office, array_keys($types))) {
      throw new NotFoundHttpException();
    }

    $type = TrothOfficerType::load($office);
    // We have a valid path, lets get the group information:
    $groups = unserialize(\Drupal::config('troth_officer.adminsettings')->get('groups'));
    $group = $groups[array_key_first($group)];
    if ($group['archive']) {
      throw new NotFoundHttpException();
    }

    $output = [];
    $output['#title'] = $group['name'] . ": " . $type->getName();
    $output[] = [
      '#markup' => $type->getDescription()['value'],
    ];
    $now = new DateTimePlus();
    $query = \Drupal::entityQuery('troth_officer')
      ->condition('enddate', $now->format('U'), '>=')
      ->condition('bundle', $office, '=');
    $entids = $query->execute();
    if (count($entids) == 0) {
      $output[] = [
        '#plain_text' => "The office is currently open.",
      ];
    }
    else {
      $header = [
        '#picture' => '',
        '#name' => 'Name',
        '#start' => 'Term Start',
        '#end' => 'Term End',
      ];
      $render = [
        '#theme' => 'table',
        '#cache' => ['disabled' => TRUE],
        '#caption' => 'The current office holder is:',
        '#header' => $header,
      ];
      if (count($entids) > 1) {
        $render['#caption'] = 'The current office holders are:';
      }
      $rows = [];
      foreach ($entids as $entid) {
        $entity = TrothOfficer::load($entid);
        $officer = $entity->getOfficer();
        $picture = '';
        if (!empty($officer->user_picture) && $officer->user_picture->isEmpty() === FALSE) {
          $image_uri = $officer->user_picture->first()->entity->getFileUri();
          ;
          $image = [
            '#theme' => 'image_style',
            '#style_name' => 'thumbnail',
            '#uri' => $image_uri,
          ];
          $picture = render($image);
        }
        $name = $officer->field_profile_first_name->value . " " . $officer->field_profile_last_name->value;
        $start = \Drupal::service('date.formatter')->format($entity->getStartDate(), 'troth_date');
        $end = \Drupal::service('date.formatter')->format($entity->getEndDate(), 'troth_date');
        $rows[$entity->getStartDate()] = [
          '#picture' => $picture,
          '#name' => $name,
          '#start' => $start,
          '#end' => $end,
        ];
      }
      ksort($rows);
      $render['#rows'] = $rows;
      $render['#attributes'] = ['class' => 'troth_officer_table'];
      $output[] = $render;
    }
    $output[] = [
      '#markup' => t("<H2>Contact the @office</H2>", ['@office' => $type->getName()]),
    ];
    if ($type->getEmail() != '') {
      $config = \Drupal::config('contact.settings');
      $contact_form = \Drupal::entityTypeManager()
        ->getStorage('contact_form')
        ->load($office);
      $message = \Drupal::entityTypeManager()
        ->getStorage('contact_message')
        ->create(['contact_form' => $contact_form->id()]);
      $form = \Drupal::service('entity.form_builder')
        ->getForm($message);
      $output[] = $form;
    }
    $output = $this->addEditLink($output);
    return $output;
  }

  /**
   * Returns a simple page.
   *
   * @return array
   *   A simple renderable array.
   */
  public function archiveMainPage() {
    $output['#title'] = "Archives";
    $groups = unserialize(\Drupal::config('troth_officer.adminsettings')->get('groups'));
    $output[] = [
      '#markup' => t('<p>You can find a listing of all the previous officers and their dates of service in our Archives.</p><ul>'),
    ];
    foreach ($groups as $group) {
      $name = $group['name'];
      $dir = $group['shortname'];
      $output[] = [
        '#markup' => t('<li><a href="/leadership/archives/@dir">@name</a></li>', [
          '@dir' => $dir,
          '@name' => $name,
        ]),
      ];
    }
    $output = $this->addEditLink($output);
    return $output;
  }

  /**
   * Returns a simple page.
   *
   * @return array
   *   A simple renderable array.
   */
  public function archivePage($group = NULL) {
    if ($group == NULL || $group == '') {
      throw new NotFoundHttpException();
    }

    $group = troth_officer_office_groups($group);
    if (count($group) == 0) {
      throw new NotFoundHttpException();
    }

    // We have a valid path, lets get the group information:
    $groups = unserialize(\Drupal::config('troth_officer.adminsettings')->get('groups'));
    $group = $groups[array_key_first($group)];
    $output = [];
    $output['#title'] = "Archives: " . $group['name'];
    $output[] = [
      '#markup' => $group['description']['value'],
    ];

    $items = [];
    $types = \Drupal::service('entity_type.bundle.info')->getBundleInfo('troth_officer');
    $current_path = \Drupal::service('path.current')->getPath();
    $current_path = \Drupal::request()->getRequestUri();
    foreach ($types as $bundle => $data) {
      $now = new DateTimePlus();
      $query = \Drupal::entityQuery('troth_officer')
        ->condition('enddate', $now->format('U'), '>=')
        ->condition('bundle', $bundle, '=');
      $entids = $query->execute();
      $items[$bundle] = ['#markup' => "<a href=\"$current_path/$bundle\">" . $data['label'] . "</a>"];
    }
    $output[] = [
      '#theme' => 'item_list',
      '#list_type' => 'ul',
      '#items' => $items,
    ];
    $output = $this->addEditLink($output);
    return $output;
  }

  /**
   * Returns a simple page.
   *
   * @return array
   *   A simple renderable array.
   */
  public function archiveOfficePage($group = NULL, $office = NULL) {
    if ($group == NULL || $group == '' || $office == NULL || $office == '') {
      throw new NotFoundHttpException();
    }

    $group = troth_officer_office_groups($group);
    if (count($group) == 0) {
      throw new NotFoundHttpException();
    }
    $types = \Drupal::service('entity_type.bundle.info')->getBundleInfo('troth_officer');
    if (!in_array($office, array_keys($types))) {
      throw new NotFoundHttpException();
    }

    $type = TrothOfficerType::load($office);
    // We have a valid path, lets get the group information:
    $groups = unserialize(\Drupal::config('troth_officer.adminsettings')->get('groups'));
    $group = $groups[array_key_first($group)];

    $output = [];
    $output['#title'] = "Archives: " . $group['name'] . ": " . $type->getName();
    $output[] = [
      '#markup' => $type->getDescription()['value'],
    ];
    $now = new DateTimePlus();
    $query = \Drupal::entityQuery('troth_officer')
      ->condition('bundle', $office, '=');
    $entids = $query->execute();
    $header = [
      '#picture' => '',
      '#name' => 'Name',
      '#start' => 'Term Start',
      '#end' => 'Term End',
    ];
    $render = [
      '#theme' => 'table',
      '#cache' => ['disabled' => TRUE],
      '#caption' => 'Past and Present Office Holders',
      '#empty' => 'There are no officers to display.',
      '#header' => $header,
    ];
    $rows = [];
    foreach ($entids as $entid) {
      $entity = TrothOfficer::load($entid);
      $officer = $entity->getOfficer();
      $name = $officer->field_profile_first_name->value . " " . $officer->field_profile_last_name->value;
      $picture = '';
      if ($entity->getEndDate() > $now->format('U')) {
        $name = $name . " (Current holder)";
        $picture = '';
        if (!empty($officer->user_picture) && $officer->user_picture->isEmpty() === FALSE) {
          $image_uri = $officer->user_picture->first()->entity->getFileUri();
          ;
          $image = [
            '#theme' => 'image_style',
            '#style_name' => 'thumbnail',
            '#uri' => $image_uri,
          ];
          $picture = render($image);
        }
      }
      $start = \Drupal::service('date.formatter')->format($entity->getStartDate(), 'troth_date');
      $end = \Drupal::service('date.formatter')->format($entity->getEndDate(), 'troth_date');
      $rows[$entity->getStartDate()] = [
        '#picture' => $picture,
        '#name' => $name,
        '#start' => $start,
        '#end' => $end,
      ];

      ksort($rows);
      $render['#rows'] = $rows;
      $render['#attributes'] = ['class' => 'troth_officer_table'];
    }
    $output[] = $render;
    $output[] = [
      '#markup' => t("<H2>Contact the @office</H2>", ['@office' => $type->getName()]),
    ];
    if ($type->getEmail() != '') {
      $config = \Drupal::config('contact.settings');
      $contact_form = \Drupal::entityTypeManager()
        ->getStorage('contact_form')
        ->load($office);
      $message = \Drupal::entityTypeManager()
        ->getStorage('contact_message')
        ->create(['contact_form' => $contact_form->id()]);
      $form = \Drupal::service('entity.form_builder')
        ->getForm($message);
      $output[] = $form;
    }
    $output = $this->addEditLink($output);
    return $output;
  }

  /**
   * Returns a simple page.
   *
   * @return array
   *   A simple renderable array.
   */
  private function addEditLink($output = []) {
    $link = Url::fromRoute('troth_officer.admin_settings_form');
    $link->setOptions(['absolute' => TRUE, 'https' => TRUE]);
    if ($link->access()) {
      $output[] = [
        '#markup' => t('<p><strong>To edit this page <a href=":link">go here</a></strong></p>', [
          ':link' => $link->toString(),
        ]),
      ];
    }
    return $output;
  }

}
