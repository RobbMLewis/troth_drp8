<?php

namespace Drupal\troth_google\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Drupal\troth_google\Entity\TrothGoogleGroupType;
use Drupal\Core\Url;

/**
 * Validates the MemberProfileMail constraint.
 */
class TrothGoogleGroupUniqueConstraintValidator extends ConstraintValidator {

  /**
   * {@inheritdoc}
   */
  public function validate($entity, Constraint $constraint) {

    // Check for uniqueness of entry with user and list.
    $query = \Drupal::entityQuery('troth_google')
      ->condition('uid', $entity->getOwnerId(), '=')
      ->condition('bundle', $entity->bundle(), '=');
    if (!empty($entity->id())) {
      $query->condition('id', $entity->id(), '!=');
    }
    $entids = $query->execute();
    if (count($entids) > 0) {
      foreach ($entids as $id) {
        $type = TrothGoogleGroupType::load($entity->bundle());
        $link = Url::fromRoute('entity.troth_google.edit_form', ['troth_google' => $id]);
        $link->setOptions(['absolute' => TRUE, 'https' => TRUE]);
        $this->context->addViolation($constraint->uniqueUser, [
          '%user' => $entity->getOwner()->name->value,
          '%list' => $type->getGroupId(),
          '@link' => $link->toString(),
        ]);
      }
    }

    // Check for uniqueness of entry with user, email, and list.
    $query = \Drupal::entityQuery('troth_google')
      ->condition('uid', $entity->getOwnerId(), '=')
      ->condition('email', $entity->getEmail(), '=')
      ->condition('bundle', $entity->bundle(), '=');
    if (!empty($entity->id())) {
      $query->condition('id', $entity->id(), '!=');
    }
    $entids = $query->execute();
    if (count($entids) > 0) {
      foreach ($entids as $id) {
        $type = TrothGoogleGroupType::load($entity->bundle());
        $link = Url::fromRoute('entity.troth_google.edit_form', ['troth_google' => $id]);
        $link->setOptions(['absolute' => TRUE, 'https' => TRUE]);
        $this->context->addViolation($constraint->uniqueRecord, [
          '%user' => $entity->getOwner()->name->value,
          '%email' => $entity->getEmail(),
          '%list' => $type->getGroupId(),
          '@link' => $link->toString(),
        ]);
      }
    }

    // Confirm email is users.
    $query = \Drupal::entityQuery('user')
      ->condition('uid', $entity->getOwnerId(), '=');
    $or = $query->orConditionGroup()
      ->condition('mail', $entity->getEmail())
      ->condition('field_profile_alt_email', $entity->getEmail());
    $query->condition($or);
    $uids = $query->execute();
    if (count($uids) == 0) {
      $mails = [$entity->getOwner()->mail->value];
      if ($entity->getOwner()->field_profile_alt_email->value != '') {
        $mails[] = $entity->getOwner()->field_profile_alt_email->value;
      }

      $this->context->addViolation($constraint->badEmail, [
        '%email' => $entity->getEmail(),
        '%user' => $entity->getOwner()->name->value,
        '%valid' => implode(', ', $mails),
      ]);
    }
    // Confirm user is allowed to be subscribed.
    $account = $entity->getOwner();
    $perm = "edit own troth_google " . $entity->bundle();
    if (!$account->hasPermission($perm)  ||  !$account->field_profile_ban_lists->value == 0) {
      $type = TrothGoogleGroupType::load($entity->bundle());
      $this->context->addViolation($constraint->noPerm, [
        '%user' => $entity->getOwner()->name->value,
        '%list' => $type->getName(),
      ]);
    }
  }

}
