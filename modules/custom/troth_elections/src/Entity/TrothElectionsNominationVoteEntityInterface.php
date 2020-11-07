<?php

namespace Drupal\troth_elections\Entity;

use Drupal\Core\Entity\EntityChangedInterface;

/**
 * Provides an interface for defining Troth Elections Email entity entities.
 *
 * @ingroup troth_elections_emails
 */
interface TrothElectionsNominationVoteEntityInterface extends EntityChangedInterface {

  /**
   * Gets the TrothElectionsNominationVote entity Member Hash.
   *
   * @return string
   *   email of the TrothElectionsNominationVote entity.
   */
  public function getMemHash();

  /**
   * Sets the TrothElectionsNominationVote entity Member Hash.
   *
   * @param string $uidhash
   *   The TrothElectionsNominationVote entity Member Hash.
   *
   * @return \Drupal\troth_elections\Entity\TrothElectionsNominationVoteEntityInterface
   *   The called TrothElectionsNominationVote entity entity.
   */
  public function setMemHash($uidhash);

  /**
   * Sets the TrothElectionsNominationVote entity Member Hash.
   *
   * @param int $uid
   *   The TrothElectionsNominationVote entity Member Hash.
   *
   * @return \Drupal\troth_elections\Entity\TrothElectionsNominationVoteEntityInterface
   *   The called TrothElectionsNominationVote entity entity.
   */
  public function setMemHashUid($uid);

  /**
   * Gets the TrothElectionsNominationVote entity Vote.
   *
   * @return string
   *   email of the TrothElectionsNominationVote entity.
   */
  public function getOfficeId();

  /**
   * Sets the TrothElectionsNominationVote entity Vote.
   *
   * @param string $office_id
   *   The TrothElectionsNominationVote entity Vote.
   *
   * @return \Drupal\troth_elections\Entity\TrothElectionsNominationVoteEntityInterface
   *   The called TrothElectionsNominationVote entity entity.
   */
  public function setOfficeId($office_id);

  /**
   * Gets the TrothElectionsNominationVote entity Vote.
   *
   * @return string
   *   email of the TrothElectionsNominationVote entity.
   */
  public function getCandidate();

  /**
   * Sets the TrothElectionsNominationVote entity Vote.
   *
   * @param string $candidate
   *   The TrothElectionsNominationVote entity Vote.
   *
   * @return \Drupal\troth_elections\Entity\TrothElectionsNominationVoteEntityInterface
   *   The called TrothElectionsNominationVote entity entity.
   */
  public function setCandidate($candidate);

  /**
   * Gets the TrothElectionsNominationVote entity Vote.
   *
   * @return int
   *   email of the TrothElectionsNominationVote entity.
   */
  public function getVote();

  /**
   * Sets the TrothElectionsNominationVote entity Vote.
   *
   * @param int $vote
   *   The TrothElectionsNominationVote entity Vote.
   *
   * @return \Drupal\troth_elections\Entity\TrothElectionsNominationVoteEntityInterface
   *   The called TrothElectionsNominationVote entity entity.
   */
  public function setVote($vote);

  /**
   * Gets the TrothElectionsNominationVote entity creation timestamp.
   *
   * @return int
   *   Creation timestamp of the TrothElectionsNominationVote entity.
   */
  public function getCreatedTime();

  /**
   * Sets the TrothElectionsNominationVote entity creation timestamp.
   *
   * @param int $timestamp
   *   The TrothElectionsNominationVote entity creation timestamp.
   *
   * @return \Drupal\troth_elections\Entity\TrothElectionsNominationVoteEntityInterface
   *   The called TrothElectionsNominationVote entity entity.
   */
  public function setCreatedTime($timestamp);

}
