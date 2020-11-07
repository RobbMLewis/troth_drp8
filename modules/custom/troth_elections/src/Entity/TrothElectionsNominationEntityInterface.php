<?php

namespace Drupal\troth_elections\Entity;

use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Troth Elections Email entity entities.
 *
 * @ingroup troth_elections_emails
 */
interface TrothElectionsNominationEntityInterface extends EntityChangedInterface, EntityOwnerInterface {

  /**
   * Gets the TrothElectionsNomination entity Office ID.
   *
   * @return string
   *   email of the TrothElectionsNomination entity.
   */
  public function getOfficeId();

  /**
   * Sets the TrothElectionsNomination entity Office ID.
   *
   * @param string $office_id
   *   The TrothElectionsNomination entity Office ID.
   *
   * @return \Drupal\troth_elections\Entity\TrothElectionsNominationEntityInterface
   *   The called TrothElectionsNomination entity entity.
   */
  public function setOfficeId($office_id);

  /**
   * Gets the TrothElectionsNomination entity creation timestamp.
   *
   * @return int
   *   Creation timestamp of the TrothElectionsNomination entity.
   */
  public function getCreatedTime();

  /**
   * Sets the TrothElectionsNomination entity creation timestamp.
   *
   * @param int $timestamp
   *   The TrothElectionsNomination entity creation timestamp.
   *
   * @return \Drupal\troth_elections\Entity\TrothElectionsNominationEntityInterface
   *   The called TrothElectionsNomination entity entity.
   */
  public function setCreatedTime($timestamp);

}
