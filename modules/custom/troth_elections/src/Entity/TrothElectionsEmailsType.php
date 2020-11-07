<?php

namespace Drupal\troth_elections\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;

/**
 * TrothElections Emails.
 *
 * @ConfigEntityType(
 *   id = "troth_elections_emails_type",
 *   label = @Translation("Troth Elections Propositions"),
 *   bundle_of = "troth_elections_emails_type",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid",
 *   },
 *   config_prefix = "emails_type",
 *   config_export = {
 *     "id",
 *     "label",
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
class TrothElectionsEmailsType extends ConfigEntityBundleBase {
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

}
