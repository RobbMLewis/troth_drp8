<?php

namespace Drupal\troth_user\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Edit Troth User Admin form.
 */
class TrothUserAdminForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'troth_user.adminsettings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'troth_user_admin_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('troth_user.adminsettings');
    $form['general'] = [
      '#type' => 'fieldset',
      '#title' => t('General Settings'),
      '#collapsible' => TRUE,
      '#collapsed' => FALSE,
    ];
    $form['general']['clerk_name'] = [
      '#title' => t("Current Clerk's Name"),
      '#description' => t('The name of the current Clerk'),
      '#type' => 'textfield',
      '#default_value' => $config->get('clerk_name'),
      '#required' => TRUE,
    ];
    $form['general']['steer_name'] = [
      '#title' => t("Current Steer's Name"),
      '#description' => t('The name of the current Steer'),
      '#type' => 'textfield',
      '#default_value' => $config->get('steer_name'),
      '#required' => TRUE,
    ];
    $form['general']['idunna_issue'] = [
      '#title' => t("Current Current issue of Idunna."),
      '#description' => t('Current Current issue of Idunna.'),
      '#type' => 'number',
      '#step' => 1,
      '#min' => 1,
      '#default_value' => $config->get('idunna_issue'),
      '#required' => TRUE,
    ];
    $form['general']['send_email'] = [
      '#title' => t("Send From Email"),
      '#description' => t('The Email address to send membership emails from.'),
      '#type' => 'email',
      '#default_value' => $config->get('send_email'),
      '#required' => TRUE,
    ];

    $form['expire'] = [
      '#type' => 'fieldset',
      '#title' => t('Membership Expiration Settings'),
      '#collapsible' => TRUE,
      '#collapsed' => FALSE,
    ];
    $form['expire']['grace_period'] = [
      '#type' => 'number',
      '#title' => $this->t('Grace Period (weeks)'),
      '#description' => $this->t("How many weeks does someone have to renew before they're considered a rejoin?"),
      '#default_value' => $config->get('grace_period') ?: 12,
      '#min' => 0,
      '#step' => 1,
    ];
    $form['expire']['email_address_on_expire'] = [
      '#title' => t('Expired Email Address(es)'),
      '#description' => t('What Organizational Email Address(es) should be mailed when a member expires?  If multiple, separate with commas.'),
      '#type' => 'textfield',
      '#default_value' => $config->get('email_address_on_expire'),
      '#required' => TRUE,
    ];
    $form['expire']['about'] = [
      '#type' => 'fieldset',
      '#title' => t('Membership About to Expire Settings'),
      '#collapsible' => TRUE,
      '#collapsed' => TRUE,
    ];
    $form['expire']['about']['email_about_to_expire_when'] = [
      '#title' => t('When to Email those About to Expire'),
      '#description' => t('Number of weeks before expiration that a member should be notified.  Enter comma separated values'),
      '#type' => 'textfield',
      '#default_value' => $config->get('email_about_to_expire_when') ?: '4,1',
      '#required' => TRUE,
    ];
    $form['expire']['about']['email_about_to_expire_send'] = [
      '#title' => t('Send email to Org Email Addresses?'),
      '#description' => t('Check to send the about to expire emails to the Org Email Addresses set above.'),
      '#type' => 'checkbox',
      '#default_value' => $config->get('email_about_to_expire_send') ?: 1,
      '#required' => FALSE,
    ];
    $form['expire']['about']['email_about_to_expire'] = [
      '#title' => t('Email Message for those About to Expire'),
      '#description' => t('Message to be sent to members that are about to expire.  Please use the tokens below for customization.'),
      '#type' => 'textarea',
      '#rows' => 15,
      '#default_value' => $config->get('email_about_to_expire') ?: $this->aboutToExpireEmail(),
      '#required' => TRUE,
    ];
    $form['expire']['about']['token_help1'] = [
      '#theme' => 'token_tree_link',
      '#token_types' => ['user', 'troth-user'],
      '#global_types' => FALSE,
      '#click_insert' => TRUE,
      '#collapsible' => TRUE,
      '#collapsed' => FALSE,
    ];
    $form['expire']['lapsed'] = [
      '#type' => 'fieldset',
      '#title' => t('Membership In Grace Settings'),
      '#collapsible' => TRUE,
      '#collapsed' => TRUE,
    ];

    $form['expire']['lapsed']['email_grace_expire_when'] = [
      '#title' => t('When to Email those in Grace Period'),
      '#description' => t('Number of weeks after expiration that a member should be notified.  Enter comma separated values'),
      '#type' => 'textfield',
      '#default_value' => $config->get('email_grace_expire_when') ?: 4,
      '#required' => TRUE,
    ];
    $form['expire']['lapsed']['email_grace_expire_send'] = [
      '#title' => t('Send email to Org Email Addresses?'),
      '#description' => t('Check to send the grace period emails to the Org Email Addresses set above.'),
      '#type' => 'checkbox',
      '#default_value' => $config->get('email_grace_expire_send') ?: 1,
      '#required' => FALSE,
    ];
    $form['expire']['lapsed']['email_grace_expire'] = [
      '#title' => t('Email Message for those in Grace Period'),
      '#description' => t('Message to be sent to members that are in the 3 month grace period.  Please use the tokens below for customization.'),
      '#type' => 'textarea',
      '#rows' => 15,
      '#default_value' => $config->get('email_grace_expire') ?: $this->graceExpireEmail(),
      '#required' => TRUE,
    ];
    $form['expire']['lapsed']['token_help2'] = [
      '#theme' => 'token_tree_link',
      '#token_types' => ['user', 'troth-user'],
      '#global_types' => FALSE,
      '#click_insert' => TRUE,
      '#collapsible' => TRUE,
      '#collapsed' => FALSE,
    ];
    $form['expire']['expired'] = [
      '#type' => 'fieldset',
      '#title' => t('Membership is Expired Settings'),
      '#collapsible' => TRUE,
      '#collapsed' => TRUE,
    ];
    $form['expire']['expired']['email_expire_when'] = [
      '#title' => t('When to Email those that Expired'),
      '#description' => t('Number of weeks after expiration and grace period that a member should be notified.  Enter comma separated values'),
      '#type' => 'textfield',
      '#default_value' => $config->get('email_expire_when') ?: 1,
      '#required' => TRUE,
    ];
    $form['expire']['expired']['email_expire_send'] = [
      '#title' => t('Send email to Org Email Addresses?'),
      '#description' => t('Check to send the expired emails to the Org Email Addresses set above. (Reccomended)'),
      '#type' => 'checkbox',
      '#default_value' => $config->get('email_expire_send') ?: 1,
      '#required' => FALSE,
    ];
    $form['expire']['expired']['email_on_expire'] = [
      '#title' => t('Email Message for those who are Expired'),
      '#description' => t('Message to be sent to members that have expired, after their 3 month grace period.  Please use the tokens below for customization.'),
      '#type' => 'textarea',
      '#rows' => 15,
      '#default_value' => $config->get('email_on_expire') ?: $this->expireEmail(),
      '#required' => TRUE,
    ];
    $form['expire']['expired']['token_help3'] = [
      '#theme' => 'token_tree_link',
      '#token_types' => ['user', 'troth-user'],
      '#global_types' => FALSE,
      '#click_insert' => TRUE,
      '#collapsible' => TRUE,
      '#collapsed' => FALSE,
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    if (!empty($form_state->getValue('email_address_on_expire'))) {
      $emails = preg_replace("/\s+/", "", $form_state->getValue('email_address_on_expire'));
      $emails = explode(',', $emails);
      $bad = [];
      foreach ($emails as $email) {
        if (!\Drupal::service('email.validator')->isValid($email)) {
          $bad[] = $email;
        }
      }
      if (count($bad) > 0) {
        $form_state->setErrorByName('email_address_on_expire', $this->t('The following email addresses are not valid: %bad', [
          '%bad' => implode(',', $bad),
        ]));
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);
    $this->config('troth_user.adminsettings')->set('clerk_name', $form_state->getValue('clerk_name'))->save();
    $this->config('troth_user.adminsettings')->set('steer_name', $form_state->getValue('steer_name'))->save();
    $this->config('troth_user.adminsettings')->set('idunna_issue', $form_state->getValue('idunna_issue'))->save();
    $this->config('troth_user.adminsettings')->set('send_email', $form_state->getValue('send_email'))->save();
    $this->config('troth_user.adminsettings')->set('grace_period', $form_state->getValue('grace_period'))->save();
    $this->config('troth_user.adminsettings')->set('email_address_on_expire', preg_replace("/\s+/", "", $form_state->getValue('email_address_on_expire')))->save();
    $this->config('troth_user.adminsettings')->set('email_about_to_expire_when', $form_state->getValue('email_about_to_expire_when'))->save();
    $this->config('troth_user.adminsettings')->set('email_about_to_expire_send', $form_state->getValue('email_about_to_expire_send'))->save();
    $this->config('troth_user.adminsettings')->set('email_about_to_expire', $form_state->getValue('email_about_to_expire'))->save();
    $this->config('troth_user.adminsettings')->set('email_grace_expire_when', $form_state->getValue('email_grace_expire_when'))->save();
    $this->config('troth_user.adminsettings')->set('email_grace_expire_send', $form_state->getValue('email_grace_expire_send'))->save();
    $this->config('troth_user.adminsettings')->set('email_grace_expire', $form_state->getValue('email_grace_expire'))->save();
    $this->config('troth_user.adminsettings')->set('email_expire_when', $form_state->getValue('email_expire_when'))->save();
    $this->config('troth_user.adminsettings')->set('email_expire_send', $form_state->getValue('email_expire_send'))->save();
    $this->config('troth_user.adminsettings')->set('email_on_expire', $form_state->getValue('email_on_expire'))->save();
  }

  /**
   * Returns the default email for those about to exipre.
   */
  private function aboutToExpireEmail() {
    $message = <<< EOF
Dear [troth-user:first-name],

Your membership in the Troth will be expiring on [troth-user:expire-date]. We appreciate your membership and hope that you will take the time to renew.  Renewal will allow you continued access to member content on the website, various forums including the email lists and Facebook group.  To renew, please log into the website and then go to  https://thetroth.org/renew.html 

If you do not recall your login information, please go to https://thetroth.org/password and enter [user:name] or [user:mail] into the form and you will be emailed a one time login link to follow.  Once you log into the website you will be required to enter a new password.

If you have no intentions to renew, we wish you the best on whatever path you go down.  If you would be kind enough to email us back as to why you are leaving, it would be greatly appreciated.

Blessings,
[troth-user:Clerk-name]
Clerk of the Troth
EOF;
    return $message;
  }

  /**
   * Returns the default email for those in grace period.
   */
  private function graceExpireEmail() {
    $message = <<< EOF
Dear [troth-user:first-name],

Your membership in the Troth expired on [troth-user:expire-date], but you are in a 3 month grace period until you lose member privlidges. We appreciate your membership and hope that you will take the time to renew.  Renewal will allow you continued access to member content on the website, various forums including the email lists and FaceBook group.  To renew, please log into the website and then go to  https://thetroth.org/renew.html 

If you do not recall your login information, please go to https://thetroth.org/password and enter [user:name] or [user:mail] into the form and you will be emailed a one time login link to follow.  Once you log into the website you will be required to enter a new password.

If you have no intentions to renew, we wish you the best on whatever path you go down.  If you would be kind enough to email us back as to why you are leaving, it would be greatly appreciated.

Blessings,
[troth-user:Clerk-name]
Clerk of the Troth
EOF;
    return $message;
  }

  /**
   * Returns email for those that have expired.
   */
  private function expireEmail() {
    $message = <<< EOF
Dear [troth-user:first-name],

Your membership in the Troth has expired on [troth-user:expire-date].  This means that you will no longer have access to member only content on the website, and you will be removed from any member only forums.  We do hope that you will re-join.  If wish to re-join, please log into the website and go to https://thetroth.org/renew.html .

If you do not recall your login information, please go to https://thetroth.org/password and enter [user:name] or [user:mail] into the form and you will be emailed a one time login link to follow.  Once you log into the website you will be required to enter a new password.

If you have no intentions to re-join, we wish you the best on whatever path you go down.  If you would be kind enough to email us back as to why you are leaving, it would be greatly appreciated.

Blessings,
[troth-user:Clerk-name]
Clerk of the Troth
EOF;
    return $message;
  }

}
