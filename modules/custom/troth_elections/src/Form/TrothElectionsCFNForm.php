<?php

namespace Drupal\troth_elections\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Edit Troth User Admin form.
 */
class TrothElectionsCFNForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'troth_elections_cfn_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $path = \Drupal::config('troth_elections.adminsettings')->get('troth_elections_path');
    if ($path == '') {
      drupal_set_message('You must first setup the elections storage path', 'warning');
      return $this->redirect('troth_elections.admin_settings_form');
    }

    // Get stored CFN.  There should be 2 files in the "troth_elections_path",
    // check for the token one.
    $tokfile = $path . '/cfn_token.txt';
    $emailfile = $path . '/cfn.txt';

    if (!file_destination($tokfile, FILE_EXISTS_ERROR)) {
      $token = file_get_contents($tokfile);
      $cfn = file_get_contents($emailfile);
      $cfn = "<p>" . preg_replace('/\n/', '</p><p>', $cfn) . "</p>";
    }
    else {
      $tokendefault = DRUPAL_ROOT . '/' . drupal_get_path('module', 'troth_elections') . '/email_templates/default_cfn.txt';
      $token = file_get_contents($tokendefault);
    }

    if (isset($cfn)) {
      $form['cfn_fieldset'] = [
        '#type' => 'details',
        '#open' => TRUE,
        '#title' => $this->t('CFN Email'),
        '#description' => $this->t('This is the current CFN email'),
      ];
      $form['cfn_fieldset']['current'] = [
        '#type' => 'item',
        '#markup' => $cfn,
      ];
    }
    $form['cfn'] = [
      '#title' => t('Call for Nominations Email'),
      '#type' => 'textarea',
      '#rows' => 50,
      '#default_value' => $token,
      '#prefix' => t('<p>Please use the available tokens listed below to put in place holder text that will be pulled from the database.</p><p>Please use "[FIRSTNAME]" where you want the email\'s recipient\'s first name to appear.</p><p>Once you submit the form, the current email will be displayed on this page.</p>'),
      '#required' => TRUE,
    ];
    $form['token_help1'] = [
      '#token_types' => ['troth-elections'],
      '#theme' => 'token_tree_link',
      '#global_types' => FALSE,
      '#click_insert' => TRUE,
      '#collapsible' => TRUE,
      '#collapsed' => FALSE,
    ];

    $form['actions'] = [
      '#type' => 'actions',
    ];

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save CFN'),
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
    // Get the new CFN and replace tokens.
    $cfn_token = $form_state->getValue('cfn');
    $token_service = \Drupal::token();
    $cfn = $token_service->replace($cfn_token);
    $path = \Drupal::config('troth_elections.adminsettings')->get('troth_elections_path');
    $tokfile = $path . '/cfn_token.txt';
    $emailfile = $path . '/cfn.txt';

    // Save the files.
    $fh = fopen($tokfile, 'w');
    fwrite($fh, $cfn_token);
    fclose($fh);

    $fh = fopen($emailfile, 'w');
    fwrite($fh, $cfn);
    fclose($fh);
  }

}
