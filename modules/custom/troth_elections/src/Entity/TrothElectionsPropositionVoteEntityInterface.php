<?php

namespace Drupal\troth_elections\Entity;

use Drupal\Core\Entity\EntityChangedInterface;

/**
 * Provides an interface for defining Troth Elections Email entity entities.
 *
 * @ingroup troth_elections_emails
 */
interface TrothElectionsPropositionVoteEntityInterface extends EntityChangedInterface {

  /**
   * Gets the TrothElectionsPropositionVote entity Member Hash.
   *
   * @return string
   *   email of the TrothElectionsPropositionVote entity.
   */
  public function getMemHash();

  /**
   * Sets the TrothElectionsPropositionVote Member Hash.
   *
   * @param string $uidhash
   *   The TrothElectionsPropositionVote entity Member Hash.
   *
   * @return \Drupal\troth_elections\Entity\TrothElectionsPropositionVoteEntityInterface
   *   The called TrothElectionsPropositionVote entity entity.
   */
  public function setMemHash($uidhash);

  /**
   * Sets the TrothElectionsPropositionVote Member Hash.
   *
   * @param int $uid
   *   The TrothElectionsPropositionVote entity Member UID.
   *
   * @return \Drupal\troth_elections\Entity\TrothElectionsPropositionVoteEntityInterface
   *   The called TrothElectionsPropositionVote entity entity.
   */
  public function setMemHashUid($uid);

  /**
   * Gets the TrothElectionsPropositionVote entity vote.
   *
   * @return string
   *   email of the TrothElectionsPropositionVote entity.
   */
  public function getPropositionId();

  /**
   * Sets the TrothElectionsPropositionVote entity vote.
   *
   * @param string $proposition_id
   *   The TrothElectionsPropositionVote entity vote.
   *
   * @return \Drupal\troth_elections\Entity\TrothElectionsPropositionVoteEntityInterface
   *   The called TrothElectionsPropositionVote entity entity.
   */
  public function setPropositionId($proposition_id);

  /**
   * Gets the TrothElectionsPropositionVote entity vote.
   *
   * @return string
   *   email of the TrothElectionsPropositionVote entity.
   */
  public function getVote();

  /**
   * Sets the TrothElectionsPropositionVote entity vote.
   *
   * @param string $vote
   *   The TrothElectionsPropositionVote entity vote.
   *
   * @return \Drupal\troth_elections\Entity\TrothElectionsPropositionVoteEntityInterface
   *   The called TrothElectionsPropositionVote entity entity.
   */
  public function setVote($vote);

  /**
   * Gets the TrothElectionsPropositionVote entity creation timestamp.
   *
   * @return int
   *   Creation timestamp of the TrothElectionsPropositionVote entity.
   */
  public function getCreatedTime();

  /**
   * Sets the TrothElectionsPropositionVote entity creation timestamp.
   *
   * @param int $timestamp
   *   The TrothElectionsPropositionVote entity creation timestamp.
   *
   * @return \Drupal\troth_elections\Entity\TrothElectionsPropositionVoteEntityInterface
   *   The called TrothElectionsPropositionVote entity entity.
   */
  public function setCreatedTime($timestamp);

}
