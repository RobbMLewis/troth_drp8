<?php

namespace Drupal\troth_donate\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\commerce_cart\Event\CartEntityAddEvent;
use Drupal\commerce_cart\Event\CartEvents;
use Drupal\commerce_product\Entity\ProductVariation;

/**
 * Applies taxes to orders during the order refresh process.
 */
class CartEventSubscriber implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events = [
      CartEvents::CART_ENTITY_ADD => 'adjustDonationUnitPrice',
    ];
    return $events;
  }

  /**
   * {@inheritdoc}
   */
  public function adjustDonationUnitPrice(CartEntityAddEvent $event) {
    // First get all items in the cart.
    $items = $event->getCart()->getItems();

    // Go through the items.
    foreach ($items as $key => $item) {
      $item_type = $item->type->target_id;

      // We only want to work on donation product variations.
      if ($item_type == 'donation') {
        // We need to get the variation's default price.
        $variation = ProductVariation::load($item->getPurchasedEntityId());
        $default_price = $variation->getPrice();

        // We also need to get the quantity in the cart.
        $quantity = $item->getQuantity();

        // We only need to do something if quantity > 1.
        if ($quantity > 1) {
          // Get the unit price of what is in the cart.
          $unit_price = $item->getUnitPrice();

          // If the unit price is higher than devault price
          // Then we've done the adjustment once before.
          // Multiply by quantity - 1 and add to the unit price.
          if ($unit_price->greaterThan($default_price)) {
            $new_price = $default_price->multiply($quantity - 1);
            $new_price = $new_price->add($unit_price);
          }
          else {
            // We've never touched this, just multiply
            // by quantity.
            $new_price = $unit_price->multiply($quantity);
          }

          // Set unit price to new price and quantity to 1.
          $item->setUnitPrice($new_price, TRUE);
          $item->setQuantity(1);
        }
      }
    }
  }

}
