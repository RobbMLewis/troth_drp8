<?php

namespace Drupal\troth_user\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use CommerceGuys\Addressing\Country\CountryRepository;
use Drupal\user\Entity\User;

/**
 * Edit Troth User Admin form.
 */
class TrothUserSearchForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'troth_user_search_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = troth_user_member_search_form();

    // We want to store the field names in $form_state['storage'].
    $storage = $form_state->getStorage();
    $storage['fields'] = [
      'uid',
      'username',
      'first_name',
      'last_name',
      'email',
      'city',
      'state',
      'postal',
      'country',
    ];
    $form_state->setStorage($storage);

    // If there are results, they will be in $storage['out'].
    if (isset($storage['out'])) {
      $form['out'] = [
        '#type' => 'item',
        '#title' => $this->t('Results'),
        '#markup' => $storage['out'],
      ];
    }
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // Get field list from storage.
    $storage = $form_state->getStorage();
    $fields = $storage['fields'];

    // Make sure that there were fields entered to search by.
    $count = 0;
    foreach ($fields as $field) {
      if (!empty($form_state->getValue($field))) {
        $count++;
      }
    }

    // If there was nothing to search by, return errors.
    if ($count == 0) {
      $messenger = \Drupal::messenger();
      $messenger->addMessage($this->t('Please submit one or more search terms'), $messenger::TYPE_ERROR);
      foreach ($fields as $field) {
        $form_state->setErrorByName($field);
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Rebuild so that submitted values stay.
    $form_state->setRebuild();

    // Get fields from storage.
    $storage = $form_state->getStorage();
    $fields = $storage['fields'];

    // Get fields to search.
    $search = [];
    foreach ($fields as $field) {
      $search[$field] = $form_state->getValue($field);
    }

    // Search the fields.
    $uids = troth_user_member_search($search);
    if (count($uids) == 0) {
      // No results, set message.
      $messenger = \Drupal::messenger();
      $messenger->addMessage($this->t('No members found.'), $messenger::TYPE_WARNING);
    }
    else {
      // Start the item list that will be displayed as results.
      $out = '<ul>';
      foreach ($uids as $uid) {
        // Load the user and get the data.
        $account = User::load($uid);
        $uid = $account->uid->value;
        $name = $account->name->value;
        $firstName = $account->field_profile_first_name->value;
        $lastName = $account->field_profile_last_name->value;
        $city = $account->field_profile_location->locality;
        $state = $account->field_profile_location->administrative_area;
        $countryRepository = new CountryRepository();
        $country = $countryRepository->get($account->field_profile_location->country_code);
        $country = $country->getName();

        // Add to the out data.
        $out .= "<li><a href=\"/user/$uid\">$uid $name</a> $firstName $lastName,  $city, $state $country</li>";
      }
      // Close out the out list.
      $out .= "</ul>";
    }

    // Save the out data to storage so it can be retreived by the form.
    $storage['out'] = $out;
    $form_state->setStorage($storage);
  }

}
