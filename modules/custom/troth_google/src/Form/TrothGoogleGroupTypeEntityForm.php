<?php

namespace Drupal\troth_google\Form;

use Drupal\Core\Entity\BundleEntityFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\user\Entity\Role;

/**
 * Form for the TrothGoolgeType entity.
 */
class TrothGoogleGroupTypeEntityForm extends BundleEntityFormBase {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $entity_type = $this->entity;

    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Group Name'),
      '#maxlength' => 255,
      '#default_value' => $entity_type->label(),
      '#description' => $this->t("Your Google Group's human readable name."),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $entity_type->id(),
      '#machine_name' => [
        'exists' => '\Drupal\troth_google\Entity\TrothGoogleGroupType::load',
      ],
      '#disabled' => !$entity_type->isNew(),
    ];

    $form['group_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Group ID'),
      '#maxlength' => 255,
      '#default_value' => $entity_type->getGroupId(),
      '#description' => $this->t("Your Group ID is usually what comes before the @ symbol in your group email address. For example, if your group email address is mygroup@googlegroups.com your group ID will be 'mygroup'"),
      '#required' => TRUE,
    ];

    $form['group_description'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Description'),
      '#default_value' => $entity_type->getDescription(),
      '#description' => $this->t('Please enter a short description of what this list is about. This will display on user registration and mailing list tab.'),
      '#required' => TRUE,
    ];

    $form['group_required'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Group is Required'),
      '#default_value' => $entity_type->getRequired(),
      '#description' => $this->t('If checked, the user will be required to subscribed to the list.'),
      '#required' => FALSE,
    ];

    $form['group_registration'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Add to Registration'),
      '#default_value' => $entity_type->getRegistration(),
      '#description' => $this->t('If checked, the list will be added to the registration form.'),
      '#required' => FALSE,
    ];

    $form['group_expired'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Keep When Expired'),
      '#default_value' => $entity_type->getExpired(),
      '#description' => $this->t('If checked, nobody will be forced off the list when their membership expires.'),
      '#required' => FALSE,
    ];

    $form['group_limit_role'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Limit by Role'),
      '#default_value' => $entity_type->getLimitRole(),
      '#description' => $this->t('If checked, members will be limited by roles.'),
      '#required' => FALSE,
    ];
    $roles = $entity_type->getRoles();
    if ($roles == '') {
      $roles = [];
    }

    $form['group_roles'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Roles'),
      '#default_value' => $roles,
      '#description' => $this->t('Check the roles are allowed to subscribe.'),
      '#required' => FALSE,
      '#states' => [
        'invisible' => [
          ':input[name="group_limit_role"]' => ['checked' => FALSE],
        ],
        'visible' => [
          ':input[name="group_limit_role"]' => ['checked' => TRUE],
        ],
      ],
    ];
    foreach (Role::loadMultiple() as $role) {
      /** @var \Drupal\user\RoleInterface $role */
      if ($role->id() !== Role::ANONYMOUS_ID && $role->id() !== Role::AUTHENTICATED_ID) {
        if (!$role->isAdmin()) {
          $form['group_roles']['#options'][$role->id()] = $role->label();
        }
      }
    }

    $form['group_message'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Message to send former members'),
      '#default_value' => $entity_type->getMessage(),
      '#description' => $this->t('This is the email that will be sent to people that are subscribed to the mailing list, but their subscribed email cannot be found in any accounts. Please use the tokens below to personalize the message.'),
      '#required' => FALSE,
    ];

    $form['token_help'] = [
      '#theme' => 'token_tree_link',
      '#token_types' => ['user', 'troth-user'],
      '#global_types' => FALSE,
      '#click_insert' => TRUE,
      '#collapsible' => TRUE,
      '#collapsed' => FALSE,
    ];

    return $this->protectBundleIdElement($form);
  }

  /**
   * {@inheritdoc}
   */
  public function buildEntity(array $form, FormStateInterface $form_state) {
    // Filter out unchecked roles.
    $form_state->setValue('group_roles', array_filter($form_state->getValue('group_roles')));
    return parent::buildEntity($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity_type = $this->entity;
    $entity_type->save();

    $form_display = \Drupal::entityTypeManager()
      ->getStorage('entity_form_display')
      ->load('profile.membership_join.default');
    if (count(troth_google_registration_mailing_lists()) > 0) {
      $form_display->setComponent('field_subscribe_mailing_lists', [
        'type' => 'options_buttons',
        'region' => 'content',
      ]);
    }
    else {
      $form_display->removeComponent('field_subscribe_mailing_lists');
    }
    $form_display->save();

    // Set permissions.
    $type_id = $entity_type->id();
    $bundle_of = $entity_type->getEntityType()->getBundleOf();
    $permissions = [
      "create $bundle_of $type_id",
      "view own $bundle_of $type_id",
      "edit own $bundle_of $type_id",
      "delete own $bundle_of $type_id",
    ];

    // Get roles, we don't want anonymous, authenticated, or admin.
    $roles = [];
    foreach (Role::loadMultiple() as $role) {
      /** @var \Drupal\user\RoleInterface $role */
      if ($role->id() !== Role::ANONYMOUS_ID && $role->id() !== Role::AUTHENTICATED_ID) {
        if (!$role->isAdmin()) {
          $roles[$role->id()] = $role->label();
        }
      }
    }

    // Check if the limit by role is checked.
    if ($entity_type->getLimitRole() == 0) {
      // We don't want to limit by role.
      // Grant permissions to Authenticated Members.
      $role_object = Role::load(Role::AUTHENTICATED_ID);
      foreach ($permissions as $perm) {
        $role_object->grantPermission($perm);
      }
      $role_object->save();
      $this->messenger()->addMessage($this->t('%role had the following permissions added: %perm', [
        '%role' => "Authenticated User",
        '%perm' => implode(',', $permissions),
      ]));

      // Revoke permission from Anonymous, just in case it was set.
      $role_object = Role::load(Role::ANONYMOUS_ID);
      foreach ($permissions as $perm) {
        $role_object->revokePermission($perm);
      }
      $role_object->save();
      $this->messenger()->addMessage($this->t('%role had the following permissions revoked: %perm', [
        '%role' => "Anonymous User",
        '%perm' => implode(',', $permissions),
      ]));

      // Revoke permissions from other roles that have it set other than Admin.
      foreach ($roles as $role => $name) {
        $role_object = Role::load($role);
        if (!$role_object->isAdmin()) {
          foreach ($permissions as $perm) {
            $role_object->revokePermission($perm);
          }
          $role_object->save();
          $this->messenger()->addMessage($this->t('%role had the following permissions revoked: %perm', [
            '%role' => $name,
            '%perm' => implode(',', $permissions),
          ]));
        }
      }
    }
    else {
      // We want to limit by role.
      // Revoke permissions for Authenticated users.
      $role_object = Role::load(Role::AUTHENTICATED_ID);
      foreach ($permissions as $perm) {
        $role_object->revokePermission($perm);
      }
      $role_object->save();
      $this->messenger()->addMessage($this->t('%role had the following permissions revoked: %perm', [
        '%role' => "Authenticated User",
        '%perm' => implode(',', $permissions),
      ]));

      // Revoke permissions for Anonymous users.
      $role_object = Role::load(Role::ANONYMOUS_ID);
      foreach ($permissions as $perm) {
        $role_object->revokePermission($perm);
      }
      $role_object->save();
      $this->messenger()->addMessage($this->t('%role had the following permissions revoked: %perm', [
        '%role' => "Anonymous User",
        '%perm' => implode(',', $permissions),
      ]));

      // Get allowed roles.
      $allowed_roles = [];
      // $entity_type->getRoles() returns role=>role, not role=>name.
      foreach ($entity_type->getRoles() as $role) {
        $allowed_roles[$role] = $roles[$role];
      }

      // Add permissions for those allowed.
      foreach ($allowed_roles as $role => $name) {
        $role_object = Role::load($role);
        if (!$role_object->isAdmin()) {
          foreach ($permissions as $perm) {
            $role_object->grantPermission($perm);
          }
          $role_object->save();
          $this->messenger()->addMessage($this->t('%role had the following permissions added: %perm', [
            '%role' => $name,
            '%perm' => implode(',', $permissions),
          ]));
        }
      }

      // Find those not allowed.
      $remove_roles = array_diff_assoc($roles, $allowed_roles);
      // Remove permissions for not allowed.
      foreach ($remove_roles as $role => $name) {
        $role_object = Role::load($role);
        if (!$role_object->isAdmin()) {
          foreach ($permissions as $perm) {
            $role_object->revokePermission($perm);
          }
          $role_object->save();
          $this->messenger()->addMessage($this->t('%role had the following permissions revoked: %perm', [
            '%role' => $name,
            '%perm' => implode(',', $permissions),
          ]));
        }
      }
    }

    // $this->postSave($entity_type, $this->operation);
    $this->messenger()->addMessage($this->t('Saved the %label profile type.', [
      '%label' => $this->entity->label(),
    ]));
    $form_state->setRedirectUrl($entity_type->toUrl('collection'));
  }

  /**
   * Form submission handler to redirect to Manage fields page of Field UI.
   */
  public function redirectToFieldUi(array $form, FormStateInterface $form_state) {
    if ($form_state->getTriggeringElement()['#parents'][0] === 'save_continue' && $route_info = FieldUI::getOverviewRouteInfo('profile', $this->entity->id())) {
      $form_state->setRedirectUrl($route_info);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function delete(array $form, FormStateInterface $form_state) {
    $form_state->setRedirect('entity.troth_google_type.delete_form', [
      'entity_type' => $this->entity->id(),
    ]);
  }

}
