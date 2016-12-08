<?php
/*
-----------------------------------------------------------------------------
@plugindesc Cloud Based Asynchronous Multiplayer - CBAM
            Server side script
@author     Purzelkater (mailto:purzelkater(at)online.de)
@version    0.9.2
@date       2016/04/02
@filename   cbam.php
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

-----------------------------------------------------------------------------

### Contact ###
Feel free to contact me. You can find me as Purzelkater on
http://forums.rpgmakerweb.com or mail on purzelkater(at)online.de.

-----------------------------------------------------------------------------
*/

try {
	define("MODE_UNKNOWN",          0);
	define("MODE_LOGGED_OUT",       1);
	define("MODE_PASSIVE",          2);
	define("MODE_ACTIVE",           3);
	define("MODE_SERVER_ERROR",    99);

	define("ACTION_NONE",          -1);
	define("ACTION_TITLE",          0);
	define("ACTION_LOGIN",          1);
	define("ACTION_LOGOUT",         2);
	define("ACTION_LOAD",           3);
	define("ACTION_SAVE",           4);
	define("ACTION_MENU",           5);
	define("ACTION_EXIT",           6);
	define("ACTION_NEW",            7);
	define("ACTION_CLEARGAME",      8);
	define("ACTION_CLEARUSER",      9);
	define("ACTION_QUICKLOAD",     13);
	define("ACTION_QUICKSAVE",     14);
	define("ACTION_SWITCHPASSIVE", 17);
	define("ACTION_SWITCHACTIVE",  18);
	
	if(!isset($_POST["action"]))   die("No action found");
	if(!isset($_POST["property"])) die("No data found");

	try {
		// Open database
		$db = new PDO('sqlite:cbam.sqlite');
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
		// Decode all data from input stream
		$_action   = (int) $_POST["action"];
		$_property = json_decode($_POST["property"]);
		$_gamedata = SQLite3::escapeString($_POST["data"]);
		$_gameinfo = SQLite3::escapeString($_POST["info"]);
		$_time     = time(); // Current server time
		
		$_property->GAME->NAME     = SQLite3::escapeString($_property->GAME->NAME);
		$_property->USER->NAME     = SQLite3::escapeString($_property->USER->NAME);
		$_property->USER->PASSWORD = SQLite3::escapeString($_property->USER->PASSWORD);

		// Verify game
		//-------------------------------------------------------------------------
		$sql = "SELECT * FROM game WHERE gameName='".$_property->GAME->NAME."'";
		$rows = $db->query($sql)->fetchAll();
		
		if (count($rows)>0) {
		
			// Game was found in database -> load game settings from database and overwrite plugin settings
			$_property->GAME->MODE = MODE_LOGGED_OUT;
			$_property->GAME->RESPONSE = "Game found on server";

			$_property->GAME->LOGINACTOR        = (int) $rows[0]["loginActorId"];
			$_property->GAME->MAXUSERS          = (int) $rows[0]["maxUsers"];
			$_property->GAME->PARTYMODE         = (int) $rows[0]["partyMode"];
			$_property->GAME->AFTERNEWEVENT     = (int) $rows[0]["afterNewEvent"];
			$_property->GAME->AFTERLOADEVENT    = (int) $rows[0]["afterLoadEvent"];
			$_property->GAME->BEFORESAVEEVENT   = (int) $rows[0]["beforeSaveEvent"];
			$_property->GAME->AFTERPASSIVEEVENT = (int) $rows[0]["afterPassiveEvent"];
			$_property->ALLOW->REGISTER         = (int) $rows[0]["allowRegister"];
			$_property->ALLOW->PASSIVE          = (int) $rows[0]["allowPassive"];
			$_property->ALLOW->GUESTS           = (int) $rows[0]["allowGuests"];
			$_property->ALLOW->RESETUSER        = (int) $rows[0]["allowResetUser"];
			$_property->ALLOW->RESETGAME        = (int) $rows[0]["allowResetGame"];
			$_property->FIRSTUSERADMIN          = (int) $rows[0]["firstUserAdmin"];
			$_property->SAVEONEND               = (int) $rows[0]["saveOnEnd"];
			$_property->ALLOW->LOAD             = (int) $rows[0]["allowLoad"];
			$_property->PASSIVEONSAVE           = (int) $rows[0]["passiveOnSave"];
			$_property->GAME->HOMEPAGE          =       $rows[0]["homepage"];
			$_property->LOGGING	                = (int) $rows[0]["logLevel"];

		} elseif ($_property->ALLOW->CREATE==1) {
		
			// Game was not found but plugin allow insert the new game into database
			$sql = "INSERT INTO game (gameName,loginActorId,maxUsers,partyMode,afterNewEvent,afterLoadEvent,beforeSaveEvent,afterPassiveEvent,allowRegister,allowPassive,allowGuests,allowResetUser,allowResetGame,firstUserAdmin,saveOnEnd,allowLoad,passiveOnSave,homepage,logLevel,registration) VALUES ("
				."'".$_property->GAME->NAME."',"
				.$_property->GAME->LOGINACTOR.","
				.$_property->GAME->MAXUSERS.","
				.$_property->GAME->PARTYMODE.","
				.$_property->GAME->AFTERNEWEVENT.","
				.$_property->GAME->AFTERLOADEVENT.","
				.$_property->GAME->BEFORESAVEEVENT.","
				.$_property->GAME->AFTERPASSIVEEVENT.","
				.$_property->ALLOW->REGISTER.","
				.$_property->ALLOW->PASSIVE.","
				.$_property->ALLOW->GUESTS.","
				.$_property->ALLOW->RESETUSER.","
				.$_property->ALLOW->RESETGAME.","
				.$_property->FIRSTUSERADMIN.","
				.$_property->SAVEONEND.","
				.$_property->ALLOW->LOAD.","
				.$_property->PASSIVEONSAVE.","
				."'".$_property->GAME->HOMEPAGE."',"
				.$_property->LOGGING.","
				.$_time.")";			

				if (($db->exec($sql))>0) {
					$_property->GAME->MODE = MODE_LOGGED_OUT;
					$_property->GAME->RESPONSE = "Not logged in";
				} else {
					$_property->GAME->MODE = MODE_SERVER_ERROR;
					$_property->GAME->RESPONSE = "Gamedata not found";
				};

		} else {

			// Game was not found and insert into database is not allowed
			$_property->GAME->MODE = MODE_SERVER_ERROR;
			$_property->GAME->RESPONSE = "Gamedata not found";
			$_property->USER->ROLE = 0;
			$_action = ACTION_TITLE;

		}
		//-------------------------------------------------------------------------
		// Okay, why I was called?
		switch ($_action) {
			/*********************************************
			  Start new game
			*********************************************/
			case ACTION_NEW:
				if ($_property->USER->NAME!="") {			
					// Verify if the user exists for this game
					$sql = "SELECT * FROM user WHERE gameName='".$_property->GAME->NAME."' AND userName='".$_property->USER->NAME."'";
					$rows = $db->query($sql)->fetchAll();
		
					if (count($rows)>0) {
						// User found, may be okay
						$_property->GAME->RESPONSE = "User ".$_property->USER->NAME." found";
						$_property->USER->ROLE  = (int) $rows[0]["userRole"];
						$_property->USER->ACTOR = (int) $rows[0]["gameActorId"];
						
						// Well, only an admin can start a new game
						if ($_property->USER->ROLE==99) {
					
							// Is an active player online?
							$sql = "SELECT * FROM sessiondata WHERE gameName='".$_property->GAME->NAME."' AND action='active'";
							$rows = $db->query($sql)->fetchAll();
							
							if (count($rows)>0) {												
								$_property->GAME->RESPONSE = $rows[0]["userName"]." is active player";
								
								// Active player found -> Who is it?
								if ($_property->USER->NAME==$rows[0]["userName"]) {
				
									// You are the active player -> Refresh last seen time
									$sql = "UPDATE user SET lastSeen=".$_time." WHERE gameName='".$_property->GAME->NAME."' AND userName='".$_property->USER->NAME."'";
									if (($db->exec($sql))>0) {								
										$_property->GAME->MODE = MODE_ACTIVE;
										
										// Insert session log
										$sql = "INSERT INTO session (gameName,gameVersion,userName,action,lastChange) VALUES ("
											."'".$_property->GAME->NAME."',".$_property->GAME->VERSION.",'".$_property->USER->NAME."',"."'new game',".$_time.")";							
										$db->exec($sql);
									} else {
										$_property->GAME->RESPONSE = "Can't write session data";
										$_property->GAME->MODE = MODE_SERVER_ERROR;
									};																		
								} else {
									// Another player is active in the game, you can't start a new at this time!
									$_property->GAME->MODE = MODE_PASSIVE;
								};					
							} else {					
								// No active player found -> You are the active player now!
								$_property->GAME->RESPONSE = "No session data";
								
								// Write session data for new active player
								$sql = "INSERT INTO sessiondata (gameName,gameVersion,userName,action,lastChange) VALUES ("
									."'".$_property->GAME->NAME."',"
									.$_property->GAME->VERSION.","
									."'".$_property->USER->NAME."',"
									."'active',"
									.$_time.")";
								if (($db->exec($sql))>0) {								
									$_property->GAME->MODE = MODE_ACTIVE;
									
									// Insert session log
									$sql = "INSERT INTO session (gameName,gameVersion,userName,action,lastChange) VALUES ("
										."'".$_property->GAME->NAME."',".$_property->GAME->VERSION.",'".$_property->USER->NAME."',"."'new game',".$_time.")";							
									$db->exec($sql);
								} else {
									// Database write error
									$_property->GAME->RESPONSE = "Can't write session data";
									$_property->GAME->MODE = MODE_SERVER_ERROR;
								};
							};
						};
					} else {
						$_property->GAME->RESPONSE = "User ".$_property->USER->NAME." not found";
						$_property->USER->ROLE  = 0;
						$_property->USER->ACTOR = $_property->GAME->LOGINACTOR;
					};
				};			
			
				break;
			/*********************************************
			  Exit game to title screen
			*********************************************/
			case ACTION_EXIT:
				if ($_property->USER->NAME!="") {
					// Insert session log
					$sql = "INSERT INTO session (gameName,gameVersion,userName,action,lastChange) VALUES ("
						."'".$_property->GAME->NAME."',".$_property->GAME->VERSION.",'".$_property->USER->NAME."',"."'exit session',".$_time.")";							
					$db->exec($sql);				
					// Verify if user exists for this game
					$sql = "SELECT * FROM user WHERE gameName='".$_property->GAME->NAME."' AND userName='".$_property->USER->NAME."'";
					$rows = $db->query($sql)->fetchAll();
		
					if (count($rows)>0) {
						// Free up session for current user (Login is always passive mode)
						$sql = "DELETE FROM sessiondata WHERE gameName='".$_property->GAME->NAME."' AND userName='".$_property->USER->NAME."'";
						$db->exec($sql);					

						$_property->GAME->MODE = MODE_PASSIVE;
						$_property->GAME->RESPONSE = "User ".$_property->USER->NAME." exit session";
						$_property->USER->ROLE  = (int) $rows[0]["userRole"];
						$_property->USER->ACTOR = (int) $rows[0]["gameActorId"];	
					};				
				};
			
				break;
			/*********************************************
			  You are on title screen
			*********************************************/
			case ACTION_TITLE:
				if ($_property->USER->NAME!="") {
					// Verify if user exists for this game
					$sql = "SELECT * FROM user WHERE gameName='".$_property->GAME->NAME."' AND userName='".$_property->USER->NAME."'";
					$rows = $db->query($sql)->fetchAll();
		
					if (count($rows)>0) {

						// Free up session for current user (Login is always passive mode)
						$sql = "DELETE FROM sessiondata WHERE gameName='".$_property->GAME->NAME."' AND userName='".$_property->USER->NAME."'";
						$db->exec($sql);					

						$_property->GAME->MODE = MODE_PASSIVE;
						$_property->GAME->RESPONSE = "User ".$_property->USER->NAME." logged in";
						$_property->USER->ROLE  = (int) $rows[0]["userRole"];
						$_property->USER->ACTOR = (int) $rows[0]["gameActorId"];
					} else {
						$_property->GAME->RESPONSE = "User ".$_property->USER->NAME." not found";
						$_property->USER->ROLE  = 0;
						$_property->USER->ACTOR = $_property->GAME->LOGINACTOR;					
					};				
				};
			
				break;
			/*********************************************
			  Open command menu inside the game
			*********************************************/				
			case ACTION_MENU:
				if ($_property->USER->NAME!="") {
					// Verify if user exists for this game
					$sql = "SELECT * FROM user WHERE gameName='".$_property->GAME->NAME."' AND userName='".$_property->USER->NAME."'";
					$rows = $db->query($sql)->fetchAll();
		
					if (count($rows)>0) {

						$_property->GAME->RESPONSE = "User ".$_property->USER->NAME." found";
						$_property->USER->ROLE  = (int) $rows[0]["userRole"];
						$_property->USER->ACTOR = (int) $rows[0]["gameActorId"];	

						// Is active player online?
						$sql = "SELECT * FROM sessiondata WHERE gameName='".$_property->GAME->NAME."' AND action='active'";
						$rows = $db->query($sql)->fetchAll();
						if (count($rows)>0) {
							$_property->GAME->RESPONSE = $rows[0]["userName"]." is active player";
							// Active player found -> Who is it?
							if ($_property->USER->NAME==$rows[0]["userName"]) {
								// You are the active player
								$_property->GAME->MODE = MODE_ACTIVE;
							} else {
								$_property->GAME->MODE = MODE_PASSIVE;
							};					
						} else {
							// No active player found -> You are the active player now!
							//--> NO! Don't change to active inside the game! $_property->GAME->MODE = MODE_ACTIVE;
							$_property->GAME->RESPONSE = "No session data";
						};
					} else {
						$_property->GAME->RESPONSE = "User ".$_property->USER->NAME." not found";
						$_property->USER->ROLE  = 0;
						$_property->USER->ACTOR = $_property->GAME->LOGINACTOR;					
					};				
				};
			
				break;
			/*********************************************
			  Login on title screen
			*********************************************/								
			case ACTION_LOGIN:
				if ($_property->USER->NAME!="") {	
			
					// Verify if user exists for this game
					$sql = "SELECT * FROM user WHERE gameName='".$_property->GAME->NAME."' AND userName='".$_property->USER->NAME."'";
					$rows = $db->query($sql)->fetchAll();
		
					if (count($rows)>0) {
					
						if ($_property->USER->PASSWORD==$rows[0]["password"]) {
						
							// Insert session log
							$sql = "INSERT INTO session (gameName,gameVersion,userName,action,lastChange) VALUES ("
								."'".$_property->GAME->NAME."',".$_property->GAME->VERSION.",'".$_property->USER->NAME."',"."'login',".$_time.")";							
							$db->exec($sql);						
						
							// Free up session for current user (Login is always passive mode)
							$sql = "DELETE FROM sessiondata WHERE gameName='".$_property->GAME->NAME."' AND userName='".$_property->USER->NAME."'";
							$db->exec($sql);						
				
							// If user was found, update user entry and load user data from database					
							$sql = "UPDATE user SET lastSeen=".$_time." WHERE gameName='".$_property->GAME->NAME."' AND userName='".$_property->USER->NAME."'";
							if (($db->exec($sql))>0) {
								$_property->GAME->MODE  = MODE_PASSIVE;
								$_property->GAME->RESPONSE = "User ".$_property->USER->NAME." logged in";
								$_property->USER->ROLE  = (int) $rows[0]["userRole"];
								$_property->USER->ACTOR = (int) $rows[0]["gameActorId"];

								// Free up session for current user (Login is always passive mode)
								$sql = "DELETE FROM sessiondata WHERE gameName='".$_property->GAME->NAME."' AND userName='".$_property->USER->NAME."'";
								$db->exec($sql);
													
							} else {					
								$_property->GAME->MODE = MODE_SERVER_ERROR;
								$_property->GAME->RESPONSE = "Login error";
								$_property->USER->ROLE = 0;				
								$_action = ACTION_TITLE;
							};
						} else {
							// Insert session log
							$sql = "INSERT INTO session (gameName,gameVersion,userName,action,lastChange) VALUES ("
								."'".$_property->GAME->NAME."',".$_property->GAME->VERSION.",'".$_property->USER->NAME."',"."'login failed',".$_time.")";							
							$db->exec($sql);						
						
								$_property->GAME->MODE = MODE_LOGGED_OUT;
								$_property->GAME->RESPONSE = "Login or password incorrect";
								$_property->USER->ROLE = 0;						
								$_action = ACTION_TITLE;							
						};

					} elseif ($_property->ALLOW->REGISTER==1 || $_property->FIRSTUSERADMIN==1) {
				
						// User was not found in database but new user registartion is allowd -> Check for empty user slot
						$sql = "SELECT COUNT(*) AS userCount FROM user WHERE gameName='".$_property->GAME->NAME."'";
						$rows = $db->query($sql)->fetchAll();
						$userCount = (int) $rows[0]["userCount"];
					
						if ($userCount>0 && $_property->ALLOW->REGISTER==1) {
					
							// Try to register new user -> Verify maxUSer allowed for this game
							if ($userCount>=$_property->GAME->MAXUSERS) {

								$_property->GAME->MODE = MODE_LOGGED_OUT;
								$_property->GAME->RESPONSE = "User limit exceeded";
								$_property->USER->ROLE = 0;						
								$_action = ACTION_TITLE;
						
							} else {
						
								// Create next user
								$freeActorFound = 0;
								$nextActorId = 1;
								$sql = "SELECT gameActorId FROM user WHERE gameName='".$_property->GAME->NAME."' ORDER BY gameActorId";
								$rows = $db->query($sql)->fetchAll();
			
								do {
									if ($nextActorId==$_property->GAME->LOGINACTOR) {
										$nextActorId++;
									} else {
										$freeActorFound=1;
										foreach ($rows as $row) {
											if ($row["gameActorId"]==$nextActorId) {
												$freeActorFound=0;
												$nextActorId++;
												break;
											};											
										};
									};
								} while ($freeActorFound==0);

								$sql = "INSERT INTO user (gameName,userName,password,userRole,gameActorId,registration,lastSeen,lastChange) VALUES ("
									."'".$_property->GAME->NAME."',"
									."'".$_property->USER->NAME."',"
									."'".$_property->USER->PASSWORD."',"
									."1,".$nextActorId.",".$_time.",".$_time.",".$_time.")";

								if (($db->exec($sql))>0) {
									$_property->GAME->MODE  = MODE_PASSIVE;
									$_property->GAME->RESPONSE = "User ".$_property->USER->NAME." registered";
									$_property->USER->ROLE  = 1;
									$_property->USER->ACTOR = $nextActorId;					
								} else {					
									$_property->GAME->MODE = MODE_SERVER_ERROR;
									$_property->GAME->RESPONSE = "Registration error";
									$_property->USER->ROLE = 0;				
									$_action = ACTION_TITLE;
								};
	
							};
					
						} elseif ($userCount==0 && $_property->FIRSTUSERADMIN==1) {
					
							// No user for this game found in database -> Create first user as admin
							$freeActorFound = 0;
							$nextActorId = 1;
							$sql = "SELECT gameActorId FROM user WHERE gameName='".$_property->GAME->NAME."' ORDER BY gameActorId";
							$rows = $db->query($sql)->fetchAll();
			
								do {
									if ($nextActorId==$_property->GAME->LOGINACTOR) {
										$nextActorId++;
									} else {
										$freeActorFound=1;
										foreach ($rows as $row) {
											if ($row["gameActorId"]==$nextActorId) {
												$freeActorFound=0;
												$nextActorId++;
												break;
											};											
										};
									};
								} while ($freeActorFound==0);						
							
							$sql = "INSERT INTO user (gameName,userName,password,userRole,gameActorId,registration,lastSeen,lastChange) VALUES ("
								."'".$_property->GAME->NAME."',"
								."'".$_property->USER->NAME."',"
								."'".$_property->USER->PASSWORD."',"
								."99,".$nextActorId.",".$_time.",".$_time.",".$_time.")";
								
							if (($db->exec($sql))>0) {
								$_property->GAME->MODE  = MODE_PASSIVE;
								$_property->GAME->RESPONSE = "Admin ".$_property->USER->NAME." registered";
								$_property->USER->ROLE  = 99;
								$_property->USER->ACTOR = $nextActorId;					
							} else {					
								$_property->GAME->MODE = MODE_SERVER_ERROR;
								$_property->GAME->RESPONSE = "Registration error";
								$_property->USER->ROLE = 0;				
								$_action = ACTION_TITLE;
							};									
	
						} else {
							
							$_property->GAME->MODE = MODE_LOGGED_OUT;
							$_property->GAME->RESPONSE = "Registration not allowed";
							$_property->USER->ROLE = 0;						
							$_action = ACTION_TITLE;
						
						};
			
					} else {

						// User was not found in database and new user registration is disabled
						$_property->GAME->MODE = MODE_LOGGED_OUT;
						$_property->GAME->RESPONSE = "User ".$_property->USER->NAME." not found";
						$_property->USER->ROLE = 0;						
						$_action = ACTION_TITLE;

					};
				};
			
				break;
			/*********************************************
			  Logout on title screen
			*********************************************/				
			case ACTION_LOGOUT:
				$sql = "INSERT INTO session (gameName,gameVersion,userName,action,lastChange) VALUES ("
					."'".$_property->GAME->NAME."',".$_property->GAME->VERSION.",'".$_property->USER->NAME."','logout',".$_time.")";							
				$db->exec($sql);			
			
				// Free up session for current user (Login is always passive mode)
				$sql = "DELETE FROM sessiondata WHERE gameName='".$_property->GAME->NAME."' AND userName='".$_property->USER->NAME."'";
				$db->exec($sql);

				$_property->GAME->MODE = MODE_LOGGED_OUT;
				$_property->GAME->RESPONSE = "User ".$_property->USER->NAME." logged off";
				$_property->USER->ROLE = 0;
				$_property->USER->NAME = "";
				$_property->USER->PASSWORD = "";
				
				break;
			/*********************************************
			  Load latest savegame
			*********************************************/				
			case ACTION_LOAD:
				if ($_property->USER->NAME!="") {
				
					// How many players can join the game?
					$sql = "SELECT * FROM sessiondata WHERE gameName='".$_property->GAME->NAME."'";
					$rows = $db->query($sql)->fetchAll();
					
					if (count($rows)>=$_property->GAME->MAXUSERS) {
						$_property->GAME->MODE = MODE_SERVER_ERROR;
						$_property->GAME->RESPONSE = "Player count exceeded";
					} else {

						if ($_property->USER->ROLE!=0) {

							// Is active player online?
							$sql = "SELECT * FROM sessiondata WHERE gameName='".$_property->GAME->NAME."' AND action='active'";
							$rows = $db->query($sql)->fetchAll();
					
							if (count($rows)>0) {
								// Free up session for current user
								$sql = "DELETE FROM sessiondata WHERE gameName='".$_property->GAME->NAME."' AND userName='".$_property->USER->NAME."'";
								$db->exec($sql);					
					
								// Active player found -> Who is it?
								if ($_property->USER->NAME==$rows[0]["userName"]) {
						
									// You are the active player!
									$sql = "INSERT INTO sessiondata (gameName,gameVersion,userName,action,lastChange) VALUES ("
										."'".$_property->GAME->NAME."',"
										.$_property->GAME->VERSION.","
										."'".$_property->USER->NAME."',"
										."'active',"
										.$_time.")";
									if (($db->exec($sql))>0) {
										// You are the active player
										$_property->GAME->MODE = MODE_ACTIVE;

										$sql = "INSERT INTO session (gameName,gameVersion,userName,action,lastChange) VALUES ("
											."'".$_property->GAME->NAME."',".$_property->GAME->VERSION.",'".$_property->USER->NAME."',"."'set active',".$_time.")";							
										$db->exec($sql);
								
									} else {
										$_property->GAME->MODE = MODE_SERVER_ERROR;
										$_property->GAME->RESPONSE = "Start session failed";
									};						

								} else {

									// No active player found -> You are an passive player now!
									$sql = "INSERT INTO sessiondata (gameName,gameVersion,userName,action,lastChange) VALUES ("
										."'".$_property->GAME->NAME."',"
										.$_property->GAME->VERSION.","
										."'".$_property->USER->NAME."',"
										."'passive',"
										.$_time.")";
									if (($db->exec($sql))>0) {
										// You are a passive player
										$_property->GAME->MODE = MODE_PASSIVE;							
									} else {
										$_property->GAME->MODE = MODE_SERVER_ERROR;
										$_property->GAME->RESPONSE = "Start session failed";
									};						

								};					
							} else {
								// Free up session for current user
								$sql = "DELETE FROM sessiondata WHERE gameName='".$_property->GAME->NAME."' AND userName='".$_property->USER->NAME."'";
								$db->exec($sql);						
						
								// No active player found -> You are the active player now!
								$sql = "INSERT INTO sessiondata (gameName,gameVersion,userName,action,lastChange) VALUES ("
									."'".$_property->GAME->NAME."',"
									.$_property->GAME->VERSION.","
									."'".$_property->USER->NAME."',"
									."'active',"
									.$_time.")";
						
								if (($db->exec($sql))>0) {
									$_property->GAME->MODE = MODE_ACTIVE;
							
									$sql = "INSERT INTO session (gameName,gameVersion,userName,action,lastChange) VALUES ("
										."'".$_property->GAME->NAME."',".$_property->GAME->VERSION.",'".$_property->USER->NAME."',"."'set active',".$_time.")";							
									$db->exec($sql);							
														
								} else {
									$_property->GAME->MODE = MODE_SERVER_ERROR;
									$_property->GAME->RESPONSE = "Start session failed";
								};
							};
						} else {
							$_property->GAME->MODE = MODE_PASSIVE;
							$_property->GAME->RESPONSE = "Guest session";
						};
					
						// Looking for savegame
						$sql = "SELECT * FROM savegame WHERE gameName='".$_property->GAME->NAME."' ORDER BY id DESC LIMIT 1";
						$rows = $db->query($sql)->fetchAll();
				
						if (count($rows)>0) {
							if (floatval($rows[0]["gameVersion"])>floatval($_property->GAME->VERSION)) {
								$_property->GAME->RESPONSE = "Client to old";
							} else {
								$_gamedata = $rows[0]["gameData"];
								$_gameinfo = $rows[0]["gameInfo"];
								$_property->GAME->RESPONSE = $rows[0]["gameData"];
							
								$sql = "INSERT INTO session (gameName,gameVersion,userName,action,lastChange) VALUES ("
									."'".$_property->GAME->NAME."',".$_property->GAME->VERSION.",'".$_property->USER->NAME."',"."'load game',".$_time.")";							
								$db->exec($sql);														
							};
						} else {
							$_property->GAME->RESPONSE = "No savegame found";
						};
					};
				};
				
				break;								
			/*********************************************
			  Load latest savegame from qicksave slot
			*********************************************/				
			case ACTION_QUICKLOAD:
				if ($_property->USER->NAME!="") {
				
					if ($_property->USER->ROLE!=0) {

						// Is active player online?
						$sql = "SELECT * FROM sessiondata WHERE gameName='".$_property->GAME->NAME."' AND action='active'";
						$rows = $db->query($sql)->fetchAll();
					
						if (count($rows)>0) {
							// Free up session for current user
							$sql = "DELETE FROM sessiondata WHERE gameName='".$_property->GAME->NAME."' AND userName='".$_property->USER->NAME."'";
							$db->exec($sql);					
					
							// Active player found -> Who is it?
							if ($_property->USER->NAME==$rows[0]["userName"]) {
						
								// You are the active player!
								$sql = "INSERT INTO sessiondata (gameName,gameVersion,userName,action,lastChange) VALUES ("
									."'".$_property->GAME->NAME."',"
									.$_property->GAME->VERSION.","
									."'".$_property->USER->NAME."',"
									."'active',"
									.$_time.")";
								if (($db->exec($sql))>0) {
									// You are the active player
									$_property->GAME->MODE = MODE_ACTIVE;
								
								} else {
									$_property->GAME->MODE = MODE_SERVER_ERROR;
									$_property->GAME->RESPONSE = "Start session failed";
								};						

							} else {

								// No active player found -> You are an passive player now!
								$sql = "INSERT INTO sessiondata (gameName,gameVersion,userName,action,lastChange) VALUES ("
									."'".$_property->GAME->NAME."',"
									.$_property->GAME->VERSION.","
									."'".$_property->USER->NAME."',"
									."'passive',"
									.$_time.")";
								if (($db->exec($sql))>0) {
									// You are a passive player
									$_property->GAME->MODE = MODE_PASSIVE;							
								} else {
									$_property->GAME->MODE = MODE_SERVER_ERROR;
									$_property->GAME->RESPONSE = "Start session failed";
								};						

							};					
						} else {
							// Free up session for current user
							$sql = "DELETE FROM sessiondata WHERE gameName='".$_property->GAME->NAME."' AND userName='".$_property->USER->NAME."'";
							$db->exec($sql);						
						
							// No active player found -> You are the active player now!
							$sql = "INSERT INTO sessiondata (gameName,gameVersion,userName,action,lastChange) VALUES ("
								."'".$_property->GAME->NAME."',"
								.$_property->GAME->VERSION.","
								."'".$_property->USER->NAME."',"
								."'active',"
								.$_time.")";
						
							if (($db->exec($sql))>0) {
								$_property->GAME->MODE = MODE_ACTIVE;							
														
							} else {
								$_property->GAME->MODE = MODE_SERVER_ERROR;
								$_property->GAME->RESPONSE = "Start session failed";
							};
						};
					} else {
						$_property->GAME->MODE = MODE_PASSIVE;
						$_property->GAME->RESPONSE = "Guest session";
					};
					
					// Looking for savegame
					$sql = "SELECT * FROM quicksave WHERE gameName='".$_property->GAME->NAME."' ORDER BY id DESC LIMIT 1";
					$rows = $db->query($sql)->fetchAll();
					
					if (count($rows)<=0) {
						$sql = "SELECT * FROM savegame WHERE gameName='".$_property->GAME->NAME."' ORDER BY id DESC LIMIT 1";
						$rows = $db->query($sql)->fetchAll();
					};
				
					if (count($rows)>0) {
						if (floatval($rows[0]["gameVersion"])>floatval($_property->GAME->VERSION)) {
							$_property->GAME->RESPONSE = "Client to old";
						} else {
							$_gamedata = $rows[0]["gameData"];
							$_gameinfo = $rows[0]["gameInfo"];
							$_property->GAME->RESPONSE = $rows[0]["gameData"];														
						};
					} else {
						$_property->GAME->RESPONSE = "No savegame found";
					};
				};
			
				break;				
			/*********************************************
			  Save the game
			*********************************************/
			case ACTION_SAVE:
				if ($_property->USER->NAME!="") {
				
					// Verify if user exists for this game
					$sql = "SELECT * FROM user WHERE gameName='".$_property->GAME->NAME."' AND userName='".$_property->USER->NAME."'";
					$rows = $db->query($sql)->fetchAll();				
				
					if (count($rows)>0) {
					
						$_property->GAME->RESPONSE = "User ".$_property->USER->NAME." found";
						$_property->USER->ROLE  = (int) $rows[0]["userRole"];
						$_property->USER->ACTOR = (int) $rows[0]["gameActorId"];
						
						// Is active player online?
						$sql = "SELECT * FROM sessiondata WHERE gameName='".$_property->GAME->NAME."' AND action='active'";
						$rows = $db->query($sql)->fetchAll();
						
						if (count($rows)>0) {
							$_property->GAME->RESPONSE = $rows[0]["userName"]." is active player";
							// Active player found -> Who is it?
							if ($_property->USER->NAME==$rows[0]["userName"]) {
								// You are the active player
								$_property->GAME->MODE = MODE_ACTIVE;
							} else {
								$_property->GAME->MODE = MODE_PASSIVE;
							};					
						} else {
							// No active player found -> You are the active player now!
							//--> NO! Don't change to active inside the game! $_property->GAME->MODE = MODE_ACTIVE;
							$_property->GAME->RESPONSE = "No session data";
						};						
						
						// Save the game only if you are active
						if ($_property->GAME->MODE==MODE_ACTIVE) {
						
							$sql = "UPDATE quicksave SET "
								."gameVersion=".$_property->GAME->VERSION.","
								."userName='".$_property->USER->NAME."',"
								."gameData='".$_gamedata."',"
								."gameInfo='".$_gameinfo."',"
								."lastChange=".$_time." WHERE "
								."gameName='".$_property->GAME->NAME."'";															
							if (($db->exec($sql))<=0) {	
								$sql = "INSERT INTO quicksave (gameName,gameVersion,userName,gameData,gameInfo,lastChange) VALUES ("
									."'".$_property->GAME->NAME."',"
									.$_property->GAME->VERSION.","
									."'".$_property->USER->NAME."',"
									."'".$_gamedata."',"
									."'".$_gameinfo."',"
									.$_time.")";		
								$db->exec($sql);
							};						
						
							$sql = "INSERT INTO savegame (gameName,gameVersion,userName,gameData,gameInfo,lastChange) VALUES ("
								."'".$_property->GAME->NAME."',"
								.$_property->GAME->VERSION.","
								."'".$_property->USER->NAME."',"
								."'".$_gamedata."',"
								."'".$_gameinfo."',"
								.$_time.")";		
						
							if (($db->exec($sql))>0) {
								$_property->GAME->MODE = MODE_ACTIVE;
								$_property->GAME->RESPONSE = "Game saved";
						
								$sql = "INSERT INTO session (gameName,gameVersion,userName,action,lastChange) VALUES ("
									."'".$_property->GAME->NAME."',".$_property->GAME->VERSION.",'".$_property->USER->NAME."',"."'save game',".$_time.")";							
								$db->exec($sql);

							} else {
								$_property->GAME->MODE = MODE_SERVER_ERROR;
								$_property->GAME->RESPONSE = "Save game failed";
						};
						};
					
					} else {
						$_property->GAME->RESPONSE = "User ".$_property->USER->NAME." not found";
						$_property->USER->ROLE  = 0;
						$_property->USER->ACTOR = $_property->GAME->LOGINACTOR;					
					};

				};
				
				break;
			/*********************************************
			  Save the game on quicksave slot
			*********************************************/
			case ACTION_QUICKSAVE:
				if ($_property->USER->NAME!="") {
				
					// Verify if user exists for this game
					$sql = "SELECT * FROM user WHERE gameName='".$_property->GAME->NAME."' AND userName='".$_property->USER->NAME."'";
					$rows = $db->query($sql)->fetchAll();				
				
					if (count($rows)>0) {
					
						$_property->GAME->RESPONSE = "User ".$_property->USER->NAME." found";
						$_property->USER->ROLE  = (int) $rows[0]["userRole"];
						$_property->USER->ACTOR = (int) $rows[0]["gameActorId"];
						
						// Is active player online?
						$sql = "SELECT * FROM sessiondata WHERE gameName='".$_property->GAME->NAME."' AND action='active'";
						$rows = $db->query($sql)->fetchAll();
						
						if (count($rows)>0) {
							$_property->GAME->RESPONSE = $rows[0]["userName"]." is active player";
							// Active player found -> Who is it?
							if ($_property->USER->NAME==$rows[0]["userName"]) {
								// You are the active player
								$_property->GAME->MODE = MODE_ACTIVE;
							} else {
								$_property->GAME->MODE = MODE_PASSIVE;
							};					
						} else {
							// No active player found -> You are the active player now!
							//--> NO! Don't change to active inside the game! $_property->GAME->MODE = MODE_ACTIVE;
							$_property->GAME->RESPONSE = "No session data";
						};						
						
						// Save the game only if you are active
						if ($_property->GAME->MODE==MODE_ACTIVE) {
							$sql = "UPDATE quicksave SET "
								."gameVersion=".$_property->GAME->VERSION.","
								."userName='".$_property->USER->NAME."',"
								."gameData='".$_gamedata."',"
								."gameInfo='".$_gameinfo."',"
								."lastChange=".$_time." WHERE "
								."gameName='".$_property->GAME->NAME."'";							
								
							if (($db->exec($sql))>0) {	
								$_property->GAME->MODE = MODE_ACTIVE;
								$_property->GAME->RESPONSE = "Game quick saved";
							} else {
								$sql = "INSERT INTO quicksave (gameName,gameVersion,userName,gameData,gameInfo,lastChange) VALUES ("
									."'".$_property->GAME->NAME."',"
									.$_property->GAME->VERSION.","
									."'".$_property->USER->NAME."',"
									."'".$_gamedata."',"
									."'".$_gameinfo."',"
									.$_time.")";		
						
								if (($db->exec($sql))>0) {
									$_property->GAME->MODE = MODE_ACTIVE;
									$_property->GAME->RESPONSE = "Game quick saved";
								} else {
									$_property->GAME->MODE = MODE_SERVER_ERROR;
									$_property->GAME->RESPONSE = "Quick save game failed";
								};
							};
						};
					
					} else {
						$_property->GAME->RESPONSE = "User ".$_property->USER->NAME." not found";
						$_property->USER->ROLE  = 0;
						$_property->USER->ACTOR = $_property->GAME->LOGINACTOR;					
					};

				};
				
				break;
			/*********************************************
			  Reset game settings from admin menu
			*********************************************/				
			case ACTION_CLEARGAME:
				if ($_property->USER->ROLE==99) {
					$sql = "DELETE FROM game WHERE gameName='".$_property->GAME->NAME."'";
					if (($db->exec($sql))>0) {
						$_property->GAME->MODE = MODE_SERVER_ERROR;
						$_property->GAME->RESPONSE = "Game deleted";
						$_action = ACTION_TITLE;						
					} else {
						$_property->GAME->MODE = MODE_SERVER_ERROR;
						$_property->GAME->RESPONSE = "Game delete failed";
						$_action = ACTION_TITLE;
					};
				} else {
					$_property->GAME->MODE = MODE_SERVER_ERROR;
					$_property->GAME->RESPONSE = "Action not allowed";
					$_action = ACTION_TITLE;			
				};
			
				break;
			/*********************************************
			  Clear active user from session data
			*********************************************/				
			case ACTION_CLEARUSER:	
				if ($_property->USER->ROLE==99) {
					$sql = "DELETE FROM sessiondata WHERE gameName='".$_property->GAME->NAME."' AND action='active'";
					if (($db->exec($sql))>0) {
						$_property->GAME->MODE = MODE_SERVER_ERROR;
						$_property->GAME->RESPONSE = "Active user deleted";
						$_action = ACTION_TITLE;						
					} else {
						$_property->GAME->MODE = MODE_SERVER_ERROR;
						$_property->GAME->RESPONSE = "Active user delete failed";
						$_action = ACTION_TITLE;
					};
				} else {
					$_property->GAME->MODE = MODE_SERVER_ERROR;
					$_property->GAME->RESPONSE = "Action not allowed";
					$_action = ACTION_TITLE;			
				};				
				break;
				
			/*********************************************
			  Save the game and switch to passive mode
			*********************************************/
			case ACTION_SWITCHPASSIVE:
				if ($_property->USER->NAME!="") {
				
					// Verify if user exists for this game
					$sql = "SELECT * FROM user WHERE gameName='".$_property->GAME->NAME."' AND userName='".$_property->USER->NAME."'";
					$rows = $db->query($sql)->fetchAll();				
				
					if (count($rows)>0) {
					
						$_property->GAME->RESPONSE = "User ".$_property->USER->NAME." found";
						$_property->USER->ROLE  = (int) $rows[0]["userRole"];
						$_property->USER->ACTOR = (int) $rows[0]["gameActorId"];
						
						// Is active player online?
						$sql = "SELECT * FROM sessiondata WHERE gameName='".$_property->GAME->NAME."' AND action='active'";
						$rows = $db->query($sql)->fetchAll();
						
						if (count($rows)>0) {
							$_property->GAME->RESPONSE = $rows[0]["userName"]." is active player";
							// Active player found -> Who is it?
							if ($_property->USER->NAME==$rows[0]["userName"]) {
								// You are the active player
								//$_property->GAME->MODE = MODE_ACTIVE;
								
								$sql = "UPDATE quicksave SET "
									."gameVersion=".$_property->GAME->VERSION.","
									."userName='".$_property->USER->NAME."',"
									."gameData='".$_gamedata."',"
									."gameInfo='".$_gameinfo."',"
									."lastChange=".$_time." WHERE "
									."gameName='".$_property->GAME->NAME."'";									
								if (($db->exec($sql))<=0) {	
									$sql = "INSERT INTO quicksave (gameName,gameVersion,userName,gameData,gameInfo,lastChange) VALUES ("
										."'".$_property->GAME->NAME."',"
										.$_property->GAME->VERSION.","
										."'".$_property->USER->NAME."',"
										."'".$_gamedata."',"
										."'".$_gameinfo."',"
										.$_time.")";		
									$db->exec($sql);
								};						
							
								$sql = "INSERT INTO savegame (gameName,gameVersion,userName,gameData,gameInfo,lastChange) VALUES ("
									."'".$_property->GAME->NAME."',"
									.$_property->GAME->VERSION.","
									."'".$_property->USER->NAME."',"
									."'".$_gamedata."',"
									."'".$_gameinfo."',"
									.$_time.")";		
							
								if (($db->exec($sql))>0) {
									$_property->GAME->MODE = MODE_ACTIVE;
									$_property->GAME->RESPONSE = "Game saved";
							
									$sql = "INSERT INTO session (gameName,gameVersion,userName,action,lastChange) VALUES ("
										."'".$_property->GAME->NAME."',".$_property->GAME->VERSION.",'".$_property->USER->NAME."',"."'save game',".$_time.")";							
									$db->exec($sql);
									
									// Game saved -> switch to passive
									$sql = "UPDATE sessiondata SET action='passive' WHERE gameName='".$_property->GAME->NAME."' AND action='active'";
									
									if (($db->exec($sql))>0) {
										$_property->GAME->MODE = MODE_PASSIVE;
										$_property->GAME->RESPONSE = "Switched to passive";
										
										$sql = "INSERT INTO session (gameName,gameVersion,userName,action,lastChange) VALUES ("
										."'".$_property->GAME->NAME."',".$_property->GAME->VERSION.",'".$_property->USER->NAME."',"."'set passive',".$_time.")";							
										$db->exec($sql);										
									};

								} else {
									$_property->GAME->MODE = MODE_SERVER_ERROR;
									$_property->GAME->RESPONSE = "Save game failed";
								};								
								
							} else {
								$_property->GAME->MODE = MODE_PASSIVE;
							};					
						} else {
							// No active player found -> You are the active player now!
							//--> NO! Don't change to active inside the game! $_property->GAME->MODE = MODE_ACTIVE;
							$_property->GAME->RESPONSE = "No session data";
						};						
						
					} else {
						$_property->GAME->RESPONSE = "User ".$_property->USER->NAME." not found";
						$_property->USER->ROLE  = 0;
						$_property->USER->ACTOR = $_property->GAME->LOGINACTOR;					
					};

				};
				
				break;

			/*********************************************
			  Try to switch to active mode
			*********************************************/
			case ACTION_SWITCHACTIVE:
				if ($_property->USER->NAME!="") {
				
					// Verify if user exists for this game
					$sql = "SELECT * FROM user WHERE gameName='".$_property->GAME->NAME."' AND userName='".$_property->USER->NAME."'";
					$rows = $db->query($sql)->fetchAll();				
				
					if (count($rows)>0) {
					
						$_property->GAME->RESPONSE = "User ".$_property->USER->NAME." found";
						$_property->USER->ROLE  = (int) $rows[0]["userRole"];
						$_property->USER->ACTOR = (int) $rows[0]["gameActorId"];
						
						// Is active player online?
						$sql = "SELECT * FROM sessiondata WHERE gameName='".$_property->GAME->NAME."' AND action='active'";
						$rows = $db->query($sql)->fetchAll();
						
						if (count($rows)>0) {
							$_property->GAME->RESPONSE = $rows[0]["userName"]." is active player";
							// Active player found -> Who is it?
							if ($_property->USER->NAME==$rows[0]["userName"]) {
								// You are the active player
								$_property->GAME->MODE = MODE_ACTIVE;
							} else {
								$_property->GAME->MODE = MODE_PASSIVE;
							};					
						} else {
							// No active player found -> You are the active player now!
							$sql = "UPDATE sessiondata SET action='passive' WHERE gameName='".$_property->GAME->NAME."' AND userName='".$_property->USER->NAME."'";
								
							if (($db->exec($sql))>0) {
								$_property->GAME->MODE = MODE_ACTIVE;
								$_property->GAME->RESPONSE = "Switched to active";
									
								$sql = "INSERT INTO session (gameName,gameVersion,userName,action,lastChange) VALUES ("
									."'".$_property->GAME->NAME."',".$_property->GAME->VERSION.",'".$_property->USER->NAME."',"."'switched to active',".$_time.")";							
								$db->exec($sql);										
							} else {
								$_property->GAME->MODE = MODE_SERVER_ERROR;
								$_property->GAME->RESPONSE = "Switch mode failed";
							};
						};
					
					} else {
						$_property->GAME->RESPONSE = "User ".$_property->USER->NAME." not found";
						$_property->USER->ROLE  = 0;
						$_property->USER->ACTOR = $_property->GAME->LOGINACTOR;					
					};

				};
				
				break;
						
				
				
		};
		
		// Close database	
		$db = NULL;

		// Encode response
		$json = array();
		$json["property"] = $_property;
		$json["gamedata"] = $_gamedata;
		$json["gameinfo"] = $_gameinfo;

		// Send response
		echo json_encode($json);

	} catch(PDOException $e) {

		//echo "PDO Exception : ".$e->getMessage();
		echo "PDO Exception: ".$e;

	}

} catch (Exception $e) {
    //echo "Exception: ".$e->getMessage();
	echo "Exception: ".$e;
}	
?>

	