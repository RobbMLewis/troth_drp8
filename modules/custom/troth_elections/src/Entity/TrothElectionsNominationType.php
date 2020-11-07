<?php

namespace Drupal\troth_elections\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;

/**
 * TrothElections Nominations.
 *
 * @ConfigEntityType(
 *   id = "troth_elections_nomination_type",
 *   label = @Translation("Troth Elections Nominations"),
 *   bundle_of = "troth_elections_nomination_type",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid",
 *   },
 *   config_prefix = "nomination_type",
 *   config_export = {
 *     "id",
 *     "label",
 *     "office_id",
 *     "uid",
 *     "nominated",
 *     "accepted",
 *     "declined",
 *     "ineligible",
 *     "numnoms",
 *   },
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\DefaultHtmlRouteProvider",
 *     },
 *   },
 *   admin_permission = "administer site configuration",
 * )
 */
class TrothElectionsNominationType extends ConfigEntityBundleBase {
  /**
   * The machine name of the practical type.
   *
   * @var string
   */
  protected $id;

  /**
   * The human-readable name of the practical type.
   *
   * @var string
   */
  protected $label;

  /**
   * A brief description of the practical type.
   *
   * @var string
   */
  protected $office_id;

  /**
   * A brief description of the practical type.
   *
   * @var string
   */
  protected $uid;

  /**
   * A brief description of the practical type.
   *
   * @var bool
   */
  protected $nominated;

  /**
   * A brief description of the practical type.
   *
   * @var bool
   */
  protected $accepted;

  /**
   * A brief description of the practical type.
   *
   * @var bool
   */
  protected $declined;

  /**
   * A brief description of the practical type.
   *
   * @var bool
   */
  protected $ineligible;

  /**
   * A brief description of the practical type.
   *
   * @var int
   */
  protected $numnoms;

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return $this->label;
  }

  /**
   * {@inheritdoc}
   */
  public function setName($label) {
    $this->label = $label;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getOffice() {
    return $this->office_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setOffice($office_id) {
    $this->office_id = $office_id;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getNominee() {
    return $this->uid;
  }

  /**
   * {@inheritdoc}
   */
  public function setNominee($uid) {
    $this->uid = $uid;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getNominated() {
    return $this->nominated;
  }

  /**
   * {@inheritdoc}
   */
  public function setNominated($nominated) {
    $this->nominated = $nominated;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getAccepted() {
    return $this->accepted;
  }

  /**
   * {@inheritdoc}
   */
  public function setAccepted($accepted) {
    $this->accepted = $accepted;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getDeclined() {
    return $this->declined;
  }

  /**
   * {@inheritdoc}
   */
  public function setDeclined($declined) {
    $this->declined = $declined;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getIneligible() {
    return $this->ineligible;
  }

  /**
   * {@inheritdoc}
   */
  public function setIneligible($ineligible) {
    $this->ineligible = $ineligible;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getNumNoms() {
    return $this->numnoms;
  }

  /**
   * {@inheritdoc}
   */
  public function setNumNoms($numnoms) {
    $this->numnoms = $numnoms;
    return $this;
  }

}
