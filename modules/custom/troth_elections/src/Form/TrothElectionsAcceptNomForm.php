<?php

namespace Drupal\troth_elections\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\user\Entity\User;
use Drupal\troth_officer\Entity\TrothOfficerType;
use Drupal\troth_elections\Entity\TrothElectionsNominationType;
use Drupal\Core\Url;

/**
 * Edit Troth User Admin form.
 */
class TrothElectionsAcceptNomForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'troth_elections_accept_nom_form';
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
          ->execute();
        $nominations = $entityStorage->loadMultiple($results);
        $nominees = [];
        // Confirm you have enough nominations.
        $num_required = \Drupal::config('troth_elections.adminsettings')->get('troth_elections_ballot_num_noms');
        foreach ($nominations as $nomination) {
          $uid = $nomination->getNominee();
          $id = $nomination->id();
          $office_id = $nomination->getOffice();
          $results = \Drupal::entityQuery('troth_elections_nomination')
            ->condition('bundle', $id, '=')
            ->condition('office_id', $office_id, '=')
            ->execute();
          if (count($results) >= $num_required) {
            if (!isset($nominees[$uid])) {
              $account = User::load($uid);
              $firstName = $account->field_profile_first_name->value;
              $lastName = $account->field_profile_last_name->value;
              $nominees[$uid] = "#$uid: $lastName, $firstName";
            }
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
          ->execute();

        $nominations = $entityStorage->loadMultiple($results);
        $offices = [];
        $status = [];
        $num_required = \Drupal::config('troth_elections.adminsettings')->get('troth_elections_ballot_num_noms');
        foreach ($nominations as $nomination) {
          $id = $nomination->id();
          $office_id = $nomination->getOffice();
          $results = \Drupal::entityQuery('troth_elections_nomination')
            ->condition('bundle', $id, '=')
            ->condition('office_id', $office_id, '=')
            ->execute();
          if (count($results) >= $num_required) {

            $office = TrothOfficerType::load($office_id);
            $offices[$office_id] = $office->getName();
            $accepted = $nomination->getAccepted();
            $declined = $nomination->getDeclined();
            $ineligible = $nomination->getIneligible();
            if ($accepted == 1) {
              $status[$office_id] = 1;
            }
            elseif ($declined == 1) {
              $status[$office_id] = 2;
            }
            elseif ($ineligible == 1) {
              $status[$office_id] = 3;
            }
            else {
              $status[$office_id] = 0;
            }
          }
        }
        // Store the offices for use in submit.
        $storage['offices'] = $offices;

        $options = [
          0 => '--Please Select--',
          1 => 'Accepted',
          2 => 'Declined',
          3 => 'Ineligible',
        ];

        if (count($offices) > 0) {
          $form['header'] = [
            '#type' => 'item',
            '#markup' => $this->t("You have been nominated for the following office(s).  Please accept or decline the nomination.  You may only accept one nomination."),
          ];
        }
        else {
          $form['header'] = [
            '#type' => 'item',
            '#markup' => $this->t("You have not been nominated by @num people for any position at this time.", ['@num' => $num_required]),
          ];
          return $form;

        }
        // Build the form.
        foreach ($offices as $office_id => $office_name) {
          $form[$office_id] = [
            '#title' => $this->t('@name', ['@name' => $office_name]),
            '#type' => 'select',
            '#options' => $options,
            '#default_value' => $status[$office_id],
            '#required' => TRUE,
          ];
        }

        $form['actions']['submit'] = [
          '#type' => 'submit',
          '#value' => $this->t('Accept/Decline Nominations'),
        ];

        break;

      case 3:
        // troth_elections.nom_bio_form.
        $link = Url::fromRoute('troth_elections.nom_bio_form');

        $message = $this->t('Thank you for updating your nomination(s).  If you accepted a nomination, please go to <a href=":link">Enter Nomination Bio</a> to update your biographical statement.', [':link' => $link->toString()]);
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
    if ($page == 2) {
      $offices = $storage['offices'];
      $numAcc = 0;
      $officeAcc = [];
      foreach ($offices as $office_id => $office_name) {
        $status = $form_state->getValue($office_id);
        if ($status == 1) {
          $numAcc++;
          $officeAcc[] = $office_id;
        }
      }
      if ($numAcc > 1) {
        foreach ($officeAcc as $office_id) {
          $form_state->setErrorByName($office_id, $this->t('You have accepted multiple offices, please accept only one.'));
        }
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
    $page = $storage['page'];

    if ($page == 1) {
      // Return the nominee accepting.
      $storage['nominee'] = $form_state->getValue('nominee');
    }

    if ($page == 2) {
      $offices = $storage['offices'];
      $nominee = $storage['nominee'];
      foreach ($offices as $office_id => $office_name) {
        $status = $form_state->getValue($office_id);
        $accepted = 0;
        $declined = 0;
        $ineligible = 0;
        if ($status == 1) {
          $accepted = 1;
          $declined = 0;
          $ineligible = 0;
        }
        elseif ($status == 2) {
          $accepted = 0;
          $declined = 1;
          $ineligible = 0;
        }
        elseif ($status == 3) {
          $accepted = 0;
          $declined = 0;
          $ineligible = 1;
        }
        $id = $office_id . "_" . $nominee;
        $nominationEntity = TrothElectionsNominationType::load($id);
        $nominationEntity->setAccepted($accepted);
        $nominationEntity->setDeclined($declined);
        $nominationEntity->setIneligible($ineligible);
        $nominationEntity->save();
      }
    }

    $storage['page']++;
    $form_state->setStorage($storage);
  }

}
