<?php

namespace Drupal\troth_officer;

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
class TrothOfficeListBuilder extends EntityListBuilder {

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
  public function load() {

    $entity_query = \Drupal::service('entity.query')->get('troth_office');
    $header = $this->buildHeader();

    $entity_query->pager(50);
    $entity_query->tableSort($header);

    $entids = $entity_query->execute();

    return $this->storage->loadMultiple($entids);
  }

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['office_name'] = [
      'data' => $this->t('Office Name'),
      'field' => 'office_name',
      'specifier' => 'office_name',
    ];
    $header['office_type'] = [
      'data' => $this->t('Office Group'),
      'field' => 'office_type',
      'specifier' => 'office_type',
    ];

    $header['office_email'] = [
      'data' => $this->t('Email'),
      'field' => 'office_email',
      'specifier' => 'office_email',
    ];

    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    $groups = troth_officer_office_groups();
    $row['office_name'] = $entity->getName();
    $row['office_type'] = $groups[$entity->getOfficeType()];
    $row['office_email'] = $entity->getEmail();

    return $row + parent::buildRow($entity);
  }

}
