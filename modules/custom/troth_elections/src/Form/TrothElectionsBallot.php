<?php

namespace Drupal\troth_elections\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\user\Entity\User;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\troth_elections\Entity\TrothElectionsNominationType;
use Drupal\troth_elections\Entity\TrothElectionsNominationBios;
use Drupal\troth_elections\Entity\TrothElectionsNominationVoter;
use Drupal\troth_elections\Entity\TrothElectionsPropositionVote;
use Drupal\troth_elections\Entity\TrothElectionsNominationVote;

/**
 * Edit Troth User Admin form.
 */
class TrothElectionsBallot extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'troth_elections_ballot_form';
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

    // Record the current IP address.
    $storage['ip'] = \Drupal::request()->getClientIp();

    switch ($page) {
      case 1:
        $form['header'] = [
          '#type' => 'item',
          '#markup' => $this->t('Please search for the person <b><u>VOTING</u></b>.'),
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
          '#markup' => $this->t("Please choose the person who is <b><u>VOTING</u></b> from the lsit below.  If you don't see them in the list, please refresh the page and search again."),
        ];
        $form['voter'] = [
          '#type' => 'radios',
          '#options' => $results,
          '#required' => TRUE,
        ];
        $form['actions']['submit'] = [
          '#type' => 'submit',
          '#value' => $this->t('Choose Voter'),
        ];
        break;

      case 3:
        // Set voter in storage.
        if (isset($storage['voter'])) {
          $voter = $storage['voter'];
        }
        else {
          $voter = \Drupal::currentUser()->id();
          $storage['voter'] = $voter;
        }
        $account = User::load($voter);
        $storage['voterHash'] = sha1($voter);

        // Lets load the voter entity if it exists.
        $query = \Drupal::entityQuery('troth_elections_nomination_voter')
          ->condition('bundle', 'voter', '=')
          ->condition('uid', $voter, 'IN');
        $entids = $query->execute();
        if (count($entids) == 1) {
          $entid = reset($entids);
          $voterEntity = TrothElectionsNominationVoter::load($entid);
          $storage['voterEntity'] = $voterEntity;
        }

        // Lets load the voter vote entity if it exists.
        $query = \Drupal::entityQuery('troth_elections_nomination_vote')
          ->condition('uidhash', $storage['voterHash'], '=');
        $entids = $query->execute();
        if (count($entids) > 0) {
          $nomVotes = TrothElectionsNominationVote::loadMultiple($entids);
          foreach ($nomVotes as $nomVote) {
            $id = $nomVote->id();
            $office_id = $nomVote->getOfficeId();
            $candidate_id = $nomVote->getCandidate();
            $storage['nomVotes'][$office_id][$candidate_id] = $nomVote;
          }
        }

        // Lets load the voter proposition entity if it exists.
        $query = \Drupal::entityQuery('troth_elections_proposition_vote')
          ->condition('uidhash', $storage['voterHash'], '=');
        $entids = $query->execute();
        if (count($entids) > 0) {
          $propoVotes = TrothElectionsPropositionVote::loadMultiple($entids);
          foreach ($propoVotes as $propoVote) {
            $propid = $propoVote->bundle();
            $storage['propoVotes'][$propid] = $propoVote;
          }
        }

        $form['header'] = [
          '#type' => 'item',
          '#markup' => $this->t("Welcome <b>@name</b>!<br \>Thank you for participating in this year's election.  Below you will find the positions up for election, a description of what the position does, and a list of candidates, with a short statement from them, that running for that position.  Following this, you will be able to make your vote.  Please take your time to inform yourself prior to voting.  You may come back here at any time during the election and change your vote.  Only your last vote will count.", ['@name' => $account->field_profile_first_name->value]),
        ];
        // Get all open offices.
        $officestorage = \Drupal::entityTypeManager()
          ->getStorage('troth_officer_type');
        $results = \Drupal::entityQuery('troth_officer_type')
          ->condition('office_number_open', 0, '>')
          ->condition('office_open', 1, '=')
          ->execute();
        $entities = $officestorage->loadMultiple($results);
        $offices = [];

        foreach ($entities as $office_id => $office) {
          $offices[$office_id]['office'] = $office;
          $results = \Drupal::entityQuery('troth_elections_nomination_type')
            ->condition('office_id', $office_id, '=')
            ->condition('accepted', 1, '=')
            ->execute();
          foreach ($results as $nomtype) {
            $nomination = TrothElectionsNominationType::load($nomtype);
            $candidate = User::load($nomination->getNominee());
            $firstName = $candidate->field_profile_first_name->value;
            $lastName = $candidate->field_profile_last_name->value;
            $prefName = $candidate->field_profile_troth_name->value;
            $joinDate = new DrupalDateTime($candidate->field_profile_member_start_date->value);
            $candidate_id = $lastName . $firstName . $candidate->id();
            $offices[$office_id][$candidate_id]['firstName'] = $firstName;
            $offices[$office_id][$candidate_id]['lastName'] = $lastName;
            if ($prefName != '') {
              $prefName = " ($prefName)";
            }
            $offices[$office_id][$candidate_id]['prefName'] = $prefName;
            $name = "$lastName, $firstName $prefName";
            $offices[$office_id][$candidate_id]['name'] = rtrim($name);
            $offices[$office_id][$candidate_id]['joinDate'] = $joinDate;
            $offices[$office_id][$candidate_id]['uid'] = $candidate->id();
            $offices[$office_id][$candidate_id]['bio'] = $this->t("<p>No Candidate Statement was Provided</p>");
            $bioresults = \Drupal::entityQuery('troth_elections_nomination_bios')
              ->condition('bundle', $nomtype, '=')
              ->condition('uid', $candidate->id(), '=')
              ->execute();
            foreach ($bioresults as $bioId) {
              $bioEntity = TrothElectionsNominationBios::load($bioId);
              if ($bioEntity != NULL) {
                $offices[$office_id][$candidate_id]['bio'] = $bioEntity->getBio()->value;
              }
            }
          }
        }
        // Add offices to storage.
        $storage['offices'] = $offices;

        // Check if there are offices open.
        // If not, state that, otherwise process offices.
        if (count($offices) == 0) {
          $form['no_offices'] = [
            '#type' => 'item',
            '#markup' => $this->t('There are no offices up for election at this time.'),
          ];
        }
        else {
          ksort($offices);
          $order = ['steer', 'rede'];
          foreach ($order as $office_id) {
            if (isset($offices[$office_id])) {
              $office = $offices[$office_id]['office'];
              $candidates = $offices[$office_id];
              unset($candidates['office']);
              unset($offices[$office_id]);
              ksort($candidates);

              $output = '';
              $output .= $this->t('<H3>@name</H3>', ['@name' => $office->getName()]);
              $output .= $this->t('<p><b>Number Open: @open, Term Length: @term year(s)</b></p>', [
                '@open' => $office->getNumOpen(),
                '@term' => $office->getTerm(),
              ]);
              $output .= $office->getDescription()['value'];
              $output .= $this->t('<H3>Candidates:</H3>');
              $candidateOptions = [];
              foreach ($candidates as $candidate) {
                $output .= $this->t('<p>Name: <b>@name</b><br \>Member Since: @year</p>', [
                  '@name' => $candidate['name'],
                  '@year' => $candidate['joinDate']->format('Y'),
                ]);
                $output .= $candidate['bio'];
                $candidateOptions[$candidate['uid']] = $candidate['name'];
              }
              $candidateOptions[-1] = $this->t('Write In Candidate');
              $candidateOptions[-2] = $this->t('Abstain/No Vote');
              $form[$office_id . '_head'] = [
                '#type' => 'item',
                '#markup' => $output,
                '#prefix' => '<hr />',
              ];
              $form[$office_id] = [
                '#type' => 'checkboxes',
                '#title' => $this->t('Vote for @office (Select @num)', [
                  '@office' => $office->getName(),
                  '@num' => $office->getNumOpen(),
                ]),
                '#options' => $candidateOptions,
              ];
              $form[$office_id . '_write_in'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Write In Candidate'),
                '#description' => $this->t('If you want to vote for multiple candidates, please enter them all here and seperate with a comma'),
                '#size' => 100,
                '#states' => [
                  'invisible' => [
                    ':input[name="' . $office_id . '[-1]"]' => ['checked' => FALSE],
                  ],
                  'visible' => [
                    ':input[name="' . $office_id . '[-1]"]' => ['checked' => TRUE],
                  ],
                ],
              ];
              // Now we add in any votes.
              if (isset($storage['nomVotes'][$office_id])) {
                $writein = [];
                foreach ($storage['nomVotes'][$office_id] as $candidate_id => $voteEntity) {
                  $vote = $voteEntity->getVote();
                  if ($vote > 0) {
                    $fixed = [];
                    if (is_numeric($candidate_id)) {
                      $form[$office_id]['#default_value'][] = $candidate_id;
                      if (!isset($candidateOptions[$candidate_id])) {
                        $account = User::load($candidate_id);
                        $firstName = $account->field_profile_first_name->value;
                        $lastName = $account->field_profile_last_name->value;
                        $prefName = $account->field_profile_troth_name->value;
                        if ($prefName != '') {
                          $prefName = " ($prefName)";
                        }
                        $candidateOptions[$candidate_id] = rtrim("$lastName, $firstName $prefName");
                        $soffice = $storage['offices'];
                        $soffice[$office_id][$candidate_id]['name'] = $candidateOptions[$candidate_id];
                        $soffice[$office_id][$candidate_id]['uid'] = $candidate_id;
                        $fixed[] = $candidateOptions[$candidate_id];
                        $storage['offices'] = $soffice;
                      }
                    }
                    else {
                      $writein[] = $candidate_id;
                    }
                    if (count($fixed) > 0) {
                      $form[$office_id]['#options'] = $candidateOptions;
                      $form[$office_id]['#description'] = $this->t('Your write in vote <b>@name</b> has been fixed to a Member ID by the elections officer.  You can find them at the end of the list instead of as a write in.', [
                        '@name' => implode('; ', $fixed),
                      ]);
                    }
                  }
                }
                if (count($writein) > 0) {
                  $form[$office_id]['#default_value'][] = -1;
                  $form[$office_id . '_write_in']['#default_value'] = implode(', ', $writein);
                }
              }
            }
          }

          // Now we do the same thing with the remaining offices.
          foreach ($offices as $office_id => $office) {
            $office = $offices[$office_id]['office'];
            $candidates = $offices[$office_id];
            unset($candidates['office']);
            unset($offices[$office_id]);
            ksort($candidates);

            $output = '';
            $output .= $this->t('<H3>@name</H3>', ['@name' => $office->getName()]);
            $output .= $this->t('<p><b>Number Open: @open, Term Length: @term year(s)</b></p>', [
              '@open' => $office->getNumOpen(),
              '@term' => $office->getTerm(),
            ]);
            $output .= $office->getDescription()['value'];
            $output .= $this->t('<H3>Candidates:</H3>');
            $candidateOptions = [];
            foreach ($candidates as $candidate) {
              $output .= $this->t('<p>Name: <b>@name</b><br \>Member Since: @year</p>', [
                '@name' => $candidate['name'],
                '@year' => $candidate['joinDate']->format('Y'),
              ]);
              $output .= $candidate['bio'];
              $candidateOptions[$candidate['uid']] = $candidate['name'];
            }
            $candidateOptions[-1] = $this->t('Write In Candidate');
            $candidateOptions[-2] = $this->t('Abstain/No Vote');

            $form[$office_id . '_head'] = [
              '#type' => 'item',
              '#markup' => $output,
              '#prefix' => '<hr />',
            ];
            $form[$office_id] = [
              '#type' => 'checkboxes',
              '#title' => $this->t('Vote for @office (Select @num)', [
                '@office' => $office->getName(),
                '@num' => $office->getNumOpen(),
              ]),
              '#options' => $candidateOptions,
            ];
            $form[$office_id . '_write_in'] = [
              '#type' => 'textfield',
              '#title' => $this->t('Write In Candidate'),
              '#description' => $this->t('If you want to vote for multiple candidates, please enter them all here and seperate with a comma'),
              '#size' => 100,
              '#states' => [
                'invisible' => [
                  ':input[name="' . $office_id . '[-1]"]' => ['checked' => FALSE],
                ],
                'visible' => [
                  ':input[name="' . $office_id . '[-1]"]' => ['checked' => TRUE],
                ],
              ],
            ];
            // Now we add in any votes.
            if (isset($storage['nomVotes'][$office_id])) {
              $writein = [];
              foreach ($storage['nomVotes'][$office_id] as $candidate_id => $voteEntity) {
                $vote = $voteEntity->getVote();
                if ($vote > 0) {
                  $fixed = [];
                  if (is_numeric($candidate_id)) {
                    $form[$office_id]['#default_value'][] = $candidate_id;
                    if (!isset($candidateOptions[$candidate_id])) {
                      $account = User::load($candidate_id);
                      $firstName = $account->field_profile_first_name->value;
                      $lastName = $account->field_profile_last_name->value;
                      $prefName = $account->field_profile_troth_name->value;
                      if ($prefName != '') {
                        $prefName = " ($prefName)";
                      }
                      $candidateOptions[$candidate_id] = rtrim("$lastName, $firstName $prefName");
                      $soffice = $storage['offices'];
                      $soffice[$office_id][$candidate_id]['name'] = $candidateOptions[$candidate_id];
                      $soffice[$office_id][$candidate_id]['uid'] = $candidate_id;
                      $fixed[] = $candidateOptions[$candidate_id];
                      $storage['offices'] = $soffice;
                    }
                  }
                  else {
                    $writein[] = $candidate_id;
                  }
                  if (count($fixed) > 0) {
                    $form[$office_id]['#options'] = $candidateOptions;
                    $form[$office_id]['#description'] = $this->t('Your write in vote <b>@name</b> has been fixed to a Member ID by the elections officer.  You can find them at the end of the list instead of as a write in.', [
                      '@name' => implode('; ', $fixed),
                    ]);
                  }
                }
              }
              if (count($writein) > 0) {
                $form[$office_id]['#default_value'][] = -1;
                $form[$office_id . '_write_in']['#default_value'] = implode(', ', $writein);
              }
            }
          }
        }

        // Hanlde Propositions.
        $propstorage = \Drupal::entityTypeManager()
          ->getStorage('troth_elections_proposition_type');
        $results = \Drupal::entityQuery('troth_elections_proposition_type')
          ->execute();
        $entities = $propstorage->loadMultiple($results);
        $storage['propositions'] = $entities;
        ksort($entities);
        if (count($entities) > 0) {
          $form['propositions'] = [
            '#type' => 'item',
            '#title' => $this->t('Propositions'),
            '#markup' => $this->t('The following propositions are up for consideration.'),
            '#prefix' => '<hr />',
          ];
          foreach ($entities as $entity_id => $entity) {
            $title = $entity->getName();
            $options = explode(PHP_EOL, $entity->getOptions());
            $text = $entity->getText();
            $form['prop_' . $entity_id . 'text'] = [
              '#type' => 'item',
              '#title' => $this->t('@title', ['@title' => $entity->getName()]),
              '#markup' => $entity->getText()['value'],
              '#prefix' => '<hr />',
            ];
            $options = explode(PHP_EOL, $entity->getOptions());
            $form['prop_' . $entity_id] = [
              '#title' => $this->t('Vote on Proposition'),
              '#type' => 'select',
              '#options' => $options,
              '#empty_value' => "",
              '#empty_option' => 'Please Select',
              '#required' => TRUE,
            ];
            if (isset($storage['propoVotes'][$entity_id])) {
              $oldvote = $storage['propoVotes'][$entity_id]->getVote();
              $oldvote = array_search($oldvote, $options);
              $form['prop_' . $entity_id]['#default_value'] = $oldvote;
            }
          }
        }

        // Handle Proxies.
        if (\Drupal::config('troth_elections.adminsettings')->get('troth_elections_proxy')) {
          $form['proxy'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Proxy for Annual Meeting'),
            '#size' => 100,
            '#description' => $this->t('If you are not able to attend the annual meeting, you may designate another member to be your proxy and vote on any votes for you.'),
            '#prefix' => '<hr />',
          ];
        }

        // Get signature, password, and captcha to confirm
        // they meant to vote.
        $form['signature'] = [
          '#type' => 'textfield',
          '#title' => $this->t('Signature'),
          '#size' => 100,
          '#description' => $this->t('Please digitally sign your name.'),
          '#prefix' => '<hr />',
          '#required' => TRUE,
        ];
        if (\Drupal::currentUser()->id() == $storage['voter']) {
          $form['password'] = [
            '#type' => 'password',
            '#title' => $this->t('Password'),
            '#description' => $this->t('Please confirm your identity by entering your password.'),
            '#size' => 20,
            '#required' => TRUE,
          ];
        }

        $form['captcha'] = [
          '#type' => 'captcha',
          '#captcha_type' => 'image_captcha/Image',
        ];
        $form['actions']['submit'] = [
          '#type' => 'submit',
          '#value' => $this->t('Submit Your Votes'),
        ];

        break;

      case 4:
        $account = User::load($storage['voter']);
        $votingEndDate = new DrupalDateTime(\Drupal::config('troth_elections.adminsettings')->get('troth_elections_voting_end_date'), 'America/Los_Angeles');

        $message = $this->t('@name, thank you for taking the time to vote.  Your votes have been recorded and you should receive a copy by email soon.  You may return to the ballot and make any changes you want up until the end of voting on @date, midnight, Pacific time.', [
          '@name' => $account->field_profile_first_name->value,
          '@date' => $votingEndDate->format('j F, Y'),
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

    if ($page == 1) {
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
    elseif ($page == 3) {
      $offices = $storage['offices'];
      foreach ($offices as $office_id => $office) {
        $count = 0;
        $abstain = 0;
        // Process votes to get a count and abstains.
        $votes = $form_state->getValue($office_id);
        foreach ($votes as $vote) {
          if ($vote > 0) {
            $count++;
          }
          elseif ($vote == -2) {
            $abstain++;
          }
        }
        // Candidates are comma separated, so explode on comma.
        if ($votes[-1] != 0) {
          $writein = explode(',', $form_state->getValue($office_id . '_write_in'));
          foreach ($writein as $vote) {
            if (trim($vote) != '') {
              $count++;
            }
          }
        }

        // Error checking:
        // Votes must == # open
        // If # open > 1, abstain must be marked if less than # open.
        $numOpen = $office['office']->getNumOpen();
        // First check for overvoting.
        if ($count + $abstain > $numOpen) {
          $form_state->setErrorByName($office_id, $this->t('You have over voted.  You are only allowed @num Votes', ['@num' => $numOpen]));
          if (isset($writein)) {
            $form_state->setErrorByName($office_id . '_write_in', $this->t('You have over voted.  You are only allowed @num Votes', ['@num' => $numOpen]));
          }
        }
        elseif ($count + $abstain < $numOpen) {
          if ($abstain == 0) {
            // They have undervoted and not abstained the rest.
            $form_state->setErrorByName($office_id, $this->t('You have voted fewer than @num votes, and have not abstained from those votes.', ['@num' => $numOpen]));
            if (isset($writein)) {
              $form_state->setErrorByName($office_id . '_write_in', $this->t('You have voted fewer than @num votes, and have not abstained from those votes.', ['@num' => $numOpen]));
            }
          }
        }

        // Validate Password.
        if (\Drupal::currentUser()->id() == $storage['voter']) {
          $account = User::load($storage['voter']);
          $password_hasher = \Drupal::service('password');
          $pass = $password_hasher->check($form_state->getValue('password'), $account->getPassword());
          if ($pass == FALSE) {
            $form_state->setErrorByName('password', $this->t('The password entered is not your current password.'));
          }
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
    unset($storage['uids']);

    if ($page == 1) {
      // Get fields to search.
      $fields = $storage['fields'];
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
      $voter = explode('::', $form_state->getValue('voter'));
      $storage['voter'] = $voter[1];
    }

    if ($page == 3) {
      $ip = $storage['ip'];
      $voter = $storage['voter'];
      $voterHash = $storage['voterHash'];
      $offices = $storage['offices'];
      $proxy = $form_state->getValue('proxy');
      if ($proxy == NULL) {
        $proxy = 'None';
      }
      $signature = $form_state->getValue('signature');

      // Start the message and the logfile.
      $account = User::load($voter);
      $firstName = $account->field_profile_first_name->value;
      $lastName = $account->field_profile_last_name->value;
      $email = $account->getEmail();
      $now = new DrupalDateTime();
      $now->setTimezone(timezone_open('America/Los_Angeles'));
      $votingStartDate = new DrupalDateTime(\Drupal::config('troth_elections.adminsettings')->get('troth_elections_voting_open_date'), 'America/Los_Angeles');
      $message = $this->t("A ballot for the @year Troth Elections was just submitted (@date) from @first @last, membership ID @uid.\n\nFor your records, the votes submitted were:\n\n", [
        '@year' => $votingStartDate->format('Y'),
        '@date' => $now->format('j F, Y H:i:s T'),
        '@first' => $firstName,
        '@last' => $lastName,
        '@uid' => $voter,
      ]);
      $log = $votingStartDate->format('Y') . "\t$voter\t$firstName $lastName\t";
      $logVotes = [];
      $logProps = [];

      // Build/save voter entity.
      if (isset($storage['voterEntity'])) {
        $voterEntity = $storage['voterEntity'];
        $voterEntity->setSignature($signature);
        $voterEntity->setProxy($proxy);
        $voterEntity->setIp($ip);
      }
      else {
        $voterEntity = TrothElectionsNominationVoter::create([
          'bundle' => 'voter',
          'uid' => $voter,
          'signature' => $signature,
          'proxy' => $proxy,
          'ip' => $ip,
        ]);
      }
      $voterEntity->save();

      // Build/Save Vote entity.
      foreach ($offices as $office_id => $office) {
        $message .= $office['office']->getName() . "\n";
        $candidates = [];
        foreach ($office as $id => $data) {
          if ($id != 'office') {
            $candidates[$data['uid']] = $data['name'];
          }
        }
        $votes = $form_state->getValue($office_id);
        foreach ($votes as $candidate => $vote) {
          if ($vote != 0) {
            $vote = 1;
            if ($candidate > 0) {
              $message .= "\t" . $candidates[$candidate] . "\n";
            }
            elseif ($candidate == -2) {
              $message .= "\tAbstain/No Vote\n";
            }
          }
          $logVotes[$office_id][$candidate] = $vote;
        }
        $writeins = explode(',', $form_state->getValue($office_id . '_write_in'));
        // $votes[-1] == write in.
        // $votes[-2] == abstain.
        $writein = [];
        foreach ($writeins as $candidate) {
          $candidate = trim($candidate);
          if ($candidate != '') {
            if ($votes[-1] != 0) {
              // Write in vote was registered.
              $writein[$candidate] = 1;
            }
            else {
              $writein[$candidate] = 0;
            }
          }
        }
        if ($logVotes[$office_id][-1] == 1) {
          foreach ($writein as $candidate => $vote) {
            if ($vote == 1) {
              $message .= "\t$candidate\n";
              $logVotes[$office_id][$candidate] = $vote;
            }
          }
        }

        // We don't need the write in vote id any more.
        unset($votes[-1]);
        unset($logVotes[$office_id][-1]);

        if (isset($storage['nomVotes'][$office_id])) {
          // We update votes.
          foreach ($storage['nomVotes'][$office_id] as $candidate_id => $voteEntity) {
            if (is_numeric($candidate_id)) {
              // We have a choice from the standard options.
              $vote = $votes[$candidate_id];
              unset($votes[$candidate_id]);
              if ($vote != 0) {
                $vote = 1;
              }
              $voteEntity->setNewRevision();
              $voteEntity->setVote($vote);
              $voteEntity->save();
            }
            else {
              // Candidate is a write in.
              if (isset($writein[$candidate_id])) {
                $voteEntity->setNewRevision();
                $voteEntity->setVote($writein[$candidate_id]);
                $voteEntity->save();
                unset($writein[$candidate_id]);
              }
            }
          }
          // Now we check if there are anything left and we create the entities.
          $new = $votes + $writein;
          if (count($new) > 0) {
            foreach ($new as $candidate_id => $vote) {
              if ($vote != 0) {
                $vote = 1;
              }
              $voteEntity = TrothElectionsNominationVote::create([
                'bundle' => $office_id,
                'uidhash' => $voterHash,
                'office_id' => $office_id,
                'candidate' => $candidate_id,
                'vote' => $vote,
              ]);
              $voteEntity->save();
            }
          }
        }
        else {
          // We don't have any votes on record.
          // Create them.
          $votes += $writein;
          foreach ($votes as $candidate_id => $vote) {
            if ($vote != 0) {
              $vote = 1;
            }
            $voteEntity = TrothElectionsNominationVote::create([
              'bundle' => $office_id,
              'uidhash' => $voterHash,
              'office_id' => $office_id,
              'candidate' => $candidate_id,
              'vote' => $vote,
            ]);
            $voteEntity->save();
          }
        }
      }

      // Build/save proposition votes entity.
      if (isset($storage['propoVotes'])) {
        $propoVotes = $storage['propoVotes'];
      }
      $propositions = $storage['propositions'];
      foreach ($propositions as $propId => $prop) {
        $vote = $form_state->getValue('prop_' . $propId);
        $options = explode(PHP_EOL, $prop->getOptions());
        $message .= "\n\nProposition Votes:\n\n";
        $message .= "\t" . $prop->getName() . ": " . $options[$vote] . "\n\n";
        $logProps[$propId] = $options[$vote];

        if (isset($propoVotes[$propId])) {
          $propEntity = $propoVotes[$propId];
          $propEntity->setNewRevision();
          $propEntity->setVote($options[$vote]);
        }
        else {
          $propEntity = TrothElectionsPropositionVote::create([
            'bundle' => $prop->id(),
            'uidhash' => $voterHash,
            'proposition_id' => $propId,
            'vote' => $options[$vote],
          ]);
        }
        $propEntity->save();
      }

      $log .= serialize($logVotes);
      if (count($logProps) > 0) {
        $log .= "\t" . serialize($logProps);
      }
      if (\Drupal::config('troth_elections.adminsettings')->get('troth_elections_proxy')) {
        $message .= "Proxy for votes taken at the Annual Meeting, if any: $proxy\n\n";
        $log .= "\t$proxy";
      }
      $message .= "Digital signature hash for these votes: " . sha1($signature);
      $log .= "\t$signature\t" . $now->format('j F, Y H:i:s T') . "\t$ip\n";

      // Write log file.
      $path = \Drupal::config('troth_elections.adminsettings')->get('troth_elections_path');
      $logFile = "$path/" . $votingStartDate->format('Y') . "_votes.txt";
      $fh = fopen($logFile, 'a') or die("can't open $logFile");
      fwrite($fh, $log);
      fclose($fh);

      // Send the Emails.
      $subject = "Troth " . $now->format('j F, Y H:i:s T') . " Election Ballot Submission ($voter - $firstName $lastName)";
      $eomail = \Drupal::config('troth_elections.adminsettings')->get('troth_elections_eo_email');
      $to = [$eomail, $email];
      $to = implode(',', $to);
      $from = $eomail;
      $params['sub'] = $subject;
      $params['message'] = $message;
      $langcode = $account->getPreferredLangcode();

      $mailManager = \Drupal::service('plugin.manager.mail');
      $result = $mailManager->mail('troth_user', 'troth_mail', $to, $langcode, $params, NULL, TRUE);
    }

    $storage['page']++;
    $form_state->setStorage($storage);
  }

}
