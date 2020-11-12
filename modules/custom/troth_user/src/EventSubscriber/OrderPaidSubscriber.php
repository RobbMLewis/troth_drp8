<?php

namespace Drupal\troth_user\EventSubscriber;

use Drupal\troth_google\Entity\TrothGoogleGroup;
use Drupal\troth_google\Entity\TrothGoogleGroupType;
use Drupal\commerce_order\Event\OrderEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\user\Entity\User;

/**
 * Class OrderCompleteSubscriber.
 *
 * @package Drupal\troth_user
 */
class OrderPaidSubscriber implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events = [
      'commerce_order.order.paid' => 'trothOnPaid',
    ];
    return $events;
  }

  /**
   * Places the order after it has been fully paid through an off-site gateway.
   *
   * @param \Drupal\commerce_order\Event\OrderEvent $event
   *   The event.
   */
  public function trothOnPaid(OrderEvent $event) {
    // Get order items.
    $order_items = $order->getItems();
    foreach ($order_items as $line_item) {
      $product_variation = $line_item->getPurchasedEntity();
      $years = intval($line_item->getQuantity());
      if (isset($product_variation->field_inmate)) {
        $inmate = $product_variation->field_inmate->getValue()[0]['value'];
      }
      else {
        $inmate = 0;
      }
      $membershipType = $product_variation->field_membership_type->getValue()[0]['value'];

      if ($product_variation->getOrderItemTypeId() == 'membership_join') {
        $familyId = 0;
        $control = 'self';
        /*
         * We need to add in a loop over all profiles here
         * so that we can get the family members entered.
         */
        $profileIds = $line_item->field_membership_join->getValue();
        foreach ($profileIds as $profileId) {
          $profile = \Drupal::entityTypeManager()->getStorage('profile')->load($profileId['target_id']);

          if ($inmate == 1) {
            $inmateNo = $profile->field_inmate_number->getValue()[0]['value'];
          }
          else {
            $inmateNo = NULL;
          }
          $fieldData = [
            'mail' => $profile->field_email->value,
            'name' => $profile->field_preferred_username->value,
          // We pass years instead of dates.
          // Dates handled in account creation function.
            'years' => $years,
            'field_user_facebook_url' => $profile->field_user_facebook_url->getValue()[0],
            'field_user_instagram_url' => $profile->field_user_instagram_url->getValue()[0],
            'field_user_linkedin_url' => $profile->field_user_linkedin_url->getValue()[0],
            'field_user_twitter_url' => $profile->field_user_twitter_url->getValue()[0],
            'field_profile_location' => $profile->address->getValue()[0],
            'field_profile_first_name' => $profile->field_profile_first_name->value,
            'field_profile_last_name' => $profile->field_profile_last_name->value,
            'field_profile_troth_name' => $profile->field_profile_troth_name->value,
            'field_telephone_number' => $profile->field_telephone_number->value,
            'field_profile_birth_date' => $profile->field_profile_birth_date->value,
            'field_profile_member_notes' => $profile->field_profile_member_notes->getValue()[0],
            'field_profile_family_id' => $familyId,
            'field_profile_membership_control' => $control,
            'field_profile_membership_type' => $membershipType,
            'field_profile_expire_issue' => \Drupal::config('troth_user.adminsettings')->get('idunna_issue') + (4 * intval($line_item->getQuantity())),
            'field_profile_membership_status' => 'active',
            'field_profile_visibility' => 0,
          // We need to flip the boolean as the variation has
          // 0=electronic, 1=paper.
            'field_profile_e_copies' => !$product_variation->field_paper_idunna->getValue()[0]['value'],
            'field_profile_inmate' => $inmate,
            'field_profile_inmate_number' => $inmateNo,
          ];

          $account = $this->createUpdateUser($fieldData);
          // Remove the member role for subscribers.
          // Add subscriber role for subscribers.
          if ($membershipType == 'subscription') {
            $account->removeRole('member');
            $account->addRole('subscriber_only');
            $account->save();
          }

          // We need to save the acceptance of code of conduct.
          if ($profile->field_code_of_conduct->value) {
            $conditions = legal_get_conditions();
            legal_save_accept($conditions['version'], $conditions['revision'], $conditions['language'], $account->get('uid')->getString());
          }
          // We now need to update the family ID and control for
          // family memberships.
          if ($membershipType == 'family') {
            if ($control == 'self') {
              $control = 'primary';
            }
            elseif ($control == 'primary') {
              $control = 'secondary';
            }
            else {
              $control = 'child';
            }
            if ($familyId == 0) {
              $familyId = $account->id();
            }
            $account->set('field_profile_family_id', $familyId);
            $account->set('field_profile_membership_control', $control);
            $account->save();
          }
          if (\Drupal::moduleHandler()->moduleExists('troth_google') && isset($profile->field_subscribe_mailing_lists)) {
            // We want to get all bundles they could have subscribed to.
            $tosub = [];
            foreach ($profile->field_subscribe_mailing_lists->getValue() as $k => $v) {
              $tosub[] = $v['value'];
            }

            // Create all google entities they can have.
            $types = \Drupal::service('entity_type.bundle.info')->getBundleInfo('troth_google');
            foreach ($types as $bundle => $data) {
              $type = TrothGoogleGroupType::load($bundle);
              // Check permissions of *account*.  if they can edit we display.
              $perm = "edit own troth_google " . $bundle;
              if ($account->hasPermission($perm)  && $account->field_profile_ban_lists->value == 0) {
                // We need to create an entity for the user.
                $entity = TrothGoogleGroup::create([
                  'bundle' => $bundle,
                  'uid' => $account->id(),
                  'email' => $account->getEmail(),
                  'subscribed' => 0,
                ]);
                // Subscribe required.
                if ($type->getRequired() || in_array($bundle, $tosub)) {
                  // New member needs to be subscribed to a required list
                  // Or they requested subscription to the list.
                  $entity->trothGoogleSubscribe();
                  $entity->setSubscribed(1);
                }
                $entity->save();
              }
            }
          }
        }
      }
      if ($product_variation->getOrderItemTypeId() == 'membership_renew') {
        // There should only be one profile, get all values.
        $profileId = $line_item->field_membership_renew->getValue()[0]['target_id'];
        $profile = \Drupal::entityTypeManager()->getStorage('profile')->load($profileId);
        $orderUid = $order->uid->getString();
        $renewMail = $profile->field_renew_email->value;
        $renewUid = $profile->field_member_id->value;
        $renewSelf = $profile->field_renew_self->value;
        $renewUsername = $profile->field_preferred_username->value;

        // Figure out the UID of the user renewing.
        $renewaluid = '';
        if ($renewSelf == 1) {
          $renewaluid = $orderUid;
        }
        elseif ($renewUid != '') {
          $renewaluid = $renewUid;
        }
        elseif ($renewUsername != '') {
          $query = \Drupal::entityQuery('user')
            ->condition('name', $renewUsername, 'like');
          $uids = $query->execute();
          $renewaluid = reset($uids);
        }
        elseif ($renewMail != '') {
          $query = \Drupal::entityQuery('user');
          $or = $query->orConditionGroup()
            ->condition('mail', $renewMail, 'like')
            ->condition('field_profile_alt_email', $renewMail, 'like');
          $query->condition($or);
          $uids = $query->execute();
          $renewaluid = reset($uids);
        }
        $account = User::load($renewaluid);
        // Get current membership type.
        $mtype = $account->field_profile_membership_type->value;
        $otype = $product_variation->field_membership_type->value;
        $oldfamid = $account->field_profile_family_id->value;

        // If otype is single, make sure family stuff is not set.
        if ($otype == 'single' || $otype == 'inmate' || $otype == 'subscription') {
          $fieldData = [
            'years' => intval($line_item->getQuantity()),
            'field_profile_membership_status' => 'active',
            'field_profile_family_id' => 0,
            'field_profile_membership_control' => 'self',
            'field_profile_membership_type' => $otype,
          // We need to flip the boolean as the variation has
          // 0=electronic, 1=paper.
            'field_profile_e_copies' => !$product_variation->field_paper_idunna->getValue()[0]['value'],
          ];
          $account = $this->createUpdateUser($fieldData, $account);
          // Remove the member role for subscribers.
          // Add subscriber role for subscribers.
          if ($otype == 'subscription') {
            $account->removeRole('member');
            $account->addRole('subscriber_only');
            $account->save();
          }
          // We need to save the acceptance of code of conduct.
          if ($profile->field_code_of_conduct->value) {
            $conditions = legal_get_conditions();
            legal_save_accept($conditions['version'], $conditions['revision'], $conditions['language'], $account->get('uid')->getString());
          }
        }
        elseif ($otype == 'family') {
          // We need to get the member's family ID
          // and update all family members.
          $famid = $account->field_profile_family_id->value;
          if ($famid == 0) {
            // This is a convert, so we only update them.
            $famid = $account->id();
            $uids[$famid] = $famid;
          }
          else {
            // Find everyone with that family id.
            $query = \Drupal::entityQuery('user')
              ->condition('field_profile_family_id', $famid, '=');
            $uids = $query->execute();
          }
          // We go through each family member.
          foreach ($uids as $uid) {
            $account = User::load($uid);
            // Take care of converts and set them to primary.
            $control = $account->field_profile_membership_control->value;
            if ($control == 'self') {
              $control = 'primary';
            }

            $fieldData = [
              'years' => intval($line_item->getQuantity()),
              'field_profile_membership_status' => 'active',
              'field_profile_family_id' => $famid,
              'field_profile_membership_control' => 'primary',
              'field_profile_membership_type' => 'family',
            // We need to flip the boolean as the variation has
            // 0=electronic, 1=paper.
              'field_profile_e_copies' => !$product_variation->field_paper_idunna->getValue()[0]['value'],
            ];
            $account = $this->createUpdateUser($fieldData, $account);
            // We need to save the acceptance of code of conduct.
            if ($profile->field_code_of_conduct->value) {
              $conditions = legal_get_conditions();
              legal_save_accept($conditions['version'], $conditions['revision'], $conditions['language'], $account->get('uid')->getString());
            }
          }
        }
        $to = [];
        if ($mtype != $otype) {
          // We need to email people to notify.
          $message = t("A member has renewed with changing their membership type from @old to @new.  They have confirmed on checkout that this is their desire.\n\n",
            [
              '@old' => $otype,
              '@new' => $mtype,
            ]);
          if (($mtype == 'single' || $mtype == 'inmate' || $mtype == 'subscription') && $otype == 'family') {
            $message .= t("The membership has been updated to a Family membership, and the member has been set as the controlling member.  There are no other members in their family.  Please get in contact to get the new members entered (reply all).");
          }
          elseif ($mtype == 'family' && ($otype == 'single' || $otype == 'inmate' || $otype == 'subscription')) {
            // Find everyone with that family id.
            $query = \Drupal::entityQuery('user')
              ->condition('field_profile_family_id', $oldfamid, '=');
            $uids = $query->execute();
            $message .= t("The memberhip has been updated to a @type membership, and the member has had the family membership information removed.  The other members of the family may need to have their control settings updated.  Please look at member ID's @ids.",
              [
                '@ids' => implode(',', $uids),
                '@type' => $otype,
              ]);
          }
          $to[] = 'clerk@thetroth.org';
        }
        else {
          // No Change, We just need to let the member know it processed.
          $message = t('Thank you for your renewal.  It has been processed and your expire date and Idunna issues have been updated.  If you have any questions, please reach out to the clerk at clerk@thetroth.org.');
        }
        $renewalaccount = User::load($renewaluid);
        $langcode = $renewalaccount->getPreferredLangcode();
        $to[] = $renewalaccount->mail->value;
        $to = implode(',', $to);
        $param['sub'] = "Troth Membership Renewal";
        $param['message'] = $message;
        $param['from'] = 'clerk@thetroth.org';
        $result = $mailManager->mail('troth_user', 'troth_mail', $to, $langcode, $param, NULL, TRUE);
        if ($result['result'] != TRUE) {
          \Drupal::logger('troth_user')->error('Renewal Email did not send to %uid. %to, %message', [
            '%uid' => $renewaluid,
            '%to' => $to,
            '%message' => $param['message'],
          ]);
        }
        else {
          \Drupal::logger('troth_user')->notice('Renewal Email sent to %uid. %to, %message', [
            '%uid' => $renewaluid,
            '%to' => $to,
            '%message' => $param['message'],
          ]);
        }
      }
    }
  }

  /**
   * Create or update user.
   */
  private function createUpdateUser($fieldData, $account = NULL) {
    if (is_null($account)) {
      $mail = $fieldData['mail'];
      $account = user_load_by_mail($mail);
    }
    if ($account) {
      $years = intval($fieldData['years']);
      unset($fieldData['years']);
      foreach ($fieldData as $k => $v) {
        $account->$k = $v;
      }
      $idunna = $account->field_profile_expire_issue->value + (4 * $years);
      $defIdunna = \Drupal::config('troth_user.adminsettings')->get('idunna_issue') + (4 * $years);
      if ($defIdunna > $idunna) {
        $idunna = $defIdunna;
      }

      // The email exists for the user, we just need to update the expire date
      // by the quantity # of years.
      $now = new DrupalDateTime();
      $graceWeeks = \Drupal::config('troth_user.adminsettings')->get('grace_period');
      $graceDate = new DrupalDateTime('-' . $graceWeeks . ' weeks');

      $expireDate = new DrupalDateTime($account->field_profile_member_expire_date->value);
      if ($expireDate < $graceDate) {
        $anotes = $account->field_profile_admin_notes->value;
        $oldStart = new DrupalDateTime($account->field_profile_member_start_date->value);
        $anotes .= "\nMember renewed after grace period.  Start date changed from " . $oldStart->format('Y-m-d') . " to " . $now->format('Y-m-d');
        $account->field_profile_admin_notes = $anotes;
        $expireDate = new DrupalDateTime();
        $idunna = $defIdunna;
        $account->field_profile_member_start_date = $now->format('Y-m-d');
      }
      $expireDate->modify('+' . $years . ' years');
      $account->field_profile_member_expire_date = $expireDate->format('Y-m-d');
      $account->field_profile_last_renew_date = $now->format('Y-m-d');
      $account->field_profile_expire_issue = $idunna;
      $account->save();
      \Drupal::logger('troth_user')->notice("Membership extended for " . $account->name->value . " by " . $years . " years.");
    }
    else {
      $years = $fieldData['years'];
      unset($fieldData['years']);
      $joinDate = new DrupalDateTime();
      $expireDate = new DrupalDateTime("+$years years");
      $fieldData['field_profile_member_start_date'] = $joinDate->format('Y-m-d');
      $fieldData['field_profile_last_renew_date'] = $joinDate->format('Y-m-d');
      $fieldData['field_profile_member_expire_date'] = $expireDate->format('Y-m-d');
      $fieldData['status'] = 1;
      $account = entity_create('user', $fieldData);
      $account->save();
      _user_mail_notify('register_no_approval_required', $account);
      \Drupal::logger('troth_user')->notice("User account created for " . $fieldData['mail'] . "for " . $years . " years.");
    }
    $account->addRole('member');
    $account->save();
    return $account;
  }

}
