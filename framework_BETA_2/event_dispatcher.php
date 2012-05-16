<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

// xxx params to become default params and on dispatch provide the ability to override
// default params with vars passed by reference

class Event_Dispatcher {
  private static $_events = array();
  private static $_eventsTriggered = array();

  /**
  * Add an observer to the event dispatcher
  *
  * Example: System::attachObserver(System_Event::ready, 'Hook::init');
  *
  * @param string event
  * @param array observable class
  * @return boolean
  */
  public static function attachObserver ($event, $observable) {
    // xxx asserts
  
    // normalise the callback for comparison
    if (is_array($observable) == TRUE) {
      $observable = implode('::', $observable);
    }

    self::$_events[$event][$observable] = $observable;

    return TRUE;
  }

  /**
  * Add an observer to an event queue, before a given observer.
  *
  * @param string event
  * @param array existing observer
  * @param array new observer
  * @return boolean
  */
  public static function attachObserverBefore ($event, $existing, $observable) {
    // xxx asserts
  
    if (empty(self::$_events[$event]) == TRUE || array_key_exists($existing, self::$_events[$event]) == FALSE) {
      // Just add the observer if there are no events or the existing observer doesn't exist
      return self::attachObserver($event, $observable);
    }
    else {
      $newObserver = array($observable => $observable);
      $position = array_search($existing, array_keys(self::$_events[$event]), TRUE);
      $initialObservers = array_splice(self::$_events[$event], 0, $position);
      self::$_events[$event] = array_merge($initialObservers, $newObserver, self::$_events[$event]);

      return TRUE;
    }
  }

  static public function attachObserverAtPosition ($event, $position, $observable) {
    if (empty(self::$_events[$event]) == TRUE) {
      // Just add the observer if there are no events or the existing observer doesn't exist
      return self::attachObserver($event, $observable);
    }
    elseif ($position == 0) {
      self::$_events[$event] = array_reverse(self::$_events[$event]);
      self::attachObserver($event, $observable);
      self::$_events[$event] = array_reverse(self::$_events[$event]);
      return TRUE;
    }
    elseif ($position >= count(self::$_events[$event])) {
      return self::attachObserver($event, $observable);
    }
    else {
      $newObserver = array($observable => $observable);
      $initialObservers = array_splice(self::$_events[$event], 0, $position);
      self::$_events[$event] = array_merge($initialObservers, $newObserver, self::$_events[$event]);

      return TRUE;
    }
  }

  /**
  * Add an observer to an event, after a given observer.
  *
  * @param string event
  * @param array existing observer
  * @param array new observer
  * @return boolean
  */
  public static function attachObserverAfter ($event, $existing, $observable) {
    // xxx asserts
  
    if (empty(self::$_events[$event]) == TRUE || array_key_exists($existing, self::$_events[$event]) == FALSE) {
      // Just add the observer if there are no events or the existing observer doesn't exist
      return self::attachObserver($event, $observable);
    }
    else {
      $newObserver = array($observable => $observable);
      $position = array_search($existing, array_keys(self::$_events[$event]), TRUE) + 1;
      $initialObservers = array_splice(self::$_events[$event], 0, $position);
      self::$_events[$event] = array_merge($initialObservers, $newObserver, self::$_events[$event]);

      return TRUE;
    }
  }

  /**
  * Get all observers for an event.
  *
  * @param string event
  * @return array
  */
  public static function listObservers ($event) {
    // xxx asserts
  
    if (empty(self::$_events[$event]) == TRUE) {
      return array();
    }
    else {
      return self::$_events[$event];
    }
  }

  /**
  * Clear some or all observer from an event.
  *
  * @param string event
  * @param array specific observer to remove, FALSE for all observers
  * @return void
  */
  public static function detachObservers ($event, $observer = FALSE) {
    // xxx asserts
  
    if ($observer === FALSE) {
      self::$_events[$event] = array();
    }
    elseif (isset(self::$_events[$event])) {
      // Loop through each of the observer and compare it to the observer
      // requested for removal. The observer is removed if it matches.
      foreach (self::$_events[$event] as $eventObserver) {
        if ($observer === $eventObserver) {
          unset(self::$_events[$event][$observer]);
        }
      }
    }
  }

  /**
  * Execute all of the observer attached to an event.
  *
  * @param string event
  * @return void
  */
  public static function notifyObservers ($event, $eventData = NULL) {
    Assert::isString($event);
    Assert::isObject($eventData, TRUE);
   
    if (empty(self::$_events[$event]) == FALSE) {
      $observers = self::listObservers($event);

      foreach ($observers as $observer) {
        if (strpos($observer, '::') !== FALSE) {
          $observer = explode('::', $observer);
        }
        call_user_func($observer, $eventData);
      }
    }

    self::$_eventsTriggered[$event] = $event;
  }

  /**
  * Check if a given event has been run, xxx make an accessor
  *
  * @param string event
  * @return boolean
  */
  public static function eventTriggered ($event) {
    // xxx asserts
  
    return isset(self::$_eventsTriggered[$event]);
  }
}

?>