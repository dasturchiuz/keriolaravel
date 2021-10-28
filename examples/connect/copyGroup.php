<?php
/**
 * Administration API for Kerio Connect - Sample Application.
 *
 * Copy group with its members to a new one.
 *
 * @copyright	Copyright &copy; 2012-2012 Kerio Technologies s.r.o.
 * @version		1.4.0.234 
 */
require_once(dirname(__FILE__) . '/../config.php');
require_once(dirname(__FILE__) . '/../class/MyApi.php');
require_once(dirname(__FILE__) . '/../class/HtmlHelper.php');

$name = 'Example: Copy group with its members to a new one';
$api = new MyApi($name, $vendor, $version);

$html = new HtmlHelper();
$html->printHeader($name);
$html->printInfo('Do you want to create a new group with the same users as in another local group? Run this script.');

/* Did user submit the form? Init variables */
if('POST' == $_SERVER['REQUEST_METHOD']) {
	$isFormPosted 	 = true;

	$domainId 		 = $_POST['domain'];
	$newGroupName	 = $_POST['newGroup'];
	$isDomainUnchanged = "false" == $_POST['isDomainChanged'];

	if (isset($_POST['group'])) {
		$groupId = $_POST['group'];
	}
}
else {
	$isFormPosted = false;
}

/* Main application */
try {

	/* Login */
	$session = $api->login($hostname, $username, $password);

	if($isFormPosted) {

		/* Has user changed the domain name? No => create a new group of given name */
		if ($isDomainUnchanged) {

			if('' != $newGroupName) {

				/* Get group with its full detail */
				$fields = array('id', 'name', 'description', 'publishInGal', 'role', 'hasDomainRestriction', 'emailAddresses');
				$conditions = array(array(
	            	'fieldName' 	=> 'id',
					'comparator'	=> 'Eq',
					'value' 		=> $groupId
				));

				try {

					$groupDetails = $api->getGroups($fields, $domainId, $conditions);
					$groupDetail = $groupDetails[0];

					/* prepare params for Groups.create request */
					$groupDetails = array(
						'groups' => array(array(
							'name' 					=> $newGroupName,
							'description'			=> $groupDetail['description'],
							'publishInGal' 			=> $groupDetail['publishInGal'],
							'role' 					=> $groupDetail['role'],
							'hasDomainRestriction' 	=> $groupDetail['hasDomainRestriction'],
							'emailAddresses' 		=> $groupDetail['emailAddresses'],
							'domainId'				=> $domainId
						))
					);

					try {
						/* Create a new group, store id of this group */
						$result = $api->createGroup($groupDetails);
						$newGroupId = $result[0]['id'];

						/* Get list of users which belong to the group */
						$fields = array('id');
						$conditions = 	array(array(
			            	'fieldName' 	=> 'userGroups',
							'comparator'	=> 'Eq',
							'value' 		=> $groupId
						));

						$userList = $api->getUsers($fields, $domainId, $conditions);

						/* Prepare user Ids to be set to newly created group */
						$userListToGroup = array();

						foreach ($userList as $user) {
							array_push($userListToGroup, $user['id']);
						}

						/* Add members to the new group */
						$api->addMembersToGroup($newGroupId, $userListToGroup);

						$html->printSuccess(sprintf('<div class="success">Group <b>%s</b> has been successfully copied as <b>%s</b></div>', $groupDetail['name'], $newGroupName));
					}

					catch (KerioApiException $error) {
						$html->printError($error->getMessage());
					}
				}
				catch (KerioApiException $error) {
					$html->printError($error->getMessage());
				}
			}
			else {
				$html->printError('Please provide all details.');
			}
		}
	}

	/* Print HTML Form */

	$fields = array('id', 'name');
	$domainList = $api->getDomains($fields);

	if(!isset($domainId)) {
		$domainId = $domainList[0]['id'];
	}

	$fields = array('id', 'name');
	$conditions = array(array(
		'fieldName' 	=> 'itemSource',
		'comparator'	=> 'Eq',
		'value' 		=> 'DSInternalSource'	//limit output for local groups only
	));

	$groupList = $api->getGroups($fields, $domainId, $conditions);

	$groupId = isset($groupId) ? $groupId : '';

	$html->printCopyGroupForm($domainList, $groupList, $domainId, $groupId, '');
}

/* Catch possible errors */
catch (KerioApiException $error) {
	$html->printError($error->getMessage());
}

/* Logout */
if (isset($session)) {
	$api->logout();
}

$html->printFooter();
