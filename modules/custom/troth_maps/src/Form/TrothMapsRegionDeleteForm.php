<?php

namespace Drupal\troth_maps\Form;

use Drupal\Core\Entity\ContentEntityDeleteForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a generic base class for a content entity deletion form.
 *
 * @internal
 *
 * @todo Re-evaluate and streamline the entity deletion form class hierarchy in
 *   https://www.drupal.org/node/2491057.
 */
class TrothMapsRegionDeleteForm extends ContentEntityDeleteForm {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);

    /** @var \Drupal\Core\Entity\ContentEntityInterface $entity */
    $entity = $this->getEntity();
    // Check if there are any stewards.
    $stewards = \Drupal::entityQuery('troth_steward')
      ->condition('region_id', $entity->id(), '=')
      ->count()->execute();
    if ($stewards > 0) {
      $cancel = $form['actions']['cancel'];
      $form = [];
      $form['warning'] = [
        '#type' => 'item',
        '#title' => t('Error'),
        '#markup' => t('<H3>You are trying to delete a region that has Stewards in it.  Please remove the stewards before deleting.  You can also archive the region.</H3>'),
      ];
      $form['actions']['cancel'] = $cancel;

    }

    return $form;
  }

}
