<!DOCTYPE html>
<?php
/*
-----------------------------------------------------------------------------
@plugindesc Cloud Based Asynchronous Multiplayer - CBAM
            Database manager
@author     Purzelkater (mailto:purzelkater(at)online.de)
@version    0.9.2
@date       2016/04/02
@filename   cbamm.php
@url        http://forums.rpgmakerweb.com/index.php?/profile/81913-purzelkater/
@license    CC BY 3.0 (https://creativecommons.org/licenses/by/3.0/)
-----------------------------------------------------------------------------

### Terms of Use ###
CBAM files are distributed as is under the creative commons license CC BY 3.0
(Attribution 3.0 Unported) for free.
You are free to share, copy, redistribute or edit them for any purpose, even
commercially under the following terms: You must give appropriate credit, 
provide a link to the license, and indicate if changes were made. You may do
so in any reasonable manner, but not in any way that suggests the licensor
endorses you or your use.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHOR BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN
ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

The pictures used in this script are from prixabay.com, provided under CC0
and can be freely used without credits.

-----------------------------------------------------------------------------

### Contact ###
Feel free to contact me. You can find me as Purzelkater on
http://forums.rpgmakerweb.com or mail on purzelkater(at)online.de.

-----------------------------------------------------------------------------
*/
	error_reporting(E_ALL);
	
	
	define("USERNAME", "Admin"); // Set your CBAM Manager username!
	define("PASSWORD", "99Katanas");      // Set your CBAM Manager password!
	
	define("GAMENAME",  64); // max length of game names
	define("MAXLENGTH", 16); // max length of user names and password (should be set in plugin too)
	define("ACTION",   255);	
		
		// Actions:		0 = Welcome screen
		//				1 = List games		
		//				2 = Edit game (with gameId)			
		//				3 = Show all player (with gameId)
		//				4 = Show actual session (with gameId)
		//				5 = Show session log (with gameId)
		//				6 = Show savegames (with gameId)
		//				7 = Edit player (with gameId)
		//				8 = Show quicksave (with gameId)
		
		//				11 = Delete a game
		//				13 = Delete (all) player (with gameId / id)	
		//				14 = Delete (all) session data (with gameId / id)
		//				15 = Clear session log (with gameId)
		//				16 = Delete all savegames (with gameId / id)
		//				17 = (unused)
		//				18 = Delete quicksave game (with gameId)
	
		//				20 = Add new game
		//				21 = Init database
		//				22 = Reset database
		//				23 = Add new player
	
	// Open database

	$db = new PDO('sqlite:cbam.sqlite');
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
	$_role            = array(1 => "Normal", 99 => "<b>Admin</b>");

	$_self            = $_SERVER["PHP_SELF"];
	$_userName        = isNullOrEmpty("cbam_username");
	$_password        = isNullOrEmpty("cbam_password");
	$_action          = isNullOrEmpty("action",true);
	$_game            = isNullOrEmpty("game",true);
	$_id              = isNullOrEmpty("id",true);
	$_confirmed       = isNullOrEmpty("confirmed",true);
	$_gameName        = "";
		
	//--> Debug: echo "action: ".$_action." / game: ".$_game." / id: ".$_id." / confirmed: ".$_confirmed;
	
	// Read all parameters
	$__gameName          = isNullOrEmpty("gameName");
	$__loginActorId      = isNullOrEmpty("loginActorId",true,"1");
	$__maxUsers          = isNullOrEmpty("maxUsers",true,"4");
	$__partyMode         = isNullOrEmpty("partyMode",true,"0");
	$__afterNewEvent     = isNullOrEmpty("afterNewEvent",true,"0");
	$__afterLoadEvent    = isNullOrEmpty("afterLoadEvent",true,"0");
	$__beforeSaveEvent   = isNullOrEmpty("beforeSaveEvent",true,"0");
	$__afterPassiveEvent = isNullOrEmpty("afterPassiveEvent",true,"0");
	$__allowRegister     = isNullOrEmpty("allowRegister",true,"1");
	$__allowPassive      = isNullOrEmpty("allowPassive",true,"1");
	$__allowGuests       = isNullOrEmpty("allowGuests",true,"1");
	$__allowResetUser    = isNullOrEmpty("allowResetUser",true,"1");
	$__allowResetGame    = isNullOrEmpty("allowResetGame",true,"0");
	$__allowLoad         = isNullOrEmpty("allowLoad",true,"1");
	$__passiveOnSave     = isNullOrEmpty("passiveOnSave",true,"0");
	$__firstUserAdmin    = isNullOrEmpty("firstUserAdmin",true,"1");
	$__saveOnEnd         = isNullOrEmpty("saveOnEnd",true,"1");
	$__homepage          = isNullOrEmpty("homepage");
	$__logLevel          = isNullOrEmpty("logLevel",true,"1");
	$__userName          = isNullOrEmpty("userName");
	$__password          = isNullOrEmpty("password");	
	$__userRole          = isNullOrEmpty("userRole",true,"1");

	// Extract values from POST
	function isNullOrEmpty($key,$isNumber=false,$default="") {
		if (isset($_POST[$key])) {
			if ($isNumber) {
				return intval($_POST[$key]);
			} else {
				return $_POST[$key];
			};
		} else {
			if ($isNumber) {
				return intval($default);
			} else {
				return $default;
			};					
		};		
	};
	
	function alterTable($db,$table,$column) {
		try {
			
			$db->exec("ALTER TABLE ".$table." ADD COLUMN ".$column);
		
		} catch (Exception $e) {
			//echo "Exception: ".$e->getMessage();
		};
	};
	
	// Returns form "checked" option
	function isChecked($key,$value=1) {
		if (intval($key)==$value) {
			return "checked";
		} else {
			return "";
		};
	};
		
	// Return a button form element
	function button($text,$action=0,$game=0,$id=0,$confirmed=0) {		
		$button = "<form action='".$_SERVER["PHP_SELF"]."' method='post'>";
		$button .= "<input type='hidden' name='cbam_username' value='".$_POST["cbam_username"]."'>";
		$button .= "<input type='hidden' name='cbam_password' value='".$_POST["cbam_password"]."'>";
		if (isset($action)) {
			$button .= "<input type='hidden' name='action' value='".intval($action)."'>";
		};
		if (isset($game)) {
			$button .= "<input type='hidden' name='game' value='".intval($game)."'>";
		};
		if (isset($id)) {
			$button .= "<input type='hidden' name='id' value='".intval($id)."'>";
		};
		if (isset($confirmed)) {
			$button .= "<input type='hidden' name='confirmed' value='".intval($confirmed)."'>";
		};
		$button .= "<button type='submit'>".$text."</button>";
		$button .= "</form> ";
		return $button;
	};
	
	function img_artist() {
		// width='634' height='396'
		return "<img style='max-width: 100%; float: center;' alt='artist.jpg' src='data:image/jpeg;base64,
			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'>";	
	};
	
	function img_manager() {
		return "<img style='float: right; vertical-align:top;' width='339' height='256' alt='manager.png' src='data:image/png;base64,
			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'>";
	};
	
	function img_logo() {
		return "<img style='float: left; vertical-align:middle; margin-right: 1.0em;' width='105' height='64' alt='cloud.png' src='data:image/png;base64,
			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'>";
	}
?>

<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title>CBAM - Manager</title>
	<style type="text/css">

		body {
			/* min-width: 630px; */
			min-width: 980px;
			margin: 0;
			padding: 0;
			font-family: Sans-serif;
			line-height: 1.5em;
			background: #1477A6;
		}

		error {
			color: #f00;
		}
		
		h1 {
			text-shadow: 2px 2px #DEDEDE;
		}
		
		h3 {
			text-shadow: 1px 1px #DEDEDE;
		}	

		main a {
			color: red;
			text-decoration: none;
		}

		main form {
			display: inline;
		}
		
		main small {
			color: grey;
		}
		
		nav ul {
			list-style-type: none;
			margin: 0;
			padding: 0;
		}

		nav ul a {
			color: darkgreen;
			text-decoration: none;
		}
				
		nav h3 {
			-webkit-margin-after: 0.2em;
		}
		
		p {
			color: #555;
		}
			
		td {
			vertical-align: top;
		}
		
		th {
			text-align:left;
		}
		
		
		#center {
			background: #fff;
			padding: 10px 20px;
			width: 100%;
		}

		#container .column {
			padding-top: 1em;
		}
		
		#container {
			padding-left: 200px;
			padding-right: 190px;
			overflow: hidden;
		}
		
		#container .column {
			position: relative;
			float: left;
			padding-bottom: 1001em;
			margin-bottom: -1000em;
		}
		
		#footer {
			clear: both;
			font-size: 0.7em;
			padding: 0.3em;
			background: #1477A6;
			border-top: 3px solid #888;
		}

		#footer a {
			color: #fff;
			text-decoration: underline;		
		}		
		
		#footer p {
			padding-left: 1.0em;
			text-shadow: 2px 2px 2px rgba(0, 0, 0, 0.9);
			color: #fff;
		}	

		#header {
			font-size: large;
			padding: 0.3em;
			background: #1477A6;
			border-bottom: 3px solid #888;	
		}
		
		#header p {
			padding-left: 1.0em;
			text-shadow: 4px 4px 4px rgba(0, 0, 0, 0.9);
			color: #fff;
		}
		
		#left {
			background: #96BEFF;
			width: 180px;
			padding: 0 10px;
			right: 240px;
			margin-left: -100%;
			border-right: 2px solid #1477A6; 
			-webkit-box-shadow: 5px 5px 5px 0px rgba(0,0,0,0.75);
			-moz-box-shadow: 5px 5px 5px 0px rgba(0,0,0,0.75);
			box-shadow: 5px 5px 5px 0px rgba(0,0,0,0.75);	
		}
		
		#right {
			background: #DFEBFF;
			width: 130px;
			padding: 0 10px;
			margin-right: -100%;
			border-left: 1px solid #1477A6; 
			-webkit-box-shadow: -5px 5px 5px 0px rgba(0,0,0,0.75);
			-moz-box-shadow: -5px 5px 5px 0px rgba(0,0,0,0.75);
			box-shadow: -5px 5px 5px 0px rgba(0,0,0,0.75);	
		}
			
		/* IE hack */
		* html #left {
			left: 150px;
		}
		* html body {
			overflow: hidden;
		}		
		* html #footer-wrapper {
			float: left;
			position: relative;
			width: 100%;
			padding-bottom: 10010px;
			margin-bottom: -10000px;
			background: #fff;
		}
		
	</style>
</head>

<body>
	<!-- Header -->
	<div id="header"><p>
		<?php echo img_logo(); ?>
		<small>RPG Maker MV</small><br />Cloud Based Asynchronous Multiplayer
	</p></div>

	<!-- Center main content -->
	<div id="container">
		<main id="center" class="column">
			<article>	
				<?php
					if ($_userName==USERNAME && $_password==PASSWORD) {
				
						if ($_action==0) {
							/*************************************************************************************************
							  WELCOME TO CBAM - SHOW SELECTED GAME SUMMARY
							*************************************************************************************************/
							if ($_game==0) {
								echo "<h1>Welcome to CBAM - Manager</h1>";
								echo "<h3>for RPG Maker MV</h3>";
								echo "<p>This manager is the server side companion app for the CBAM plugin, made for RPG Maker MV. Please use it carefully.</p>";
								echo "<p>Feel free to contat me on <a href='http://forums.rpgmakerweb.com/index.php?/profile/81913-purzelkater/' target='_blank'>official RPG Maker forum</a>.</p>";
							} else {
								$sql = "SELECT * FROM game WHERE id=".$_game;
								$rows = $db->query($sql)->fetchAll();

								if (count($rows)>0) {
									$_gameName = $rows[0]["gameName"];
									echo "<h1>".$_gameName."</h1>";
									
									echo "<p><b>Registration</b>: ".date('Y-m-d h:i:s A', $rows[0]["registration"]);
									echo "<p><b>Homepage</b>: <a href='".$rows[0]["homepage"]."' target='_blank'>Open link</a>";
									echo "<p>";
									$sql = "SELECT COUNT(*) AS counter FROM user WHERE gameName='".$_gameName."'";
									$rows = $db->query($sql)->fetchAll();
									if (count($rows)>0) {	
										echo "<b>Registered user</b>: ".$rows[0]["counter"];
									} else {
										echo "<b>Registered user</b>: 0";									
									};
									echo "<br>";
									$sql = "SELECT COUNT(*) AS counter FROM sessiondata WHERE gameName='".$_gameName."'";
									$rows = $db->query($sql)->fetchAll();
									if (count($rows)>0) {	
										echo "<b>Player in game</b>: ".$rows[0]["counter"];
									} else {
										echo "<b>Player in game</b>: 0";
									};
									echo "<br>";
									$sql = "SELECT COUNT(*) AS counter FROM savegame WHERE gameName='".$_gameName."'";
									$rows = $db->query($sql)->fetchAll();
									if (count($rows)>0) {	
										echo "<b>Savegames</b>: ".$rows[0]["counter"];
									} else {
										echo "<b>Savegames</b>: 0";
									};	
									echo "</p>";
									
								} else {
									echo "<h1>Welcome to CBAM - Manager</h1>";
									echo "<h3>Game #".$_game." not found</h3>";
									echo button("Back to game list",1);
									$_game = 0;
								};	
							};
							echo img_artist();
						} elseif ($_action==1) {
							try {
							/*************************************************************************************************
							  LIST ALL GAMES - CHOOSE ONE!
							*************************************************************************************************/					
							echo "<h1>Games</h1>";
						
							$sql = "SELECT * FROM game ORDER BY gameName";
							$rows = $db->query($sql)->fetchAll();
			
							if (count($rows)>0) {
								echo "<h3>Please select a game!</h3>";
								echo "<p><table width='99%'>";
								echo "<tr>";
								echo "<th>Name</th>";
								echo "<th>Added</th>";
								echo "<th></th>";
								echo "<th></th>";
								echo "</tr>";							
								foreach ($rows as $row) {
									echo "<tr>";
									echo "<td><b>".$row["gameName"]."</b></td>";
									echo "<td>".date('Y-m-d h:i:s A', $row["registration"])."</td>";
									echo "<td>".button("Select",0,$row["id"])."</td>";
									echo "<td>".button("Delete",11,$row["id"])."</td>";
									echo "</tr>";
								};
								echo "</table></p>";
							} else {
								echo "<h3>No games found</h3>";
								echo "<p><error>Add a game, if you want!</error></p>";
							};
								echo img_artist();
							} catch (Exception $e) {
								echo 'Exception: '.$e->getMessage()."<br>";
							};

						} elseif ($_action==20) {
							/*************************************************************************************************
							  ADD NEW GAME - FORM INPUT
							*************************************************************************************************/
							$error = "";
							if ($_confirmed==1) {					
								if ($__gameName=="") {
									$error = "Game name is empty. Please enter a name";
									$_confirmed = 0;
								} else {
									$sql = "SELECT * FROM game WHERE gameName='".$__gameName."'";
									$rows = $db->query($sql)->fetchAll();		
									if (count($rows)>0) {
										$error = "Game exists. Please change the name!";
										$_confirmed = 0;
									};							
								};
							};
							
							// Show game input form
							if ($_confirmed==1) {
								echo "<h1>".$__gameName."</h1>";
								
								$sql = "INSERT INTO game (gameName,loginActorId,maxUsers,partyMode,afterNewEvent,afterLoadEvent,beforeSaveEvent,afterPassiveEvent,allowRegister,allowPassive,allowGuests,allowResetUser,allowResetGame,firstUserAdmin,saveOnEnd,allowLoad,passiveOnSave,homepage,logLevel,registration) VALUES ("
									."'".$__gameName."',"
									.$__loginActorId.","
									.$__maxUsers.","
									.$__partyMode.","	
									.$__afterNewEvent.","
									.$__afterLoadEvent.","
									.$__beforeSaveEvent.","
									.$__afterPassiveEvent.","
									.$__allowRegister.","
									.$__allowPassive.","
									.$__allowGuests.","
									.$__allowResetUser.","
									.$__allowResetGame.","
									.$__firstUserAdmin.","
									.$__saveOnEnd.","
									.$__allowLoad.","
									.$__passiveOnSave.","
									."'".$__homepage."',"
									.$__logLevel.","
									.time().")";
								
								if (($db->exec($sql))>0) {
									echo "<p>Game added to database.</p>";
								} else {
									echo "<p><error>Add game failed.</error></p>";
								};								
							} else {
								echo "<h1>Add new game</h1>";
								echo "<h3>Edit game settings</h3>";
								
								if ($error!="") {
									echo "<p><error>".$error."</error></p>";
								};
								
								echo "<table>";
								echo "<form action='".$_self."' method='post'>";
								
								echo "<input type='hidden' name='cbam_username' value='".$_userName."'>";
								echo "<input type='hidden' name='cbam_password' value='".$_password."'>";							
								
								echo "<input type='hidden' name='action' value='20'>";
								echo "<input type='hidden' name='confirmed' value='1'>";

								echo "<tr>";
								echo "<td nowrap><label for='gameName'>Game Name:</label></td>";
								echo "<td><input type='text' name='gameName' size='40' maxlength='".GAMENAME."' value='".$__gameName."' /></td>";
								echo "<td><small>The name of the game (<b>must be unique</b> like an identifier).</small></td>";
								echo "</tr>";

								echo "<tr>";
								echo "<td nowrap><label for='loginActorId'>Guest ActorId:</label></td>";
								echo "<td><input type='text' name='loginActorId' size='4' maxlength='4' value='".$__loginActorId."' /></td>";
								echo "<td><small>The id of the actor (look into your RMMV database) used for guests and storing login name.</small></td>";
								echo "</tr>";

								echo "<tr>";
								echo "<td nowrap><label for='maxUsers'>Max. User:</label></td>";
								echo "<td><input type='text' name='maxUsers' size='4' maxlength='4' value='".$__maxUsers."' /></td>";
								echo "<td><small>How many user can have an login for this game?</small></td>";
								echo "</tr>";
								
								echo "<tr>";
								echo "<td nowrap><label for='partyMode'>Party Mode:</label></td>";
								echo "<td><input type='radio' name='partyMode' value='0' ".isChecked($__partyMode,0)."> None<br>";
								echo "<input type='radio' name='partyMode' value='1' ".isChecked($__partyMode,1)."> First Player<br>";
								echo "<input type='radio' name='partyMode' value='2' ".isChecked($__partyMode,2)."> One Player</td>";
								echo "<td><small>None: The plugin will not change the parties or actors itself (recommend for use with <b>HIME Party Manager</b>).<br>First player: CBAM will re-sort the party actors to make the the player actor first/leader.<br>One Player: CBAM will clear the party and adds only the player actor again.</small></td>";
								echo "<tr>";

								echo "<tr>";
								echo "<td nowrap><label for='afterNewEvent'>After New Event:</label></td>";
								echo "<td><input type='text' name='afterNewEvent' size='4' maxlength='4' value='".$__afterNewEvent."' /></td>";
								echo "<td><small>Which common event (see your RMMV database) should be executed after new a new game was started (can be used to intialize the actor party, 0=disabled)?</small></td>";
								echo "</tr>";

								echo "<tr>";
								echo "<td nowrap><label for='afterLoadEvent'>After Load Event:</label></td>";
								echo "<td><input type='text' name='afterLoadEvent' size='4' maxlength='4' value='".$__afterLoadEvent."' /></td>";
								echo "<td><small>Which common event (see your RMMV database) should be executed after new a savegame was loaded (can be used to intialize the actor party, 0=disabled)?</small></td>";
								echo "</tr>";

								echo "<tr>";
								echo "<td nowrap><label for='beforeSaveEvent'>Before Save Event:</label></td>";
								echo "<td><input type='text' name='beforeSaveEvent' size='4' maxlength='4' value='".$__beforeSaveEvent."' /></td>";
								echo "<td><small>Which common event (see your RMMV database) should be executed before your game will be saved (attention: maybe this event will executed AFTER the game was saved, 0=disabled)?</small></td>";
								echo "</tr>";

								echo "<tr>";
								echo "<td nowrap><label for='afterPassiveEvent'>After Passive Event:</label></td>";
								echo "<td><input type='text' name='afterPassiveEvent' size='4' maxlength='4' value='".$__afterPassiveEvent."' /></td>";
								echo "<td><small>Which common event (see your RMMV database) should be executed after the game was saved and the player become passive (0=disabled)?</small></td>";
								echo "</tr>";
								
								echo "<tr>";
								echo "<td nowrap><label for='allowRegister'>Allow Register:</label></td>";
								echo "<td><input type='checkbox' name='allowRegister' value='1' ".isChecked($__allowRegister)."></td>";
								echo "<td><small>Can an user register themselves on first login if there are free places on the game (see Max. Users)?</small></td>";
								echo "</tr>";
								
								echo "<tr>";
								echo "<td nowrap><label for='allowPassive'>Allow Passive:</label></td>";
								echo "<td><input type='checkbox' name='allowPassive' value='1' ".isChecked($__allowPassive)."></td>";
								echo "<td><small>Can a player join the game in passive (view only) mode (passive player can't save a game)?</small></td>";
								echo "</tr>";

								echo "<tr>";
								echo "<td nowrap><label for='allowGuests'>Allow Guests:</label></td>";
								echo "<td><input type='checkbox' name='allowGuests' value='1' ".isChecked($__allowGuests)."></td>";
								echo "<td><small>Can an user join the game without login (guest are view only and can't save a game, Passive mode must be allowed too for this)?</small></td>";
								echo "</tr>";
								
								echo "<tr>";
								echo "<td nowrap><label for='allowResetUser'>Allow Reset User:</label></td>";
								echo "<td><input type='checkbox' name='allowResetUser' value='1' ".isChecked($__allowResetUser)."></td>";
								echo "<td><small>ADMIN ONLY: Can an admin remove the actual active player?</small></td>";
								echo "</tr>";

								echo "<tr>";
								echo "<td nowrap><label for='allowResetGame'>Allow Reset Game:</label></td>";
								echo "<td><input type='checkbox' name='allowResetGame' value='1' ".isChecked($__allowResetGame)."></td>";
								echo "<td><small>ADMIN ONLY: Can an admin reset all game settings on the server? Warning: This will overwrite all server side settings with plugin defaults.</small></td>";
								echo "</tr>";

								echo "<tr>";
								echo "<td nowrap><label for='firstUserAdmin'>First is Admin:</label></td>";
								echo "<td><input type='checkbox' name='firstUserAdmin' value='1' ".isChecked($__firstUserAdmin)."></td>";
								echo "<td><small>Will the first registered user become an admin (Allow register must be enabled too)?</small></td>";
								echo "</tr>";
								
								echo "<tr>";
								echo "<td nowrap><label for='saveOnEnd'>Save on End:</label></td>";
								echo "<td><input type='checkbox' name='saveOnEnd' value='1' ".isChecked($__saveOnEnd)."></td>";
								echo "<td><small>Should the game be saved automatically if the active player exits the game to title screen?</small></td>";
								echo "</tr>";
								
								echo "<tr>";
								echo "<td nowrap><label for='allowLoad'>Allow Load:</label></td>";
								echo "<td><input type='checkbox' name='allowLoad' value='1' ".isChecked($__allowLoad)."></td>";
								echo "<td><small>Can the player load the game from the command menu? If possible, the player would become active with this.</small></td>";
								echo "</tr>";								
								
								echo "<tr>";
								echo "<td nowrap><label for='passiveOnSave'>Passive after Save:</label></td>";
								echo "<td><input type='checkbox' name='passiveOnSave' value='1' ".isChecked($__passiveOnSave)."></td>";
								echo "<td><small>Switch to passive after the game was saved on command menu?</small></td>";
								echo "</tr>";
								
								echo "<tr>";
								echo "<td nowrap><label for='homepage'>Homepage:</label></td>";
								echo "<td><input type='text' name='homepage' size='40' maxlength='255' value='".$__homepage."' /></td>";
								echo "<td><small>Enter URL/Link to the gmae homepage (for credits, infos, etc.)!</small></td>";
								echo "</tr>";

								echo "<tr>";
								echo "<td nowrap><label for='logLevel'>Logging Level:</label></td>";
								echo "<td><input type='radio' name='logLevel' value='0' ".isChecked($__logLevel,0)."> Only script errors<br>";
								echo "<input type='radio' name='logLevel' value='1' ".isChecked($__logLevel,1)."> Errors<br>";
								echo "<input type='radio' name='logLevel' value='2' ".isChecked($__logLevel,2)."> Error &amp; Warnings<br>";
								echo "<input type='radio' name='logLevel' value='3' ".isChecked($__logLevel,3)."> Server responses<br>";
								echo "<input type='radio' name='logLevel' value='4' ".isChecked($__logLevel,4)."> All</td>";
								echo "<td><small>Select how many infos will be shown on console log!</small></td>";
								echo "<tr>";							
								
								echo "<tr><td colspan='3'>&nbsp;</td></tr>";
								echo "<tr><td colspan='3'><button type='submit'>Add game</button></td></tr>";
							
								echo "</form>";
								echo "</table>";
							};
						} elseif ($_action==21) {
							/*************************************************************************************************
							  INITIALIZE DATABASE
							*************************************************************************************************/					
							echo "<h1>Initialize Database</h1>";
							
							echo "<p>Hint: This will only add new tables if needed. Existing data will not be changed.</p>";

							try {						
							
								echo "Create table <b>game</b>: ";
								$db->exec("CREATE TABLE IF NOT EXISTS game(
									id INTEGER PRIMARY KEY AUTOINCREMENT,
									gameName VARCHAR(".GAMENAME.") NOT NULL DEFAULT '',
									loginActorId INT NOT NULL DEFAULT 1,
									maxUsers INT NOT NULL DEFAULT 4,
									partyMode TINYINT NOT NULL DEFAULT 0,
									afterNewEvent INT NOT NULL DEFAULT 0,
									afterLoadEvent INT NOT NULL DEFAULT 0,
									beforeSaveEvent INT NOT NULL DEFAULT 0,
									afterPassiveEvent INT NOT NULL DEFAULT 0,
									allowRegister TINYINT NOT NULL DEFAULT 1,
									allowPassive TINYINT NOT NULL DEFAULT 1,
									allowGuests TINYINT NOT NULL DEFAULT 1,
									allowResetUser TINYINT NOT NULL DEFAULT 1,
									allowResetGame TINYINT NOT NULL DEFAULT 1,
									firstUserAdmin TINYINT NOT NULL DEFAULT 1,
									saveOnEnd TINYINT NOT NULL DEFAULT 1,
									allowLoad TINYINT NOT NULL DEFAULT 1,
									passiveOnSave TINYINT NOT NULL DEFAULT 0,
									homepage VARCHAR(255) NOT NULL DEFAULT '',
									logLevel TINYINT NOT NULL DEFAULT 1,
									registration DATETIME)");
								echo("done<br>");
								
								alterTable($db,"game","allowLoad TINYINT NOT NULL DEFAULT 1");
								alterTable($db,"game","passiveOnSave TINYINT NOT NULL DEFAULT 0");
								alterTable($db,"game","afterPassiveEvent INT NOT NULL DEFAULT 0");
						
								echo "Create table <b>user</b>: ";
								$db->exec("CREATE TABLE IF NOT EXISTS user(
									id INTEGER PRIMARY KEY AUTOINCREMENT,
									gameName VARCHAR(".GAMENAME.") NOT NULL DEFAULT '',
									userName VARCHAR(".MAXLENGTH.") NOT NULL DEFAULT '',
									password VARCHAR(".MAXLENGTH.") NOT NULL DEFAULT '',
									userRole INYINT NOT NULL DEFAULT 0,
									gameActorId INTEGER NOT NULL DEFAULT 0,
									registration DATETIME,
									lastSeen DATETIME,
									lastChange DATETIME)");
								echo("done<br>");
				
								echo "Create table <b>savegame</b>: ";
								$db->exec("CREATE TABLE IF NOT EXISTS savegame(
									id INTEGER PRIMARY KEY AUTOINCREMENT,
									gameName VARCHAR(".GAMENAME.") NOT NULL DEFAULT '',
									gameVersion FLOAT NOT NULL DEFAULT 0,
									userName VARCHAR(".MAXLENGTH.") NOT NULL DEFAULT '',
									gameData TEXT NOT NULL DEFAULT '',
									gameInfo TEXT NOT NULL DEFAULT '',
									lastChange DATETIME)");
								echo("done<br>");
							
								echo "Create table <b>quicksave</b>: ";
								$db->exec("CREATE TABLE IF NOT EXISTS quicksave(
									id INTEGER PRIMARY KEY AUTOINCREMENT,
									gameName VARCHAR(".GAMENAME.") NOT NULL DEFAULT '',
									gameVersion FLOAT NOT NULL DEFAULT 0,
									userName VARCHAR(".MAXLENGTH.") NOT NULL DEFAULT '',
									gameData TEXT NOT NULL DEFAULT '',
									gameInfo TEXT NOT NULL DEFAULT '',
									lastChange DATETIME)");
								echo("done<br>");						
							
								echo "Create table <b>sessiondata</b>: ";
								$db->exec("CREATE TABLE IF NOT EXISTS sessiondata(
									id INTEGER PRIMARY KEY AUTOINCREMENT,
									gameName VARCHAR(".GAMENAME.") NOT NULL DEFAULT '',
									gameVersion FLOAT NOT NULL DEFAULT 0,
									userName VARCHAR(".MAXLENGTH.") NOT NULL DEFAULT '',
									action VARCHAR(".ACTION.") NOT NULL DEFAULT '',
									lastChange DATETIME)");
								echo("done<br>");
			
								echo "Create table <b>session</b>: ";
								$db->exec("CREATE TABLE IF NOT EXISTS session(
									id INTEGER PRIMARY KEY AUTOINCREMENT,
									gameName VARCHAR(".GAMENAME.") NOT NULL DEFAULT '',
									gameVersion FLOAT NOT NULL DEFAULT 0,
									userName VARCHAR(".MAXLENGTH.") NOT NULL DEFAULT '',
									action VARCHAR(".ACTION.") NOT NULL DEFAULT '',
									lastChange DATETIME)");
								echo("done<br>");
				
								echo "Finished<br />";
							
							} catch (PDOException $e) {
								echo "Exception: ".$e->getMessage()."<br>";
							};
				
				
							
			
						} elseif ($_action==22) {
							/*************************************************************************************************
							  RESET DATABASE
							*************************************************************************************************/					
							echo "<h1>Reset Database</h1>";
							
							echo "<p>Attention: You must re-initialize the database in order to use CBAM again.</p>";
							
							if ($_confirmed==1) {
								echo "Delete table <b>game</b>: ";
								$db->exec("DROP TABLE IF EXISTS game");
								echo("done<br>");

								echo "Delete table <b>user</b>: ";
								$db->exec("DROP TABLE IF EXISTS user");
								echo("done<br>");
				
								echo "Delete table <b>savegame</b>: ";
								$db->exec("DROP TABLE IF EXISTS savegame");
								echo("done<br>");
								
								echo "Delete table <b>quicksave</b>: ";
								$db->exec("DROP TABLE IF EXISTS quicksave");
								echo("done<br>");							
							
								echo "Delete table <b>sessiondata</b>: ";
								$db->exec("DROP TABLE IF EXISTS sessiondata");
								echo("done<br>");
			
								echo "Delete table <b>session</b>: ";
								$db->exec("DROP TABLE IF EXISTS session");
								echo("done<br>");
				
								echo "Finished<br />";
							
							} else {
								echo "<p>Du you REALY want to delete ALL data?</p>";
								echo "<p><b><error>WARNING: All data will be lost! You will delete all games, all users and all savedata.</error></b></p>";
								echo button("Yes",22,0,0,1);
								echo button("No");
							};
						} else {
							/*************************************************************************************************
							  ACTION!
							*************************************************************************************************/					
							$sql = "SELECT * FROM game WHERE id=".$_game;
							$rows = $db->query($sql)->fetchAll();
							
							if (count($rows)>0) {
								$_gameName = $rows[0]["gameName"];
								echo "<h1>".$_gameName."</h1>";					
						
								switch ($_action) {
								//---------------------------------------------------------------------------------------
									case 2: // Edit game
										echo "<h3>Edit game settings</h3>";
										
										$error = "";
										if ($_confirmed==1) {					
											if ($__gameName=="") {
												$error = "Game name was empty. Please enter a name";
												$_confirmed = 0;
											} else {
												$sql = "SELECT * FROM game WHERE gameName='".$__gameName."' AND NOT id=".$_game;
												$rows = $db->query($sql)->fetchAll();		
												if (count($rows)>0) {
													$error = "Game exists. Please change the name!";
													$_confirmed = 0;
												};							
											};

										};									

										if ($_confirmed==1) {
											// Game name has changed
											echo "<p>";
											if ($_gameName!=$__gameName) {
												$sql = "UPDATE game SET gameName='".$__gameName."' WHERE id=".$_game;
												echo "Update Game Name: <b>".$db->exec($sql)."</b><br>";
												
												$sql = "UPDATE user SET gameName='".$__gameName."' WHERE gameName='".$_gameName."'";
												echo "Update User: <b>".$db->exec($sql)."</b><br>";
												
												$sql = "UPDATE savegame SET gameName='".$__gameName."' WHERE gameName='".$_gameName."'";
												echo "Update Savegames: <b>".$db->exec($sql)."</b><br>";
												
												$sql = "UPDATE sessiondata SET gameName='".$__gameName."' WHERE gameName='".$_gameName."'";
												echo "Update Session Data: <b>".$db->exec($sql)."</b><br>";
												
												$sql = "UPDATE session SET gameName='".$__gameName."' WHERE gameName='".$_gameName."'";
												echo "Update Sessions: <b>".$db->exec($sql)."</b><br>";							
											};
											
											$sql = "UPDATE game SET "
												."loginActorId=".$__loginActorId.","
												."maxUsers=".$__maxUsers.","
												."partyMode=".$__partyMode.","
												."afterNewEvent=".$__afterNewEvent.","
												."afterLoadEvent=".$__afterLoadEvent.","
												."beforeSaveEvent=".$__beforeSaveEvent.","
												."afterPassiveEvent=".$__afterPassiveEvent.","
												."allowRegister=".$__allowRegister.","
												."allowPassive=".$__allowPassive.","
												."allowGuests=".$__allowGuests.","
												."allowResetUser=".$__allowResetUser.","
												."allowResetGame=".$__allowResetGame.","
												."firstUserAdmin=".$__firstUserAdmin.","
												."saveOnEnd=".$__saveOnEnd.","
												."allowLoad=".$__allowLoad.","
												."passiveOnSave=".$__passiveOnSave.","
												."homepage='".$__homepage."',"
												."logLevel=".$__logLevel." WHERE id=".$_game;
												
											echo "Update Game Settings: <b>".$db->exec($sql)."</b><br>";

											echo "Finished</p>";
											
										} else {
											if ($error!="") {
												echo "<p><error>".$error."</error></p>";
											};
										
											echo "<table>";
											echo "<form action='".$_self."' method='post'>";
											echo "<input type='hidden' name='cbam_username' value='".$_userName."'>";
											echo "<input type='hidden' name='cbam_password' value='".$_password."'>";										
											echo "<input type='hidden' name='action' value='2'>";
											echo "<input type='hidden' name='confirmed' value='1'>";
											echo "<input type='hidden' name='game' value='".$_game."'>";
											echo "<input type='hidden' name='id' value='".$_id."'>";

											echo "<tr>";
											echo "<td nowrap><label for='gameName'>Game Name:</label></td>";
											echo "<td><input type='text' name='gameName' size='40' maxlength='".GAMENAME."' value='".$rows[0]["gameName"]."'></td>";
											echo "<td><small>The name of the game (<b>must be unique</b> like an identifier).</small></td>";
											echo "</tr>";

											echo "<tr>";
											echo "<td nowrap><label for='loginActorId'>Guest ActorId:</label></td>";
											echo "<td><input type='text' name='loginActorId' size='4' maxlength='4' value='".$rows[0]["loginActorId"]."'></td>";
											echo "<td><small>The id of the actor (look into your RMMV database) used for guests and storing login name.</small></td>";
											echo "</tr>";

											echo "<tr>";
											echo "<td nowrap><label for='maxUsers'>Max. User:</label></td>";
											echo "<td><input type='text' name='maxUsers' size='4' maxlength='4' value='".$rows[0]["maxUsers"]."'></td>";
											echo "<td><small>How many user can have an login for this game?</small></td>";
											echo "</tr>";
								
											echo "<tr>";
											echo "<td nowrap><label for='partyMode'>Party Mode:</label></td>";
											echo "<td><input type='radio' name='partyMode' value='0' ".isChecked($rows[0]["partyMode"],0)."> None<br>";
											echo "<input type='radio' name='partyMode' value='1' ".isChecked($rows[0]["partyMode"],1)."> First Player<br>";
											echo "<input type='radio' name='partyMode' value='2' ".isChecked($rows[0]["partyMode"],2)."> One Player</td>";
											echo "<td><small>None: The plugin will not change the parties or actors itself (recommend for use with <b>HIME Party Manager</b>).<br>First player: CBAM will re-sort the party actors to make the the player actor first/leader.<br>One Player: CBAM will clear the party and adds only the player actor again.</small></td>";
											echo "<tr>";
		
											echo "<tr>";
											echo "<td nowrap><label for='afterNewEvent'>After New Event:</label></td>";
											echo "<td><input type='text' name='afterNewEvent' size='4' maxlength='4' value='".$rows[0]["afterNewEvent"]."'></td>";
											echo "<td><small>Which common event (see your RMMV database) should be executed after new a new game was started (can be used to intialize the actor party, 0=disabled)?</small></td>";
											echo "</tr>";

											echo "<tr>";
											echo "<td nowrap><label for='afterLoadEvent'>After Load Event:</label></td>";
											echo "<td><input type='text' name='afterLoadEvent' size='4' maxlength='4' value='".$rows[0]["afterLoadEvent"]."'></td>";
											echo "<td><small>Which common event (see your RMMV database) should be executed after new a savegame was loaded (can be used to intialize the actor party, 0=disabled)?</small></td>";
											echo "</tr>";

											echo "<tr>";
											echo "<td nowrap><label for='beforeSaveEvent'>Before Save Event:</label></td>";
											echo "<td><input type='text' name='beforeSaveEvent' size='4' maxlength='4' value='".$rows[0]["beforeSaveEvent"]."'></td>";
											echo "<td><small>Which common event (see your RMMV database) should be executed before your game will be saved (attention: maybe this event will executed AFTER the game was saved, 0=disabled)?</small></td>";
											echo "</tr>";

											echo "<tr>";
											echo "<td nowrap><label for='afterPassiveEvent'>After Passive Event:</label></td>";
											echo "<td><input type='text' name='afterPassiveEvent' size='4' maxlength='4' value='".$rows[0]["afterPassiveEvent"]."'></td>";
											echo "<td><small>Which common event (see your RMMV database) should be executed after new a the game was saved and the player become passive (0=disabled)?</small></td>";
											echo "</tr>";											
											
											echo "<tr>";
											echo "<td nowrap><label for='allowRegister'>Allow Register:</label></td>";
											echo "<td><input type='checkbox' name='allowRegister' value='1' ".isChecked($rows[0]["allowRegister"])."></td>";
											echo "<td><small>Can an user register themselves on first login if there are free places on the game (see Max. Users)?</small></td>";
											echo "</tr>";
								
											echo "<tr>";
											echo "<td nowrap><label for='allowPassive'>Allow Passive:</label></td>";
											echo "<td><input type='checkbox' name='allowPassive' value='1' ".isChecked($rows[0]["allowPassive"])."></td>";
											echo "<td><small>Can a player join the game in passive (view only) mode (passive player can't save a game)?</small></td>";
											echo "</tr>";
		
											echo "<tr>";
											echo "<td nowrap><label for='allowGuests'>Allow Guests:</label></td>";
											echo "<td><input type='checkbox' name='allowGuests' value='1' ".isChecked($rows[0]["allowGuests"])."></td>";
											echo "<td><small>Can an user join the game without login (guest are view only and can't save a game, Passive mode must be allowed too for this)?</small></td>";
											echo "</tr>";
									
											echo "<tr>";
											echo "<td nowrap><label for='allowResetUser'>Allow Reset User:</label></td>";
											echo "<td><input type='checkbox' name='allowResetUser' value='1' ".isChecked($rows[0]["allowResetUser"])."></td>";
											echo "<td><small>ADMIN ONLY: Can an admin remove the actual active player?</small></td>";
											echo "</tr>";

											echo "<tr>";
											echo "<td nowrap><label for='allowResetGame'>Allow Reset Game:</label></td>";
											echo "<td><input type='checkbox' name='allowResetGame' value='1' ".isChecked($rows[0]["allowResetGame"])."></td>";
											echo "<td><small>ADMIN ONLY: Can an admin reset all game settings on the server? Warning: This will overwrite all server side settings with plugin defaults.</small></td>";
											echo "</tr>";

											echo "<tr>";
											echo "<td nowrap><label for='firstUserAdmin'>First is Admin:</label></td>";
											echo "<td><input type='checkbox' name='firstUserAdmin' value='1' ".isChecked($rows[0]["firstUserAdmin"])."></td>";
											echo "<td><small>Will the first registered user become an admin (Allow register must be enabled too)?</small></td>";
											echo "</tr>";
								
											echo "<tr>";
											echo "<td nowrap><label for='saveOnEnd'>Save on End:</label></td>";
											echo "<td><input type='checkbox' name='saveOnEnd' value='1' ".isChecked($rows[0]["saveOnEnd"])."></td>";
											echo "<td><small>Should the game be saved automatically if the active player exits the game to title screen?</small></td>";
											echo "</tr>";
											

											echo "<tr>";
											echo "<td nowrap><label for='allowLoad'>Allow Load:</label></td>";
											echo "<td><input type='checkbox' name='allowLoad' value='1' ".isChecked($rows[0]["allowLoad"])."></td>";
											echo "<td><small>Can the player load the game from the command menu? If possible, the player would become active with this.</small></td>";
											echo "</tr>";								
											
											echo "<tr>";
											echo "<td nowrap><label for='passiveOnSave'>Passive after Save:</label></td>";
											echo "<td><input type='checkbox' name='passiveOnSave' value='1' ".isChecked($rows[0]["passiveOnSave"])."></td>";
											echo "<td><small>Switch to passive after the game was saved on command menu?</small></td>";
											echo "</tr>";											

											echo "<tr>";
											echo "<td nowrap><label for='homepage'>Homepage:</label></td>";
											echo "<td><input type='text' name='homepage' size='40' maxlength='255' value='".$rows[0]["homepage"]."'></td>";
											echo "<td><small>Enter URL/Link to the gmae homepage (for credits, infos, etc.)!</small></td>";
											echo "</tr>";
		
											echo "<tr>";
											echo "<td nowrap><label for='logLevel'>Logging Level:</label></td>";
											echo "<td><input type='radio' name='logLevel' value='0' ".isChecked($rows[0]["logLevel"],0)."> Only script errors<br>";
											echo "<input type='radio' name='logLevel' value='1' ".isChecked($rows[0]["logLevel"],1)."> Errors<br>";
											echo "<input type='radio' name='logLevel' value='2' ".isChecked($rows[0]["logLevel"],2)."> Error &amp; Warnings<br>";
											echo "<input type='radio' name='logLevel' value='3' ".isChecked($rows[0]["logLevel"],3)."> Server responses<br>";
											echo "<input type='radio' name='logLevel' value='4' ".isChecked($rows[0]["logLevel"],4)."> All</td>";
											echo "<td><small>Select how many infos will be shown on console log!</small></td>";
											echo "<tr>";							
								
											echo "<tr><td colspan='3'>&nbsp;</td></tr>";
											echo "<tr><td colspan='3'><button type='submit'>Change game</button></td></tr>";
							
											echo "</form>";
											echo "</table>";									
										};
										
										break;
								//---------------------------------------------------------------------------------------
									case 3: // Show player list								
										echo "<h3>Show player list</h3>";
									
										$sql = "SELECT * FROM user WHERE gameName='".$_gameName."' ORDER BY userName";
										$rows = $db->query($sql)->fetchAll();
			
										if (count($rows)>0) {							
											echo "<p><table width='99%'>";
											echo "<tr><th>Login</th><th>State</th><th>Actor Id</th><th>Registration</th><th>Last seen</th><th></th><th></th></tr>";														
											foreach ($rows as $row) {
													echo "<tr><td>".$row["userName"]."</td><td>".$_role[$row["userRole"]]."</td><td>".$row["gameActorId"]."</td><td>".date('Y-m-d h:i:s A', $row["registration"])."</td><td>";
													if (isset($row["lastSeen"])) {
														echo date('Y-m-d h:i:s A', $row["lastSeen"]);
													} else {
														echo "Never";
													};												
													echo "</td><td>";
													echo button("Edit",7,$_game,$row["id"]);
													echo "</td><td>";
													echo button("Delete",13,$_game,$row["id"]);
													echo "</td></tr>";
											};
											echo "</table></p>";
											echo button("Delete all player",13,$_game);
										} else {							
											echo "<p><error>No player for this game found.</error></p>";
										};
								
										break;
									case 4: // Show session data							
										echo "<h3>Show active session data</h3>";
									
										$sql = "SELECT * FROM sessiondata WHERE gameName='".$_gameName."' ORDER BY userName";
										$rows = $db->query($sql)->fetchAll();
			
										if (count($rows)>0) {							
											echo "<p><table width='99%'>";
											echo "<tr><th>Version</th><th>User</th><th>State</th><th>Date/Time</th><th></th></tr>";														
											foreach ($rows as $row) {
												if ($row["action"]=="active") {
													echo "<tr><td>V".$row["gameVersion"]."</td><td>".$row["userName"]."</td><td>".$row["action"]."</td><td>".date('Y-m-d h:i:s A', $row["lastChange"])."</td><td>";
													echo button("Delete",14,$_game,$row["id"]);
													echo "</td></tr>";
												} else {
													echo "<tr><td>V".$row["gameVersion"]."</td><td>".$row["userName"]."</td><td>".$row["action"]."</td><td>".date('Y-m-d h:i:s A', $row["lastChange"])."</td><td></td></tr>";
												};
											};
											echo "</table></p>";
											echo button("Delete session data",14,$_game);
										} else {							
											echo "<p><error>No active session for this game found.</error></p>";
										};
								
										break;
									//---------------------------------------------------------------------------------------
									case 5: // Show session log						
										echo "<h3>Show session log</h3>";
									
										$sql = "SELECT * FROM session WHERE gameName='".$_gameName."' ORDER BY id DESC";
										$rows = $db->query($sql)->fetchAll();
			
										if (count($rows)>0) {							
											echo "<p><table width='99%'>";
											echo "<tr><th>Version</th><th>User</th><th>Action</th><th>Date/Time</th></tr>";														
											foreach ($rows as $row) {
												echo "<tr><td>V".$row["gameVersion"]."</td><td>".$row["userName"]."</td><td>".$row["action"]."</td><td>".date('Y-m-d h:i:s A', $row["lastChange"])."</td></tr>";
											};
											echo "</table></p>";
											echo button("Delete session log",15,$_game);
										} else {							
											echo "<p><error>No session log for this game found.</error></p>";
										};
									
										break;
									//---------------------------------------------------------------------------------------
									case 6: // Show savegame
										echo "<h3>Show savegames</h3>";
									
										$count = 0;
										$sql = "SELECT * FROM savegame WHERE gameName='".$_gameName."' ORDER BY id DESC";
										$rows = $db->query($sql)->fetchAll();

										if (count($rows)>0) {							
											echo "<p><table width='99%'>";
											echo "<tr><th>Version</th><th>User</th><th>Playtime</th><th>Date/Time</th><th></th></tr>";														
											foreach ($rows as $row) {
												$info = json_decode($row["gameInfo"]);
												$count++;
												if ($count==1) {
													echo "<tr><th>V".$row["gameVersion"]."</th><th>".$row["userName"]."</th><th>".$info->playtime."</th><th>".date('Y-m-d h:i:s A', $row["lastChange"])."</th><th>";
													echo button("Delete",16,$_game,$row["id"]);
													echo "</th></tr>";
												} else {
													echo "<tr><td>V".$row["gameVersion"]."</td><td>".$row["userName"]."</td><td>".$info->playtime."</td><td>".date('Y-m-d h:i:s A', $row["lastChange"])."</td><td>";
													echo button("Delete",16,$_game,$row["id"]);
													echo "</td></tr>";
												};
											};
											echo "</table></p>";
											echo button("Delete all savegames",16,$_game);
										} else {							
											echo "<p><error>No savegame for this game found.</error></p>";
										};

										break;
									//---------------------------------------------------------------------------------------
									case 7: // Edit player								
										echo "<h3>Edit player</h3>";
									
										$error = "";
										if ($_confirmed==1) {
											if ($__userName=="") {
												$error = "Login name is empty. Please enter a name";
												$_confirmed = 0;
											} else {
												$sql = "SELECT * FROM user WHERE gameName='".$_gameName."' AND userName='".$__userName."' AND NOT id=".$_id;
												$rows = $db->query($sql)->fetchAll();		
												if (count($rows)>0) {
													$error = "Player exists. Please change the login name!";
													$_confirmed = 0;
												};											
											};
										};
										
										$sql = "SELECT * FROM user WHERE id=".$_id;
										$rows = $db->query($sql)->fetchAll();
										$db->exec($sql);									
										
										// Show game input form
										if ($_confirmed==1) {
											// User name has changed
											echo "<p>";
											if ($rows[0]["userName"]!=$__userName) {
												$sql = "UPDATE user SET userName='".$__userName."' WHERE id=".$_id;
												echo "Update Login Name: <b>".$db->exec($sql)."</b><br>";
												
												$sql = "UPDATE savegame SET gameName='".$__userName."' WHERE userName='".$rows[0]["userName"]."'";
												echo "Update Savegames: <b>".$db->exec($sql)."</b><br>";
												
												$sql = "UPDATE sessiondata SET gameName='".$__userName."' WHERE userName='".$rows[0]["userName"]."'";
												echo "Update Session Data: <b>".$db->exec($sql)."</b><br>";
												
												$sql = "UPDATE session SET gameName='".$__userName."' WHERE userName='".$rows[0]["userName"]."'";
												echo "Update Sessions: <b>".$db->exec($sql)."</b><br>";							
											};
											
											$sql = "UPDATE user SET "
												."password='".$__password."',"
												."gameActorId=".$__loginActorId.","
												."userRole=".$__userRole.","
												."lastChange=".time()." WHERE id=".$_id;
											echo "Update Player Settings: <b>".$db->exec($sql)."</b><br>";

											echo "Finished</p>";
									
										} else {
											
											if ($error!="") {
												echo "<p><error>".$error."</error></p>";
											};

											echo "<table>";
											echo "<form action='".$_self."' method='post'>";
											echo "<input type='hidden' name='cbam_username' value='".$_userName."'>";
											echo "<input type='hidden' name='cbam_password' value='".$_password."'>";										
											echo "<input type='hidden' name='game' value='".$_game."'>";
											echo "<input type='hidden' name='id' value='".$_id."'>";
											echo "<input type='hidden' name='action' value='7'>";
											echo "<input type='hidden' name='confirmed' value='1'>";

											echo "<tr>";
											echo "<td nowrap><label for='userName'>Login:</label></td>";
											echo "<td><input type='text' name='userName' size='20' maxlength='".MAXLENGTH."' value='".$rows[0]["userName"]."' /></td>";
											echo "<td><small>The login name of the player.</small></td>";
											echo "</tr>";
											
											echo "<tr>";
											echo "<td nowrap><label for='password'>Password:</label></td>";
											echo "<td><input type='password' name='password' size='20' maxlength='".MAXLENGTH."' value='".$rows[0]["password"]."' /></td>";
											echo "<td><small>The password for the player.</small></td>";
											echo "</tr>";										
											
											echo "<tr>";
											echo "<td nowrap><label for='userRole'>Privileges:</label></td>";
											echo "<td><input type='radio' name='userRole' value='1' ".isChecked($rows[0]["userRole"],1)."> Normal Player<br>";
											echo "<input type='radio' name='userRole' value='99' ".isChecked($rows[0]["userRole"],99)."> Admin<br>";
											echo "<td><small>Select if the player should have admin rights!</small></td>";
											echo "<tr>";
								
											echo "<tr>";
											echo "<td nowrap><label for='loginActorId'>ActorId:</label></td>";
											echo "<td><input type='text' name='loginActorId' size='4' maxlength='4' value='".$rows[0]["gameActorId"]."' /></td>";
											echo "<td><small>Enter the id of the actor (see your RMMV database) the player will use in the game (as party leader)!</small></td>";
											echo "</tr>";										

											echo "<tr><td colspan='3'>&nbsp;</td></tr>";
											echo "<tr><td colspan='3'><button type='submit'>Change player</button></td></tr>";
							
											echo "</form>";
											echo "</table>";
										};									
										
										break;
									//---------------------------------------------------------------------------------------
									case 8: // Show quicksaves
										echo "<h3>Show quicksave games</h3>";
									
										$count = 0;
										$sql = "SELECT * FROM quicksave WHERE gameName='".$_gameName."' ORDER BY id DESC";
										$rows = $db->query($sql)->fetchAll();

										if (count($rows)>0) {							
											echo "<p><table width='99%'>";
											echo "<tr><th>Version</th><th>User</th><th>Playtime</th><th>Date/Time</th><th></th></tr>";														
											foreach ($rows as $row) {
												$info = json_decode($row["gameInfo"]);
												$count++;

												echo "<tr><td>V".$row["gameVersion"]."</td><td>".$row["userName"]."</td><td>".$info->playtime."</td><td>".date('Y-m-d h:i:s A', $row["lastChange"])."</td><td>";
												echo button("Delete",18,$_game,$row["id"]);
												echo "</td></tr>";
											};
											echo "</table></p>";
										} else {							
											echo "<p><error>No quicksave for this game found.</error></p>";
										};

										break;									
									//---------------------------------------------------------------------------------------
									case 11: // Delete game
										echo "<h3>Delete game</h3>";
										
										if ($_confirmed==1) {
											$sql = "DELETE FROM game WHERE gameName='".$_gameName."'";
											if (($db->exec($sql))>0) {
												echo "<p>Game deleted.</p>";
											} else {
												echo "<p><error>Delete game failed.</error></p>";
											};									
										} else {
											// Realy!?
											echo "<p>Do you REALY want to delete this game (settings)?</p>";
											echo button("Yes",11,$_game,$_id,1);
											echo button("No",1,$_game);											
										};
								
										break;									
									//---------------------------------------------------------------------------------------
									case 13: // Delete user
										echo "<h3>Delete user</h3>";
										
										if ($_confirmed==1) {
											if ($_id==0) {
												$sql = "DELETE FROM user WHERE gameName='".$_gameName."'";
											} else {
												$sql = "DELETE FROM user WHERE gameName='".$_gameName."' AND id=".$_id;
											}										
											if (($db->exec($sql))>0) {
												echo "<p>User deleted.</p>";
											} else {
												echo "<p><error>Delete user failed.</error></p>";
											};									
										} else {
											// Realy!?
											if ($_id==0) {
												echo "<p>Do you REALY want to delete ALL user?</p>";
												echo button("Yes",13,$_game,0,1);
												echo button("No",3,$_game);											
											} else {
												echo "<p>Do you REALY want to delete this user?</p>";
												echo button("Yes",13,$_game,$_id,1);
												echo button("No",3,$_game);											
											};
										};
								
										break;									
									//---------------------------------------------------------------------------------------
									case 14: // Clear session data
										echo "<h3>Delete actual session data</h3>";
										
										if ($_confirmed==1) {
											if ($_id==0) {
												$sql = "DELETE FROM sessiondata WHERE gameName='".$_gameName."'";
											} else {
												$sql = "DELETE FROM sessiondata WHERE gameName='".$_gameName."' AND action='active'";
											};
											if (($db->exec($sql))>0) {
												echo "<p>Session data deleted.</p>";
											} else {
												echo "<p><error>Delete session data failed.</error></p>";
											};									
										} else {
											// Realy!?
											if ($_id==0) {
												echo "<p>Du you REALY want to delete ALL session data?</p>";
												echo button("Yes",14,$_game,0,1);
												echo button("No",4,$_game);											
											} else {
												echo "<p>Du you REALY want to delete the ACTIVE session data?</p>";
												echo button("Yes",14,$_game,$_id,1);
												echo button("No",4,$_game);
											};
										};
								
										break;									
									//---------------------------------------------------------------------------------------
									case 15: // Delete session log
										echo "<h3>Delete session log</h3>";
										
										if ($_confirmed==1) {
											// Do it!
											$sql = "DELETE FROM session WHERE gameName='".$_gameName."'";
											if (($db->exec($sql))>0) {
												echo "<p>Session log deleted.</p>";
											} else {
												echo "<p><error>Delete session log failed.</error></p>";
											};
										} else {
											// Realy!?
											echo "<p>Du you REALY want to delete the session log?</p>";
											echo button("Yes",15,$_game,0,1);
											echo button("No",5,$_game);										
										};
								
										break;									
									//---------------------------------------------------------------------------------------
									case 16: // Delete savegame
										echo "<h3>Delete savegame</h3>";
										
										if ($_confirmed==1) {
											if ($_id==0) {
												$sql = "DELETE FROM savegame WHERE gameName='".$_gameName."'";
											} else {
												$sql = "DELETE FROM savegame WHERE gameName='".$_gameName."' AND id=".$_id;
											};
											if (($db->exec($sql))>0) {
												echo "<p>Savegame deleted.</p>";
											} else {
												echo "<p><error>Delete savegame failed.</error></p>";
											};									
										} else {
											// Realy!?
											if ($_id==0) {
												echo "<p>Du you REALY want to delete ALL savegames?</p>";
												echo button("Yes",16,$_game,0,1);
												echo button("No",6,$_game);
											} else {									
												echo "<p>Du you REALY want to delete this savegame?</p>";
												echo button("Yes",16,$_game,$_id,1);
												echo button("No",6,$_game);											
												//echo "<p><a href='".$_self."?game=".$_game."&action=16&confirmed=1' target='_self'>Yes</a> / <a href='".$_self."?game=".$_game."&action=6' target='_self'>No</a></p>";
											};
										};
								
										break;		
									//---------------------------------------------------------------------------------------
									case 18: // Delete quicksave game
										echo "<h3>Delete quicksave game</h3>";
										
										if ($_confirmed==1) {

											$sql = "DELETE FROM quicksave WHERE gameName='".$_gameName."' AND id=".$_id;
										
											if (($db->exec($sql))>0) {
												echo "<p>Quicksave game deleted.</p>";
											} else {
												echo "<p><error>Delete quicksave game failed.</error></p>";
											};									
										} else {
											// Realy!?
											echo "<p>Du you REALY want to delete this quicksave game?</p>";
											echo button("Yes",18,$_game,$_id,1);
											echo button("No",8,$_game);											
										};
								
										break;										
									//---------------------------------------------------------------------------------------
									case 23: // Add new player
										echo "<h3>Add new player</h3>";
									
										$error = "";
										if ($_confirmed==1) {
											if ($__userName=="") {
												$error = "Login name is empty. Please enter a name";
												$_confirmed = 0;
											} else {
												$sql = "SELECT * FROM user WHERE gameName='".$_gameName."' AND userName='".$__userName."'";
												$rows = $db->query($sql)->fetchAll();		
												if (count($rows)>0) {
													$error = "Player exists. Please change the login name!";
													$_confirmed = 0;
												};											
											};
										};
										
										// Show game input form
										if ($_confirmed==1) {
											
											$sql = "INSERT INTO user (gameName,userName,password,userRole,gameActorId,registration,lastChange) VALUES ("
												."'".$_gameName."',"
												."'".$__userName."',"
												."'".$__password."',"
												.$__userRole.","
												.$__loginActorId.","
												.time().","
												.time().")";
												
											if (($db->exec($sql))>0) {
												echo "<p>Player added to database.</p>";
											} else {
												echo "<p><error>Add player failed.</error></p>";
											};				
										} else {
								
											if ($error!="") {
												echo "<p><error>".$error."</error></p>";
											};

											echo "<table>";
											echo "<form action='".$_self."' method='post'>";
											echo "<input type='hidden' name='cbam_username' value='".$_userName."'>";
											echo "<input type='hidden' name='cbam_password' value='".$_password."'>";										
											echo "<input type='hidden' name='game' value='".$_game."'>";
											echo "<input type='hidden' name='action' value='23'>";
											echo "<input type='hidden' name='confirmed' value='1'>";

											echo "<tr>";
											echo "<td nowrap><label for='userName'>Login:</label></td>";
											echo "<td><input type='text' name='userName' size='20' maxlength='".MAXLENGTH."' value='".$__userName."' /></td>";
											echo "<td><small>The login name of the player (should be unique and safty).</small></td>";
											echo "</tr>";

											echo "<tr>";
											echo "<td nowrap><label for='password'>Password:</label></td>";
											echo "<td><input type='password' name='password' size='20' maxlength='".MAXLENGTH."' value='".$__password."' /></td>";
											echo "<td><small>The password for the player.</small></td>";
											echo "</tr>";	
											
											echo "<tr>";
											echo "<td nowrap><label for='userRole'>Privileges:</label></td>";
											echo "<td><input type='radio' name='userRole' value='1' ".isChecked($__userRole,1)."> Normal Player<br>";
											echo "<input type='radio' name='userRole' value='99' ".isChecked($__userRole,99)."> Admin<br>";
											echo "<td><small>Select if the player should have admin rights!</small></td>";
											echo "<tr>";
								
											echo "<tr>";
											echo "<td nowrap><label for='loginActorId'>ActorId:</label></td>";
											echo "<td><input type='text' name='loginActorId' size='4' maxlength='4' value='".$__loginActorId."' /></td>";
											echo "<td><small>Enter the id of the actor (see your RMMV database) the player will use in the game (as party leader)!</small></td>";
											echo "</tr>";										

											echo "<tr><td colspan='3'>&nbsp;</td></tr>";
											echo "<tr><td colspan='3'><button type='submit'>Add player</button></td></tr>";
							
											echo "</form>";
											echo "</table>";
										};

										break;
									//---------------------------------------------------------------------------------------
									default:
									//---------------------------------------------------------------------------------------
										echo "<h3>Unknown action</h3>";
									//---------------------------------------------------------------------------------------
								}; // end switch					
							} else {
								echo "<h3>Game #".$_game." no found</h3>";
								echo "<p><a href='".$_self."' target='_self'>Back to game list</a></p>";
								$_game = 0;
							};
							
						};
					
					} else {
						echo img_manager();
						echo "<h1>Welcome to CBAM - Manager</h1>";
						echo "<h3>for RPG Maker MV</h3>";						
						echo "<p>";							
							echo "<table>";
							echo "<form action='".$_self."' method='post'>";

							echo "<tr>";
							echo "<td nowrap><label for='cbam_username'>Username:</label></td>";
							echo "<td><input type='text' name='cbam_username' size='20' maxlength='255' value='' /></td>";
							echo "</tr>";
										
							echo "<tr>";
							echo "<td nowrap><label for='cbam_password'>Password:</label></td>";
							echo "<td><input type='password' name='cbam_password' size='20' maxlength='255' value='' /></td>";
							echo "</tr>";																			

							echo "<tr><td colspan='2'>&nbsp;</td></tr>";
							echo "<tr><td colspan='2'><button type='submit'>Login</button></td></tr>";
						
							echo "</form>";
							echo "</table>";						
					
						echo "</p>";
					};
				?>
				
			</article>	
		</main>
		
		<!-- Left menu -->
		<nav id="left" class="column">
			<?php
				if ($_userName==USERNAME && $_password==PASSWORD) {
			
					echo "<h3>Games</h3>";
					echo "<ul>";
					echo "<li>".button("Choose a game",1)."</li>";
					if ($_game!=0) {
						echo "<li>".button("Edit game settings",2,$_game). "</li>";
					};
					echo "<li>".button("Add a new game",20)."</li>";
					echo "</ul>";
					if ($_game!=0) {
						echo "<h3>Player</h3>";
						echo "<ul>";
						echo "<li>".button("Show all player",3,$_game)."</li>";
						echo "<li>".button("Add new player",23,$_game)."</li>";
						echo "</ul>";
						echo "<h3>Session</h3>";
						echo "<ul>";
						echo "<li>".button("Show active session",4,$_game)."</li>";
						echo "<li>".button("Show session log",5,$_game)."</li>";
						echo "</ul>";
						echo "<h3>Saves</h3>";
						echo "<ul>";
						echo "<li>".button("Show savegames",6,$_game)."</li>";
						echo "<li>".button("Show quicksaves",8,$_game)."</li>";
						echo "</ul>";
					}; 
					echo "<h3>Database</h3>";
					echo "<ul>";
					echo "<li>".button("Initialize database",21)."</li>";
					echo "<li>".button("Reset database",22)."</li>";
					echo "</ul>";
				
				};
			?>
		</nav>
		
		<!-- Right state informations -->
		<div id="right" class="column">			
			<?php 
				if ($_userName==USERNAME && $_password==PASSWORD) {
				
					echo "<h3>States</h3>";
					
					if ($_game!=0) { 
				
						$sql = "SELECT * FROM game WHERE id=".$_game;
						$rows = $db->query($sql)->fetchAll();
						if (count($rows)>0) {
							$_gameName = $rows[0]["gameName"];
							echo "<p><b>Game added:</b><br>".date('Y-m-d', $rows[0]["registration"])."<br>".date('h:i:s A', $rows[0]["registration"])."</p>";
						};
						
						$sql = "SELECT MIN(lastChange) AS firstSession, MAX(lastChange) AS lastSession FROM session WHERE gameName='".$_gameName."'";
						$rows = $db->query($sql)->fetchAll();
						if (count($rows)>0) {
							if (isset($rows[0]["firstSession"])) {
								echo "<p><b>First session:</b><br>".date('Y-m-d', $rows[0]["firstSession"])."<br>".date('h:i:s A', $rows[0]["firstSession"])."</p>";
							};
							if (isset($rows[0]["lastSession"])) {
								echo "<p><b>Last session:</b><br>".date('Y-m-d', $rows[0]["lastSession"])."<br>".date('h:i:s A', $rows[0]["lastSession"])."</p>";
							};
						};

						$sql = "SELECT * FROM savegame WHERE gameName='".$_gameName."' ORDER BY id DESC LIMIT 1";
						$rows = $db->query($sql)->fetchAll();
						if (count($rows)>0) {
							echo "<p><b>Last save:</b><br>".date('Y-m-d', $rows[0]["lastChange"])."<br>".date('h:i:s A', $rows[0]["lastChange"])."</p>";
						};
						
						$sql = "SELECT * FROM sessiondata WHERE gameName='".$_gameName."' AND action='active'";
						$rows = $db->query($sql)->fetchAll();
						if (count($rows)>0) {
							echo "<p><b>Active user:</b><br>".$rows[0]["userName"]."</p>";
						};

					}; 
				
				};
			?>
		</div>

	</div>
		
	<!-- Footer -->
	<div id="footer-wrapper">
		<footer id="footer"><p>RPG Maker MV &copy; 2015 KADOKAWA CORPORATION / YOJI OJIMA<br>CBAM Plugin &amp; CBAM Manager are licensed under <a href='https://creativecommons.org/licenses/by/3.0/'>CC BY 3.0</a></p></footer>
	</div>

	<?php
		// Close database
		$db = null;
	?>
</body>
</html>
