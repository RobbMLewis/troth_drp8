<?php

namespace Drupal\troth_elections\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\user\Entity\User;
use Drupal\troth_elections\Entity\TrothElectionsNominationType;
use Drupal\troth_elections\Entity\TrothElectionsNomination;
use Drupal\troth_officer\Entity\TrothOffice;

/**
 * Edit Troth User Admin form.
 */
class TrothElectionsMakeNomForm extends FormBase {

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

    switch ($page) {
      case 1:
        $form['header'] = [
          '#type' => 'item',
          '#markup' => $this->t('Please search for the person <b><u>MAKING</u></b> the nomination.'),
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

      case 2:
        $uids = $storage['uids'];
        unset($storage['uids']);
        $results = [];
        foreach ($uids as $uid) {
          $account = User::load($uid);
          $uid = $account->uid->value;
          $firstName = $account->field_profile_first_name->value;
          $lastName = $account->field_profile_last_name->value;
          $email = $account->mail->value;
          $altmail = $account->field_profile_alt_email->value;
          if ($altmail != '') {
            $email = "$email / $altmail";
          }
          $results[$lastName . $firstName . '::' . $uid] = "$uid: $lastName, $firstName; $email";
        }
        ksort($results);
        $form['header'] = [
          '#type' => 'item',
          '#markup' => $this->t("Please choose the person who is <b><u>MAKING</u></b> the nomination from the lsit below.  If you don't see them in the list, please refresh the page and search again."),
        ];
        $form['nominator'] = [
          '#type' => 'radios',
          '#options' => $results,
          '#required' => TRUE,
        ];
        $form['actions']['submit'] = [
          '#type' => 'submit',
          '#value' => $this->t('Choose Nominator'),
        ];
        break;

      case 3:
        $form['header'] = [
          '#type' => 'item',
          '#markup' => $this->t('Please search for the person <b><u>YOU ARE NOMINATING</u></b>'),
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

      case 4:
        if (isset($storage['nominator'])) {
          $nominator = $storage['nominator'];
        }
        else {
          $nominator = \Drupal::currentUser()->id();
          $storage['nominator'] = $nominator;
        }

        $uids = $storage['uids'];
        unset($storage['uids']);
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
          if ($visible == 0) {
            $firstName = $this->maskString($firstName);
            $lastName = $this->maskString($lastName);
            $email = $this->maskString($email, strpos($email, '@') + 1);
            $altmail = $this->maskString($altmail, strpos($email, '@') + 1);
          }

          if ($altmail != '') {
            $email = "$email / $altmail";
          }
          $results[$key] = "$lastName, $firstName; $email";
        }
        ksort($results);
        $form['office_id'] = [
          '#title' => $this->t('Office Nominating For'),
          '#description' => $this->t('Select the office that you are nominating for.'),
          '#type' => 'select',
          '#options' => $offices,
          '#required' => TRUE,
        ];
        $form['nominee'] = [
          '#title' => $this->t('Person you are Nominating.'),
          '#description' => $this->t('Select the person you are nominating for the office selected above.'),
          '#type' => 'radios',
          '#options' => $results,
          '#required' => TRUE,
        ];
        $form['actions']['submit'] = [
          '#type' => 'submit',
          '#value' => $this->t('Make Nomination'),
        ];
        break;

      case 5:
        $message = $this->t('Thank you for making a nomination.  Your nomination of @name for @office has been recorded.  Reload the page if you desire to make another nomination.', [
          '@name' => $storage['name'],
          '@office' => $storage['office'],
        ]);
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

    if ($page == 1 || $page == 3) {
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
    if ($page == 4) {
      // We need to check if we allow self nomination,
      // and throw error if we're not.
      $self_nom = \Drupal::config('troth_elections.adminsettings')->get('troth_elections_ballot_self_noms');
      if ($self_nom == 0) {
        $nominee = explode('::', $form_state->getValue('nominee'))[1];
        if (isset($storage['nominator'])) {
          $nominator = $storage['nominator'];
        }
        else {
          $nominator = \Drupal::currentUser()->id();
        }

        if ($nominee == $nominator) {
          $messenger = \Drupal::messenger();
          $messenger->addMessage($this->t('You are not allowed to self nominate.'), $messenger::TYPE_ERROR);
          $form_state->setErrorByName('nominee');
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
    $fields = $storage['fields'];
    $page = $storage['page'];
    unset($storage['uids']);

    if ($page == 1 || $page == 3) {
      // Get fields to search.
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

    if ($page == 2) {
      $nominator = explode('::', $form_state->getValue('nominator'));
      $storage['nominator'] = $nominator[1];
    }

    if ($page == 4) {
      $nominator = $storage['nominator'];
      $nominee = explode('::', $form_state->getValue('nominee'))[1];
      $account = User::load($nominee);
      $office_id = $form_state->getValue('office_id');
      $label = "$office_id:$nominee";
      $id = $office_id . "_" . $nominee;
      $nominationEntity = TrothElectionsNominationType::load($id);
      if ($nominationEntity == NULL) {
        // The nomination doesn't exist, create it.
        $nominationEntity = TrothElectionsNominationType::create([
          'id' => $id,
          'label' => $label,
          'office_id' => $office_id,
          'uid' => $nominee,
          'nominated' => 0,
          'accepted' => 0,
          'declinded' => 0,
          'numnoms' => 1,
        ]);
        $nominationEntity->save();
      }
      // See if this nomination has already happened.
      $results = \Drupal::entityQuery('troth_elections_nomination')
        ->condition('bundle', $id, '=')
        ->condition('uid', $nominator, '=')
        ->condition('office_id', $office_id, '=')
        ->execute();
      if (count($results) == 0) {
        // Its a new nomiation, create it.
        $nominatorEntity = TrothElectionsNomination::create([
          'bundle' => $id,
          'uid' => $nominator,
          'office_id' => $office_id,
        ]);
        $nominatorEntity->save();
      }

      // Now we process the nomination and see if we need to send an email.
      // Check the number of nominations.  If > 2, the person is nominated.
      $results = \Drupal::entityQuery('troth_elections_nomination')
        ->condition('bundle', $id, '=')
        ->condition('office_id', $office_id, '=')
        ->execute();
      $numnoms = count($results);
      $nominationEntity->setNumNoms($numnoms);

      if ($numnoms >= 1) {
        // Has the person been nominated before?
        $nominated = $nominationEntity->getNominated();
        if ($nominated == 0) {
          // They have not been nominated and haven't received emails
          // We need to set them as nominated and send them the nom email.
          $nominationEntity->setNominated(1);

          // Get defaults for the email.
          $mailManager = \Drupal::service('plugin.manager.mail');
          $token_service = \Drupal::token();
          $message = \Drupal::config('troth_elections.adminsettings')->get('trothElectionsYouveBeenNominated');
          $eoEmail = \Drupal::config('troth_elections.adminsettings')->get('troth_elections_eo_email');

          // Get nominee info.
          $nomEmail = $account->getEmail();
          $langcode = $account->getPreferredLangcode();

          // We send an email to them.
          $params['message'] = $token_service->replace($message, [
            'uid' => $nominee,
            'office_id' => $office_id,
          ]);
          $params['sub'] = "You have been nominated for the Troth elections.";
          $params['from'] = $eoEmail;
          $debug = \Drupal::config('troth_elections.adminsettings')->get('troth_elections_debug');
          if ($debug == 1) {
            $to = $eoEmail;
          }
          else {
            $to = "$nomEmail,$eoEmail";
          }
          $result = $mailManager->mail('troth_user', 'troth_mail', $to, $langcode, $params, NULL, TRUE);
          if ($result['result'] != TRUE) {
            \Drupal::logger('troth_elections')->error('Nominations Email did not send to: %to, %message', [
              '%to' => $to,
              '%message' => $params['message'],
            ]);
          }
          else {
            \Drupal::logger('troth_elections')->notice('Nominations Email sent. %to, %message', [
              '%to' => $to,
              '%message' => $params['message'],
            ]);
          }
        }
      }
      // We have updated the nomination entity, we need to save it.
      $nominationEntity->save();

      // Now we set a message for display.
      $firstName = $account->field_profile_first_name->value;
      $lastName = $account->field_profile_last_name->value;
      $email = $account->mail->value;
      $altmail = $account->field_profile_alt_email->value;
      $visible = $account->field_profile_visibility->value;
      if ($visible == 0) {
        $firstName = $this->maskString($firstName);
        $lastName = $this->maskString($lastName);
        $email = $this->maskString($email, strpos($email, '@') + 1);
        $altmail = $this->maskString($altmail, strpos($email, '@') + 1);
      }

      if ($altmail != '') {
        $email = "$email / $altmail";
      }
      $office = TrothOffice::load($office_id);
      $storage['name'] = "$lastName, $firstName; $email";
      $storage['office'] = $office->getName();
    }

    $storage['page']++;
    $form_state->setStorage($storage);
  }

  /**
   * {@inheritdoc}
   */
  private function maskString($string = NULL, $length = 4, $repeat = 5) {
    if ($string == NULL || strlen($string) <= $length) {
      return $string;
    }
    return substr($string, 0, $length) . str_repeat("*", $repeat);
  }

}
