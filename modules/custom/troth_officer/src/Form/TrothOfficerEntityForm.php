<?php

namespace Drupal\troth_officer\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form for TrothOfficerEntity.
 */
class TrothOfficerEntityForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity = &$this->entity;
    $account = $entity->getOfficer();
    // $roles = $entity->bundle->entity->getRoles();
    $roles = [];
    $added = [];
    foreach ($roles as $rid => $name) {
      if ($name === $rid) {
        $account->addRole($rid);
        $added[] = $name;
      }
    }
    $account->save();

    drupal_set_message($this->t('Added @roles roles to member # @uid.', [
      '@roles' => implode(', ', $added),
      '@uid' => $account->id(),
    ]));
    $message_params = [
      '%entity_label' => $entity->id(),
    // '%bundle_label' => $entity->bundle->entity->getName(),
    ];
    $status = parent::save($form, $form_state);
    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %bundle_label entity:  %entity_label.', $message_params));
        break;

      default:
        drupal_set_message($this->t('Saved the %bundle_label entity:  %entity_label.', $message_params));
    }
    $officer_entity_id = $entity->getEntityType()->id();
    $form_state->setRedirect("entity.{$officer_entity_id}.canonical", [$officer_entity_id => $entity->id()]);
  }

}
