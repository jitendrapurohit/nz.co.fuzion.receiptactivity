<?php

require_once 'receiptactivity.civix.php';

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function receiptactivity_civicrm_config(&$config) {
  _receiptactivity_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function receiptactivity_civicrm_xmlMenu(&$files) {
  _receiptactivity_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function receiptactivity_civicrm_install() {
  civicrm_api3('OptionValue', 'create', array(
    'option_group_id' => "activity_type",
    'label' => "Receipt",
    'name' => "ReceiptActivity",
    'description' => "Receipt Sent",
    'icon' => "fa-envelope-o",
  ));
  _receiptactivity_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_postInstall
 */
function receiptactivity_civicrm_postInstall() {
  _receiptactivity_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function receiptactivity_civicrm_uninstall() {
  $activity = civicrm_api3('OptionValue', 'getsingle', array(
    'sequential' => 1,
    'return' => array("id"),
    'name' => "ReceiptActivity",
  ));
  civicrm_api3('OptionValue', 'delete', array(
    'id' => $activity['id'],
  ));
  _receiptactivity_civix_civicrm_uninstall();
}

/**
 * Implementation of hook_civicrm_alterMailParams()
 */
function receiptactivity_civicrm_alterMailParams(&$params, $context) {
  if ($context == 'messageTemplate' && empty($params['receipt_activity_id']) && !empty($params['valueName'])) {
    $valueName = explode('_', $params['valueName']);

    if (!empty($valueName[2]) && $valueName[2] == 'receipt') {
      $activityParams = array(
        'source_contact_id' => CRM_Utils_Array::value('contactId', $params),
        'activity_type_id' => "ReceiptActivity",
        'source_record_id' => CRM_Utils_Array::value('contributionId', $params),
        'status_id' => "Scheduled",
      );

      $id = CRM_Core_Session::getLoggedInContactID();
      if ($id) {
        $activityParams['source_contact_id'] = $id;
      }
      $result = civicrm_api3('activity', 'create', $activityParams);
      $params['receipt_activity_id'] = $result['id'];
    }
  }
}

/**
 * Implementation of hook_civicrm_postEmailSend( )
 * Update the status of activity created in hook_civicrm_alterMailParams, and add target_contact_id
 */
function receiptactivity_civicrm_postEmailSend(&$params) {
  // check if an activityId was added in hook_civicrm_alterMailParams
  // if so, update the activity's status and add a target_contact_id
  if (!empty($params['receipt_activity_id'])) {
    $activityParams = array(
      'id' => $params['receipt_activity_id'],
      'subject' => 'Receipt Sent - ' .  CRM_Utils_Array::value('subject', $params),
      'details' => CRM_Utils_Array::value('html', $params),
      'status_id' => 'Completed',
      'target_contact_id' => CRM_Core_DAO::getFieldValue('CRM_Core_DAO_Email', $params['toEmail'], 'contact_id', 'email'),
    );
    $result = civicrm_api3('activity', 'create', $activityParams);
  }
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function receiptactivity_civicrm_enable() {
  _receiptactivity_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function receiptactivity_civicrm_disable() {
  _receiptactivity_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function receiptactivity_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _receiptactivity_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function receiptactivity_civicrm_managed(&$entities) {
  _receiptactivity_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types.
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function receiptactivity_civicrm_caseTypes(&$caseTypes) {
  _receiptactivity_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_angularModules
 */
function receiptactivity_civicrm_angularModules(&$angularModules) {
  _receiptactivity_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function receiptactivity_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _receiptactivity_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

// --- Functions below this ship commented out. Uncomment as required. ---

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_preProcess
 *
function receiptactivity_civicrm_preProcess($formName, &$form) {

} // */

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_navigationMenu
 *
function receiptactivity_civicrm_navigationMenu(&$menu) {
  _receiptactivity_civix_insert_navigation_menu($menu, NULL, array(
    'label' => ts('The Page', array('domain' => 'nz.co.fuzion.receiptactivity')),
    'name' => 'the_page',
    'url' => 'civicrm/the-page',
    'permission' => 'access CiviReport,access CiviContribute',
    'operator' => 'OR',
    'separator' => 0,
  ));
  _receiptactivity_civix_navigationMenu($menu);
} // */
