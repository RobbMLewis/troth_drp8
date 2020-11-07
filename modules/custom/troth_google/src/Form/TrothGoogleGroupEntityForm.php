<?php

namespace Drupal\troth_google\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form for TrothGoogleGroupEntity.
 */
class TrothGoogleGroupEntityForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity = &$this->entity;
    $entityOriginal = \Drupal::entityTypeManager()->getStorage('troth_google')->loadUnchanged($entity->id());
    // If the email address is changed, unsubscribe the old one if needed
    // and subscribe the new one.
    if (!empty($entityOriginal) && $entity->getEmail() != $entityOriginal->getEmail()) {
      if ($entityOriginal->getSubscribed() == 1) {
        // We need to unsubscribe old email address.
        $entity->trothGoogleUnsubscribe($entityOriginal->getEmail());
      }
      if ($entity->getSubscribed() == 1) {
        // We need to subscribe new email address.
        $entity->trothGooglesubscribe($entity->getEmail());
      }
    }
    else {
      // The email address didn't change.
      // Only run the subscribe/unsubscribe scripts if there are changes.
      if (empty($entityOriginal) || $entity->getSubscribed() != $entityOriginal->getSubscribed()) {
        if ($entity->getSubscribed() == 1) {
          $entity->trothGooglesubscribe($entity->getEmail());
        }
        else {
          $entity->trothGoogleUnsubscribe($entity->getEmail());
        }
      }
    }
    $message_params = [
      '%entity_label' => $entity->id(),
      '%content_entity_label' => $entity->getEntityType()->getLabel()->render(),
      '%bundle_label' => $entity->bundle->entity->label(),
    ];
    $status = parent::save($form, $form_state);
    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %bundle_label - %content_entity_label entity:  %entity_label.', $message_params));
        break;

      default:
        drupal_set_message($this->t('Saved the %bundle_label - %content_entity_label entity:  %entity_label.', $message_params));
    }
    $content_entity_id = $entity->getEntityType()->id();
    $form_state->setRedirect("entity.{$content_entity_id}.canonical", [$content_entity_id => $entity->id()]);
  }

}
