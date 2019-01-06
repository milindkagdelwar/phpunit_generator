<?php

/**
 * Handles sending of email.
 *
 * PHP Version 5
 */

namespace Drupal\my_module\Plugin\QueueWorker;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Queue\QueueWorkerBase;
use Drupal\Core\Mail\MailManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 *
 * @inheritdoc
 */
class EmailEventBase extends QueueWorkerBase implements ContainerFactoryPluginInterface {

  /**
   *
   * @var Drupal\Core\Mail\MailManager
   */
  protected $mail;
  /**
   * constructor
   */
  public function __construct(MailManager $mail) {
    $this->mail = $mail;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $container->get('plugin.manager.mail')
    );
  }

  /**
   * Processes a single item of Queue.
   *
   */
  public function processItem($data) {
    $params['subject'] = t('query');
    $params['message'] = $data->query;
    $params['from'] = $data->email;
    $params['username'] = $data->username;
    $to = \Drupal::config('system.site')->get('mail');
    $this->mail->mail('my_module','query_mail',$to,'en',$params,NULL,true);
  }
}