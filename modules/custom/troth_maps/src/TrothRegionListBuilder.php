<?php

namespace Drupal\troth_maps;

use Drupal\Core\Datetime\DateFormatter;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Render\RendererInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class TrothStewardTypeListBuilder.
 */
class TrothRegionListBuilder extends EntityListBuilder {

  /**
   * The date formatter service.
   *
   * @var \Drupal\Core\Datetime\DateFormatter
   */
  protected $dateFormatter;
  /**
   * The renderer.
   *
   * @var \Drupal\Core\Render\RendererInterface
   */
  protected $renderer;

  /**
   * The Bundle Names.
   *
   * @var array
   */
  protected $names;

  /**
   * Constructs a new TrothStewardListBuilder object.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type definition.
   * @param \Drupal\Core\Entity\EntityStorageInterface $storage
   *   The entity storage class.
   * @param \Drupal\Core\Datetime\DateFormatter $date_formatter
   *   The date formatter service.
   * @param \Drupal\Core\Render\RendererInterface $renderer
   *   The renderer.
   */
  public function __construct(EntityTypeInterface $entity_type, EntityStorageInterface $storage, DateFormatter $date_formatter, RendererInterface $renderer) {
    parent::__construct($entity_type, $storage);
    $this->dateFormatter = $date_formatter;
    $this->renderer = $renderer;
  }

  /**
   * {@inheritdoc}
   */
  public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type) {
    return new static(
      $entity_type,
      $container->get('entity.manager')->getStorage($entity_type->id()),
      $container->get('date.formatter'),
      $container->get('renderer')
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function getEntityIds() {
    $regiontype = \Drupal::routeMatch()->getParameter('regiontype');
    $query = $this->getStorage()->getQuery();
    if ($regiontype == 'region') {
      $query->condition('region_type', 'region', '=');
    }
    elseif ($regiontype == 'local') {
      $query->condition('region_type', 'local', '=');
    }
    elseif ($regiontype == 'special') {
      $query->condition('region_type', 'special', '=');
    }
    if ($regiontype == 'archive') {
      $query->condition('archived', 1, '=');
    }
    else {
      $query->condition('archived', 0, '=');
    }
    $query->sort('region_name', 'ASC');
    return $query->execute();
  }

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['region'] = $this->t('Region Name');
    $header['email'] = $this->t('Email Address');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /** @var \Drupal\practical\Entity\TrothStewardEntityInterface $entity */
    $row['region'] = $entity->getRegionName();
    $row['email'] = $entity->getRegionEmail();
    return $row + parent::buildRow($entity);
  }

}
