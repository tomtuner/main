<?php
/**
 * SmartController
 *
 * SmartController actually isnt very smart!  I just need a class inheriting from Controller
 * that has no internal methods.  This is use as an intermediary for "automagic" when requesting
 * for web content that does not have a defined controller.
 */
class SmartController extends Controller {}
?>
