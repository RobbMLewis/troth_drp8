<?php

namespace Drupal\troth_google\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\troth_google\Entity\TrothGoogleGroupType;
use Drupal\troth_google\Entity\TrothGoogleGroup;
use Drupal\user\Entity\User;
use Drupal\Component\Render\FormattableMarkup;

/**
 * Provides route responses for the Example module.
 */
class TrothGoogleGroupController extends ControllerBase {

  /**
   * Returns a simple page.
   *
   * @return array
   *   A simple renderable array.
   */
  public function summaryPage($user = NULL) {
    // Get page account and current user.
    $account = User::load($user);
    $user = \Drupal::currentUser();

    // Set what kind of permission: own or any
    // This is for the user we are looking at.
    // We need to first get all the list names/bundles.
    $types = \Drupal::service('entity_type.bundle.info')->getBundleInfo('troth_google');
    $allowed = [];
    $notAllowed = [];
    foreach ($types as $bundle => $data) {
      $type = TrothGoogleGroupType::load($bundle);
      // Check permissions of *account*.  if they can edit we display.
      $perm = "edit own troth_google " . $type->id();
      if ($account->hasPermission($perm)  && $account->field_profile_ban_lists->value == 0) {
        $allowed[$bundle] = $type;
      }
      else {
        $notAllowed[$bundle] = $type;
      }
    }

    // Lets make sure they are not subscribed to any lists they're not allowed.
    // Check for uniqueness of entry with user, email, and list.
    if (count($notAllowed) > 0) {
      $query = \Drupal::entityQuery('troth_google')
        ->condition('uid', $account->id(), '=')
        ->condition('bundle', array_keys($notAllowed), 'IN');
      $entids = $query->execute();
      foreach ($entids as $entid) {
        $entity = TrothGoogleGroup::load($entid);
        $name = $notAllowed[$entity->bundle()]->getName();
        $entity->trothGoogleUnsubscribe();
        $entity->save();
        $entity->delete();
        \Drupal::logger('troth_gogle')->notice("@user removed from @list because they don't have permissions", [
          '@user' => $account->getUsername(),
          '@list' => $name,
        ]);
      }
    }

    $header = [
      'Name',
      'Description',
      'Subscribed Email',
      'Required',
      'Subscribed',
      '',
    ];
    // Now we need to display the groups they are allowed to subscribe to.
    // We also subscribe any group they are required to be on.
    $rows = [];
    if (count($allowed) > 0) {
      $query = \Drupal::entityQuery('troth_google')
        ->condition('uid', $account->id(), '=')
        ->condition('bundle', array_keys($allowed), 'IN');
      $entids = $query->execute();
      $rows = [];
      $domain = \Drupal::config('troth_google.adminsettings')->get('domain_name');
      $current_uri = \Drupal::request()->getRequestUri();

      // Get data for enties that exist already.
      foreach ($entids as $entid) {
        $entity = TrothGoogleGroup::load($entid);
        $bundle = $entity->bundle();
        $type = $allowed[$bundle];
        // If the list is required, and they are not subscribed, subscribe them.
        if ($type->getRequired() == 1 && $entity->getSubscribed() == 0) {
          $entity->trothGoogleSubscribe();
          $entity->setSubscribed(1);
          $entity->save();
        }
        // Build row to return.  Key is human name of list.
        $rows[$type->getName()] = [
          $type->getName(),
          new FormattableMarkup('<strong>@list</strong><br />@desc', [
            '@list' => $type->getGroupId() . '@' . $domain,
            '@desc' => $type->getDescription(),
          ]),
          $entity->getEmail(),
          $type->getRequired() ? 'Yes' : 'No',
          $entity->getSubscribed() ? 'Yes' : 'No',
          new FormattableMarkup('<a href=":link">@name</a>',
            [
              ':link' => $current_uri . '/' . $bundle,
              '@name' => 'Edit',
            ]),
        ];
        // Remove from array of allowed lists.
        unset($allowed[$bundle]);
      }

      // Go through any remaining lists.
      foreach ($allowed as $type) {
        // We need to create an entity for the user.
        $entity = TrothGoogleGroup::create([
          'bundle' => $type->id(),
          'uid' => $account->id(),
          'email' => $account->getEmail(),
          'subscribed' => 0,
        ]);
        // If list is requried, we subscribe them and set the flag.
        if ($type->getRequired()) {
          $entity->trothGoogleSubscribe();
          $entity->setSubscribed(1);
        }
        // Save the entity.
        $entity->save();

        // Build row to return.  Key is human name of list.
        $rows[$type->getName()] = [
          $type->getName(),
          new FormattableMarkup('<strong>@list</strong><br />@desc', [
            '@list' => $type->getGroupId() . '@' . $domain,
            '@desc' => $type->getDescription(),
          ]),
          $entity->getEmail(),
          $type->getRequired() ? 'Yes' : 'No',
          $entity->getSubscribed() ? 'Yes' : 'No',
          new FormattableMarkup('<a href=":link">@name</a>',
          [
            ':link' => $current_uri . '/' . $bundle,
            '@name' => 'Edit',
          ]),
        ];
      }
    }

    if ($rows != '' && count($rows) > 0) {
      // Sort the rows by list name, then return the render array.
      asort($rows);
      $output[] = [
        '#theme' => 'table',
        '#cache' => ['disabled' => TRUE],
        '#caption' => 'Mailing Lists You Can Subscribe To.',
        '#empty' => 'There are no lists you can subscribe to.',
        '#header' => $header,
        '#rows' => $rows,
      ];
    }
    else {
      $output[] = [
        '#markup' => t('There are currently no mailing list to subscribe to.  Please check back in the future.'),
      ];
    }
    return $output;
  }

}
