<?php

namespace Drupal\troth_elections\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\user\Entity\User;
use Drupal\troth_officer\Entity\TrothOfficerType;
use Drupal\troth_elections\Entity\TrothElectionsNominationBios;

/**
 * Edit Troth User Admin form.
 */
class TrothElectionsNomBioForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'troth_elections_make_nom_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $page = NULL) {
    $storage = $form_state->getStorage();
    if (isset($storage['page'])) {
      $page = $storage['page'];
    }
    else {
      $storage['page'] = $page;
    }
    // We need the enties, so load them up front.
    $entityStorage = \Drupal::entityTypeManager()
      ->getStorage('troth_elections_nomination_type');

    switch ($page) {
      case 1:
        // Get all the nominations.
        $results = \Drupal::entityQuery('troth_elections_nomination_type')
          ->condition('accepted', 1, '=')
          ->execute();
        $nominations = $entityStorage->loadMultiple($results);
        $nominees = [];
        foreach ($nominations as $nomination) {
          $uid = $nomination->getNominee();
          $account = User::load($uid);
          if (!isset($nominees[$uid])) {
            $firstName = $account->field_profile_first_name->value;
            $lastName = $account->field_profile_last_name->value;
            $nominees[$uid] = "#$uid: $lastName, $firstName";
          }
        }

        $form['nominee'] = [
          '#title' => $this->t('Nominee'),
          '#description' => $this->t('Select the nominee you are updating'),
          '#type' => 'select',
          '#options' => $nominees,
        ];

        $form['actions']['submit'] = [
          '#type' => 'submit',
          '#value' => $this->t('Choose Nominee'),
        ];

        break;

      case 2:
        if (isset($storage['nominee'])) {
          $nominee = $storage['nominee'];
        }
        else {
          $nominee = \Drupal::currentUser()->id();
          $storage['nominee'] = $nominee;
        }

        // Get all the offices nominee is nominated for and their status.
        $results = \Drupal::entityQuery('troth_elections_nomination_type')
          ->condition('uid', $nominee, '=')
          ->condition('accepted', 1, '=')
          ->execute();
        $nominations = $entityStorage->loadMultiple($results);
        $offices = [];
        $status = [];
        foreach ($nominations as $nomination) {
          $office_id = $nomination->getOffice();
          $office = TrothOfficerType::load($office_id);
          $offices[$office_id] = $office->getName();
          $accepted = $nomination->getAccepted();
          $declined = $nomination->getDeclined();
          if ($accepted == 1) {
            $status[$office_id] = 1;
          }
          elseif ($declined == 1) {
            $status[$office_id] = 2;
          }
          else {
            $status[$office_id] = 0;
          }
        }
        // Store the offices for use in submit.
        $storage['offices'] = $offices;

        $form['header'] = [
          '#type' => 'item',
          '#markup' => $this->t("Please enter a short statement introducing yourself to the membership.  This statement will appear on the ballot."),
        ];

        // Build the form.
        foreach ($offices as $office_id => $office_name) {
          $form[$office_id] = [
            '#title' => $this->t('Bio for @name', ['@name' => $office_name]),
            '#type' => 'text_format',
            '#required' => TRUE,
          ];
          $id = $office_id . "_" . $nominee;
          $results = \Drupal::entityQuery('troth_elections_nomination_bios')
            ->condition('bundle', $id, '=')
            ->condition('uid', $nominee, '=')
            ->execute();
          if (count($results) > 0) {
            $bioId = reset($results);
            $nomBio = TrothElectionsNominationBios::load($bioId);
            if ($nomBio != NULL) {
              $form[$office_id]['#default_value'] = $nomBio->getBio()->value ?: '';
              $form[$office_id]['#format'] = $nomBio->getBio()->format ?: 'basic_html';
            }
          }
        }

        $form['actions']['submit'] = [
          '#type' => 'submit',
          '#value' => $this->t('Submit Candidate Statement'),
        ];

        break;

      case 3:
        $message = $this->t('Thank you for updating your candidate statement.  If you have a need to change it, please come back here before the deadline.');
        $form['header'] = [
          '#type' => 'item',
          '#markup' => $message,
        ];
        break;

    }
    $form_state->setStorage($storage);
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // Enter custom validation here.
    // Get field list from storage.
    $storage = $form_state->getStorage();
    $page = $storage['page'];
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Rebuild so that submitted values stay.
    $form_state->setRebuild();

    // Get fields from storage.
    $storage = $form_state->getStorage();
    $page = $storage['page'];

    if ($page == 1) {
      // Return the nominee accepting.
      $storage['nominee'] = $form_state->getValue('nominee');
    }

    if ($page == 2) {
      $offices = $storage['offices'];
      $nominee = $storage['nominee'];
      foreach ($offices as $office_id => $office) {
        $bio = $form_state->getValue($office_id);
        $id = $office_id . "_" . $nominee;
        $results = \Drupal::entityQuery('troth_elections_nomination_bios')
          ->condition('bundle', $id, '=')
          ->condition('uid', $nominee, '=')
          ->execute();
        if (count($results) > 0) {
          $bioId = reset($results);
          $nominationBio = TrothElectionsNominationBios::load($bioId);
          $nominationBio->setBio($bio);
        }
        else {
          $nominationBio = TrothElectionsNominationBios::create([
            'bundle' => $id,
            'uid' => $nominee,
            'bio' => $bio,
          ]);
        }
        $nominationBio->save();
      }
    }

    $storage['page']++;
    $form_state->setStorage($storage);
  }

}
