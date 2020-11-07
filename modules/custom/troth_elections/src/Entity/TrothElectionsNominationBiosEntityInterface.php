<?php

namespace Drupal\troth_elections\Entity;

use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Troth Elections Email entity entities.
 *
 * @ingroup troth_elections_emails
 */
interface TrothElectionsNominationBiosEntityInterface extends EntityChangedInterface, EntityOwnerInterface {

  /**
   * Gets the TrothElectionsNominationBios entity Bio.
   *
   * @return string
   *   email of the TrothElectionsNominationBios entity.
   */
  public function getBio();

  /**
   * Sets the TrothElectionsNominationBios entity Bio.
   *
   * @param string $bio
   *   The TrothElectionsNominationBios entity Bio.
   *
   * @return \Drupal\troth_elections\Entity\TrothElectionsNominationBiosEntityInterface
   *   The called TrothElectionsNominationBios entity entity.
   */
  public function setBio($bio);

  /**
   * Gets the TrothElectionsNominationBios entity creation timestamp.
   *
   * @return int
   *   Creation timestamp of the TrothElectionsNominationBios entity.
   */
  public function getCreatedTime();

  /**
   * Sets the TrothElectionsNominationBios entity creation timestamp.
   *
   * @param int $timestamp
   *   The TrothElectionsNominationBios entity creation timestamp.
   *
   * @return \Drupal\troth_elections\Entity\TrothElectionsNominationBiosEntityInterface
   *   The called TrothElectionsNominationBios entity entity.
   */
  public function setCreatedTime($timestamp);

}
