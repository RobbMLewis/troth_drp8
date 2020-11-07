<?php

namespace Drupal\troth_elections\Form;

use Html2Text\Html2Text;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Edit Troth User Admin form.
 */
class TrothElectionsExportProp extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'troth_elections_export_prop_form';
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

    // Get proposition data.
    $propstorage = \Drupal::entityTypeManager()
      ->getStorage('troth_elections_proposition_type');
    $results = \Drupal::entityQuery('troth_elections_proposition_type')
      ->execute();
    $entities = $propstorage->loadMultiple($results);
    $propositions = [];
    foreach ($entities as $prop_id => $prop) {
      $name = $prop->getName();
      $text = $prop->getText();
      $opts = explode(PHP_EOL, $prop->getOptions());
      $options = [];
      foreach ($opts as $option) {
        $options[$option]['vote'] = 0;
        $options[$option]['voters'] = [];
      }
      $propositions[$prop_id] = [
        'name' => $prop->getName(),
        'text' => $prop->getText()['value'],
        'votes' => $options,
      ];
    }

    $connection = \Drupal::database();
    $query = $connection->select('troth_elections_proposition_vote', 'v')
      ->fields('v', ['uidhash', 'proposition_id', 'vote']);
    $results = $query->execute();

    while ($row = $results->fetchAssoc()) {
      $propositions[$row['proposition_id']]['votes'][$row['vote']]['vote']++;
      $propositions[$row['proposition_id']]['votes'][$row['vote']]['voters'][] = $row['uidhash'];
    }

    $out = '';
    foreach ($propositions as $prop_id => $data) {
      $out .= $data['name'] . ":\n";
      $text = $data['text'];
      $html2TextConverter = new Html2Text($text);
      $out .= $html2TextConverter->getText() . "\n\n";
      $out .= "Votes: \n";
      $options = $data['votes'];
      foreach ($options as $opts => $odata) {
        $out .= "Vote Option: " . $opts . "\n";
        $out .= "Num Votes: " . $odata['vote'] . "\n";
        $out .= "\t" . implode("\n\t", $odata['voters']) . "\n";
      }
    }

    // Set temporary file and save data.
    $file_system = \Drupal::service('file_system');
    $fname = $file_system->tempnam('temporary://', "troth_") . '.txt';
    file_unmanaged_save_data($out, $fname, FILE_EXISTS_REPLACE);

    // Send the file to the browser.
    $headers = [
      'Content-Type' => 'text/plain',
      'Content-Disposition' => 'attachment;filename="' . $fname . '"',
    ];
    $form_state->setResponse(new BinaryFileResponse($fname, 200, $headers, TRUE));
  }

}
