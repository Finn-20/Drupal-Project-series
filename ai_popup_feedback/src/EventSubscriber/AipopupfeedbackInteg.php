<?php 

namespace Drupal\ai_popup_feedback\EventSubscriber;

use \Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Drupal\Core\Session\UserSession;
use Drupal\Core\Session\AccountInterface;
use Drupal\user\Entity\User;
use Drupal\user\UserInterface;
use Drupal\user\Entity\Role;
use Drupal\user\RoleInterface;
use Drupal\simple_popup_blocks\SimplePopupBlocksStorage;

class AipopupfeedbackInteg implements EventSubscriberInterface {
 
  /** @var int */
	public function AipopupLoad(GetResponseEvent  $event) {
		
	} 
 
	public static function getSubscribedEvents() {
		$events[KernelEvents::REQUEST][] = array('AipopupLoad');
		return $events;
	}
}
