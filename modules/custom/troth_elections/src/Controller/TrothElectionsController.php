<?php

namespace Drupal\troth_elections\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Url;
use Drupal\Core\Render\Markup;
use Drupal\user\Entity\User;

/**
 * Provides route controllers for elections module.
 */
class TrothElectionsController extends ControllerBase {

  /**
   * Returns the elections members main page.
   *
   * @return array
   *   A simple renderable array.
   */
  public function mainPage() {
    $parent = \Drupal::config('troth_elections.adminsettings')->get('troth_elections_url');
    $query = db_select('path_alias', 'a');
    $query->fields('a', ['alias']);
    $query->condition('a.alias', $parent . "/____" . '/index%', 'like');
    $plid = $query->execute();
    $links = [];
    while ($alias = $plid->fetchField()) {
      $title = substr($alias, strlen($parent) + 1, 4);
      $links[$title] = $alias;
    }

    // Get the current election if there is one.
    $today = new DrupalDateTime();
    $electionStart = new DrupalDateTime(\Drupal::config('troth_elections.adminsettings')->get('troth_elections_nom_start_date'), 'America/Los_Angeles');
    if ($today >= $electionStart) {
      $year = $electionStart->format('Y');
      $url = Url::fromRoute('troth_elections.year_summary_' . $year);
      if (isset($url)) {
        $links[$year] = $url->toString();
      }
    }

    // Sort the links by year descending.
    krsort($links);
    if (count($links) == 0) {
      $out = '<p>There are no past or current elections to display.</p>';
    }
    else {
      $out = '<p>Past and current elections</p>';
      $out .= "<ul>";
      foreach ($links as $year => $link) {
        $out .= "<li><a href=\"$link\">$year</a></li>";
      }
      $out .= "</ul>";
    }
    $output[] = ['#markup' => $out];

    $user = \Drupal::currentUser();

    if ($user->hasPermission('troth elections officer')) {
      $output[] = ['#markup' => "<p><a href=\"$parent/admin/index.html\">Admin Area</a></p>"];
    }
    return $output;
  }

  /**
   * Returns the members elections summary page.
   *
   * @return array
   *   A simple renderable array.
   */
  public function summaryPage() {
    $today = new DrupalDateTime();
    $today->setTimezone(timezone_open('America/Los_Angeles'));
    $nominationStart = new DrupalDateTime(\Drupal::config('troth_elections.adminsettings')->get('troth_elections_nom_start_date'), 'America/Los_Angeles');
    $bioEnd = new DrupalDateTime(\Drupal::config('troth_elections.adminsettings')->get('troth_elections_nom_bio_date'), 'America/Los_Angeles');
    $voteStart = new DrupalDateTime(\Drupal::config('troth_elections.adminsettings')->get('troth_elections_voting_open_date'), 'America/Los_Angeles');
    $year = $voteStart->format('Y');
    $parent = \Drupal::config('troth_elections.adminsettings')->get('troth_elections_url');
    $output = [];
    $output[] = [
      '#markup' => $this->t("<p>Below are the nominations, and pertinent links for @year's election.</p>", [
        '@year' => $year,
      ]),
    ];

    if ($today >= $nominationStart) {
      $url = Url::fromUserInput($parent . "/" . $year . "/nominations.html");
      $output[] = [
        '#markup' => $this->t('<p><a href=":url">Nominations</a></p>', [
          ':url' => $url->toString(),
        ]),
      ];
    }
    if ($today >= $bioEnd) {
      $url = Url::fromUserInput($parent . "/" . $year . "/candidates.html");
      $output[] = [
        '#markup' => $this->t('<p><a href=":url">Candidate Statements</a></p>', [
          ':url' => $url->toString(),
        ]),
      ];
    }
    if ($today >= $voteStart) {
      $url = Url::fromUserInput($parent . "/" . $year . "/voters.html");
      $output[] = [
        '#markup' => $this->t('<p><a href=":url">Members Voting</a></p>', [
          ':url' => $url->toString(),
        ]),
      ];
    }
    return $output;
  }

  /**
   * Returns the elections nominations page.
   *
   * @return array
   *   A simple renderable array.
   */
  public function nominationDisplay() {
    return troth_elections_nomination_display();
  }

  /**
   * Returns the elections candidate statements page.
   *
   * @return array
   *   A simple renderable array.
   */
  public function candidateStatements() {
    return troth_candidate_statements();
  }

  /**
   * Returns the elections voters page.
   *
   * @return array
   *   A simple renderable array.
   */
  public function voters() {
    return troth_elections_voters();
  }

  /**
   * Returns the elections officer results page.
   *
   * @return array
   *   A simple renderable array.
   */
  public function voteResults() {
    $votingStartDate = new DrupalDateTime(\Drupal::config('troth_elections.adminsettings')->get('troth_elections_voting_open_date'), 'America/Los_Angeles');
    $logurl = Url::fromRoute('troth_elections.log_file_admin_form');
    $logurl->setOption('query', ['year' => $votingStartDate->format('Y')]);
    $fixurl = Url::fromRoute('troth_elections.fix_names_admin_form');
    $output[] = ['#markup' => t("<p>Below is a listing of the vote summary.  The rows highlighted in green are the winners of the vote.  If there are any red cells, these are rows that don't match between the text log file and the database.  This should only happen with write-in votes where you have fixed the name of the candidate.  If this is not the case, you will need to dig through the log file and the e-mails you have received to find where the error is.</p>")];
    $output[] = [
      '#markup' => t('<p><a href=":logfile">View Log File</a></p>', [
        ':logfile' => $logurl->toString(),
      ]),
    ];
    $output[] = [
      '#markup' => t('<p><a href=":fixnames">Fix Names in Results</a></p>', [
        ':fixnames' => $fixurl->toString(),
      ]),
    ];

    // Get basic stats.
    $query = \Drupal::entityQuery('user')
      ->condition('field_profile_membership_status', 'active', '=');

    $reqStartDate = new DrupalDateTime('2020-02-05', 'America/Los_Angeles');
    $reqStartDate->add(new \DateInterval('P2Y'));

    $today = new DrupalDateTime();
    $today->setTimezone(timezone_open('America/Los_Angeles'));
    if ($today > $reqStartDate) {
      // We care about join date.
      $votingStartDate = new DrupalDateTime(Drupal::config('troth_elections.adminsettings')->get('troth_elections_voting_open_date'), 'America/Los_Angeles');
      $votingStartDate->sub(new \DateInterval('P1Y'));
      $query->condition('field_profile_member_start_date', $votingStartDate->format('Y-m-d'), '<=');
    }
    $numMems = $query->count()->execute();

    $connection = \Drupal::database();
    $query = $connection->select('troth_elections_nomination_voter', 'v');
    $numVotes = $query->countQuery()->execute()->fetchField();

    $output[] = ['#markup' => t("<H2>Statistics</H2>")];
    $output[] = [
      '#markup' => t("<p>There were a total of <b>@numVotes (@pct %)</b> out of @numMems elgible voters that submitted a ballot.</p>", [
        '@numVotes' => $numVotes,
        '@pct' => round(($numVotes / $numMems) * 100, 2),
        '@numMems' => $numMems,
      ]),
    ];

    $output = array_merge($output, troth_election_db_results());
    $form = $this->formBuilder()->getForm('Drupal\troth_elections\Form\TrothElectionsExportVote');
    $output[] = $form;
    return $output;
  }

  /**
   * Returns the elections officer log file page.
   *
   * @return array
   *   A simple renderable array.
   */
  public function logFile() {
    $year = '';
    $year = \Drupal::request()->query->get('year');
    // Get office data.
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
    $candidates = [-2 => 'Abstain/No Vote'];

    // Get proposition data.
    $propstorage = \Drupal::entityTypeManager()
      ->getStorage('troth_elections_proposition_type');
    $results = \Drupal::entityQuery('troth_elections_proposition_type')
      ->execute();
    $entities = $propstorage->loadMultiple($results);
    $propositions = [];
    foreach ($entities as $prop_id => $prop) {
      $propositions[$prop_id] = $prop->getName();
    }
    if (!is_numeric($year)) {
      $votingStartDate = new DrupalDateTime(\Drupal::config('troth_elections.adminsettings')->get('troth_elections_voting_open_date'), 'America/Los_Angeles');
      $year = $votingStartDate->format('Y');
    }
    $logpath = \Drupal::config('troth_elections.adminsettings')->get('troth_elections_path');
    $logFile = "$logpath/" . $year . "_votes.txt";
    $output[] = [
      '#markup' => t("<h3>The following is the log file for the @year elections</h3>", [
        '@year' => $year,
      ]),
    ];

    if (!file_exists($logFile)) {
      $output[] = [
        '#markup' => t("The log file @file does not exist.", [
          '@file' => $logFile,
        ]),
      ];
    }
    else {
      $header = [
        'Year',
        'Member ID',
        'Legal Name',
        'Votes',
        'Proposals',
        'Proxy',
        'Signature',
        'IP Addresss',
        'Date',
      ];
      $rowData = [];
      $f = fopen($logFile, "r");
      $contents = fread($f, filesize($logFile));
      fclose($f);
      $rows = explode("\n", $contents);
      foreach ($rows as $row) {
        if ($row != '') {
          $data = explode("\t", $row);
          $logYear = array_shift($data);
          $logVoter = array_shift($data);
          $logLegal = array_shift($data);
          $logVotesArray = unserialize(array_shift($data));
          $logVotes = [];
          foreach ($logVotesArray as $office_id => $votes) {
            $logVote = [];
            foreach ($votes as $uid => $vote) {
              if (is_numeric($uid) && $uid > 0 && !isset($candidates[$uid])) {
                $account = User::load($uid);
                $candidates[$uid] = $account->field_profile_last_name->value . ", " . $account->field_profile_first_name->value;
              }
              elseif (!isset($candidates[$uid])) {
                $candidates[$uid] = $uid;
              }
              if ($vote > 0) {
                $logVote[] = t("@name - @vote", [
                  '@name' => $candidates[$uid],
                  '@vote' => $vote,
                ]);
              }
            }
            $logVotes[] = t("<b>@office:</b><br />@vote", [
              '@office' => $offices[$office_id],
              '@vote' => implode('\n', $logVote),
            ]);
          }
          $logVotes = implode('<br />', $logVotes);
          $logVotes = str_replace('\n', '<br />', $logVotes);

          $logProp = [];
          if (count($propositions) > 0) {
            $logPropArray = unserialize(array_shift($data));
            foreach ($logPropArray as $prop_id => $vote) {
              $logProp[] = t("<b>@prop</b> - @vote", [
                '@prop' => $propositions[$prop_id],
                '@vote' => $vote,
              ]);
            }
          }
          $logProp = implode('<br />', $logProp);

          $logProxy = '';
          if (\Drupal::config('troth_elections.adminsettings')->get('troth_elections_proxy')) {
            $logProxy = array_shift($data);
          }
          $logSig = array_shift($data);
          $logDate = array_shift($data);
          $logIP = array_shift($data);

          $rowData[] = [
            $logYear,
            $logVoter,
            $logLegal,
            Markup::create($logVotes),
            Markup::create($logProp),
            $logProxy,
            $logSig,
            $logDate,
            $logIP,
          ];
        }
      }
      $output[] = [
        '#theme' => 'table',
        '#cache' => ['disabled' => TRUE],
        '#header' => $header,
        '#rows' => $rowData,
      ];
    }
    return $output;
  }

  /**
   * Returns the elections officer proxy page.
   *
   * @return array
   *   A simple renderable array.
   */
  public function voteProxy() {
    $connection = \Drupal::database();
    $query = $connection->select('troth_elections_nomination_voter', 'v')
      ->fields('v', ['uid', 'proxy'])
      ->condition('proxy', 'none', 'NOT LIKE')
      ->condition('proxy', '', '!=')
      ->isNotNull('proxy');
    $results = $query->execute();

    $data = [];
    while ($row = $results->fetchAssoc()) {
      $account = User::load($row['uid']);
      $name = $account->field_profile_last_name->value . ", " . $account->field_profile_first_name->value;
      $data[] = [$name, $row['proxy']];
    }

    $output[] = ['#markup' => $this->t('<h2>Proxies</h2>')];
    if (count($data) > 0) {
      $header = ['Member', 'Proxy'];
      $output[] = [
        '#theme' => 'table',
        '#cache' => ['disabled' => TRUE],
        '#header' => $header,
        '#rows' => $data,
      ];
    }
    else {
      $output[] = ['#markup' => $this->t('There were no proxies submitted.')];
    }
    return $output;
  }

  /**
   * Returns the elections officer proposition page.
   *
   * @return array
   *   A simple renderable array.
   */
  public function voteProp() {
    $output = [];
    $output = array_merge($output, troth_election_db_prop_results());
    $form = $this->formBuilder()->getForm('Drupal\troth_elections\Form\TrothElectionsExportProp');
    $output[] = $form;
    return $output;
  }

}
