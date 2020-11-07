<?php

namespace Drupal\troth_google\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * Requires a field to have a value when the entity is published.
 *
 * @Constraint(
 *   id = "TrothGoogleGroupUnique",
 *   label = @Translation("User, email, and list are unique", context = "Validation"),
 *   type = "string"
 * )
 */
class TrothGoogleGroupUniqueConstraint extends Constraint {

  /**
   * The message will be shown if user and list constraint fails.
   *
   * @var string
   */
  public $uniqueUser = 'The user (%user) and List (%list) is not unique.  Please edit the original entry: <a href="@link">@link</a>';

  /**
   * The message will be shown if user, email and list constraint fails.
   *
   * @var string
   */
  public $uniqueRecord = 'The user (%user), Email (%email) and List (%list) is not unique.  Please edit the original entry: <a href="@link">@link</a>';

  /**
   * The message will be shown if email constraint fails.
   *
   * @var string
   */
  public $badEmail = 'The email (%email) does not belong to user (%user).  Valid emails are: %valid';

  /**
   * The message will be shown if the user doesn't have permission.
   *
   * @var string
   */
  public $noPerm = 'The user %user does not have permission to subscribe to %list.';

}
