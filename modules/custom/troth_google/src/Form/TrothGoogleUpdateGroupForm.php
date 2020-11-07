<?php

namespace Drupal\troth_google\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\user\Entity\User;
use Drupal\troth_google\Entity\TrothGoogleGroup;
use Drupal\troth_google\Entity\TrothGoogleGroupType;

/**
 * Edit Troth User Admin form.
 */
class TrothGoogleUpdateGroupForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'troth_google_update_group_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $user = NULL, $list = NULL) {
    $account = User::load($user);
    // Set possible email addresses to subscribe.
    $emailOpts = [$account->getEmail()];
    $altemail = $account->field_profile_alt_email->value ?: NULL;
    if ($altemail) {
      $emailOpts[] = $altemail;
    }

    // Get list info.
    $type = TrothGoogleGroupType::load($list);
    $domain = \Drupal::config('troth_google.adminsettings')->get('domain_name');
    $listAddress = $type->getGroupId() . '@' . $domain;
    $description = $type->getDescription();

    // We need to get the entity we're working with.
    $query = \Drupal::entityQuery('troth_google')
      ->condition('uid', $account->id(), '=')
      ->condition('bundle', $list, '=');
    $entids = $query->execute();

    while (count($entids) > 1) {
      // We have to many entities, we will delete them from the end
      // until we hit 1.
      $entid = array_pop($entids);
      $entity = TrothGoogleGroup::load($entid);
      // Unsubscribe, just in case.
      $entity->trothGoogleUnsubscribe();
      $entity->delete();
    }
    $entid = reset($entids);
    $entity = TrothGoogleGroup::load($entid);

    $form['info'] = [
      '#type' => 'item',
      '#markup' => $this->t("You may change your subscription options for @list (@desc) on this page.  If the list is requred for you to be subscribed to, you cannot unsubscribe, just change your email address.", [
        '@list' => $listAddress,
        '@desc' => $description,
      ]),
    ];
    $form['email'] = [
      '#title' => $this->t("Email Address"),
      '#description' => $this->t("Select which email address you want subscribed."),
      '#type' => 'select',
      '#options' => $emailOpts,
      '#default_value' => array_search($entity->getEmail(), $emailOpts),
    ];
    $form['subscribed'] = [
      '#title' => $this->t('Subscribe to @list', ['@list' => $listAddress]),
      '#description' => $this->t('Check to subscribe to @list', ['@list' => $listAddress]),
      '#type' => 'checkbox',
      '#default_value' => $entity->getSubscribed(),
    ];
    if ($type->getRequired()) {
      $form['subscribed']['#description'] = $this->t('You are required to subscribe to @list and cannot change this setting.', ['@list' => $listAddress]);
      $form['subscribed']['#default_value'] = 1;
      $form['subscribed']['#value'] = 1;
      $form['subscribed']['#attributes'] = ['disabled' => 'disabled'];
    }
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Update'),
    ];

    $storage['entity'] = $entity;
    $storage['emails'] = $emailOpts;
    $storage['uid'] = $user;
    $form_state->setStorage($storage);
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $storage = $form_state->getStorage();
    $entity = $storage['entity'];
    $emailOpts = $storage['emails'];
    $uid = $storage['uid'];
    $email = $emailOpts[$form_state->getValue('email')];
    $subscribe = $form_state->getValue('subscribed');
    if ($email != $entity->getEmail()) {
      // Email has changed, unsubscribe old email.
      $entity->trothGoogleUnsubscribe();
      $entity->setEmail($email);
      if ($subscribe == 1) {
        // They want to be subscribed, subscribe them.
        $entity->trothGoogleSubscribe($email);
        $entity->setSubscribed(1);
      }
      else {
        // They don't want to be subscribed, we just need to set flag.
        $entity->setSubscribed(0);
      }
    }
    else {
      // Email is the same.
      if ($subscribe == 1) {
        // They want to be subscribed, subscribe them.
        $entity->trothGoogleSubscribe($email);
        $entity->setSubscribed(1);
      }
      else {
        // They don't want to be subscribed, unsubscribe them.
        $entity->trothGoogleUnsubscribe($email);
        $entity->setSubscribed(0);
      }
    }
    $entity->save();
    drupal_set_message(t('Settings have been updated'));
    // Go back to summary page.
    $form_state->setRedirect('troth_google.summary_page', ['user' => $uid]);
  }

}
