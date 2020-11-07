<?php

namespace Drupal\troth_elections\Form;

use Drupal\Core\Url;
use Drupal\path_alias\Entity\PathAlias;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\node\Entity\Node;

/**
 * Defines a confirmation form to confirm deletion of something by id.
 */
class TrothElectionsCreatePageForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return "troth_elections_create_pages";
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $page = NULL) {
    $storage = $form_state->getStorage();
    if (!isset($storage['page'])) {
      $storage['page'] = 1;
    }
    $page = $storage['page'];

    switch ($page) {
      case 1:
        $vote_start = new DrupalDateTime(\Drupal::config('troth_elections.adminsettings')->get('troth_elections_voting_open_date'), 'America/Los_Angeles');
        $today = new DrupalDateTime();
        $minYear = $today->format('Y') - 2;
        $maxYear = $today->format('Y') + 2;
        $form['header'] = [
          '#type' => 'item',
          '#markup' => $this->t('Please choose which pages you wish to make permanent/static pages.'),
        ];
        $form['year'] = [
          '#title' => $this->t('Year'),
          '#description' => $this->t('Which year is the election for?  Default is year that appears in settings.'),
          '#type' => 'number',
          '#size' => 4,
          '#min' => $minYear,
          '#max' => $maxYear,
          '#step' => 1,
          '#default_value' => $vote_start->format('Y'),
          '#required' => TRUE,
        ];
        $form['nominations'] = [
          '#title' => $this->t('Nominations'),
          '#description' => $this->t('Create permanent/static Nominations page.'),
          '#type' => 'checkbox',
        ];
        $form['bio'] = [
          '#title' => $this->t('Candidate Statements'),
          '#description' => $this->t('Create permanent/static Candidate Statements page.'),
          '#type' => 'checkbox',
        ];
        $form['voters'] = [
          '#title' => $this->t('Voters'),
          '#description' => $this->t('Create permanent/static Voters page.'),
          '#type' => 'checkbox',
        ];
        $form['results'] = [
          '#title' => $this->t('Results'),
          '#description' => $this->t('Create permanent/static Results page.'),
          '#type' => 'checkbox',
        ];
        $form['emptytables'] = [
          '#title' => $this->t('Empty Tables'),
          '#description' => $this->t('Empty elections tables.  This will create backups and create all pages that have not been created.'),
          '#type' => 'checkbox',
        ];
        $form['actions'] = [
          '#type' => 'actions',
        ];

        $form['actions']['submit'] = [
          '#type' => 'submit',
          '#submit' => [[$this, 'submitForm']],
          '#value' => $this->t('Continue'),
        ];

        break;

      case 2:
        $year = $storage['year'];
        $message = $this->t('Are you sure you want to do the following for the @year election year?<ul>', [
          '@year' => $year,
        ]);
        if ($storage['nominations']) {
          $message .= $this->t('<li>Create Nominations Page</li>');
        }
        if ($storage['bio']) {
          $message .= $this->t('<li>Create Candidate Statements Page</li>');
        }
        if ($storage['voters']) {
          $message .= $this->t('<li>Create Voters Page</li>');
        }
        if ($storage['results']) {
          $message .= $this->t('<li>Create Results Page</li>');
        }
        if ($storage['emptytables']) {
          $message .= $this->t('<li>Archive all data and create all pages.</li>');
        }
        $message .= "</ul>";
        $form['message'] = [
          '#type' => 'item',
          '#markup' => $message,
        ];
        $form['actions'] = [
          '#type' => 'actions',
        ];

        $form['actions']['submit'] = [
          '#type' => 'submit',
          '#submit' => [[$this, 'submitForm']],
          '#value' => $this->t('Confirm'),
        ];

        $form['actions']['cancel'] = [
          '#type' => 'submit',
          '#submit' => [[$this, 'previousForm']],
          '#value' => 'Cancel',
        // No validation for back button.
          '#limit_validation_errors' => [],
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
    // Do the validation.
    $storage = $form_state->getStorage();
    if (!isset($storage['page'])) {
      $storage['page'] = 1;
    }
    $page = $storage['page'];
    if ($page == 1) {
      $count = 0;
      if ($form_state->getValue('nominations')) {
        $count++;
      }
      if ($form_state->getValue('bio')) {
        $count++;
      }
      if ($form_state->getValue('voters')) {
        $count++;
      }
      if ($form_state->getValue('results')) {
        $count++;
      }
      if ($form_state->getValue('emptytables')) {
        $count++;
      }
      if ($count == 0) {
        $form_state->setErrorByName('nominations', $this->t('You have not chosen an action.'));
        $form_state->setErrorByName('bio', $this->t('You have not chosen an action.'));
        $form_state->setErrorByName('voters', $this->t('You have not chosen an action.'));
        $form_state->setErrorByName('results', $this->t('You have not chosen an action.'));
        $form_state->setErrorByName('emptytables', $this->t('You have not chosen an action.'));
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Rebuild so that submitted values stay.
    $form_state->setRebuild();

    $storage = $form_state->getStorage();
    if (!isset($storage['page'])) {
      $storage['page'] = 1;
    }
    $page = $storage['page'];

    switch ($page) {
      case 1:
        $storage['year'] = $form_state->getValue('year');
        $storage['nominations'] = $form_state->getValue('nominations');
        $storage['bio'] = $form_state->getValue('bio');
        $storage['voters'] = $form_state->getValue('voters');
        $storage['results'] = $form_state->getValue('results');
        $storage['emptytables'] = $form_state->getValue('emptytables');
        break;

      case 2:
        $year = $storage['year'];
        if ($storage['nominations']) {
          $this->createNominations($year);
        }
        if ($storage['bio']) {
          $this->createBio($year);
        }
        if ($storage['voters']) {
          $this->createVoter($year);
        }
        if ($storage['results']) {
          $this->createResults($year);
        }
        if ($storage['emptytables']) {
          $this->createNominations($year);
          $this->createBio($year);
          $this->createVoter($year);
          $this->createResults($year);
          $this->createElections($year);

          $batch = [
            'title' => t('Archiving Data'),
            'init_message' => t('Data is being archived and deleted from the database.'),
            'operations' => [
              [
                'empty_entity',
                [$year, 'troth_elections_emails'],
              ],
              [
                'empty_entity',
                [$year, 'troth_elections_proposition_vote'],
              ],
              [
                'empty_entity',
                [$year, 'troth_elections_nomination_vote'],
              ],
              [
                'empty_entity',
                [$year, 'troth_elections_nomination_voter'],
              ],
              [
                'empty_entity',
                [$year, 'troth_elections_nomination_bios'],
              ],
              [
                'empty_entity',
                [$year, 'troth_elections_nomination'],
              ],
              [
                'empty_entity_type',
                [$year, 'troth_elections_proposition_type'],
              ],
              [
                'empty_entity_type',
                [$year, 'troth_elections_nomination_type'],
              ],
            ],
            'file' => drupal_get_path('module', 'troth_elections') . '/troth_elections.batch.inc',
            'finished' => 'batch_finish_callback',
          ];
          batch_set($batch);
          $path = \Drupal::config('troth_elections.adminsettings')->get('troth_elections_path');
          chdir($path);
          $files = glob("$year*.csv");
          $files[] = $year . "_votes.txt";
          $zip = new \ZipArchive();
          if ($zip->open($year . "_archives.zip", constant("ZipArchive::CREATE")) === TRUE) {
            foreach ($files as $file) {
              // Add file to the zip file.
              $zip->addFile($file);
            }
            // All files are added, so close the zip file.
            $zip->close();
          }
          foreach ($files as $file) {
            // Delete the files.
            unlink($file);
          }

        }
        $storage['page'] = 1;
        break;
    }

    $storage['page']++;
    $form_state->setStorage($storage);

  }

  /**
   * {@inheritdoc}
   */
  public function previousForm(array &$form, FormStateInterface $form_state) {
    // Got back to the beginning.
    $storage['page'] = 1;
    $form_state->setStorage($storage);
  }

  /**
   * Get NID from alias;.
   */
  private function getNid($alias = NULL) {
    if ($alias == NULL) {
      return NULL;
    }
    $path = \Drupal::service('path.alias_manager')->getPathByAlias($alias);
    if (preg_match('/node\/(\d+)/', $path, $matches)) {
      return $matches[1];
    }
    return NULL;
  }

  /**
   * Create the nomination page.
   */
  private function createNominations($year = NULL) {
    if ($year == NULL) {
      $vote_start = new DrupalDateTime(\Drupal::config('troth_elections.adminsettings')->get('troth_elections_voting_open_date'), 'America/Los_Angeles');
      $year = $vote_start->format('Y');
    }
    $parent = \Drupal::config('troth_elections.adminsettings')->get('troth_elections_url');
    $alias = $parent . "/" . $year . '/nominations';
    $nid = $this->getNid($alias);
    if (is_numeric($nid)) {
      $node = Node::load($nid);
    }
    else {
      $node = Node::create([
        'type' => 'members_page',
        'uid' => 1,
        'promote' => 0,
        'sticky' => 0,
        'title' => $this->t('@year Nominations', ['@year' => $year]),
      ]);
    }
    $body = troth_elections_nomination_display();
    $body = \Drupal::service('renderer')->render($body)->__toString();
    $node->body->value = $body;
    $node->body->format = 'full_html';
    $node->setPublished(TRUE);
    $node->save();
    $nid = $node->id();
    $path_alias = \Drupal::service('path_alias.manager')->getAliasByPath('/node/' . $nid);
    if ($path_alias == '/node/' . $nid) {
      $path_alias = PathAlias::create([
        'path' => '/node/' . $nid,
        'alias' => $alias,
      ]);
      $path_alias->save();
    }
    $url = Url::fromUserInput($alias);
    drupal_set_message($this->t('@year Nominations page created, :url', [
      '@year' => $year,
      ':url' => $url->toString(),
    ]));
  }

  /**
   * Create the bio page.
   */
  private function createBio($year = NULL) {
    if ($year == NULL) {
      $vote_start = new DrupalDateTime(\Drupal::config('troth_elections.adminsettings')->get('troth_elections_voting_open_date'), 'America/Los_Angeles');
      $year = $vote_start->format('Y');
    }
    $parent = \Drupal::config('troth_elections.adminsettings')->get('troth_elections_url');
    $alias = $parent . "/" . $year . '/candidates';
    $nid = $this->getNid($alias);
    if (is_numeric($nid)) {
      $node = Node::load($nid);
    }
    else {
      $node = Node::create([
        'type' => 'members_page',
        'uid' => 1,
        'promote' => 0,
        'sticky' => 0,
        'title' => $this->t('@year Candidate Statements', ['@year' => $year]),
      ]);
    }
    $body = troth_candidate_statements();
    $body = \Drupal::service('renderer')->render($body)->__toString();
    $node->body->value = $body;
    $node->body->format = 'full_html';
    $node->setPublished(TRUE);
    $node->save();
    $nid = $node->id();
    $path_alias = \Drupal::service('path_alias.manager')->getAliasByPath('/node/' . $nid);
    if ($path_alias == '/node/' . $nid) {
      $path_alias = PathAlias::create([
        'path' => '/node/' . $nid,
        'alias' => $alias,
      ]);
      $path_alias->save();
    }
    $url = Url::fromUserInput($alias);
    drupal_set_message($this->t('@year Candidate Statements page created, :url', [
      '@year' => $year,
      ':url' => $url->toString(),
    ]));
  }

  /**
   * Create the voter page.
   */
  private function createVoter($year = NULL) {
    if ($year == NULL) {
      $vote_start = new DrupalDateTime(\Drupal::config('troth_elections.adminsettings')->get('troth_elections_voting_open_date'), 'America/Los_Angeles');
      $year = $vote_start->format('Y');
    }
    $parent = \Drupal::config('troth_elections.adminsettings')->get('troth_elections_url');
    $alias = $parent . "/" . $year . '/voters';
    $nid = $this->getNid($alias);
    if (is_numeric($nid)) {
      $node = Node::load($nid);
    }
    else {
      $node = Node::create([
        'type' => 'members_page',
        'uid' => 1,
        'promote' => 0,
        'sticky' => 0,
        'title' => $this->t('@year Voters', ['@year' => $year]),
      ]);
    }
    $body = troth_elections_voters();
    $body = \Drupal::service('renderer')->render($body)->__toString();
    $node->body->value = $body;
    $node->body->format = 'full_html';
    $node->setPublished(TRUE);
    $node->save();
    $nid = $node->id();
    $path_alias = \Drupal::service('path_alias.manager')->getAliasByPath('/node/' . $nid);
    if ($path_alias == '/node/' . $nid) {
      $path_alias = PathAlias::create([
        'path' => '/node/' . $nid,
        'alias' => $alias,
      ]);
      $path_alias->save();
    }
    $url = Url::fromUserInput($alias);
    drupal_set_message($this->t('@year Voters page created, :url', [
      '@year' => $year,
      ':url' => $url->toString(),
    ]));
  }

  /**
   * Create the results page.
   */
  private function createResults($year = NULL) {
    if ($year == NULL) {
      $vote_start = new DrupalDateTime(\Drupal::config('troth_elections.adminsettings')->get('troth_elections_voting_open_date'), 'America/Los_Angeles');
      $year = $vote_start->format('Y');
    }
    $parent = \Drupal::config('troth_elections.adminsettings')->get('troth_elections_url');
    $alias = $parent . "/" . $year . '/results';
    $nid = $this->getNid($alias);
    if (is_numeric($nid)) {
      $node = Node::load($nid);
    }
    else {
      $node = Node::create([
        'type' => 'members_page',
        'uid' => 1,
        'promote' => 0,
        'sticky' => 0,
        'title' => $this->t('@year Results', ['@year' => $year]),
      ]);
    }
    $body = troth_election_db_results('Y');
    $body = \Drupal::service('renderer')->render($body)->__toString();
    $node->body->value = $body;
    $node->body->format = 'full_html';
    $node->setPublished(TRUE);
    $node->save();
    $nid = $node->id();
    $path_alias = \Drupal::service('path_alias.manager')->getAliasByPath('/node/' . $nid);
    if ($path_alias == '/node/' . $nid) {
      $path_alias = PathAlias::create([
        'path' => '/node/' . $nid,
        'alias' => $alias,
      ]);
      $path_alias->save();
    }
    $url = Url::fromUserInput($alias);
    drupal_set_message($this->t('@year Results page created, :url', [
      '@year' => $year,
      ':url' => $url->toString(),
    ]));
  }

  /**
   * Create the results page.
   */
  private function createElections($year = NULL) {
    if ($year == NULL) {
      $vote_start = new DrupalDateTime(\Drupal::config('troth_elections.adminsettings')->get('troth_elections_voting_open_date'), 'America/Los_Angeles');
      $year = $vote_start->format('Y');
    }
    $parent = \Drupal::config('troth_elections.adminsettings')->get('troth_elections_url');
    $alias = $parent . "/" . $year;
    $nid = $this->getNid($alias);
    if (is_numeric($nid)) {
      $node = Node::load($nid);
    }
    else {
      $node = Node::create([
        'type' => 'members_page',
        'uid' => 1,
        'promote' => 0,
        'sticky' => 0,
        'title' => $this->t('@year Results', ['@year' => $year]),
      ]);
    }
    $body = $this->t("<p>Below are the nominations, and pertinent links for @year's election.</p>", [
      '@year' => $year,
    ]);
    $url = Url::fromUserInput($parent . "/" . $year . "/nominations");
    $body .= $this->t('<p><a href=":url">Nominations</a></p>', [':url' => $url->toString()]);

    $url = Url::fromUserInput($parent . "/" . $year . "/candidates");
    $body .= $this->t('<p><a href=":url">Candidate Statements</a></p>', [':url' => $url->toString()]);

    $url = Url::fromUserInput($parent . "/" . $year . "/voters");
    $body .= $this->t('<p><a href=":url">Members Voting</a></p>', [':url' => $url->toString()]);

    $url = Url::fromUserInput($parent . "/" . $year . "/results");
    $body .= $this->t('<p><a href=":url">Results</a></p>', [':url' => $url->toString()]);

    $node->body->value = $body;
    $node->body->format = 'full_html';
    $node->setPublished(TRUE);
    $node->save();
    $nid = $node->id();
    $path_alias = \Drupal::service('path_alias.manager')->getAliasByPath('/node/' . $nid);
    if ($path_alias == '/node/' . $nid) {
      $path_alias = PathAlias::create([
        'path' => '/node/' . $nid,
        'alias' => $alias,
      ]);
      $path_alias->save();
    }
    $url = Url::fromUserInput($alias);
    drupal_set_message($this->t('@year main page created, :url', [
      '@year' => $year,
      ':url' => $url->toString(),
    ]));
  }

}
