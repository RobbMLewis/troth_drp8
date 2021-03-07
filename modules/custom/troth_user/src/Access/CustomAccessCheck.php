<?php

namespace Drupal\troth_user\Access;

use Drupal\Core\Routing\Access\AccessInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Checks access for displaying configuration translation page.
 */
class CustomAccessCheck implements AccessInterface {

  /**
   * A custom access check.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   Run access checks for this account.
   *
   * @return \Drupal\Core\Access\AccessResultInterface
   *   The access result.
   */
  public function access(AccountInterface $account) {
    // Check that this is a members page.
    if ($this->membersPage()) {
      // If the user has permission to view members pages.
      if ($account->hasPermission('troth user view member pages') || $account->hasRole('administrator')) {
        return AccessResult::neutral();
      }
      else {
        // Not admin or member, return forbidden.
        return AccessResult::forbidden();
      }

      // Not covered by these access checks, return neutral.
      return AccessResult::neutral();
    }
  }

  /**
   * Check that this is a members page.
   */
  public function membersPage() {
    $current_path = \Drupal::service('path.current')->getPath();
    $result = explode('/', ltrim(\Drupal::service('path.alias_manager')->getAliasByPath($current_path), '/'));
    if ($result[0] == 'members') {
      return TRUE;
    }
    else {
      return FALSE;
    }
  }

}
