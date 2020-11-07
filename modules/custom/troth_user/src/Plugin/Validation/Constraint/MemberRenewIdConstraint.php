<?php

namespace Drupal\troth_user\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * Checks that the user name is unique.
 *
 * @Constraint(
 *   id = "MemberRenewId",
 *   label = @Translation("Member Renew Profile Member ID", context = "Validation"),
 *   type = "string"
 * )
 */
class MemberRenewIdConstraint extends Constraint {

  /**
   * The message will be shown if username constraint fails.
   *
   * @var string
   */
  public $noMemberFound = 'There was no member found with Member ID %id';

  /**
   * The message will be shown if alt email constraint fails.
   *
   * @var string
   */
  public $multipleMembersFound = 'Multiple members were found with Member ID %id: %out';

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
