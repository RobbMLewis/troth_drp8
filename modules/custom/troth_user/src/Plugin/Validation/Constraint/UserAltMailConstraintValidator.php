<?php

namespace Drupal\troth_user\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Validates the UserAltMail constraint.
 */
class UserAltMailConstraintValidator extends ConstraintValidator {

  /**
   * {@inheritdoc}
   */
  public function validate($entity, Constraint $constraint) {
    $entity = $entity->getEntity();
    $altMail = strtolower($entity->get('field_profile_alt_email')->getString());
    $mail = strtolower($entity->get('mail')->getString());
    $uid = strtolower($entity->get('uid')->getString());

    // Check to see if the 2 email addresses are the same.
    if ($mail === $altMail && $altMail != '') {
      $this->context->addViolation($constraint->repeatedMail);
    }

    // Check to see if the email address is already in use in users.
    $query = \Drupal::entityQuery('user');
    $or = $query->orConditionGroup()
      ->condition('mail', $altMail)
      ->condition('field_profile_alt_email', $altMail);
    $query->condition($or);
    $query->condition('uid', $uid, '!=');
    $uids = $query->execute();
    if (count($uids) > 0 && $altMail != '') {
      $this->context->addViolation($constraint->usedMail, ['%mail' => $altMail]);
    }
  }

}
