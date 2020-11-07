<?php

namespace Drupal\troth_user\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Validates the MemberProfileMail constraint.
 */
class MemberProfileMailConstraintValidator extends ConstraintValidator {

  /**
   * {@inheritdoc}
   */
  public function validate($entity, Constraint $constraint) {
    $entity = $entity->getEntity();
    $altMail = strtolower($entity->get('field_profile_alt_email')->getString());
    $mail = strtolower($entity->get('field_email')->getString());

    // Check to see if the 2 email addresses are the same.
    if ($mail === $altMail) {
      $this->context->addViolation($constraint->repeatedMail);
    }

    // Check to see if the email address is already in use in users.
    $query = \Drupal::entityQuery('user');
    $or = $query->orConditionGroup()
      ->condition('mail', $mail)
      ->condition('field_profile_alt_email', $mail);
    $query->condition($or);
    $uids = $query->execute();
    if (count($uids) > 0) {
      $this->context->addViolation($constraint->usedMail, ['%mail' => $mail]);
    }
  }

}
