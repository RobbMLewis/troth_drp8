<?php

namespace Drupal\troth_maps\Entity;

/**
 * Provides an interface for defining Troth Maps Regions entity entities.
 *
 * @ingroup troth_maps_regions
 */
interface TrothMapsRegionsEntityInterface {

  /**
   * Gets the TrothMapsRegions entity email.
   *
   * @return int
   *   Region ID of the TrothMapsRegions entity.
   */
  public function getRegid();

  /**
   * Gets the TrothMapsRegions entity region_name.
   *
   * @return string
   *   region_name of the TrothMapsRegions entity.
   */
  public function getRegionName();

  /**
   * Sets the TrothMapsRegions entity region_name.
   *
   * @param string $region_name
   *   The TrothMapsRegions entity region_name.
   *
   * @return \Drupal\troth_maps\Entity\TrothMapsRegionsEntityInterface
   *   The called TrothMapsRegions entity entity.
   */
  public function setRegionName($region_name);

  /**
   * Gets the TrothMapsRegions entity archived.
   *
   * @return bool
   *   archived of the TrothMapsRegions entity.
   */
  public function getArchived();

  /**
   * Sets the TrothMapsRegions entity archived.
   *
   * @param bool $archived
   *   The TrothMapsRegions entity archived.
   *
   * @return \Drupal\troth_maps\Entity\TrothMapsRegionsEntityInterface
   *   The called TrothMapsRegions entity entity.
   */
  public function setArchived($archived);

  /**
   * Gets the TrothMapsRegions entity region_name.
   *
   * @return string
   *   region_email of the TrothMapsRegions entity.
   */
  public function getRegionEmail();

  /**
   * Sets the TrothMapsRegions entity region_email.
   *
   * @param string $region_email
   *   The TrothMapsRegions entity region_email.
   *
   * @return \Drupal\troth_maps\Entity\TrothMapsRegionsEntityInterface
   *   The called TrothMapsRegions entity entity.
   */
  public function setRegionEmail($region_email);

  /**
   * Gets the TrothMapsRegions entity region_type.
   *
   * @return string
   *   region_type of the TrothMapsRegions entity.
   */
  public function getRegionType();

  /**
   * Sets the TrothMapsRegions entity region_type.
   *
   * @param string $region_type
   *   The TrothMapsRegions entity region_type.
   *
   * @return \Drupal\troth_maps\Entity\TrothMapsRegionsEntityInterface
   *   The called TrothMapsRegions entity entity.
   */
  public function setRegionType($region_type);

  /**
   * Gets the TrothMapsRegions entity build_values.
   *
   * @return array
   *   State of the TrothMapsRegions build_values.
   */
  public function getBuildValues();

  /**
   * Sets the TrothMapsRegions entity build_values.
   *
   * @param array $build_values
   *   The TrothMapsRegions entity build_values.
   *
   * @return \Drupal\troth_maps\Entity\TrothMapsRegionsEntityInterface
   *   The called TrothMapsRegions entity entity.
   */
  public function setBuildValues(array $build_values);

  /**
   * Gets the TrothMapsRegions entity kml_color.
   *
   * @return string
   *   kml_color of the TrothMapsRegions entity.
   */
  public function getKmlColor();

  /**
   * Sets the TrothMapsRegions entity kml_color.
   *
   * @param string $kml_color
   *   The TrothMapsRegions entity kml_color.
   *
   * @return \Drupal\troth_maps\Entity\TrothMapsRegionsEntityInterface
   *   The called TrothMapsRegions entity entity.
   */
  public function setKmlColor($kml_color);

  /**
   * Gets the TrothMapsRegions entity border_color.
   *
   * @return string
   *   border_color of the TrothMapsRegions entity.
   */
  public function getBorderColor();

  /**
   * Sets the TrothMapsRegions entity border_color.
   *
   * @param string $border_color
   *   The TrothMapsRegions entity border_color.
   *
   * @return \Drupal\troth_maps\Entity\TrothMapsRegionsEntityInterface
   *   The called TrothMapsRegions entity entity.
   */
  public function setBorderColor($border_color);

  /**
   * Gets the TrothMapsRegions entity create_shape.
   *
   * @return bool
   *   create_shape of the TrothMapsRegions entity.
   */
  public function getCreateShape();

  /**
   * Sets the TrothMapsRegions entity create_shape.
   *
   * @param bool $create_shape
   *   The TrothMapsRegions entity create_shape.
   *
   * @return \Drupal\troth_maps\Entity\TrothMapsRegionsEntityInterface
   *   The called TrothMapsRegions entity entity.
   */
  public function setCreateShape($create_shape);

  /**
   * Gets the TrothMapsRegions entity transparency.
   *
   * @return float
   *   transparency of the TrothMapsRegions entity.
   */
  public function getTransparency();

  /**
   * Sets the TrothMapsRegions entity border_color.
   *
   * @param float $transparency
   *   The TrothMapsRegions entity transparency.
   *
   * @return \Drupal\troth_maps\Entity\TrothMapsRegionsEntityInterface
   *   The called TrothMapsRegions entity entity.
   */
  public function setTransparency($transparency);

  /**
   * Gets the TrothMapsRegions entity geom.
   *
   * @return string
   *   Geom of the TrothMapsRegions entity.
   */
  public function getGeom();

  /**
   * Sets the TrothMapsRegions entity geom.
   *
   * @param string $format
   *   The TrothMapsRegions entity geom format.
   *
   * @return \Drupal\troth_maps\Entity\TrothMapsRegionsEntityInterface
   *   The called TrothMapsRegions entity entity.
   */
  public function getGeomFormat($format);

  /**
   * Sets the TrothMapsRegions entity geom.
   *
   * @param string $geom
   *   The TrothMapsRegions entity geom.
   * @param string $format
   *   The TrothMapsRegions entity geom format.
   *
   * @return \Drupal\troth_maps\Entity\TrothMapsRegionsEntityInterface
   *   The called TrothMapsRegions entity entity.
   */
  public function setGeom($geom, $format);

}
