<?php

namespace Drupal\troth_elections\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\user\Entity\User;
use Drupal\troth_elections\Entity\TrothElectionsNominationVote;

/**
 * Edit Troth User Admin form.
 */
class TrothElectionsFixNames extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'troth_elections_fix_names_form';
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

    switch ($page) {
      case 1:
        // Get the distinct candidates.
        $connection = \Drupal::database();
        $query = $connection->select('troth_elections_nomination_vote', 'v')
          ->fields('v', ['candidate'])
          ->condition('vote', 0, '>');
        $results = $query->distinct()->execute()->fetchCol();

        $candidates = [];
        foreach ($results as $candidate) {
          if (!is_numeric($candidate)) {
            $candidates[] = $candidate;
          }
        }
        $storage['candidates'] = $candidates;

        $form['candidate'] = [
          '#title' => $this->t('Candidate'),
          '#description' => $this->t('Select the write in vote you are attaching to a name.'),
          '#type' => 'select',
          '#options' => $candidates,
        ];

        $form['actions']['submit'] = [
          '#type' => 'submit',
          '#value' => $this->t('Fix Name'),
        ];

        break;

      case 2:
        $form['header'] = [
          '#type' => 'item',
          '#markup' => $this->t('Please search for the person whom <b><u>@name</u></b> should be replaced with.', [
            '@name' => $storage['candidate'],
          ]),
        ];
        $storage['fields'] = [
          'uid',
          'username',
          'first_name',
          'last_name',
          'email',
          'status',
        ];
        array_push($form, troth_user_member_search_form($storage['fields']));

        break;

      case 3:
        $uids = $storage['uids'];
        $results = [];
        foreach ($uids as $uid) {
          $account = User::load($uid);
          $uid = $account->uid->value;
          $firstName = $account->field_profile_first_name->value;
          $lastName = $account->field_profile_last_name->value;
          $email = $account->mail->value;
          $altmail = $account->field_profile_alt_email->value;
          $visible = $account->field_profile_visibility->value;
          $key = $lastName . $firstName . '::' . $uid;

          if ($altmail != '') {
            $email = "$email / $altmail";
          }
          $results[$key] = "$lastName, $firstName; $email";
        }
        ksort($results);

        $form['candidate_fixed'] = [
          '#title' => $this->t('Name to be Changed To.'),
          '#description' => $this->t('Please select the name of the person that @name should be changed to.', [
            '@name' => $storage['candidate'],
          ]),
          '#type' => 'radios',
          '#options' => $results,
          '#required' => TRUE,
        ];
        $form['actions']['submit'] = [
          '#type' => 'submit',
          '#value' => $this->t('Fix Name'),
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
      $candidates = $storage['candidates'];
      $storage['candidate'] = $candidates[$form_state->getValue('candidate')];
    }

    if ($page == 2) {
      // Get fields to search.
      $fields = $storage['fields'];
      unset($storage['uids']);
      $search = [];
      foreach ($fields as $field) {
        $search[$field] = $form_state->getValue($field);
      }

      // Search the fields.
      $uids = troth_user_member_search($search);
      if (count($uids) == 0) {
        $messenger = \Drupal::messenger();
        $messenger->addMessage($this->t('No members found with your search criteria, please try again.'), $messenger::TYPE_ERROR);
        return;
      }
      else {
        $storage['uids'] = $uids;
      }
    }
    if ($page == 3) {
      // We now update the database.
      $candidate = $storage['candidate'];
      $uid = explode('::', $form_state->getValue('candidate_fixed'))[1];
      $results = \Drupal::entityQuery('troth_elections_nomination_vote')
        ->condition('candidate', $candidate, '=')
        ->execute();
      $entities = TrothElectionsNominationVote::loadMultiple($results);
      $num = 0;
      foreach ($entities as $entity) {
        $entity->setCandidate($uid);
        $entity->save();
        $num++;
      }

      drupal_set_message($this->t('You have updated @num votes from @candidate to @uid.', [
        '@num' => $num,
        '@candidate' => $candidate,
        '@uid' => $uid,
      ]));

      $storage['page'] = 0;
    }

    $storage['page']++;
    $form_state->setStorage($storage);
  }

}
