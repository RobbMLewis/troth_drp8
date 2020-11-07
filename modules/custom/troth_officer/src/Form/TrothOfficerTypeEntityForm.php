<?php

namespace Drupal\troth_officer\Form;

use Drupal\Core\Url;
use Drupal\Core\Entity\BundleEntityFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\contact\Entity\ContactForm;
use Drupal\menu_link_content\Entity\MenuLinkContent;
use Drupal\user\Entity\Role;

/**
 * Form for the TrothOfficerType entity.
 */
class TrothOfficerTypeEntityForm extends BundleEntityFormBase {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $user = \Drupal::currentUser();
    $admin = 0;
    if ($user->hasPermission('access administration pages')) {
      $admin = 1;
    }

    $groups = troth_officer_office_groups();
    if (count($groups) == 0) {
      $reddirectUrl = Url::fromRoute('troth_officer.admin_settings_form');
      $reddirectUrl->setOptions(['absolute' => TRUE, 'https' => TRUE]);
      drupal_set_message($this->t('There are no office groups available.  <a href=":url">Create one first.</a>', [':url' => $reddirectUrl->toString()]), 'error');
    }

    $form = parent::form($form, $form_state);

    $entity_type = $this->entity;

    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Office Name'),
      '#maxlength' => 255,
      '#default_value' => $entity_type->getName(),
      '#description' => $this->t("The human readable name of the office."),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $entity_type->id(),
      '#machine_name' => [
        'exists' => '\Drupal\troth_officer\Entity\TrothOfficerType::load',
      ],
      '#disabled' => !$entity_type->isNew(),
    ];

    $form['office_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Office ID'),
      '#maxlength' => 255,
      '#default_value' => $entity_type->getOfficeId(),
      '#description' => $this->t("The shortened name for the office, eg VP for Vice President"),
      '#required' => TRUE,
    ];

    $form['office_type'] = [
      '#type' => 'select',
      '#title' => $this->t('Office Group'),
      '#options' => $groups,
      '#default_value' => $entity_type->getOfficeType(),
      '#description' => $this->t("The group of offices this office belongs to, eg BOD for the board of directors."),
      '#required' => TRUE,
    ];
    if (count($groups) == 1) {
      $form['office_type']['#disabled'] = TRUE;
      $form['office_type']['#description'] = $this->t("The group of offices this office belongs to, eg BOD for the board of directors.  If this is not the right grouping, create a new one.");
      if (!$entity_type->getOfficeType()) {
        $form['office_type']['#default_value'] = array_key_first($groups);
      }
    }
    $form['office_email'] = [
      '#type' => 'email',
      '#title' => $this->t('Email Address'),
      '#default_value' => $entity_type->getEmail(),
      '#description' => $this->t('The email address for this office.  This may be left blank.'),
      '#required' => FALSE,
    ];

    $form['office_description'] = [
      '#type' => 'text_format',
      '#title' => $this->t('Description'),
      '#default_value' => $entity_type->getDescription() ? $entity_type->getDescription()['value'] : '',
      '#format' => $entity_type->getDescription() ? $entity_type->getDescription()['value'] : 'basic_html',
      '#description' => $this->t('Please enter the description of this office.  This should be from the bylaws if possible as it will be used on both the officer page and elections (if enabled).'),
      '#required' => TRUE,
    ];

    $form['office_term'] = [
      '#type' => 'number',
      '#title' => $this->t('Term Length'),
      '#step' => 1,
      '#min' => 0,
      '#max' => 3,
      '#default_value' => $entity_type->getTerm() ?: 2,
      '#description' => $this->t('How many years is the term for this office?'),
      '#required' => FALSE,
    ];

    $roles = $entity_type->getRoles();
    if ($roles == '') {
      $roles = [];
    }

    $form['office_roles'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Roles to Apply'),
      '#default_value' => $roles,
      '#description' => $this->t('The roles that automatically get applied to this office.'),
      '#required' => FALSE,
    ];

    foreach (Role::loadMultiple() as $role) {
      /** @var \Drupal\user\RoleInterface $role */
      if ($role->id() !== Role::ANONYMOUS_ID && $role->id() !== Role::AUTHENTICATED_ID) {
        $form['office_roles']['#options'][$role->id()] = $role->label();
      }
    }

    if (\Drupal::moduleHandler()->moduleExists('troth_elections')) {
      $form['office_open'] = [
        '#type' => 'checkbox',
        '#title' => $this->t('Office Open'),
        '#default_value' => $entity_type->getOpen(),
        '#description' => $this->t('If checked, this office will show up in the elections.'),
        '#required' => FALSE,
      ];

      $form['office_number_open'] = [
        '#type' => 'number',
        '#title' => $this->t('Number Open'),
        '#step' => 1,
        '#min' => 0,
        '#max' => 10,
        '#default_value' => $entity_type->getNumOpen() ?: 0,
        '#description' => $this->t('Enter the number of positions currently open.  This will be used for elections.'),
        '#required' => FALSE,
        '#states' => [
          'invisible' => [
            ':input[name="office_open"]' => ['checked' => FALSE],
          ],
          'visible' => [
            ':input[name="office_open"]' => ['checked' => TRUE],
          ],
        ],
      ];
    }

    // hide/prevent editing of fields if it's the EO.
    if ($admin == 0) {
      $form['label']['#attributes'] = ['readonly' => 'readonly'];
      $form['label']['#disabled'] = TRUE;
      $form['id']['#type'] = 'hidden';
      $form['office_id']['#type'] = 'hidden';
      $form['office_type']['#type'] = 'hidden';
      $form['office_email']['#type'] = 'hidden';
    }
    return $this->protectBundleIdElement($form);
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity_type = $this->entity;
    $status = $entity_type->save();
    $email = $entity_type->getEmail();
    $cf_bundle = $entity_type->id();
    $contact_form = \Drupal::entityTypeManager()
      ->getStorage('contact_form')
      ->load($cf_bundle);

    if ($email == NULL || $email == '') {
      if ($contact_form != '') {
        $contact_form->delete();
      }
    }
    elseif ($contact_form == NULL || $contact_form == '') {
      $contact_form = ContactForm::create([
        'id' => $entity_type->id(),
        'label' => $cf_bundle,
        'recipients' => [$email],
        'message' => $this->t('Your email has been sent.'),
        'redirect' => '',
      ]);
      $contact_form->save();
    }
    else {
      $contact_form->setRecipients([$email]);
      $contact_form->save();
    }

    // Create the menu link.
    $groups = troth_officer_office_groups();
    $path = 'internal:/about/leadership/' . $groups[$entity_type->getOfficeType()] . '/' . $entity_type->id() . '.html';
    $query = \Drupal::entityQuery('menu_link_content')
      ->condition('link.uri', $path)
      ->condition('menu_name', 'main');

    $result = $query->execute();
    $menu_link_id = (!empty($result)) ? reset($result) : FALSE;
    if (!$menu_link_id) {
      $parentpath = 'internal:/about/leadership/' . $groups[$entity_type->getOfficeType()] . 'html';
      $query = \Drupal::entityQuery('menu_link_content')
        ->condition('link.uri', $parentpath)
        ->condition('menu_name', 'main');

      $result = $query->execute();
      $menu_link_id = (!empty($result)) ? reset($result) : FALSE;
      $parent = MenuLinkContent::load($menu_link_id);
      $menu_link = MenuLinkContent::create([
        'title' => $entity_type->getName(),
        'link' => ['uri' => $path],
        'menu_name' => 'main',
        'parent' => 'menu_link_content:' . $parent->uuid(),
        'expanded' => TRUE,
        'weight' => 0,
      ]);
      $menu_link->save();
    }

    $message_params = [
      '%name' => $entity_type->getName(),
      '%officer_entity_id' => $entity_type->getEntityType()->getBundleOf(),
    ];

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %name %officer_entity_id entity type.', $message_params));
        break;

      default:
        drupal_set_message($this->t('Saved the %name %officer_entity_id entity type.', $message_params));
    }

    $form_state->setRedirectUrl($entity_type->toUrl('collection'));
  }

}
