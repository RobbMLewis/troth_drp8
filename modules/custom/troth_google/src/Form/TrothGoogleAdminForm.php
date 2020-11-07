<?php

namespace Drupal\troth_google\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Edit Troth Google Admin form.
 */
class TrothGoogleAdminForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'troth_google.adminsettings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'troth_google_admin_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // We need to have things in the private file system, so check that.
    $fileSystem = \Drupal::service('file_system');
    // Check if the private file stream wrapper is ready to use.
    if (!$fileSystem->validScheme('private')) {
      $form['file_system'] = [
        '#type' => 'item',
        '#title' => $this->t('Private File System Failed'),
        '#markup' => $this->t('There is no private file system available.  This must be enabled in settings.php.  This module will not work without this enabled.'),
      ];
      return $form;
    }
    // Get the config data.
    $config = $this->config('troth_google.adminsettings');
    $form['domain_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Group domain'),
      '#default_value' => $config->get('domain_name'),
      '#required' => TRUE,
      '#size' => 40,
    ];

    $form['api_markup'] = [
      '#type' => 'item',
      '#markup' => $this->t('The following are your OAuth Web application account details as gathered from the <a href=":url" target="_blank">Google Console APIs</a>.', [':url' => 'https://code.google.com/apis/console/#access']),
    ];

    $form['client_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Client ID'),
      '#default_value' => $config->get('client_id'),
      '#required' => TRUE,
    ];

    $form['oauth_client_secret'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Client Secret JSON'),
      '#default_value' => $config->get('oauth_client_secret'),
      '#required' => TRUE,
    ];

    // We want to get the url of this page for redirect in OAuth.
    $reddirectUrl = Url::fromRoute('troth_google.admin_settings_form');
    $reddirectUrl->setOptions(['absolute' => TRUE, 'https' => TRUE]);
    $form['redirects'] = [
      '#type' => 'item',
      '#title' => $this->t('Redirect URIs'),
      '#markup' => $this->t(':url', [':url' => $reddirectUrl->toString()]),
      '#description' => $this->t('Please assure this redirect is present on your Google API console'),
    ];

    // Show the authorize link only once the client id is set.
    if ($client_id = $config->get('client_id')) {
      $url = Url::fromUri('https://accounts.google.com/o/oauth2/auth');

      $options = [
        'query' => [
          'redirect_uri' => $reddirectUrl->toString(),
          'response_type' => 'code',
          'client_id' => $client_id,
          'approval_prompt' => 'force',
          'scope' => 'https://apps-apis.google.com/a/feeds/groups/',
          'access_type' => 'offline',
        ],
      ];
      $url->setOptions($options);
      $form['link'] = [
        '#type' => 'item',
        '#title' => $this->t('Authorize Access'),
        '#markup' => $this->t('<a href=":url">Click to authorize access</a>', [':url' => $url->toString()]),
        '#description' => $this->t('Click the link above to authorize this website to access the Google API'),
      ];
    }

    // Show our current auth code.
    $code =
    $form['code'] = [
      '#type' => 'item',
      '#title' => $this->t('Current auth code'),
      '#markup' => $this->t('@code', ['@code' => \Drupal::state()->get('troth_google_oauth_code')]),
      '#description' => $this->t('Last Fetched Provision API Auth Code'),
    ];

    if (\Drupal::state()->get('troth_google_oauth_code') != '') {
      // If we have an auth code, show the access and refresh tokens we have.
      $form['access_token'] = [
        '#type' => 'item',
        '#title' => $this->t('Access token'),
        '#markup' => $this->t('@code', ['@code' => \Drupal::state()->get('troth_google_access_token')]),
        '#description' => $this->t('Last Fetched Provision API access Token'),
      ];
      $form['refresh_token'] = [
        '#type' => 'item',
        '#title' => $this->t('Refresh token'),
        '#markup' => $this->t('@code', ['@code' => \Drupal::state()->get('troth_google_refresh_token')]),
        '#description' => $this->t('Last Fetched Provision API refresh Token'),
      ];
    }

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // Enter in any validation functions here.
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);
    // We need to save the client secret json.
    if ($this->config('troth_google.adminsettings')->get('oauth_client_secret') != $form_state->getValue('oauth_client_secret')) {
      // New value, we need to save.
      $dir = "private://oauth/";
      file_prepare_directory($dir, FILE_CREATE_DIRECTORY);
      $path = $dir . "client_secret.json";
      $file = file_save_data($form_state->getValue('oauth_client_secret'), $path, FILE_EXISTS_REPLACE);
      if (!is_object($file)) {
        \Drupal::logger('troth_gogle')->error('Client_secret.json could not be saved to @path', ['@path' => $path]);
      }
    }

    // Save the config values.
    $this->config('troth_google.adminsettings')->set('domain_name', $form_state->getValue('domain_name'))->save();
    $this->config('troth_google.adminsettings')->set('client_id', $form_state->getValue('client_id'))->save();
    $this->config('troth_google.adminsettings')->set('oauth_client_secret', $form_state->getValue('oauth_client_secret'))->save();

  }

}
