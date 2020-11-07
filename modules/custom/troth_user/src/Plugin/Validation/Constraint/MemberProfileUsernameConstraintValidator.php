<?php

namespace Drupal\troth_user\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Validates the MemberProfileUsername constraint.
 */
class MemberProfileUsernameConstraintValidator extends ConstraintValidator {

  /**
   * {@inheritdoc}
   */
  public function validate($field, Constraint $constraint) {
    $username = $field->value;
    // Check that the username does not already exist.
    $ids = \Drupal::entityQuery('user')
      ->condition('name', $username)
      ->range(0, 1)
      ->execute();
    if (!empty($ids)) {
      // The username exists  we set in the constraint.
      $this->context->addViolation($constraint->duplicateUsername, ['%username' => $username]);
    }
  }

}
