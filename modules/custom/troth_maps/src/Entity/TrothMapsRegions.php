<?php

namespace Drupal\troth_maps\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\geofield\GeoPHP\GeoPHPWrapper;

/**
 * Defines the troth_maps_regions entity.
 *
 * @ContentEntityType(
 *   id = "troth_maps_regions",
 *   label = @Translation("Regions"),
 *   base_table = "troth_maps_regions",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "region_name",
 *     "region_type" = "region_type",
 *     "region_email" = "region_email",
 *     "archived" = "archived",
 *     "build_values" = "build_values",
 *     "kml_color" = "kml_color",
 *     "border_color" = "border_color",
 *     "create_shape" = "create_shape",
 *     "transparency" = "transparency",
 *     "geom" = "geom",
 *   },
 *   fieldable = FALSE,
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\troth_maps\TrothRegionListBuilder",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "form" = {
 *       "default" = "Drupal\troth_maps\Form\TrothMapsRegionEntityForm",
 *       "add" = "Drupal\troth_maps\Form\TrothMapsRegionEntityForm",
 *       "edit" = "Drupal\troth_maps\Form\TrothMapsRegionEntityForm",
 *       "delete" = "Drupal\troth_maps\Form\TrothMapsRegionDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\DefaultHtmlRouteProvider",
 *     },
 *     "storage_schema" = "Drupal\troth_maps\TrothMapsEntityStorageSchema",
 *   },
 *   links = {
 *     "canonical" = "/admin/config/troth/maps/",
 *     "edit-form" = "/admin/config/troth/maps/{troth_maps_regions}/edit",
 *     "delete-form" = "/admin/config/troth/maps/{troth_maps_regions}/delete",
 *     "collection" = "/admin/config/troth/maps/",
 *   },
 *   admin_permission = "administer site configuration",
 * )
 */
class TrothMapsRegions extends ContentEntityBase implements TrothMapsRegionsEntityInterface {

  /**
   * {@inheritdoc}
   */
  public function getRegid() {
    return $this->id();
  }

  /**
   * {@inheritdoc}
   */
  public function getRegionName() {
    return $this->get('region_name')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function getArchived() {
    return $this->get('archived')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setArchived($archived) {
    $this->set('archived', $archived);
    return $this;

  }

  /**
   * {@inheritdoc}
   */
  public function setRegionName($region_name) {
    $this->set('region_name', $region_name);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getRegionEmail() {
    return $this->get('region_email')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setRegionEmail($region_email) {
    $this->set('region_email', $region_email);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getRegionType() {
    return $this->get('region_type')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setRegionType($region_type) {
    $this->set('region_type', $region_type);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getBuildValues() {
    $values = $this->get('build_values')->value;
    return unserialize($values);
  }

  /**
   * {@inheritdoc}
   */
  public function setBuildValues($build_values) {
    $this->set('build_values', serialize($build_values));
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getKmlColor() {
    return $this->get('kml_color')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setKmlColor($kml_color) {
    $this->set('kml_color', $kml_color);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getBorderColor() {
    return $this->get('border_color')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setBorderColor($border_color) {
    $this->set('border_color', $border_color);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getTransparency() {
    return $this->get('transparency')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setTransparency($transparency) {
    $this->set('transparency', $transparency);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCreateShape() {
    return $this->get('create_shape')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCreateShape($create_shape) {
    $this->set('create_shape', $create_shape);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getGeom() {
    return $this->get('geom')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function getGeomFormat($format = 'kml') {
    $geom = $this->get('geom')->value;
    $geophp_wrapper = new GeoPHPWrapper();
    $geometry = $geophp_wrapper->load($geom);
    return $geometry->out($format);
  }

  /**
   * {@inheritdoc}
   */
  public function setGeom($geom, $format = 'wkt') {
    if ($format != 'wkt') {
      $geophp_wrapper = new GeoPHPWrapper();
      $geometry = $geophp_wrapper->load($geom, $format);
      $this->set('geom', $geometry->out('wkt'));
    }
    else {
      $this->set('geom', $geom);
    }
    return $this;
  }

  /**
   * Determines the schema for the base_table property defined above.
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['region_name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Region Name'));

    $fields['region_email'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Region Email'));

    $fields['archived'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Archived'));

    $fields['region_type'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Type of Region'));

    $fields['build_values'] = BaseFieldDefinition::create('string_long')
      ->setLabel(t('Build Values'));

    $fields['kml_color'] = BaseFieldDefinition::create('string')
      ->setLabel(t('KML Color'));

    $fields['border_color'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Border Color'));

    $fields['transparency'] = BaseFieldDefinition::create('float')
      ->setLabel(t('Transparency'));

    $fields['create_shape'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Create Shape'))
      ->setDefaultValue(1);

    $fields['geom'] = BaseFieldDefinition::create('geofield')
      ->setLabel(t('Geometry'));

    return $fields;
  }

}
