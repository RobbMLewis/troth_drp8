<?php

namespace Drupal\troth_user\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Drupal\user\Entity\User;

/**
 * Validates the MemberRenewMail constraint.
 */
class MemberRenewMailConstraintValidator extends ConstraintValidator {

  /**
   * {@inheritdoc}
   */
  public function validate($entity, Constraint $constraint) {
    $entity = $entity->getEntity();
    $memberId = strtolower($entity->get('field_member_id')->getString());
    $mail = strtolower($entity->get('field_renew_email')->getString());
    $username = strtolower($entity->get('field_preferred_username')->getString());
    $self = strtolower($entity->get('field_renew_self')->getString());
    if ($self == 1) {
      return;
    }
    if ($memberId == '' && $mail == '' && $username == '') {
      $this->context->addViolation($constraint->noDataProvided);
    }
    if ($mail == '') {
      return;
    }

    if (!\Drupal::service('email.validator')->isValid($mail)) {
      $this->context->addViolation($constraint->invalidEmail, ['%mail' => $mail]);
    }

    // Check if the Email exists.
    $query = \Drupal::entityQuery('user');
    $or = $query->orConditionGroup()
      ->condition('mail', $mail, 'like')
      ->condition('field_profile_alt_email', $mail, 'like');
    $query->condition($or);
    $uids = $query->execute();
    if (count($uids) == 0) {
      $this->context->addViolation($constraint->noMemberFound, ['%mail' => $mail]);
    }
    elseif (count($uids) > 1) {
      $out = $this->findDisplayNames($uids);
      $this->context->addViolation($constraint->multipleMembersFound, [
        '%mail' => $mail,
        '%out' => $out,
      ]);
    }

    // Check if the multiple values exists.
    $count = 0;
    $out = "Email: $mail\n";
    $query = \Drupal::entityQuery('user');
    $or = $query->orConditionGroup()
      ->condition('mail', $mail, 'like')
      ->condition('field_profile_alt_email', $mail, 'like');
    $query->condition($or);
    if ($memberId != '') {
      $count++;
      $out .= "Member ID: $memberId\n";
      $query->condition('uid', $memberId, 'like');
    }
    if ($username != '') {
      $count++;
      $out .= "Username: $username\n";
      $query->condition('name', $username, 'like');
    }
    $uids = $query->execute();
    if (count($uids) == 0) {
      $this->context->addViolation($constraint->noCombinationFound, ['%combo' => $out]);
    }
  }

  /**
   * {@inheritdoc}
   */
  private function findDisplayNames($uids) {
    $users = User::loadMultiple($uids);
    $out = '';
    foreach ($users as $account) {
      $uid = $account->uid;
      $firstName = $account->field_profile_first_name;
      $lastName = $account->field_profile_last_name;
      $mail = $account->mail;
      $out .= "$uid: $firstName $lastName, $mail\n";
    }
    return $out;
  }

}
