<?php

namespace Drupal\troth_elections\Form;

use Drupal\Core\Entity\BundleEntityFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form for the TrothElectionsPropositionType entity.
 */
class TrothElectionsPropositionTypeEntityForm extends BundleEntityFormBase {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);
    $entity_type = $this->entity;

    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Proposition Title'),
      '#maxlength' => 255,
      '#default_value' => $entity_type->getName(),
      '#description' => $this->t("The is the title of the Proposition."),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $entity_type->id(),
      '#machine_name' => [
        'exists' => '\Drupal\troth_elections\Entity\TrothElectionsPropositionType::load',
      ],
      '#disabled' => !$entity_type->isNew(),
    ];

    $form['text'] = [
      '#type' => 'text_format',
      '#title' => $this->t('Text of Proposition'),
      '#default_value' => $entity_type->getText()['value'] ?: '',
      '#format' => $entity_type->getText()['format'],
      '#description' => $this->t('Please enter the text of the Proposition to be voted on.'),
      '#required' => TRUE,
    ];

    $form['options'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Voting Options'),
      '#default_value' => $entity_type->getOptions() ?: "Approve\nDo Not Approve\nAbstain",
      '#description' => $this->t('Enter one option per line to choose from when voting.'),
      '#required' => TRUE,
    ];

    return $this->protectBundleIdElement($form);
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity_type = $this->entity;
    $entity_type->save();
    $status = parent::save($form, $form_state);

    $message_params = [
      '%name' => $entity_type->getName(),
    ];

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %name entity type.', $message_params));
        break;

      default:
        drupal_set_message($this->t('Saved the %name entity type.', $message_params));
    }
    $form_state->setRedirectUrl($entity_type->toUrl('collection'));
  }

}
