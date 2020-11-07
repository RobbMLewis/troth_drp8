<?php

namespace Drupal\troth_elections\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Datetime\DrupalDateTime;

/**
 * Edit Troth Elections Admin form.
 */
class TrothElectionsAdminForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'troth_elections.adminsettings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'troth_elections_admin_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('troth_elections.adminsettings');
    $form['troth_elections_url'] = [
      '#title' => t('Base URL for elections pages'),
      '#description' => t("URL does not include https://thetroth.org/"),
      '#type' => 'textfield',
      '#size' => 100,
      '#default_value' => $config->get('troth_elections_url') ?: '/members/elections',
      '#required' => TRUE,
    ];
    $form['troth_elections_path'] = [
      '#title' => t('Path to store the files'),
      '#description' => t("Absolute path on the server"),
      '#type' => 'textfield',
      '#size' => 100,
      '#default_value' => $config->get('troth_elections_path'),
      '#required' => TRUE,
    ];
    $form['troth_elections_eo_name'] = [
      '#title' => t('Election Officers Name'),
      '#type' => 'textfield',
      '#size' => 100,
      '#default_value' => $config->get('troth_elections_eo_name'),
      '#required' => TRUE,
    ];
    $form['troth_elections_eo_email'] = [
      '#title' => t('Election Officers Email Address'),
      '#type' => 'textfield',
      '#size' => 100,
      '#default_value' => $config->get('troth_elections_eo_email'),
      '#required' => TRUE,
    ];
    $form['troth_elections_nom_url'] = [
      '#title' => t('URL of Nomination Page'),
      '#description' => t("URL does not include https://thetroth.org/"),
      '#type' => 'textfield',
      '#size' => 100,
      '#default_value' => $config->get('troth_elections_nom_url') ?: '/members/elections/nominate',
      '#required' => TRUE,
    ];
    $form['troth_elections_nom_accept_url'] = [
      '#title' => t('URL of Nomination Acceptance Page'),
      '#description' => t("URL does not include https://thetroth.org/"),
      '#type' => 'textfield',
      '#size' => 100,
      '#default_value' => $config->get('troth_elections_nom_accept_url') ?: '/members/elections/accept',
      '#required' => TRUE,
    ];
    $form['troth_elections_nom_bio_url'] = [
      '#title' => t('URL of Bio Statement Page'),
      '#description' => t("URL does not include https://thetroth.org/"),
      '#type' => 'textfield',
      '#size' => 100,
      '#default_value' => $config->get('troth_elections_nom_bio_url') ?: '/members/elections/bios',
      '#required' => TRUE,
    ];
    $form['troth_elections_ballot_url'] = [
      '#title' => t('URL of Ballot'),
      '#description' => t("URL does not include https://thetroth.org/"),
      '#type' => 'textfield',
      '#size' => 100,
      '#default_value' => $config->get('troth_elections_ballot_url') ?: '/members/elections/ballot',
      '#required' => TRUE,
    ];
    $form['troth_elections_ballot_num_noms'] = [
      '#title' => t('Number of Nominations Required'),
      '#description' => t("How many nominations are required for someone to be nominated."),
      '#type' => 'number',
      '#min' => 1,
      '#step' => 1,
      '#default_value' => $config->get('troth_elections_ballot_num_noms') ?: '2',
      '#required' => TRUE,
    ];
    $form['troth_elections_ballot_self_noms'] = [
      '#title' => t('Allow Self Nominations'),
      '#description' => t("If checked, the person will be allowed to self nominate themselves."),
      '#type' => 'checkbox',
      '#default_value' => $config->get('troth_elections_ballot_self_noms') ?: FALSE,
      '#required' => FALSE,
    ];
    $form['troth_elections_nom_start_date'] = [
      '#title' => t('Date that Nominations Start'),
      '#type' => 'date',
      '#default_value' => $config->get('troth_elections_nom_start_date'),
      '#required' => TRUE,
    ];
    $form['troth_elections_nom_end_date'] = [
      '#title' => t('Date that Nomination Ends'),
      '#type' => 'date',
      '#default_value' => $config->get('troth_elections_nom_end_date'),
      '#required' => TRUE,
    ];
    $form['troth_elections_nom_accept_date'] = [
      '#title' => t('Date that Nomination Have to be Accepted by.'),
      '#type' => 'date',
      '#default_value' => $config->get('troth_elections_nom_accept_date'),
      '#required' => TRUE,
    ];
    $form['troth_elections_nom_bio_date'] = [
      '#title' => t('Date that Nominees Need to Enter Bio by.'),
      '#type' => 'date',
      '#default_value' => $config->get('troth_elections_nom_bio_date'),
      '#required' => TRUE,
    ];
    $form['troth_elections_voting_open_date'] = [
      '#title' => t('Date that voting starts.'),
      '#type' => 'date',
      '#default_value' => $config->get('troth_elections_voting_open_date'),
      '#required' => TRUE,
    ];
    $form['troth_elections_voting_end_date'] = [
      '#title' => t('Date that voting ends..'),
      '#type' => 'date',
      '#default_value' => $config->get('troth_elections_voting_end_date'),
      '#required' => TRUE,
    ];
    $form['trothElectionsYouveBeenNominated'] = [
      '#title' => t("You've Been Nominated Email"),
      '#type' => 'textarea',
      '#default_value' => $config->get('trothElectionsYouveBeenNominated') ?: $this->trothElectionsYouveBeenNominated(),
      '#rows' => 10,
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
    $form['trothElectionsBallotOpen'] = [
      '#title' => t("Voting is open Email"),
      '#type' => 'textarea',
      '#default_value' => $config->get('trothElectionsBallotOpen') ?: $this->trothElectionsBallotOpen(),
      '#rows' => 10,
      '#required' => TRUE,
    ];
    $form['token_help2'] = [
      '#token_types' => ['troth-elections'],
      '#theme' => 'token_tree_link',
      '#global_types' => FALSE,
      '#click_insert' => TRUE,
      '#collapsible' => TRUE,
      '#collapsed' => FALSE,
    ];
    $form['troth_elections_proxy'] = [
      '#title' => t('Enable Proxy Votes'),
      '#type' => 'checkbox',
      '#default_value' => $config->get('troth_elections_proxy'),
    ];
    $form['troth_elections_debug'] = [
      '#title' => t('Debug Elections forms'),
      '#type' => 'checkbox',
      '#default_value' => $config->get('troth_elections_debug'),
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    if (!empty($form_state->getValue('troth_elections_eo_email'))) {
      $emails = preg_replace("/\s+/", "", $form_state->getValue('troth_elections_eo_email'));
      $emails = explode(',', $emails);
      if (count($emails) > 1) {
        $form_state->setErrorByName('troth_elections_eo_email', $this->t('You have entered more than one email address, please enter only one email address.'));
      }
      $bad = [];
      foreach ($emails as $email) {
        if (!\Drupal::service('email.validator')->isValid($email)) {
          $bad[] = $email;
        }
      }
      if (count($bad) > 0) {
        $form_state->setErrorByName('troth_elections_eo_email', $this->t('The following email addresses are not valid: %bad', [
          '%bad' => implode(',', $bad),
        ]));
      }
    }

    // We need to make sure dates make sense.
    $nomStartDate = new DrupalDateTime($form_state->getValue('troth_elections_nom_start_date'), 'America/Los_Angeles');
    $nomEndDate = new DrupalDateTime($form_state->getValue('troth_elections_nom_end_date'), 'America/Los_Angeles');
    $nomAcceptDate = new DrupalDateTime($form_state->getValue('troth_elections_nom_accept_date'), 'America/Los_Angeles');
    $nomBioDate = new DrupalDateTime($form_state->getValue('troth_elections_nom_bio_date'), 'America/Los_Angeles');
    $votingStartDate = new DrupalDateTime($form_state->getValue('troth_elections_voting_open_date'), 'America/Los_Angeles');
    $votingEndDate = new DrupalDateTime($form_state->getValue('troth_elections_voting_end_date'), 'America/Los_Angeles');
    if ($nomEndDate <= $nomStartDate) {
      $form_state->setErrorByName('troth_elections_nom_end_date', $this->t('The Nomination End Date is not at least 1 day later than the Nomination Start Date'));
    }
    if ($nomAcceptDate < $nomEndDate) {
      $form_state->setErrorByName('troth_elections_nom_accept_date', $this->t('The Nomination Accept Date is not the same or later than the Nomination End Date'));
    }
    if ($nomBioDate < $nomEndDate) {
      $form_state->setErrorByName('troth_elections_nom_bio_date', $this->t('The Nomination Bio Date is not the same or later than the Nomination End Date'));
    }
    if ($votingStartDate < $nomEndDate) {
      $form_state->setErrorByName('troth_elections_voting_open_date', $this->t('The Voting Start Date is not the same or later than the Nomination End Date'));
    }
    if ($votingEndDate <= $votingStartDate) {
      $form_state->setErrorByName('troth_elections_voting_open_date', $this->t('The Voting End Date is not at least 1 day later than the Voting Start Date'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);
    $this->config('troth_elections.adminsettings')->set('troth_elections_url', $form_state->getValue('troth_elections_url'))->save();
    $this->config('troth_elections.adminsettings')->set('troth_elections_path', $form_state->getValue('troth_elections_path'))->save();
    $this->config('troth_elections.adminsettings')->set('troth_elections_eo_name', $form_state->getValue('troth_elections_eo_name'))->save();
    $this->config('troth_elections.adminsettings')->set('troth_elections_eo_email', $form_state->getValue('troth_elections_eo_email'))->save();
    $this->config('troth_elections.adminsettings')->set('troth_elections_nom_url', $form_state->getValue('troth_elections_nom_url'))->save();
    $this->config('troth_elections.adminsettings')->set('troth_elections_nom_accept_url', $form_state->getValue('troth_elections_nom_accept_url'))->save();
    $this->config('troth_elections.adminsettings')->set('troth_elections_nom_bio_url', $form_state->getValue('troth_elections_nom_bio_url'))->save();
    $this->config('troth_elections.adminsettings')->set('troth_elections_ballot_url', $form_state->getValue('troth_elections_ballot_url'))->save();
    $this->config('troth_elections.adminsettings')->set('troth_elections_ballot_num_noms', $form_state->getValue('troth_elections_ballot_num_noms'))->save();
    $this->config('troth_elections.adminsettings')->set('troth_elections_ballot_self_noms', $form_state->getValue('troth_elections_ballot_self_noms'))->save();
    $this->config('troth_elections.adminsettings')->set('troth_elections_nom_start_date', $form_state->getValue('troth_elections_nom_start_date'))->save();
    $this->config('troth_elections.adminsettings')->set('troth_elections_nom_end_date', $form_state->getValue('troth_elections_nom_end_date'))->save();
    $this->config('troth_elections.adminsettings')->set('troth_elections_nom_accept_date', $form_state->getValue('troth_elections_nom_accept_date'))->save();
    $this->config('troth_elections.adminsettings')->set('troth_elections_nom_bio_date', $form_state->getValue('troth_elections_nom_bio_date'))->save();
    $this->config('troth_elections.adminsettings')->set('troth_elections_voting_open_date', $form_state->getValue('troth_elections_voting_open_date'))->save();
    $this->config('troth_elections.adminsettings')->set('troth_elections_voting_end_date', $form_state->getValue('troth_elections_voting_end_date'))->save();
    $this->config('troth_elections.adminsettings')->set('troth_elections_youve_been_nominated', $form_state->getValue('troth_elections_youve_been_nominated'))->save();
    $this->config('troth_elections.adminsettings')->set('troth_elections_ballot_open', $form_state->getValue('troth_elections_ballot_open'))->save();
    $this->config('troth_elections.adminsettings')->set('troth_elections_proxy', $form_state->getValue('troth_elections_proxy'))->save();
    $this->config('troth_elections.adminsettings')->set('troth_elections_debug', $form_state->getValue('troth_elections_debug'))->save();

    drupal_flush_all_caches();
  }

  /**
   * Returns the default you have been nominated.
   */
  private function trothElectionsYouveBeenNominated() {
    $message = <<< EOF
Hail [troth-elections:first-name], you have been nominated for [troth-elections:nom-pos].

Please go to [troth-elections:nom-accept-url] and either accept or decline your nomination by [troth-elections:nom-accept-date].  You can change your mind at any time by going back to that url and changing your option.

If you accept the nomination, you will be directed to [troth-elections:nom-bio-url] to enter a biographical statement.  Please enter a short candidate statement to introduce yourself to all the voters.  You may change this up until [troth-elections:nom-bio-date], after which it will be set for ballot printing.  If you do not enter a statement now, the elections officer will remind you at least once before the deadline.

Good Luck,
[troth-elections:eo-name]
[troth-elections:eo-email]
EOF;
    return $message;
  }

  /**
   * Returns the default ballot is open email.
   */
  private function trothElectionsBallotOpen() {
    $message = <<< EOF
Hail [troth-elections:first-name],

Voting is now open. Please go to [troth-elections:ballot-url] and log into the website using your regular username and password.  If you cannot remember your password, please go to https://thetroth.org/password to reset your password.  The reset will send you a one time link to follow that will allow you to log in and change your password.  Please note, you will need your password to sign your ballot.

This year we are electing [troth-elections:offices] and have many qualified candidates running for the positions.  The ballot contains a short candidate statement so you may get to know the candidate followed by a place to cast your vote.  If you change your mind after you cast your ballot, you may go back and re-vote. Only your last vote will count.  Voting is open until [troth-elections:voting-closed].

Wassail,
[troth-elections:eo-name]
[troth-elections:eo-email]
EOF;
    return $message;
  }

}
