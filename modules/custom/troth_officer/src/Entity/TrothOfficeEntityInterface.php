<?php

namespace Drupal\troth_officer\Entity;

use Drupal\Core\Entity\EntityChangedInterface;

/**
 * Provides an interface for defining Troth Office entity entities.
 *
 * @ingroup troth_office
 */
interface TrothOfficeEntityInterface extends EntityChangedInterface {

  /**
   * Returns the entity office's Name.
   *
   * @return string
   *   The office name.
   */
  public function getName();

  /**
   * Sets the entity office's name.
   *
   * @param string $office_name
   *   The office name.
   *
   * @return $this
   */
  public function setName($office_name);

  /**
   * Returns the entity office's Type.
   *
   * @return string
   *   The office type.
   */
  public function getOfficeType();

  /**
   * Sets the entity office's type.
   *
   * @param string $office_type
   *   The office type.
   *
   * @return $this
   */
  public function setOfficeType($office_type);

  /**
   * Returns the entity office's email.
   *
   * @return string
   *   The office type.
   */
  public function getEmail();

  /**
   * Sets the entity office's email.
   *
   * @param string $office_email
   *   The office email.
   *
   * @return $this
   */
  public function setEmail($office_email);

  /**
   * Returns the entity office's description.
   *
   * @return array
   *   The office description.
   */
  public function getDescription();

  /**
   * Sets the entity office's description.
   *
   * @param array $office_description
   *   The office description.
   *
   * @return $this
   */
  public function setDescription(array $office_description);

  /**
   * Returns the entity office's term.
   *
   * @return int
   *   The office term.
   */
  public function getTerm();

  /**
   * Sets the entity office's term.
   *
   * @param int $office_term
   *   The office term.
   *
   * @return $this
   */
  public function setTerm($office_term);

  /**
   * Returns the entity office's open.
   *
   * @return bool
   *   The office open.
   */
  public function getOpen();

  /**
   * Sets the entity office's open.
   *
   * @param bool $office_open
   *   The office open.
   *
   * @return $this
   */
  public function setOpen($office_open);

  /**
   * Returns the entity office's number open.
   *
   * @return int
   *   The office open.
   */
  public function getNumOpen();

  /**
   * Sets the entity office's number open.
   *
   * @param int $office_number_open
   *   The office number open.
   *
   * @return $this
   */
  public function setNumOpen($office_number_open);

  /**
   * Returns the entity office's roles.
   *
   * @return array
   *   The office type.
   */
  public function getRoles();

  /**
   * Sets the entity office's roles.
   *
   * @param array $office_roles
   *   The office roles.
   *
   * @return $this
   */
  public function setRoles(array $office_roles);

  /**
   * Gets the TrothOffice entity creation timestamp.
   *
   * @return int
   *   Creation timestamp of the TrothOffice entity.
   */
  public function getCreatedTime();

  /**
   * Sets the TrothOffice entity creation timestamp.
   *
   * @param int $timestamp
   *   The TrothOffice entity creation timestamp.
   *
   * @return \Drupal\troth_office\Entity\TrothOfficeEntityInterface
   *   The called TrothOffice entity entity.
   */
  public function setCreatedTime($timestamp);

}
