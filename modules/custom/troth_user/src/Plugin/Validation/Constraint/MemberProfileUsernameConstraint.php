<?php

namespace Drupal\troth_user\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * Checks that the user name is unique.
 *
 * @Constraint(
 *   id = "MemberProfileUsername",
 *   label = @Translation("Member Profile Username", context = "Validation"),
 *   type = "string"
 * )
 */
class MemberProfileUsernameConstraint extends Constraint {

  /**
   * The message will be shown if username constraint fails.
   *
   * @var string
   */
  public $duplicateUsername = 'The username %username is already in use.';

}
