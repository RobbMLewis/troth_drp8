<?php

namespace Drupal\troth_elections\Controller;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Datetime\DrupalDateTime;

/**
 * Builds an example page.
 */
class TrothElectionsAccessController {

  /**
   * Todays Date.
   *
   * @var Drupal\Core\Datetime\DrupalDateTime
   */
  protected $today;
  /**
   * Nomination Start Date.
   *
   * @var Drupal\Core\Datetime\DrupalDateTime
   */
  protected $nomStartDate;
  /**
   * Nomination End Date.
   *
   * @var Drupal\Core\Datetime\DrupalDateTime
   */
  protected $nomEndDate;
  /**
   * Nomination Accept Date.
   *
   * @var Drupal\Core\Datetime\DrupalDateTime
   */
  protected $nomAcceptDate;
  /**
   * Nomination Bio Date.
   *
   * @var Drupal\Core\Datetime\DrupalDateTime
   */
  protected $nomBioDate;
  /**
   * Voting Start Date.
   *
   * @var Drupal\Core\Datetime\DrupalDateTime
   */
  protected $votingStartDate;
  /**
   * Voting End Date.
   *
   * @var Drupal\Core\Datetime\DrupalDateTime
   */
  protected $votingEndDate;

  /**
   * Constructs a new TrothElectionsAccessController object.
   */
  public function __construct() {
    // Set Todays date, 00:00:00 west coast time.
    $this->today = new DrupalDateTime();
    $this->today->setTimezone(timezone_open('America/Los_Angeles'));

    // Get the nom start date, 00:00:00 west coast time.
    $this->nomStartDate = new DrupalDateTime(\Drupal::config('troth_elections.adminsettings')->get('troth_elections_nom_start_date'), 'America/Los_Angeles');

    // Get the nom end date +1, 00:00:00 west coast time (midnight of set date).
    $this->nomEndDate = new DrupalDateTime(\Drupal::config('troth_elections.adminsettings')->get('troth_elections_nom_end_date'), 'America/Los_Angeles');
    // Add 1 day to make end of day.
    $this->nomEndDate->add(new \DateInterval('P1D'));

    // Get the nom accept date +1, 00:00:00 west coast time
    // (midnight of set date).
    $this->nomAcceptDate = new DrupalDateTime(\Drupal::config('troth_elections.adminsettings')->get('troth_elections_nom_accept_date'), 'America/Los_Angeles');
    // Add 1 day to make end of day.
    $this->nomAcceptDate->add(new \DateInterval('P1D'));

    // Get the nom bio date +1, 00:00:00 west coast time
    // (midnight of set date).
    $this->nomBioDate = new DrupalDateTime(\Drupal::config('troth_elections.adminsettings')->get('troth_elections_nom_bio_date'), 'America/Los_Angeles');
    // Add 1 day to make end of day.
    $this->nomBioDate->add(new \DateInterval('P1D'));

    // Get the voting start date, 00:00:00 west coast time.
    $this->votingStartDate = new DrupalDateTime(\Drupal::config('troth_elections.adminsettings')->get('troth_elections_voting_open_date'), 'America/Los_Angeles');

    // Get the voting end date +1, 00:00:00 west coast time
    // (midnight of set date).
    $this->votingEndDate = new DrupalDateTime(\Drupal::config('troth_elections.adminsettings')->get('troth_elections_voting_end_date'), 'America/Los_Angeles');
    // Add 1 day to make end of day.
    $this->votingEndDate->add(new \DateInterval('P1D'));
  }

  /**
   * Checks access for a specific request.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   Run access checks for this account.
   *
   * @return \Drupal\Core\Access\AccessResultInterface
   *   The access result.
   */
  public function accessNom(AccountInterface $account) {
    // Get if they are a valid voter.
    $validVoter = troth_elections_valid_voter($account->id());
    // Check that they are valid voter and
    // that the nominations are open.
    if ($this->today >= $this->nomStartDate && $this->today <= $this->nomEndDate && $validVoter == TRUE) {
      return AccessResult::allowed();
    }
    else {
      return AccessResult::forbidden();
    }
  }

  /**
   * Checks access for a specific request.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   Run access checks for this account.
   *
   * @return \Drupal\Core\Access\AccessResultInterface
   *   The access result.
   */
  public function accessAccept(AccountInterface $account) {
    // Get if they are a valid voter.
    $validVoter = troth_elections_valid_voter($account->id());
    // Check that they are valid voter and
    // that the nominations allowed to be accepted.
    if ($this->today >= $this->nomStartDate && $this->today <= $this->nomAcceptDate && $validVoter == TRUE) {
      return AccessResult::allowed();
    }
    else {
      return AccessResult::forbidden();
    }
  }

  /**
   * Checks access for a specific request.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   Run access checks for this account.
   *
   * @return \Drupal\Core\Access\AccessResultInterface
   *   The access result.
   */
  public function accessBio(AccountInterface $account) {
    // Get if they are a valid voter.
    $validVoter = troth_elections_valid_voter($account->id());
    // Check that they are valid voter and
    // that the bios are still allowed to be changed..
    if ($this->today >= $this->nomStartDate && $this->today <= $this->nomBioDate && $validVoter == TRUE) {
      return AccessResult::allowed();
    }
    else {
      return AccessResult::forbidden();
    }
  }

  /**
   * Checks access for a specific request.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   Run access checks for this account.
   *
   * @return \Drupal\Core\Access\AccessResultInterface
   *   The access result.
   */
  public function accessBallot(AccountInterface $account) {
    // Get if they are a valid voter.
    $validVoter = troth_elections_valid_voter($account->id());
    // Check that they are valid voter and
    // that the bios are still allowed to be changed.
    if ($this->today >= $this->votingStartDate && $this->today <= $this->votingEndDate && $validVoter == TRUE) {
      return AccessResult::allowed();
    }
    else {
      return AccessResult::forbidden();
    }
  }

}
