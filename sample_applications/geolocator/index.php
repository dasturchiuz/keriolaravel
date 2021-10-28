<?php
/**
 * Administration API for Kerio Connect - Sample Application.
 *
 * @copyright	Copyright &copy; 2012-2012 Kerio Technologies s.r.o.
 * @version		1.4.0.234
 */
require_once(dirname(__FILE__) . '/config.php'); ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Strict//EN">
<html>
	<head>
		<meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
		<meta http-equiv="Content-type" content="text/html; charset=UTF-8">
		<link rel="shortcut icon" href="images/favicon.ico">
		<link rel="stylesheet" type="text/css" href="css/style.css">
		<script src="http://code.jquery.com/jquery-latest.js"></script>
		<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?sensor=false"></script>
		<script type="text/javascript" src="http://google-maps-utility-library-v3.googlecode.com/svn/tags/markerclusterer/1.0/src/markerclusterer.js"></script>
		<script type="text/javascript" src="js/geolocator.js"></script>
		<title>Geolocator</title>
	</head>

	<body>
			<div id="map_canvas">Check your Internet connection if Google Maps were not loaded in a few seconds.</div>
			<div id="sidebar">
				<form>
				<div id="menu">
						Kerio Connect:<br>
						<select id="kerio-connect" name="kerio-connect" onChange="getConnections()">
							<option value="" selected>Please select</option>
							<?php
								foreach ($servers as $index => $server) {
									if (empty($server['ip'])) continue;
									printf('<option value="%d">%s</option>', $index, $server['name']);
								}
							?>
						</select>
						<span id="loading"></span><br>
				</div>

				<hr>

				<div id="protocols">
						<input type="radio" onChange="setMarkers(this)" name="protocol" id="protocolAny" checked>Any protocol <span id="countTotal"></span><br>
						<input disabled type="radio" onChange="clearMarkers()" name="protocol" id="protocolCustom">Frequently used:<br>
						<blockquote>
							<input disabled type="checkbox" onChange="setMarkers(this)" id="protocolSmtpNoExtension">SMTP <span id="count_protocolSmtpNoExtension"></span><br>
							<input disabled type="checkbox" onChange="setMarkers(this)" id="protocolImapsNoExtension">IMAPS <span id="count_protocolImapsNoExtension"></span><br>
							<input disabled type="checkbox" onChange="setMarkers(this)" id="protocolHttpsNoExtension">WebMail <span id="count_protocolHttpsNoExtension"></span><br>
							<input disabled type="checkbox" onChange="setMarkers(this)" id="protocolHttpsActiveSync">ActiveSync <span id="count_protocolHttpsActiveSync"></span><br>
							<input disabled type="checkbox" onChange="setMarkers(this)" id="protocolHttpsKocOffline">MS Outlook (KOFF) <span id="count_protocolHttpsKocOffline"></span><br>
							<input disabled type="checkbox" onChange="setMarkers(this)" id="protocolHttpsEWS">Outlook 2011 (EWS) <span id="count_protocolHttpsEWS"></span><br>
							<input disabled type="checkbox" onChange="setMarkers(this)" id="protocolHttpsWebDav">Entourage 2004/2008 <span id="count_protocolHttpsWebDav"></span><br>
						</blockquote>
				</div>

				<hr>

				<div id="markers">
					<ul id="list"></ul>
				</div>


				</form>
			</div>


	</body>
</html>
