<?php

namespace Drupal\troth_maps\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form for TrothStewardEntity.
 */
class TrothStewardEntityForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity = &$this->entity;

    $message_params = [];
    $status = parent::save($form, $form_state);
    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %bundle_label entity:  %entity_label.', $message_params));
        break;

      default:
        drupal_set_message($this->t('Saved the %bundle_label entity:  %entity_label.', $message_params));
    }
    $form_state->setRedirect("entity.troth_steward.collection");
  }

}
