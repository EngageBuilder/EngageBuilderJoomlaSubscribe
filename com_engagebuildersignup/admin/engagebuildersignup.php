<?php

/**
 * Entry point to EngageBuilder Signup  administration pages
 *
 * @package     Joomla.Administrator
 * @copyright   Copyright (C) 2013 EngageBuilder LLC. All rights reserved.
 */

defined('_JEXEC') or die;

JToolBarHelper::title('Save subscribers into Engagebuilder.com', 'getting-started');

$document =& JFactory::getDocument();
$document->addStyleSheet(JURI::root(false).'administrator/components/com_engagebuildersignup/css/engagebuildersignup.css');
$document->addScript(JURI::root(false).'administrator/components/com_engagebuildersignup/js/engagebuilder.js');

// Include dependancies
jimport('joomla.application.component.controller');

$app = JFactory::getApplication();
$input = $app->input;

// Execute the task.
$controller = JController::getInstance('Eb');

$controller->execute($input->get('task', 'home.display'));
$controller->redirect();
