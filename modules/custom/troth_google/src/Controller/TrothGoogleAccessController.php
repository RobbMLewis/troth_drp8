<?php

namespace Drupal\troth_google\Controller;

use Drupal\user\Entity\User;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;

/**
 * Builds an example page.
 */
class TrothGoogleAccessController {

  /**
   * Checks access for a specific request.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   Run access checks for this account.
   * @param string $user
   *   UID of the account we are viewing.
   *
   * @return \Drupal\Core\Access\AccessResultInterface
   *   The access result.
   */
  public function access(AccountInterface $account, $user = NULL) {
    $account = User::load($account->id());

    // Check permissions and combine that with any custom access checking
    // needed.
    // Pass forward parameters from the route and/or request as needed.
    // Get the number of groups available, if none, no access.
    $groups = \Drupal::service('entity_type.bundle.info')->getBundleInfo('troth_google');
    if (count($groups) == 0) {
      return AccessResult::forbidden();
    }
    elseif ($account->id() == $user) {
      // If there are groups and this is their profile, allow.
      return AccessResult::allowed();
    }
    elseif ($account->hasRole('administrator') || $account->hasRole('officer') ||$account->hasRole('tech')) {
      // If the user is admin, allow.
      return AccessResult::allowed();
    }
    else {
      // They shouldn't have access, forbid.
      return AccessResult::forbidden();
    }

  }

}
