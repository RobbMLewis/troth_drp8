<?php

namespace Drupal\troth_user\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * Checks that the user name is unique.
 *
 * @Constraint(
 *   id = "MemberRenewUsername",
 *   label = @Translation("Member Renew Profile Member Username", context = "Validation"),
 *   type = "string"
 * )
 */
class MemberRenewUsernameConstraint extends Constraint {

  /**
   * The message will be shown if username constraint fails.
   *
   * @var string
   */
  public $noMemberFound = 'There was no member found with Username %name';

  /**
   * The message will be shown if alt email constraint fails.
   *
   * @var string
   */
  public $multipleMembersFound = 'Multiple members were found with Username %name: %out';

  /**
   * The message will be shown if alt email constraint fails.
   *
   * @var string
   */
  public $noCombinationFound = 'The combination of %combo was not found.';

  /**
   * The message will be shown if alt email constraint fails.
   *
   * @var string
   */
  public $noDataProvided = 'You have chosen to renew for someone else but provided no data.  Please enter a Member ID, Username, or Email to continue.';

}
