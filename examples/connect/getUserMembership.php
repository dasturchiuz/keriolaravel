<?php
/**
 * Administration API for Kerio Connect - Sample Application.
 *
 * Display user's membership in groups, mailing lists, resources.
 *
 * @copyright	Copyright &copy; 2012-2012 Kerio Technologies s.r.o.
 * @version		1.4.0.234
 */
require_once(dirname(__FILE__) . '/../config.php');
require_once(dirname(__FILE__) . '/../class/MyApi.php');
require_once(dirname(__FILE__) . '/../class/HtmlHelper.php');

$name = 'Example: Display user\'s membership in groups, mailing lists, resources'; 
$api = new MyApi($name, $vendor, $version);

$html = new HtmlHelper();
$html->printHeader($name);
$html->printInfo('Sometimes you may need to know where a user belongs. You can see their group, mailing list or resource membership by running this script.');

if('POST' == $_SERVER['REQUEST_METHOD']) {
	$isFormPosted 	 = true;

	$isDomainUnchanged 	= "false" == $_POST['isDomainChanged'];
	$domainId 			= $_POST['domain'];
	$userId 			= $_POST['user'];

	$groupMemberList	= array();
	$mlistMemberList	= array();
	$resourceMemberList	= array();
}
else {
	$isFormPosted = false;
}

$output				= '';
$isGroupMember 		= false;
$isMListMember 		= false;
$isResourceMember 	= false;

/* Main application */
try {
	/* Login */
	$session = $api->login($hostname, $username, $password);

	/* Get list of available constants */
	$constants = $api->getConstants();

	/* Does user submit the form? */
	if($isFormPosted) {

		/* Has user changed the domain name? No => create a new group of given name */
		if ($isDomainUnchanged) {

			try {

				/* GROUPS MEMBERSHIP */

				$output .= '<h2>Groups</h2>';

				$fields = array('id', 'name');
				$groupList = $api->getGroups($fields, $domainId);

				/* Go throw all available groups */
				foreach ($groupList as $group) {

					/* Get list of available users */
					$fields = array('id', 'loginName');
					$conditions = 	array(array(
		            	'fieldName' 	=> 'userGroups',
						'comparator'	=> 'Eq',
						'value' 		=> $group['id']
					));

					$userList = $api->getUsers($fields, $domainId, $conditions);

					/* Go throw all users in the group */
					foreach ($userList as $user) {

						/* Compare user Id from group and selected user Id in the form */
						if ($user['id'] == $userId) {

							$output .= $group['name'] . '<br>';
							$isGroupMember = true;
						}
					}
				}

				if (false == $isGroupMember) {
					$output .= '<i>User doesn\'t belong to any group</i>';
				}

				/* MAILING LISTS MEMBERSHIP */

				$output .= '<h2>Mailing Lists</h2>';

				$fields = array('id', 'name');
				$mailingListList = $api->getMailingLists($fields, $domainId);

				/* Go throw all available groups */
				foreach ($mailingListList as $mailingList) {

					/* Get list of mailing list members and moderators */
					$id = $mailingList['id'];
					
					/* Skip remote servers - Distributed domain */
					if (empty($id)) continue;
					
					$fields = array('userId', 'kind');

					$userList = $api->getMlUserList($fields, $id);

					/* Go throw all users in the mailing list */
					foreach ($userList as $user) {

						/* Compare user Id from group and selected user Id in the form */
						if ($user['userId'] == $userId) {

							$output .= $mailingList['name'] . ($constants['KMS_Moderator'] == $user['kind'] ? ' (moderator)' : ' (member)') . '<br>';
							$isMListMember = true;
						}
					}
				}

				if (false == $isMListMember) {
					$output .= '<i>User doesn\'t belong to any mailing list</i>';
				}

				/* RESOURCES MEMBERSHIP */

				$output .= '<h2>Resources</h2>';

				$fields = array('name', 'resourceUsers', 'manager');
				$resourceList = $api->getResources($fields, $domainId);

				/* Go throw all available resources */
				foreach ($resourceList as $resource) {

					/* Compare user Id from group and selected user Id in the form */
					if ($resource['manager']['id'] == $userId) {

						$output .= $resource['name'] . ' (manager)' . '<br>';
						$isResourceMember = true;
					}

					foreach ($resource['resourceUsers'] as $member) {

						if ($member['id'] == $userId ) {

							$output .= $resource['name'] . ' (member)' . '<br>';
							$isResourceMember = true;
						}
					}
				}

				if (false == $isResourceMember) {
					$output .= '<i>User doesn\'t belong to any resource</i>';
				}
				
				/* ALIASES MEMBERSHIP */
				
				$output .= '<h2>Aliases</h2>';
				
				$nameAlias = $api->getUserById($userId, $domainId);
				
				$fields = array('name', 'deliverTo');
				$conditions = array(
					array(
						'fieldName' => 'name',
						'comparator' => 'Like',
						'value' => $nameAlias
					),
					array(
						'fieldName' => 'deliverTo',
						'comparator' => 'Like',
						'value' => $nameAlias
					)
				);
				$aliasList = $api->getAliases($fields, $domainId, $conditions);
				
				/* Go throw all available resources */
				$isAliasMember = false;
				foreach ($aliasList as $alias) {
					$output .= $alias['name'] . '<br>';
					$isAliasMember = true;
				}

				if (false == $isAliasMember) {
					$output .= '<i>User doesn\'t have any alias</i>';
				}
			}
			catch (KerioApiException $error) {
				$html->printError($error->getMessage());
			}
		}
	}

	/* Print HTML Form */

	$fields = array('id', 'name');
	$domainList = $api->getDomains($fields);

	if(!isset($domainId)) {
		$domainId = $domainList[0]['id'];
	}

	$fields = array('id', 'loginName');
	$userList = $api->getUsers($fields, $domainId);

	$userId   = isset($userId) ? $userId : '';
	$html->printUserMembershipForm($domainList, $userList, $domainId, $userId);

	print $output;
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
