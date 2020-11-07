<?php

namespace Drupal\troth_google\EventSubscriber;

use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Event subscriber for Troth_Google.
 */
class TrothGoogleSubscriber implements EventSubscriberInterface {

  /**
   * Check for Google OAuth Code and save tokens.
   */
  public function checkGoogleOauthCode(GetResponseEvent $event) {
    if ($event->getRequest()->query->get('code')) {
      $code = $event->getRequest()->query->get('code');
      \Drupal::state()->set('troth_google_oauth_code', $code);

      // We need the client_secret.json.  Check if it exists.
      // if it doesn't exist, create it from the config variable.
      $client_secret = "private://oauth/client_secret.json";
      if (!file_exists($client_secret)) {
        // File does not exist, create it.
        $client_secret_json = \Drupal::config('troth_google.adminsettings')->get('oauth_client_secret');
        $dir = "private://oauth/";
        file_prepare_directory($dir, FILE_CREATE_DIRECTORY);
        $path = $dir . "client_secret.json";
        $file = file_save_data($client_secret_json, $path, FILE_EXISTS_REPLACE);
        if (!is_object($file)) {
          \Drupal::logger('troth_gogle')->error('Client_secret.json could not be saved to @path', ['@path' => $path]);
          return;
        }
      }

      // We have a client file, lets authorize!
      $client = new \Google_Client();
      $reddirectUrl = Url::fromRoute('troth_google.admin_settings_form');
      $reddirectUrl->setOptions(['absolute' => TRUE, 'https' => TRUE]);
      // Offline access.
      $client->setAccessType("offline");
      $client->setScopes([
        'https://www.googleapis.com/auth/admin.directory.group',
        'https://www.googleapis.com/auth/admin.directory.group.member',
      ]);
      $client->setRedirectUri($reddirectUrl->toString());
      $client_secret_path = \Drupal::service('file_system')->realpath($client_secret);
      $client->setAuthConfig($client_secret_path);

      try {
        $token_array = $client->authenticate($code);
        // If successfull, save the token and the refresh token.
        \Drupal::state()->set('troth_google_access_token', $token_array['access_token']);
        \Drupal::state()->set('troth_google_refresh_token', $token_array['refresh_token']);
        return new RedirectResponse($reddirectUrl);
      }
      catch (Exception $e) {
        \Drupal::logger('troth_gogle')->error('An error occured: @error', ['@error' => $e->getMessage()]);
        return new RedirectResponse($reddirectUrl);
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[KernelEvents::REQUEST][] = ['checkGoogleOauthCode'];
    return $events;
  }

}
