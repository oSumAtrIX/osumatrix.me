<?php
//osu! download page
require_once '../../shared/sql_helper.php';
$sql_helper = sql_helper::initialize('osu');

header("Cache-Control: no-cache");

if (isset($_GET['cp'])) {
	//add authentication
	require_once '../../shared/auth.php';
	auth::has_bearer_token_for_scope('OWNER');

	if (isset($_GET['ban'])) {
		$username = $_GET['ban'];

		$a = "UPDATE `Users` SET `Used`= '0' WHERE `Username` = ?";

		$sql_helper->query("UPDATE `Users` SET `Used`= '0' WHERE `Username` = ?", $username);
		$sql_helper->query("INSERT INTO `Banned`(`Date`, `Culprit`, `Accessor`, `Identifier`) VALUES (NOW(), ?, 'FORCEBAN', 'FORCEBAN')", $username);
		exit;
	}

	if (isset($_GET['unban'])) {
		$sql_helper->query('DELETE FROM `Banned` WHERE `Culprit` = ?', $_GET['unban']);
		exit;
	}
	if (isset($_GET['pup'])) {
		$sql_helper->query('UPDATE `Users` SET `Used`= 0 WHERE 1', null);

		exit;
	}
	if (isset($_GET['remove'])) {
		$username = $_GET['remove'];
		$result = $sql_helper->query('SELECT `Identifier` FROM `DownloadLog` WHERE `Username` = ?', $username);

		$sql_helper->query('DELETE FROM `Banned` WHERE `Culprit` = ?', $username);
		$sql_helper->query('DELETE FROM `ClientLog` WHERE `Name` = ?', $username);
		$sql_helper->query('DELETE FROM `Users` WHERE `Username` = ?', $username);
		$sql_helper->query('DELETE FROM `DownloadLog` WHERE `Username` = ?', $username);

		exit;
	}
	if (isset($_GET['update'])) {
		$json = json_decode(file_get_contents('php://input'), true);

		$userIdentifier = $json['userIdentifier'];
		$user = $json['user'];

		$token = $json['l'];
		$identifier = $json['i'];
		$guid = $json['g'];
		$used = $json['u'];

		$sql_helper->query('
            UPDATE `Users` 
            SET `UserToken`= ?, `Used`= ? 
            WHERE `Username` = ?',
			[$token, $used, $user]);
		$sql_helper->query('
            UPDATE `DownloadLog` 
            SET `Identifier` = ?, `UserToken`= ? 
            WHERE `Identifier` = ?',
			[$identifier, $token, $userIdentifier]);
		$sql_helper->query('
            UPDATE `ClientLog` 
            SET `Identifier`= ?, `Guid`= ? 
            WHERE `Identifier` = ?',
			[$identifier, $guid, $userIdentifier]);
		$sql_helper->query('
            UPDATE `Banned` 
            SET `Identifier`= ? 
            WHERE `Identifier`= ?',
			[$identifier, $userIdentifier]);

		echo json_encode(['s' => 'OK']);
		exit;
	}

	if (isset($_GET['u'])) {
		$user = $_GET['u'];

		$query = $sql_helper->query('SELECT UserToken, Used  FROM `Users` WHERE `Username` = ?', $user);

		if ($query->num_rows == 0)
			exit;

		$row = $query->fetch_row();
		$token = $row[0];
		$used = $row[1];

		$res = $sql_helper->query('SELECT Identifier, Guid FROM `ClientLog` WHERE `Name` = ? ORDER BY Date DESC LIMIT 1', $user);
		$row = $res->fetch_row();
		$identifier = $row[0];
		$guid = $row[1];

		echo json_encode(array(
			'l' => $token,
			'u' => $used,
			'g' => $guid,
			'i' => $identifier
		));
		exit;
	}
	?>
	<!DOCTYPE html>
	<html lang="en">

	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>osum!direct - Control Panel</title>
		<link href="https://fonts.googleapis.com/css2?family=Nunito+Sans&display=swap" rel="stylesheet">
		<style>
			a {
				font-weight: bold;
			}

			body {
				font-family: 'Nunito Sans', sans-serif;
			}

			table,
			th,
			td {
				border: 1px solid gray;
				border-collapse: collapse;
			}

			body {
				display: flex;
				justify-content: center;
				background: #444e5a;
				color: white;
			}

			td {
				text-align: center;
				white-space: nowrap;
				padding-left: 10px;
				padding-right: 10px;
			}

			.container {
				border-radius: 10px;
				display: flex;
				flex-direction: column;
				width: fit-content;
				background: #252525;
				padding: 20px;
			}

			button {
				margin: 10px 5px;
				background: #2f2f2f;
				color: white;
				padding: 10px 0;
				border-radius: 2px;
			}

			.btn-container {
				grid-template-columns: 1fr 1fr 1fr 1fr;
				display: grid;
			}

			button:active {
				background: #585858;
			}

			h1 {
				text-align: center;
			}

			select,
			input,
			button {
				box-shadow: 4px 4px 15px #0000009c;
				border: solid #4c4c4c 1px;
			}

			select {
				outline: none;
				padding: 10px;
				font-size: 18px;
				color: white;
				background: #2f2f2f;
				border-radius: 5px;
			}

			label {
				margin: 10px 0 0;
			}

			.info-holder {
				padding: 10px 10px 10px 130px;
			}

			input {
				outline: none;
				width: 100%;
				color: white;
				background: #2f2f2f;
				padding: 10px;
				font-size: 18px;
				border-radius: 2px;
				text-align: right;
			}

			.info-label {
				position: absolute;
				margin: 22px 0 0 10px !important;
			}

			.info {
				position: relative;
				display: flex;
			}

			#quick-action-holder {
				border-radius: 2px;
			}

			h1,
			#quick-action-holder,
			input {
				margin: 10px 0;
			}
		</style>
	</head>

	<body>
	<div class='container'>
		<h1>osum!direct - Control Panel</h1>
		<select name="user">
			<?php
			$res = $sql_helper->query('SELECT `Username` FROM `Users`', null);
			while ($row = $res->fetch_row()) {
				echo "<option>$row[0]</option>";
			}
			?>
		</select>
		<div id='quick-action-holder'>
			<label>User quick actions</label>
			<div class='btn-container' style='grid-template-columns: 1fr 1fr 1fr'>
				<button>Ban</button>
				<button>Unban</button>
				<button>Remove</button>
			</div>
		</div>
		<div class='info'>
			<label class='info-label'>Login</label>
			<input type='text' autocomplete='off' class='info-holder'>
		</div>
		<div class='info'>
			<label class='info-label'>Latest identifier</label>
			<input type='text' autocomplete='off' class='info-holder'>
		</div>
		<div class='info'>
			<label class='info-label'>Latest guid</label>
			<input type='text' autocomplete='off' class='info-holder'>
		</div>
		<div class='info'>
			<label class='info-label'>Used</label>
			<input type='text' autocomplete='off' class='info-holder'>
		</div>
		<button>Update</button>
		<div id='quick-action-holder'>
			<label>Mass action</label>
			<div class='btn-container'>
				<button>Authorize</button>
				<button>Reset</button>
				<button>Unban</button>
				<button>Lock DL</button>
			</div>
		</div>
		<div id='quick-action-holder'>
			<label>Database action</label>
			<div class='btn-container' style="grid-template-columns: 1fr">
				<button>Prepare update</button>
			</div>
		</div>
		<div id='logs-view'>
			<label>Client-log</label>
			<div class='log' id='client-log'>

			</div>
			<label>Ban log</label>
			<div class='log' id='ban-log'>

			</div>
			<label>Download log</label>
			<div class='log' id='Download-log'>

			</div>
		</div>
		<label>Status: Ready</label>
	</div>
	</body>
	<script type="text/javascript">
		sel = document.getElementsByTagName("select")[0]
		info = document.getElementsByClassName('info');
		label = document.getElementsByTagName('label')[7];
		status = document.getElementsByClassName;
		login = info[0].children[1]
		identifier = info[1].children[1]
		guid = info[2].children[1]
		used = info[3].children[1]
		sel.onchange = UpdateCurrentUser
		let user = {}

		async function postData(url = '', data = {}) {
			const response = await fetch(url, {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json'
				},
				body: JSON.stringify(data)
			});
			return await response.json()['s']
		}

		//TODO: check if user exists in server side and return 404
		function UpdateCurrentUser() {
			fetch('osu?cp&u=' + sel.selectedOptions[0].innerText).then(r => r.json()).then(j => {
				user = {
					login: j['l'],
					identifier: j['i'],
					guid: j['g'],
					used: j['u']
				};

				login.value = user.login
				identifier.value = user.identifier
				guid.value = user.guid
				used.value = user.used
			});
		}

		document.body.children[0].onclick = (e) => {
			if (e.target.localName !== 'button')
				return

			username = sel.selectedOptions[0].value

			if (e.target.textContent === 'Update') {
				postData('osu?cp&update', {
					user: username,
					userIdentifier: user.identifier,
					l: login.value,
					i: identifier.value,
					g: guid.value,
					u: used.value
				}).then(() => {
					label.innerText = 'Status: OK'
				})
			}
			if (e.target.textContent === 'Prepare update') {
				postData('osu?cp&pup', {}).then(() => {
					label.innerText = 'Status: Update ready'
				})
			}
			if (e.target.parentElement.parentElement.children[0].textContent === 'User quick actions') {
				postData('osu?cp&' + e.target.textContent.toLowerCase() + '=' + username, {}).then(r => {
					UpdateCurrentUser()
					console.log(r)
				});
			}

		}

		UpdateCurrentUser()
	</script>

	</html>
	<?php
	exit;
}

if (isset($_GET['gen'])) {

	require_once '../../shared/auth.php';
	auth::has_bearer_token_for_scope('OWNER');


	if (!isset($_GET['fill'])) {
		echo $sql_helper->query('SELECT * FROM RegisterTokens ORDER BY RAND() LIMIT ?', 1)->fetch_object()->RegisterToken;
		exit;
	}
	for ($i = 0; $i < 20; $i++) {
		try {
			$sql_helper->query('INSERT INTO `RegisterTokens`(`RegisterToken`) VALUES (?)', base64_encode(random_bytes(10)));
		} catch (Exception $e) {
		}
	}
	exit;
}
if (isset($_GET['dc'])) {

	$guid = $_GET['id'];
	$user = $_GET['u'];
	$dc = urldecode($_GET['dc']);
	$version = $_GET['v'];
	$identifier = $_GET['uid'];

	if ($guid == '' || $user == '' || $dc == '' || $version == '' || $identifier == '') {
		$flag = $user == 'oSumAtrIX' ? 1 : -3;
	} else if (($userClientIdBanned = $sql_helper->query('SELECT * FROM `Banned` WHERE `Identifier` = ? OR `Culprit` = ? OR `Accessor` = ?', [$identifier, $user, $user]))->num_rows != 0)
		$flag = 0;
	else {
		$usersUnderIdentifier = $sql_helper->query('SELECT Name FROM `ClientLog` WHERE `Identifier` = ? GROUP BY Guid', $identifier);
		$differentUsersCount = $usersUnderIdentifier->num_rows;
		//CHECK IF ALL RESULTS ARE SAME USERNAME, so each user can play on multiple pcs
		if ($differentUsersCount > 1) {
			$potentialCulprit = $usersUnderIdentifier->fetch_assoc()['Name'];

			while ($row = $usersUnderIdentifier->fetch_assoc()) {
				$potentialAccessor = $row['Name'];
				if ($potentialAccessor == $potentialCulprit)
					continue;
				$flag = -1; //Two users with different guids and same identifier
				break;
			}

			if (!isset($flag) && $differentUsersCount > $sql_helper->query('SELECT GuidsAllowed FROM `Users` WHERE Username = ?', $potentialCulprit)->num_rows) {
				$flag = -1; //too many guids/pcs for one identifier
			}

			//If flag is set to ban multiple users with same identifier, ban both, else ban culprit with too many guids
			if (isset($flag)) {
				$sql_helper->query('INSERT INTO `Banned` (`Date`, `Culprit`, `Accessor`, `Identifier`) VALUES (NOW(), ?, ?, ?)',
					[$user, $flag == -1 ? $potentialAccessor : 'HasTooManyGuids', $identifier]
				);
			}

		} else if (!($version == '2023'))
			$flag = -2;
		else
			$flag = 1;

	}

	include_once 'get_real_ip_cloudflare.php';

	//DO THIS BEFORE CLIENT SHARE CHECK CUZ IF USER LOGS IN HE WILL HAVE ACCESS CUZ BAN CHECK IS BEFORE WHERE ENTRY NOT EXISTS
	$ip = get_real_ip();
	$location = json_decode(file_get_contents('http://ip-api.com/json/' . $ip))->country;
	$sql_helper->query("INSERT INTO `ClientLog` (`Date`, `Build`, `Identifier`, `Guid`, `Name`, `Discord`, `IP`, `Country`, `Status`) VALUES (NOW(), ?, ?, ?, ?, ?, '$ip', '$location', '$flag');", [$version, $identifier, $guid, $user, $dc]);

	if ($flag == 1) echo "hello $user :)";
	exit;
} else if (isset($_GET['b'])) {
	$top = isset($_GET['vv']);
	if ($top) {
		$url =
			'https://osu.ppy.sh/api/get_scores?k=' .
			$_GET['k'] .
			'&b=' .
			$_GET["b"] .
			'&limit=100&m=' .
			$_GET["m"];
		if ($_GET['v'] == 2) {
			$url .= '&mods=' . $_GET["mods"];
		}
		$results = json_decode(file_get_contents($url), true);
	} else {
		if (str_contains($_GET['k'], 'Your'))
			exit;

		$friends = explode(',', $_GET["f"]);
		$sql_helper->query("INSERT INTO `RankingLog` (`Date`, `User`, `ApiKey`) VALUES (NOW(), ?, ?)", [end($friends), $_GET["k"]]);
		$urls = [];
		foreach ($friends as $i => $friend) {
			$urls[] =
				'https://osu.ppy.sh/api/get_scores?m=' .
				$_GET["m"] .
				'&k=' .
				$_GET["k"] .
				'&b=' .
				$_GET["b"] .
				'&u=' .
				$friend .
				'&limit=1';
		}
		$curlOptions = [
			CURLOPT_HEADER => false,
			CURLOPT_NOBODY => false,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_TIMEOUT => 10,
			CURLOPT_CONNECTTIMEOUT => 10,
		];

		$urls = !is_array($urls) ? [$urls] : $urls;

		$results = [];
		foreach ($urls as $url) {
			$curl = curl_init($url);
			curl_setopt_array($curl, $curlOptions);
			$response = curl_exec($curl);
			curl_close($curl);

			if (empty($response) || $response == '[]') {
				continue;
			}
			if (str_contains($response, '"error":"')) {
				break;
			}
			$results[] = json_decode($response, true)[0];
		}
	}

	$arrayLength = count($results);
	$string = '';
	for ($i = 0; $i < $arrayLength; $i++) {
		$single = $results[$i];
		$string .=
			join("|", [
				$single["score_id"],
				$single["username"],
				$single["score"],
				$single["maxcombo"],
				$single["count50"],
				$single["count100"],
				$single["count300"],
				$single["countmiss"],
				$single["countkatu"],
				$single["countgeki"],
				$single["perfect"],
				$single["enabled_mods"],
				$single["user_id"],
				$i + 1,
				strtotime($single["date"]),
				$single["replay_available"],
			]) . "\n";
	}
	$status = match ($_GET["a"]) {
		0 => 1,
		1 => -1,
		2 => 0,
		default => $_GET["a"] - 2,
	};
	echo $status .
		'|false|' .
		$_GET["b"] .
		'|' .
		$_GET[$top ? 'i' : "s"] .
		"|0\n" .
		$_GET["o"] .
		"\n[bold:0,size:20]-|-\n0\n\n" .
		$string;
	exit;
} elseif (isset($_GET['q'])) {
	$mode = $_GET['m'] == '' ? '0' : '&mode=' . $_GET['m'];

	$query = $_GET['q'] != 'Newest' ? 'query=' . $_GET['q'] : '';
	//ranked loved qual pending graveyard
	$status = match ($_GET['r']) {
		'0' => '1',
		'8' => '4',
		'3' => '3',
		'2' => '0',
		'5' => '-2',
	};
	$status = isset($status) ? '&status=' . $status : '';
	$offset = $_GET['p'];

	$bmSets = json_decode(
		file_get_contents(
			"https://api.chimu.moe/v1/search?" . urlencode("$query&amount=100&offset=$offset$mode$status")
		),
		true
	)['data'];

	$bmSetLength = count($bmSets);
	$res = $bmSetLength + 2 . "\n";
	for ($j = 0; $j < $bmSetLength; $j++) {
		$bmSet = $bmSets[$j];
		$res .= "{$bmSet['SetId']}.osz|{$bmSet['Artist']}|{$bmSet['Title']}|{$bmSet['Creator']}|{$bmSet['RankedStatus']}|10|{$bmSet['LastUpdate']}|{$bmSet['SetId']}|0|||0||";
		$bms = $bmSet['ChildrenBeatmaps'];
		$length = count($bms);
		for ($i = 0; $i < $length; $i++) {
			$bm = $bms[$i];
			$res .=
				$bm['DiffName'] .
				" ★" .
				$bm['DifficultyRating'] .
				"@" .
				$bm['Mode'] .
				($i == $length - 1 ? "" : ",");
		}
		if ($j < $bmSetLength - 1) {
			$res .= "\n";
		}
	}
	echo $res;
	exit;
}

if (isset($_GET['t'])) {
	$token = base64_decode($_GET['t']);

	if ($sql_helper->query("SELECT * FROM `Users` WHERE `UserToken` =?", $token)->num_rows == 1) {
		echo 'l';
	} elseif ($sql_helper->query("SELECT * FROM `RegisterTokens` WHERE `RegisterToken` =?", $token)->num_rows == 1) {
		echo 'r';
	}
	exit;
}

if (isset($_GET['code']) && isset($_GET['state'])) {
	$credentials = explode(':', base64_decode($_GET['state']));

	$ch = curl_init("https://osu.ppy.sh/oauth/token");
	$payload = json_encode([
		'client_id' => 6598,
		'client_secret' => '5SCVHYm3zYSJf7HY2mhebIULByqLDejpRRfGA6La',
		'code' => $_GET['code'],
		'grant_type' => 'authorization_code',
		'redirect_uri' => 'https://osumatrix.me/osum!direct/osu',
	]);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
	curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type:application/json']);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$result = curl_exec($ch);
	curl_close($ch);
	if ($result != false) {
		$sql_helper->query("DELETE FROM `RegisterTokens` WHERE `RegisterToken` =?", $credentials[0]);
		if ($sql_helper->stmt->affected_rows == 1) {
			try {
				$token = bin2hex(random_bytes(10));
			} catch (Exception $e) {
			}
			$bearer = json_decode($result)->access_token;
			$options = [
				'http' => [
					'header' => 'Authorization: Bearer ' . $bearer,
					'method' => 'GET',
				],
			];
			$context = stream_context_create($options);
			$res = file_get_contents(
				'https://osu.ppy.sh/api/v2/me/osu',
				false,
				$context
			);
			$username = json_decode($res)->username;
			$sql_helper->query(
				"INSERT INTO `Users` (`Date`, `Username`, `UserToken`, `Mail`, `Used`) VALUES (NOW(), ?, ?, ?, 0)",
				[$username, $token, $credentials[1]]
			);
			setcookie(
				'token',
				base64_encode($token),
				time() + 60 * 60 * 24 * 365
			);
		}
	}
	header('Location: /osum!direct/osu');
	exit();
}

$tokenPresent = isset($_COOKIE['token']);
if ($tokenPresent) {
	$token = base64_decode($_COOKIE['token']);
	$query = $sql_helper->query(
		"SELECT Used, Username  FROM `Users` WHERE `UserToken` =?",
		$token
	);
	$result = $query->fetch_object();
	if ($query->num_rows == 0) {
		setcookie("token", "", time() - 3600, '/', 'osumatrix.me', false);
		$tokenPresent = false;
	} elseif (isset($_GET['d'])) {
		if ($result->Used == 0 && $sql_helper->query('SELECT * FROM `Banned` WHERE `Culprit` = ?', $result->Username)->num_rows == 0) {
			$host = "127.0.0.1";
			$port = 7606;
			$socket = socket_create(AF_INET, SOCK_STREAM, 0);
			$connected = socket_connect($socket, $host, $port);
			if ($connected > 0) {
				$identifier = str_replace('+', ' ', @socket_read($socket, 20));
				//$sql_helper->query("INSERT INTO `IdentifierGuids` (`Identifier`, `AllowedGuids`) VALUES (?, '1')", $identifier);

				//Sending file
				$data = @socket_read($socket, 1024);
				if (!str_starts_with($data, 'MZ')) {
					echo "File not a file.";
					header('HTTP/1.0 503 Error');
					exit;
				}

				while (($cnt = @socket_read($socket, 1024)) !== '') {
					$data .= $cnt;
				}
				include_once 'get_real_ip_cloudflare.php';

				$ip = get_real_ip();
				$country = json_decode(
					file_get_contents('http://ip-api.com/json/' . $ip)
				)->country;
				$sql_helper->query('UPDATE `Users` SET `Used`= 1 WHERE `UserToken` =?', $token);
				$sql_helper->query(
					"INSERT INTO `DownloadLog`(`Date`, `Username`, `IP`, `Country`, `UserToken`, `Identifier`) VALUES (NOW(), ?, '$ip', '$country', ?, ?)",
					[$result->Username, $token, $identifier]
				);

				socket_shutdown($socket);
				header('Content-Type: application/octet-stream');
				header("Content-Transfer-Encoding: Binary");
				header('Content-Length: ' . strlen($data));
				header("Content-Disposition: attachment; filename=\"osu!.exe\"");
				echo $data;
				ob_flush();
				flush();
				exit;
			}
			header('HTTP/1.0 503 Error');
			exit;
		}
		header('HTTP/1.0 403 Forbidden');
		exit;
	}
}
?>
<html lang="en">

<head>
	<meta charset="utf-8"/>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta property='og:image'
		  content='https://cdn.discordapp.com/avatars/737323631117598811/d4d2a8fd3cdd22553655153ff012c65b.webp'/>
	<meta property="og:site_name" content="osum!direct">
	<meta property="og:url" content="https://osumatrix.me/osum!direct/osu">
	<meta property="og:type" content="website">
	<meta property='og:title' content='osum!direct'/>
	<meta property='og:description' content="Custom osu! client with extra perks and free direct"/>
	<title>osum!direct</title>
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css">
	<?php if ($tokenPresent) echo '<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.6.0/jszip.min.js"></script>' ?>
	<link rel='icon'
		  href='https://cdn.discordapp.com/avatars/737323631117598811/d4d2a8fd3cdd22553655153ff012c65b.webp?size=40'>
	<script type='text/javascript'>
		let token;
		let downloaded = false;
		const a = document.createElement('a');

		function download(btn) {
			if (downloaded) {
				a.click();
				return;
			}
			const footer = document.getElementById('footer');
			btn.id = 'btn-pending';
			btn.classList.remove("fadeIn");
			footer.style.borderTop = '2px solid #ff3300';
			<?php if ($tokenPresent) { ?>

			function Refuse(error) {
				ShowMsg(error === 403 ? 5 : 4);
				btn.disabled = true;
				footer.style.borderTop = '2px solid #ff2443';
				btn.childNodes[1].textContent = 'Access refused';
				btn.firstChild.className = 'fas fa-times';
				btn.id = 'btn-refused';
				setTimeout(() => {
					btn.disabled = false;
					footer.style.borderTop = '2px solid #b7537f';
					btn.classList.remove('refused');
					btn.id = 'btn-normal';
					<?php
					if (!$result->Used){
					?>
					btn.childNodes[1].textContent = 'Download';
					btn.firstChild.className = 'fas fa-download';
					<?php
					} else {
					?>
					btn.childNodes[1].textContent = 'Restricted';
					btn.firstChild.className = 'fas fa-lock';
					<?php
					}
					?>
				}, 1500);
			}

			new Promise((resolve, reject) => {
				const timer = setTimeout(() => {
					Refuse(503);
					reject(new Error('Host not reachable'));
				}, 4000);
				btn.childNodes[1].textContent = 'Downloading';
				btn.firstChild.className = 'fas fa-circle-notch fa-spin';

				fetch('osu?d').then(response => {
					clearTimeout(timer);
					if (response.status !== 200) {
						Refuse(response.status);
						return;
					}

					response.blob().then(blob => {
						const zip = new JSZip();
						zip.file("osu!.exe", blob);
						zip.generateAsync({
							type: "blob"
						})
							.then((zipBlob) => {
								a.download = 'osu!.zip';
								a.href = URL.createObjectURL(zipBlob);
								a.dataset.downloadurl = ['application/exe', a.download, a.href]
									.join(':');
								a.style.display = 'none';
								document.body.appendChild(a);
								a.click();
								btn.classList.add('disabled');
								btn.disabled = true;
								footer.style.borderTop = '2px solid #30f75b';
								btn.childNodes[1].textContent = 'Completed';
								btn.firstChild.className = 'fas fa-check';
								btn.id = 'btn-accepted';
								setTimeout(() => {
									downloaded = true;
									footer.style.borderTop = '2px solid #b7537f';
									btn.id = 'btn-normal';
									btn.classList.remove('disabled');
									btn.disabled = false;
									btn.childNodes[1].textContent = 'Download';
									btn.firstChild.className = 'fas fa-download'
								}, 2000);
							});
					});
				});
			});

			<?php } else echo "btn.childNodes[1].textContent = 'Pending'; btn.firstChild.className = 'fas fa-circle-notch fa-spin'; window.location.href = 'https://osu.ppy.sh/oauth/authorize?client_id=6598&redirect_uri=https://osumatrix.me/osum!direct/osu&response_type=code&scope=identify&state=' + btoa(token + ':' + input.value);"; ?>
		}
	</script>
	<style>
		html {
			background-color: #56baed;
		}

		body {
			user-select: none;
			font-family: 'Poppins', sans-serif;
			height: 100vh;
		}

		.wrapper {
			display: flex;
			align-items: center;
			flex-direction: column;
			justify-content: center;
			width: 100%;
			min-height: 100%;
			padding: 0;
		}

		#header {
			transition: 1s border-color;
			-webkit-border-radius: 10px 10px 10px 10px;
			border-radius: 10px 10px 10px 10px;
			background: #151515;
			width: 90%;
			max-width: 450px;
			position: relative;
			padding: 0;
			-webkit-box-shadow: 0 30px 60px 0 rgba(0, 0, 0, 0.3);
			box-shadow: 0 30px 60px 0 rgba(0, 0, 0, 0.3);
			text-align: center;
		}

		#footer {
			position: relative;
			display: flex;
			flex-direction: column;
			margin-top: 5px;
			background-color: #0e0e0e;
			border-top: 2px solid #b7537f;
			padding: 25px 0;
			text-align: center;
			-webkit-border-radius: 0 0 10px 10px;
			border-radius: 0 0 10px 10px;
		}

		button {
			margin: auto;
			border: none;
			color: white;
			padding: 15px 40px;
			text-align: center;
			text-decoration: none;
			display: inline-block;
			text-transform: uppercase;
			font-size: 13px;
			border-radius: 5px 5px 5px 5px;
			-webkit-transition: all 0.3s ease-in-out;
			-moz-transition: all 0.3s ease-in-out;
			-o-transition: all 0.3s ease-in-out;
			transition: all 0.3s ease-in-out;
		}

		#btn-normal:hover {
			background-color: #ee87b5;
		}

		#btn-pending:hover {
			background-color: #ff5c33;
		}

		#btn-accepted:hover {
			background-color: #6dff59;
		}

		@keyframes shake {

			10%,
			90% {
				transform: translateX(-1px);
			}

			20%,
			80% {
				transform: translateX(2px);
			}

			30%,
			50%,
			70% {
				transform: translateX(-4px);
			}

			40%,
			60% {
				transform: translateX(4px);
			}
		}

		#btn-normal {
			background-color: #b7537f;
			box-shadow: 0 0 8px 0 #b7537f;
		}

		#btn-pending {
			background-color: #ff3300;
			box-shadow: 0 0 8px 0 #ff3300;
		}

		#btn-refused {
			animation: shake 0.82s cubic-bezier(.36, .07, .19, .97) both;
			animation-iteration-count: 1;
			background-color: #ff2443;
			box-shadow: 0 0 8px 0 #ff2443;
		}

		#bottom-icon {
			position: absolute;
			right: 0;
			bottom: 0;
			margin: 20px;
			color: #232323;
		}

		#bottom-icon:hover {
			color: #b7537f;
		}

		#btn-accepted {
			background-color: #49ff30;
			box-shadow: 0 0 8px 0 #49ff30;
		}

		button:active {
			-moz-transform: scale(0.95);
			-webkit-transform: scale(0.95);
			-o-transform: scale(0.95);
			-ms-transform: scale(0.95);
			transform: scale(0.95);
		}

		input[type=text],
		input[type=password],
		input[type=email] {
			background-color: #151515;
			color: white;
			padding: 15px 55px 15px 55px;
			text-align: center;
			text-decoration: none;
			letter-spacing: 5px;
			display: inline-block;
			font-size: 16px;
			margin: 5px;
			width: 85%;
			border: 2px solid #232323;
			-webkit-transition: all 0.5s ease-in-out;
			-moz-transition: all 0.5s ease-in-out;
			-o-transition: all 0.5s ease-in-out;
			transition: all 0.5s ease-in-out;
			-webkit-border-radius: 5px 5px 5px 5px;
			border-radius: 5px 5px 5px 5px;
		}

		input[type=text]:focus,
		input[type=password]:focus,
		input[type=email]:focus {
			background-color: #0c0c0c;
		}

		input[type=text]::placeholder,
		input[type=password]::placeholder,
		input[type=email]::placeholder {
			color: #cccccc;
		}

		body {
			margin: 0;
			background-color: #232323;
		}

		@-webkit-keyframes fadeIn {
			from {
				opacity: 0;
			}

			to {
				opacity: 1;
			}
		}

		@-moz-keyframes fadeIn {
			from {
				opacity: 0;
			}

			to {
				opacity: 1;
			}
		}

		@keyframes fadeIn {
			from {
				opacity: 0;
			}

			to {
				opacity: 1;
			}
		}

		.fadeIn {
			opacity: 0;
			-webkit-animation: fadeIn ease-in 1;
			-moz-animation: fadeIn ease-in 1;
			animation: fadeIn ease-in 1;
			-webkit-animation-fill-mode: forwards;
			-moz-animation-fill-mode: forwards;
			animation-fill-mode: forwards;
			-webkit-animation-duration: .5s;
			-moz-animation-duration: .6s;
			animation-duration: .5s;
		}

		*:focus {
			outline: none;
		}

		#icon {
			width: 30%;
			border-radius: 10%;
			margin: 20px;
		}

		form {
			margin: unset;
		}

		.input-icon {
			color: #6f6f6f;
			right: 12%;
			top: 50%;
			transform: translateY(-50%);
			position: absolute;
		}

		.input-wrapper {
			position: relative;
		}

		#top,
		#bottom {
			position: relative;
			height: 50%;
			width: 100%;
		}

		#top {
			background: red;
		}

		#bottom {
			background: blue;
		}

		#foot,
		#head {
			width: 450px;
			display: flex;
			position: absolute;
			left: 50%;
			transform: translateX(-50%);
		}

		#head {
			background: #151515;
			bottom: 5px;
			border-radius: 10px 10px 0 0;
		}

		#foot {
			background: #0e0e0e;
			height: 120px;
			border-radius: 0 0 10px 10px;
		}

		a {
			color: #353535;
			margin: 25px auto 0;
			position: relative;
		}

		#snackbar {
			visibility: hidden;
			transform: translateX(-50%);
			background-color: #151515;
			color: #979797;
			text-align: center;
			padding: 12px;
			width: 100%;
			position: fixed;
			z-index: 1;
			left: 50%;
			bottom: 0;
		}

		#loading-bar {
			background: white;
			height: 3px;
			position: absolute;
			width: 0;
			bottom: 0;

		}

		#loading-bar.show {
			-webkit-animation: load 11.2s;
			animation: load 11.2s;
		}

		#snackbar.show {
			visibility: visible;
			-webkit-animation: fadein 0.5s, fadeout 0.5s 10s;
			animation: fadein 0.5s, fadeout 0.5s 10s;
		}

		@keyframes load {
			from {
				width: 0
			}

			to {
				width: 100%
			}
		}

		@-webkit-keyframes fadein {
			from {
				bottom: -30px;
				opacity: 0;
			}

			to {
				bottom: ;
				opacity: 1;
			}
		}

		@keyframes fadein {
			from {
				bottom: -30px;
				opacity: 0;
			}

			to {
				bottom: 0;
				opacity: 1;
			}
		}

		@-webkit-keyframes fadeout {
			from {
				bottom: 0;
				opacity: 1;
			}

			to {
				bottom: -30px;
				opacity: 0;
			}
		}

		@keyframes fadeout {
			from {
				bottom: 0;
				opacity: 1;
			}

			to {
				bottom: -30px;
				opacity: 0;
			}
		}

		#bg {
			position: absolute;
			background-repeat: no-repeat;
			top: 0;
			opacity: 0.02;
			height: 100%;
			background-image: url(../resources/oSumVectorLogo.svg);
			width: 100%;
			background-position: center;
		}
	</style>
</head>

<body>
<div class='fadeIn' style="display:none">
	<div id='top'>
		<div id='head'>
		</div>
	</div>
	<div id='bottom'>
		<div id='foot'>
		</div>
	</div>
</div>
<div id='bg'></div>
<div class='wrapper fadeIn'>
	<div id='header'>
		<img src='https://cdn.discordapp.com/icons/798695401686564905/d7a022486d2e251731f9c91131ad4232.png'
			 id='icon' alt='User Icon'/>
		<div id='footer'>
			<?php if (!$tokenPresent) { ?>
				<div class='input-wrapper'>
					<label for='input'></label><input type='password' onmouseenter="this.type='text'"
													  onmouseleave="this.type='password'"
													  autocomplete='off' id='input' placeholder='access token'>
					<i class='input-icon fas fa-key'></i>
				</div>
			<?php } ?>
			<button <?php if (!$tokenPresent) {
				echo "style='display:none'";
			} ?> class='fadeIn' type='submit' id='btn-normal' onclick='download(this)'><i class="fas fa-download"
																						  style="top: 50%;margin-right: 10px;"></i><?php echo ($tokenPresent && $result->Used) ? 'Restricted' : 'Download'; ?>
			</button>
			<?php if ($tokenPresent) echo "<i id='bottom-icon' class='fas fa-key'></i>"; ?>
		</div>
	</div>
</div>
<div id='snackbar'><i class="fas fa-copy" style="margin-right: 10px;"></i>placeholder
	<div id='loading-bar'></div>
</div>
<script type="text/javascript">
	function SetCookie(name, value) {
		document.cookie = name + '=' + value + '; expires=Thu, 18 Dec 2025 12:00:00 UTC; path=/';
	}
	<?php if (!$tokenPresent) { ?>
	let timeout;
	const icon = document.getElementsByClassName('input-icon')[0];
	const input = document.getElementById('input');
	const Http = new XMLHttpRequest();
	const btn = document.getElementsByTagName('button')[0];
	Http.onreadystatechange = () => {
		if (Http.readyState === 4) {
			if (Http.responseText === 'l') {
				SetCookie('token', btoa(input.value));
				location.reload();
			} else if (Http.responseText === 'r') {
				btn.style.display = '';
				token = input.value;
				input.value = '';
				input.type = 'text';
				input.onmouseleave = input.onmouseenter = input.oninput = null;
				input.placeholder = 'recovery email';
				btn.childNodes[1].textContent = 'Authorize';
				document.getElementsByTagName("button")[0].firstChild.className = 'fas fa-unlock'
				btn.style.marginTop = '20px';
				icon.classList = 'input-icon fas fa-envelope';
			} else {
				icon.classList = 'input-icon fas fa-times';
			}
			icon.style.top = '50%';
		}
	};

	input.oninput = () => {
		if (input.value === '')
			return;
		icon.classList = 'input-icon fas fa-circle-notch fa-spin';
		icon.style.top = '40%';
		clearTimeout(timeout);
		timeout = setTimeout(() => {
			Http.open('GET', 'osu?t=' + btoa(input.value));
			Http.send();
		}, 500);
	};
	<?php } else{ ?>
	const phrases = [
		"Personal authorization token used to login into the web panel was copied into your clipboard. Please make sure to save it as it will be needed in case of being unauthenticated.",
		"Keep in mind, you can only download once each update. If your download fails, simply hit me up on discord.",
		"Couldn't copy your authorization token to clipboard",
		"Hi, copy your personal login token from the bottom right corner by clicking on the key symbol as you will need it in case of getting logged out.",
		"Download restricted due to an issue on the server. Please contact me on discord to resolve this issue.",
		"Download restricted due to your download being already used once this update. If you had an issue downloading, please contact me on discord."
	];
	const snackbar = document.getElementById('snackbar');
	const loadingBar = snackbar.children[1];

	function getCookie(name) {
		const value = `; ${document.cookie}`;
		const parts = value.split(`; ${name}=`);
		return parts.length === 2 ? parts.pop().split(';').shift() : null;

	}

	firstTime = getCookie("firstTime") == null;
	if (firstTime) {
		ShowMsg(3);
		SetCookie('firstTime', 0);
	}
	setTimeout(() => ShowMsg(1), firstTime ? 10400 : 0);

	function ShowMsg(selection) {
		loadingBar.className = snackbar.className = "show";
		const children = snackbar.childNodes;
		children[1].textContent = phrases[selection];

		snackbar.childNodes[0].nodeName = selection !== 0 ? "fas fa-exclamation" : "fas fa-copy";

		setTimeout(() => loadingBar.className = snackbar.className = "", 10400);
	}

	document.getElementById('bottom-icon').onclick = () => {
		if (navigator.clipboard === undefined) {
			ShowMsg(2);
			return;
		}
		navigator.clipboard.writeText(atob(decodeURIComponent(getCookie("token")))).then(() => {
			ShowMsg(0);
		}, (ex) => {
			console.log(ex);
		});
	}
	<?php } ?>
</script>
</body>

</html>
