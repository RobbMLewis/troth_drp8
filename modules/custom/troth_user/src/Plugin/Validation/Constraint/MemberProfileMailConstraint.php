<?php

namespace Drupal\troth_user\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * Checks that the user name is unique.
 *
 * @Constraint(
 *   id = "MemberProfileMail",
 *   label = @Translation("Member Profile Email", context = "Validation"),
 *   type = "string"
 * )
 */
class MemberProfileMailConstraint extends Constraint {

  /**
   * The message will be shown if username constraint fails.
   *
   * @var string
   */
  public $repeatedMail = 'The Email and Alternative Email addresses are the same.  Please change one.';

  /**
   * The message will be shown if alt email constraint fails.
   *
   * @var string
   */
  public $usedMail = 'The Email address you entered (%mail) is already on file.  Please delete or use a different one.';

}
