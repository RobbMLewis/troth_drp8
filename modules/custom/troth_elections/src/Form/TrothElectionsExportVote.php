<?php

namespace Drupal\troth_elections\Form;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\user\Entity\User;

/**
 * Edit Troth User Admin form.
 */
class TrothElectionsExportVote extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'troth_elections_export_vote_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // We need a simple form.  Click button, export.
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Export CSV of Full Results'),
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // Enter custom validation here.
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Rebuild so that submitted values stay.
    $form_state->setRebuild();

    // Get office data.
    $officestorage = \Drupal::entityTypeManager()
      ->getStorage('troth_office');
    $results = \Drupal::entityQuery('troth_office')
      ->condition('office_number_open', 0, '>')
      ->condition('office_open', 1, '=')
      ->execute();
    $entities = $officestorage->loadMultiple($results);
    $offices = [];
    foreach ($entities as $office_id => $office) {
      $offices[$office_id] = $office->getName();
    }

    $connection = \Drupal::database();
    $query = $connection->select('troth_elections_nomination_vote', 'v')
      ->fields('v', ['uidhash', 'office_id', 'candidate'])
      ->condition('vote', 0, '>');
    $results = $query->execute();

    $candidates = [-2 => 'Abstain/No Vote'];
    $csv[] = $this->t('"Member","Office","Candidate ID","Candidate Name"');
    while ($row = $results->fetchAssoc()) {
      $member = $row['uidhash'];
      $office = $offices[$row['office_id']];
      $candidate = $row['candidate'];
      if (!isset($candidates[$candidate])) {
        if (is_numeric($candidate) && $candidate > 0) {
          $account = User::load($candidate);
          $name = $account->field_profile_last_name->value . ", " . $account->field_profile_first_name->value;
        }
        else {
          $name = $candidate;
        }
        $candidates[$candidate] = $name;
      }
      $name = $candidates[$candidate];
      $csv[] = $this->t('"@member","@office","@candidate","@name"', [
        '@member' => $member,
        '@office' => $office,
        '@candidate' => $candidate,
        '@name' => $name,
      ]);
    }

    // Set temporary file and save data.
    $file_system = \Drupal::service('file_system');
    $fname = $file_system->tempnam('temporary://', "troth_") . '.csv';
    file_unmanaged_save_data(implode("\n", $csv), $fname, FILE_EXISTS_REPLACE);

    // Send the file to the browser.
    $headers = [
      'Content-Type' => 'text/csv',
      'Content-Disposition' => 'attachment;filename="' . $fname . '"',
    ];
    $form_state->setResponse(new BinaryFileResponse($fname, 200, $headers, TRUE));
  }

}
