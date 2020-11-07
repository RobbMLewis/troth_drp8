<?php

namespace Drupal\troth_elections\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;

/**
 * TrothElections Proposition.
 *
 * @ConfigEntityType(
 *   id = "troth_elections_proposition_type",
 *   label = @Translation("Troth Elections Propositions"),
 *   bundle_of = "troth_elections_proposition_type",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid",
 *   },
 *   config_prefix = "proposition_type",
 *   config_export = {
 *     "id",
 *     "label",
 *     "text",
 *     "options",
 *   },
 *   handlers = {
 *     "form" = {
 *       "default" = "Drupal\troth_elections\Form\TrothElectionsPropositionTypeEntityForm",
 *       "add" = "Drupal\troth_elections\Form\TrothElectionsPropositionTypeEntityForm",
 *       "edit" = "Drupal\troth_elections\Form\TrothElectionsPropositionTypeEntityForm",
 *       "delete" = "Drupal\Core\Entity\EntityDeleteForm",
 *     },
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\troth_elections\TrothElectionsPropositionListBuilder",
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\DefaultHtmlRouteProvider",
 *     },
 *   },
 *   admin_permission = "administer site configuration",
 *   links = {
 *     "canonical" = "/admin/config/troth/elections/propositions/{troth_elections_proposition_type}",
 *     "add-form" = "/admin/config/troth/elections/propositions/add",
 *     "edit-form" = "/admin/config/troth/elections/propositions/{troth_elections_proposition_type}/edit",
 *     "delete-form" = "/admin/config/troth/elections/propositions/{troth_elections_proposition_type}/delete",
 *     "collection" = "/admin/config/troth/elections/propositions",
 *   }
 * )
 */
class TrothElectionsPropositionType extends ConfigEntityBundleBase {
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
  protected $text;

  /**
   * A brief description of the practical type.
   *
   * @var string
   */
  protected $options;

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
  public function getText() {
    return $this->text;
  }

  /**
   * {@inheritdoc}
   */
  public function setText($text) {
    $this->text = $text;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getOptions() {
    return $this->options;
  }

  /**
   * {@inheritdoc}
   */
  public function setOptions($options) {
    $this->options = $options;
    return $this;
  }

}
