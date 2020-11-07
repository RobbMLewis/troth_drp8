<?php

namespace Drupal\troth_donate\Plugin\Commerce\CheckoutPane;

use Drupal\commerce_product\Entity\Product;
use Drupal\commerce_order\Entity\OrderItem;
use Drupal\commerce_price\Price;
use Drupal\commerce_product\Entity\ProductVariation;
use Drupal\commerce_store\Entity\Store;
use Drupal\commerce_product\Entity\ProductType;
use Drupal\commerce_checkout\Plugin\Commerce\CheckoutPane\CheckoutPaneBase;
use Drupal\commerce_checkout\Plugin\Commerce\CheckoutPane\CheckoutPaneInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides the donation pane.
 *
 * @CommerceCheckoutPane(
 *   id = "troth_donation",
 *   label = @Translation("Donation"),
 *   default_step = "order_information",
 *   wrapper_element = "fieldset",
 * )
 */
class TrothDonation extends CheckoutPaneBase implements CheckoutPaneInterface {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'donation_product' => '',
    ] + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationSummary() {
    if ($this->configuration['donation_product']) {
      $summary = $this->t('The current donation product is: @prod', ['@prod' => $this->configuration['donation_product']]);
    }
    else {
      $summary = $this->t('No default product set.  Please add one.');
    }

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildConfigurationForm($form, $form_state);
    $product_types = ProductType::loadMultiple();
    $products = [];
    foreach ($product_types as $type) {
      $products[$type->id()] = $type->label();
    }
    if (count($products) > 0) {
      $form['donation_product'] = [
        '#type' => 'select',
        '#title' => $this->t('Product that is the donation product'),
        '#options' => $products,
        '#default_value' => $this->configuration['donation_product'],
      ];
    }
    else {
      $form['item'] = [
        '#type' => 'item',
        '#markup' => $this->t('There are no products.  Create a product first.'),
      ];
    }
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    parent::submitConfigurationForm($form, $form_state);

    if (!$form_state->getErrors()) {
      $values = $form_state->getValue($form['#parents']);
      $this->configuration['donation_product'] = $values['donation_product'];
    }
  }

  /**
   * {@inheritdoc}
   */
  public function isVisible() {
    // Hide the pane if there's already a donation order item?
    $order_item = $this->getOrderItem();
    if ($order_item) {
      return FALSE;
    }
    elseif (count($this->getProductVariations()) > 0) {
      return TRUE;
    }
    else {
      return FALSE;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function buildPaneForm(array $pane_form, FormStateInterface $form_state, array &$complete_form) {
    $variations = ['none' => 'No Donation'] + $this->getProductVariations();
    $store = Store::load($this->order->getStoreId());
    $currency = $store->getDefaultCurrency();
    $currency_symbol = $currency->getSymbol();

    $predefined_amounts = [
      '5' => $currency_symbol . '5',
      '15' => $currency_symbol . '15',
      '25' => $currency_symbol . '25',
      '40' => $currency_symbol . '40',
      '50' => $currency_symbol . '50',
    ];

    $pane_form['fund'] = [
      '#type' => 'select',
      '#title' => $this->t('What fund do you wish to donate to?'),
      '#description' => $this->t('We would greatly appreciate your donation and direct it to the purpose you specify.'),
      '#options' => $variations,
      '#default_value' => 'none',
    ];

    $pane_form['donation'] = [
      '#type' => 'fieldset',
      '#states' => [
        'invisible' => [
          ':input[name="troth_donation[fund]"]' => ['value' => 'none'],
        ],
      ],
    ];
    $pane_form['donation']['amount'] = [
      '#type' => 'select_or_other_buttons',
      '#title' => t('Amount I would like to Donate'),
      '#options' => $predefined_amounts,
      '#default_value' => 5,
      '#required' => TRUE,
    ];

    return $pane_form;
  }

  /**
   * {@inheritdoc}
   */
  public function validatePaneForm(array &$pane_form, FormStateInterface $form_state, array &$complete_form) {
    $values = $form_state->getValue($pane_form['#parents']);
    $amount = $values['donation']['amount'][0];
    if (!is_numeric($amount)) {
      $form_state->setError($pane_form['donation']['amount'], t('The amount must be a valid number.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitPaneForm(array &$pane_form, FormStateInterface $form_state, array &$complete_form) {
    // Get the fund they are donating to.
    $values = $form_state->getValue($pane_form['#parents']);
    $fund = $values['fund'];
    if ($fund == 'none' || $fund == NULL) {
      // They didn't choose one, return.
      return;
    }

    // Load the product variation.
    $variation = ProductVariation::load($fund);

    // Generate the proper unit price and set.
    $store = Store::load($this->order->getStoreId());
    $currency = $store->getDefaultCurrencyCode();
    $unit_price = new Price(1, $currency);
    $unit_price = $unit_price->multiply($values['donation']['amount'][0]);

    // Create the order item.
    $order_item = OrderItem::create([
      'type' => $variation->getOrderItemTypeId(),
      'purchased_entity' => $fund,
      'quantity' => 1,
      'unit_price' => $unit_price,
      'overridden_unit_price' => TRUE,
    ]);

    // Save the order item and add to the order.
    $order_item->save();
    $this->order->addItem($order_item);
  }

  /**
   * Gets the donation order item.
   *
   * If one isn't found, it will be created.
   *
   * @return \Drupal\commerce_order\Entity\OrderItemInterface
   *   The donation order item.
   */
  protected function getOrderItem() {
    $donation_order_item = NULL;
    // Try to find an existing order item.
    foreach ($this->order->getItems() as $order_item) {
      if ($order_item->bundle() == $this->configuration['donation_product']) {
        $donation_order_item = $order_item;
        break;
      }
    }

    return $donation_order_item;
  }

  /**
   * Gets the donation product variations.
   *
   * If none are found, nothing is returned.
   *
   * @return array
   *   An array of product variations.
   */
  protected function getProductVariations() {
    // Get prodcuts for this store.
    $query = \Drupal::entityQuery('commerce_product')
      ->condition('type', $this->configuration['donation_product'])
      ->condition('status', 1)
      ->condition('stores', $this->order->getStoreId(), 'IN');
    $results = $query->execute();
    $entity_manager = \Drupal::entityManager();
    $vars = [];
    foreach ($results as $prod) {
      $product = Product::load($prod);
      $vars += $product->getVariationIds();
    }
    $allVariations = $entity_manager->getStorage('commerce_product_variation')->loadMultiple($vars);
    $variations = [];
    foreach ($allVariations as $variation) {
      if ($variation->isActive()) {
        $variations[$variation->id()] = $variation->getTitle();
      }
    }
    return $variations;
  }

}
