/*:
-----------------------------------------------------------------------------
@plugindesc Cloud Based Asynchronous Multiplayer - CBAM
            Plugin for RPG Maker MV
@author     Purzelkater (mailto:purzelkater(at)online.de)
@version    0.9.2 
@date       2016/04/02
@filename   CBAM.js
@url        http://forums.rpgmakerweb.com/index.php?/profile/81913-purzelkater/
@license    CC BY 3.0 (https://creativecommons.org/licenses/by/3.0/)
-----------------------------------------------------------------------------
@help 
-----------------------------------------------------------------------------
### Description ###
This file is a plugin for RPG Maker MV and part of the CBAM system. CBAM
stands for Cloud Based Asynchronous Multiplayer and gives you the ability
to create asynchronous multiplayer games with server based savegame storage.

I highly recommend you to look at the tutorial thread on the official RPG
Maker forum: 
http://forums.rpgmakerweb.com/index.php?/topic/58897-tutorial-cbam-cloud-based-asynchronous-multiplayer/

-----------------------------------------------------------------------------
### How to install ###
To use CBAM on your game you need a server, with some requirements:
° >=PHP 5.6 (maybe other versions would work but not tested)
° SQLite3 database driver
° PHP PDO support for SQLite (pdo_sqlite)

For simplification the CBAM system is using SQLite database for storage 
instead of MySQL. SQLite needs no special database server setup and uses
just a simple file (you can easily save for backup). But if you like it
should be easily to change to other database backends.

The Cloud Based Asynchronous Multiplayer system comes with 3 files:
° cbamm.php - The CBAM Manager script for server side managing
° cbam.php  - The server script, handles all plugin functions
° CBAM.js   - The RPG Maker plugin

1. Open cbamm.php on an editor and change username and password for login.
   You can skip this step and use the default values (username Admin and blank
   password).
2. Copy cbamm.php and cbam.php on your server.
3. Open a browser and open cbamm.php. Now you should see the CBAM Manager login
   screen.
   With the first call the script should create your CBAM database (cbam.sqlite).
4. Login and hit the "Initialize database" button on the left side menu to create
   the database tables as needed.
5. Copy CBAM.js to your js/plugin folder and add the plugin on the PluginManager.
   Then setup the plugin properties.
   
   IMPORTANT: As long as the CBAM plugin is active your game needs a
              semi-permanent connection to your server to run!
   HINT: If you have a server connection as needed but CBAM won't work,
         check the permissions of your scripts and database file.

-----------------------------------------------------------------------------		 
### Credits ###
° Tsukihime for the incredible HIME Party Manager and some hints behind
° izyees and mrcopra for their help with common events
° Yanfly for inspiration on external links
° Iavra for giving me some knowledge about plugin writing
and
° GameDev@YouTube for all the helpful tutorials
° quackit.com for the CBAM Manager HTML template

-----------------------------------------------------------------------------
### FAQ ###
Q: Is this some kind of MMO?
A: Definitive no. Period. A basic feature of a MMO (or just online multi-
   player) is, several player can join and play the game simultaneously.
   This ist not the intention for this plugin.

Q: What does "asynchronous multiplayer" mean?
A: This type of game can be described as a turn-based multiplayer gameplay
   between two or more players that can check in and play their turn when
   it's convenient. Similar to play-by-mail games and, more electronically,
   play-by-email or play-by-post forum games.
   Typical examples are trading card games or tabletop/board games.
   
Q: How will "asynchronous multiplayer" work on CBAM?
A: With CBAM all players concerned on a game will share the same savegame.
   Every player who joins the game will load the same, latest saved game.
   Although several player can join the game and load the same savegame on
   the same time, only one of them has the ability the save the game too.
   Moreover every player can have it's own actor on the game.
   Important: After the game was joined and the savegame was loaded, all
   players "play" their own game stand-alone. One player doesn't notice
   any others until the game was saved and new loaded.

Q: What's the matter with this party modes?
A: The simplest mode (0=none) is just a savegame sharing. On this mode, CBAM
   will not make any change on players party. However, it's the best way for
   a real multiplayer experience too. But later on that.
   With the re-group mode (1) the player party will new sorted every time a
   player joins the game. The members of the party are the same but the 
   players actor will become the leader (first person) of the party.
   Only one actor (2) goes one step further and kicks all actors from the
   player party except the players one.

Q: Your where talking about a "real multiplayer experience"?
A: Yes, CBAM can highly benefits from the incredible HIME Party Manager.
   If you add this plugin to your project too, every player can have it's own
   player party. Every player can be on a different place on a different map
   but other players can see them there. And players can merge there parties
   too.
   CBAM can activate the current player actor party every time a player joins
   the game.

Q: Why cloud based?
A: Well, of course you could save a game and send it to the next player like
   on good old play-by-email games. But CBAM makes it easier. All savegames
   are stored on one server. Wherever you are you can play the game
   immediately (if you have an internect connection).
   And without server side storage keep the savegame up-to-date would be a
   pain, believe me.

Q: What's the difference between active, passive and guest mode?
A: Only one player can be an active player at the same time in the game. Not
   before this player has left the game (or the admin has taken this state)
   another player can join the game as an active player.
   Only the active player has the ability to save the game - even if you try
   to execute the save function, the server will check if you are realy the
   active player right now.
   Beside the active player several other players can join the game at the
   same time but they are all passive players then. As a passive player they
   can't save a game and have lesser options on the ingame command menu. But
   as passive player you can move around the map as you like (by default).
   A guest is a user who has joined the game without login. As guest has no
   access to the command menu (only back to title is an option) and of course
   a guest can't save a game. Like passive players there can be several guest
   on the game at the same time.
   It's possible to disable passive player and/or guests.

Q: Who needs an admin?
A: By default an admin has some special rights on the game. As an admin you
   can kick the active player (e.g. if the game was crashed). If the player
   has joined the game at this time he becomes an passive player.
   As an admin your are able to restart a game (overwrite the savegame with
   a new once) or rewrite the server settings of a game.
   Because the admin state can be checked on the game it can be used on
   game events too, if you like.

Q: What's about security?
A: We are talking about cheating here. Well, RPG Maker MV was not made to be
   a high securtiy multiplayer engine. And with some knowledge it's easy to
   get a whole bunch of information from a game. And I'm sure CBAM has
   security gaps as big as an aircraft carrier in this case.
   But like a game on a table with friends, I hope others will play fair.

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
@param gameName
@desc The name of the game (must be unique for all your games on one server).
@default My Game
 
@param gameVersion
@desc The version of the game (newer savegames can't be loaded from older clients).
@default 1.0
 
@param gameLanguage
@desc The country code for translation (EN=English, DE=German).
@default EN
 
@param url
@desc The URL to your server (the php script bust be there).
@default http://www.my-server.com
 
@param loginActorId
@desc The Id of the actor used for guests (must be exists on your game).
@default 1
  
@param allowCreate
@desc Can the server the game add the game into database if not exists (0=no, 1=yes)?
@default 1
 
@param maxUsers
@desc How many players can join the game simultaneously?
@default 4
 
@param partyMode
@desc The party mode (0=none, 1=active player will be party leader, 2=only the active player is in the party).
@default 0

@param afterNewEvent
@desc Common event number to reserve after new game has started (0=disabled)?
@default 0
 
@param afterLoadEvent
@desc Common event number to reserve after game was loaded (0=disabled)?
@default 0

@param beforeSaveEvent
@desc Common event number to reserve before game will saved (0=disabled)?
@default 0

@param afterPassiveEvent
@desc Common event number to reserve after game was saved and player become passive (0=disabled)?
@default 0

@param allowRegister
@desc Can user register themselves on the server (0=no, 1=yes)?
@default 0

@param allowPassive
@desc Can user enter the game in passive mode (0=no, 1=yes)?
@default 1

@param allowGuests
@desc Can users without login view the game as guests (0=no, 1=yes)?
@default 1

@param allowResetGame
@desc Can admin reset game (0=no, 1=yes)?
@default 0

@param allowResetUser
@desc Can admin reset the active user (0=no, 1=yes)?
@default 1

@param firstUserAdmin
@desc Will the first user be an admin automatically (0=no, 1=yes)?
@default 1 
 
@param saveOnEnd
@desc Save the game on game end (0=no, 1=yes)?
@default 1

@param allowLoad
@desc Allow load on game (0=no, 1=yes)?
@default 1

@param passiveOnSave
@desc Switch to passive mode after save (0=no, 1=yes)?
@default 0
 
@param homepage
@desc The URL to your game homepage (leave blank to disable).
@default 

@param wbMessageActive
@desc Welcome back message for active players. Use {0} as placeholder for actor name. Leave blank for default message.
@default  

@param wbMessagePassive
@desc Welcome back message for passive players. Use {0} as placeholder for actor name. Leave blank for default message.
@default 

@param wbMessageGuest
@desc Welcome back message for guests. Use {0} as placeholder for guest text. Leave blank for default message.
@default 

@param logLevel
@desc Console output (0=Disabled, 1=Errors, 2=Errors/Warnings, 3=Errors/Warnings/Responses, 3=All).
@default 3

-------------------------------------------------------------------------------
*/
var Imported = Imported || {};
Imported["CBAM"] = true; 

var CBAM = CBAM || {};

(function($){
	"use strict";
	
	/************************************
	  Enumerations for plugin constants
	************************************/
	var YES      = 1;
	var NO       = 0;
	var MENU     = {MAIN: 0, ADMIN: 1};
	var ACTION   = {NONE: -1, TITLE: 0, LOGIN: 1, LOGOUT: 2, LOAD: 3, SAVE: 4, MENU: 5, EXIT: 6, NEW: 7, RESETGAME: 8, RESETUSER: 9, QUICKLOAD: 13, QUICKSAVE: 14, USERNAME: 15, PASSWORD: 16, SWITCHPASSIVE: 17, SWITCHACTIVE: 18};
	var MODE     = {UNKNOWN: 0, LOGGED_OUT: 1, PASSIVE: 2, ACTIVE: 3, SERVER_ERROR: 99};
	var ROLE     = {GUEST: 0, NORMAL: 1, ADMIN: 99};
	var PARTY    = {NONE: 0, REGROUP: 1, ACTIVE_ONLY: 2};
	var LOGGING  = {NONE:0, ERROR: 1, WARNING: 2, RESPONSE: 3, ALL: 4};
	
	/**********************************************************
	  Define objects to store plugin properties and game data
	**********************************************************/
	var PROPERTY = {
		SERVER: "",
		GAME: {NAME: "", VERSION: 0, LANGUAGE: "", MODE: 0, LOGINACTOR: 0, MAXUSERS: 0, RESPONSE: "", PARTYMODE: 0, HOMEPAGE: "", AFTERNEWEVENT: 0, AFTERLOADEVENT: 0, BEFORESAVEEVENT: 0, AFTERPASSIVEEVENT: 0},
		USER: {NAME: "", PASSWORD: "", ACTOR: 0, ROLE: 0, MAXLENGTH: 16},
		LOGGING: 0,
		ALLOW: {CREATE: 0, REGISTER: 0, GUESTS: 0, PASSIVE: 0, RESETGAME: 0, RESETUSER: 0, LOAD: 1},
		SAVEONEND: 0,
		SAVEPASSIVE: 0,
		FIRSTUSERADMIN: 0,
		MESSAGE: {WELCOMEACTIVE: "", WELCOMEPASSIVE: "", WELCOMEGUEST: ""}
	};
	var GAME = {
		DATA: null,
		INFO: null,
	};

	// Okay, looks I need some local variables too
	var _nextAction  = ACTION.TITLE;
	var _menuLevel   = MENU.MAIN;
	var _firstStart  = 0;
	var _transCount  = 0;
	var _lastExecute = 0;
	
	// Save existing functions for reuse
	var makeCommandList = Window_MenuCommand.prototype.makeCommandList;
	var addSaveCommand  = Window_MenuCommand.prototype.addSaveCommand;
	var commandToTitle  = Scene_GameEnd.prototype.commandToTitle;
	var commandNewGame  = Scene_Title.prototype.commandNewGame;
	var callMenu        = Scene_Map.prototype.callMenu;

	// Initialize plugin properties
	setProperties();
	
	/*****************
	  Console output
	*****************/
	function debug($level, $text) {
		try {
			if (PROPERTY.LOGGING>=$level) {
				switch($level) {
					case LOGGING.ERROR:
						console.error("[CBAM] "+$text);
						break;
					case LOGGING.WARNING:
						console.warn("[CBAM] "+$text);
						break;
					case LOGGING.RESPONSE:
						console.log("[CBAM] "+$text);
						break;					
					case LOGGING.ALL:
						console.log("[CBAM] "+$text);
						break;
				};		
			};
		} catch(e) {
			console.error("[CBAM] "+e.stack);
			alert("[CBAM] "+e.name+": "+e.message);
		};
	};	
	
	/**********************************************
	   Load all plugin properties into one object
	**********************************************/
	function setProperties() {
		try {
			debug(LOGGING.ALL,"Read plugin settings");
			
			// Extract parameters from plugin settings (RPG Maker plugin manager)
			var _parameters = PluginManager.parameters('CBAM');
			
			PROPERTY.SERVER                 = String(_parameters['url']                 || "http://www.my-server.com");
			PROPERTY.GAME.NAME              = String(_parameters['gameName']            || "My Game");
			PROPERTY.GAME.VERSION           = parseFloat(_parameters['gameVersion']     || 1.0);
			PROPERTY.GAME.LANGUAGE          = String(_parameters['gameLanguage']        || "EN").toLowerCase();
			PROPERTY.GAME.MODE              = MODE.UNKNOWN;
			PROPERTY.GAME.LOGINACTOR        = parseInt(_parameters['loginActorId']      || 1);	
			PROPERTY.GAME.MAXUSERS          = parseInt(_parameters['maxUsers']          || 4);
			PROPERTY.GAME.PARTYMODE         = parseInt(_parameters['partyMode']         || PARTY.NONE);
			PROPERTY.GAME.AFTERNEWEVENT     = parseInt(_parameters['afterNewEvent']     || 0);
			PROPERTY.GAME.AFTERLOADEVENT    = parseInt(_parameters['afterLoadEvent']    || 0);
			PROPERTY.GAME.BEFORESAVEEVENT   = parseInt(_parameters['beforeSaveEvent']   || 0);
			PROPERTY.GAME.AFTERPASSIVEEVENT = parseInt(_parameters['afterPassiveEvent'] || 0);
			PROPERTY.GAME.HOMEPAGE          = String(_parameters['homepage']            || "");
			PROPERTY.GAME.RESPONSE          = "";
			PROPERTY.USER.NAME              = "";
			PROPERTY.USER.PASSWORD          = "";
			PROPERTY.USER.ACTOR             = 0;
			PROPERTY.USER.ROLE              = ROLE.GUEST;
			PROPERTY.LOGGING                = parseInt(_parameters['logLevel']          || LOGGING.RESPONSE);
			PROPERTY.ALLOW.CREATE           = parseInt(_parameters['allowCreate']       || YES);
			PROPERTY.ALLOW.REGISTER         = parseInt(_parameters['allowRegister']     || NO);
			PROPERTY.ALLOW.PASSIVE          = parseInt(_parameters['allowPassive']      || YES);
			PROPERTY.ALLOW.GUESTS           = parseInt(_parameters['allowGuests']       || NO);
			PROPERTY.ALLOW.RESETGAME        = parseInt(_parameters['allowResetGame']    || NO);
			PROPERTY.ALLOW.RESETUSER        = parseInt(_parameters['allowResetUser']    || YES);
			PROPERTY.ALLOW.LOAD             = parseInt(_parameters['allowLoad']         || YES);
			PROPERTY.PASSIVEONSAVE          = parseInt(_parameters['passiveOnSave']     || NO);
			PROPERTY.SAVEONEND              = parseInt(_parameters['saveOnEnd']         || YES);
			PROPERTY.FIRSTUSERADMIN         = parseInt(_parameters['firstUserAdmin']    || YES);
			PROPERTY.MESSAGE.WELCOMEACTIVE  = String(_parameters['wbMessageActive']     || "");
			PROPERTY.MESSAGE.WELCOMEPASSIVE = String(_parameters['wbMessagePassive']    || "");
			PROPERTY.MESSAGE.WELCOMEGUEST   = String(_parameters['wbMessageGuest']      || "");
		} catch(e) {
			console.error("[CBAM] "+e.stack);
			alert("[CBAM] "+e.name+": "+e.message);
		};	
	};

	/*******************
	  Translation text
	*******************/
	// Use this function to translate messages visible for players.
	// Add language section for each language you want to use (just copy
	// default section and change case witch to desired language code.
	// Then use plugin property to set the language to use.
	function getText($key,$value) {
		try {
			switch (PROPERTY.GAME.LANGUAGE) {
				case "de":
					switch (String($key).toLowerCase()) {
						case "login":      return "Anmelden";
						case "logout":     return "Abmelden";
						case "username":   return "Benutzername";
						case "password":   return "Passwort";
						case "admin":      return "Admin";
						case "back":       return "Zurück";
						case "view":       return "Gastzugang";
						case "credits":    return "Impressum";
						case "exit":       return "Beenden";
						case "guest":      return "Besucher";
						case "load":       return "Laden";
						case "loggedin":   return "Angemeldet als "+String($value);
						case "resetuser":  return "Spiel freigeben";
						case "resetgame":  return "Einstellungen löschen";
						// Welcome back messages
						case "wb_active":  return "Willkommen zurück, "+String($value)+". Du bist jetzt\nder aktive Spieler.";
						case "wb_passive": return "Willkommen zurück, "+String($value)+". Da du der\npassive Spieler bist, kannst du im Moment\nnicht speichern.";
						case "wb_guest":   return "Sei gegrüßt, Besucher! Du kannst dich frei in dieser\nWelt bewegen und dir alles anschauen. Viel Spaß dabei!";
						//-------------------------------------
						default:           return String($key);
					};
				default:
					switch (String($key).toLowerCase()) {
						case "login":      return "Login";
						case "logout":     return "Logout";
						case "username":   return "Username";
						case "password":   return "Password";
						case "admin":      return "Admin";
						case "back":       return "Back";
						case "view":       return "Guest view";
						case "credits":    return "Credits";
						case "exit":       return "Exit";
						case "guest":      return "Guest";
						case "load":       return "Load";
						case "loggedin":   return "Logged in as "+String($value);
						case "resetuser":  return "Reset active player";
						case "resetgame":  return "Reset game settings";
						// Welcome back messages
						case "wb_active":  return "Welcome back "+String($value)+". You are the\nactive player now.";
						case "wb_passive": return "Welcome back "+String($value)+". You can't save\nthis game because you are in passive mode.";
						case "wb_guest":   return "Greetings visitor. You can fly around this world like a\nghost. Enjoy your travel!";
						//-------------------------------------
						default:           return String($key);
					};
			};
		} catch(e) {
			console.error("[CBAM] "+e.stack);
			alert("[CBAM] "+e.name+": "+e.message);
		};
	};

	function setActiveLocation($actorId) {
		try {
			if (typeof $gameParties !== "undefined") {
			
			var _party = 0;
			var _actor = 0;
			
			$actorId = $actorId || PROPERTY.GAME.LOGINACTOR;
			
			debug(LOGGING.ALL, "Set actor "+$actorId+" to active location");
			
			for (_party in $gameParties._parties) {
				for (_actor = 0; _actor < $gameParties._parties[_party]._actors.length; ++_actor) {
					if ($actorId==$gameParties._parties[_party]._actors[_actor]) {
						$gameParties._parties[_party]._location.mapId = $gameMap._mapId;
						$gameParties._parties[_party]._location.x     = $gamePlayer._x;
						$gameParties._parties[_party]._location.y     = $gamePlayer._y;
						
						return true;
					};
				};
			};
		
			debug(LOGGING.ALL, "Can't set actor "+$actorId+" to active location");
			
			} else {
				debug(LOGGING.WARNING, "Multiple parties not defined");
			};
			
			return false;
		} catch(e) {
			console.error("[CBAM] "+e.stack);
			alert("[CBAM] "+e.name+": "+e.message);
		};		
	};
	
	/********************************
	  Server communication function
	********************************/
	function transmit($action) {
		try {
			debug(LOGGING.ALL, "transmit: "+$action);
		
			// INC transmission counter
			_transCount = _transCount + 1;
			
			if ($action==ACTION.LOGIN) {
				debug(LOGGING.ALL,"Try to login as "+PROPERTY.USER.NAME+"@"+PROPERTY.USER.PASSWORD);
			};
			
			// Initialize HTTP Request
			var _xhttp = new XMLHttpRequest();
			var _data = "action="+encodeURIComponent($action)
				+"&property="+encodeURIComponent(JSON.stringify(PROPERTY));
 
			if ($action==ACTION.SAVE || $action==ACTION.QUICKSAVE || $action==ACTION.SWITCHPASSIVE) {
				// Generate savegame data
				GAME.INFO = JsonEx.stringify(DataManager.makeSavefileInfo());
				GAME.DATA = JsonEx.stringify(DataManager.makeSaveContents());
				if (GAME.DATA.length >= 200000) {
					debug(LOGGING.WARNING,"Save data too big");
				};
				_data += "&info="+encodeURIComponent(GAME.INFO);
				_data += "&data="+encodeURIComponent(GAME.DATA);
			};

			// Open communication and send data to server
			debug(LOGGING.ALL,"Send: "+PROPERTY.SERVER+"/cbam.php?"+_data);
			_xhttp.open('POST', PROPERTY.SERVER+"/cbam.php", false);
			_xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		
			_xhttp.send(_data);

			// Analyze server response
			switch (_xhttp.status) {
				case 200:				
					debug(LOGGING.ALL,"Response: "+_xhttp.responseText);
					
					var _result = JSON.parse(_xhttp.responseText);
					
					PROPERTY  = _result["property"] || PROPERTY;
					GAME.DATA = _result["gamedata"];
					GAME.INFO = _result["gameinfo"];
					
					debug(LOGGING.RESPONSE,"Server: "+PROPERTY.GAME.RESPONSE);
					
					break;
				default:
					PROPERTY.GAME.MODE = MODE.SERVER_ERROR
					debug(LOGGING.ERROR,"Server: "+_xhttp.statusText);
			};
			
			return _xhttp.status;
		} catch(e) {
			console.error("[CBAM] "+e.stack);
			PROPERTY.GAME.MODE = MODE.UNKNOWN;
			alert("[CBAM] "+e.name+": "+e.message);
		};
	};
	
	/***********************************
	  Enter the game - Continue or New
	***********************************/
	function enterGame($mode) {
		try {	
			debug(LOGGING.ALL, "enterGame: "+$mode);
		
			_firstStart = -1;
		
			if ($mode==ROLE.GUEST) {
				PROPERTY.USER.NAME     = getText("guest");
				PROPERTY.USER.PASSWORD = "";
				PROPERTY.USER.ACTOR    = PROPERTY.GAME.LOGINACTOR;
				PROPERTY.USER.ROLE     = ROLE.GUEST;
				PROPERTY.GAME.MODE     = MODE.PASSIVE;
			};

			// Try to load a savegame
			_nextAction = ACTION.LOAD;
			transmit(_nextAction);

			if ((PROPERTY.GAME.MODE==MODE.PASSIVE && PROPERTY.ALLOW.PASSIVE==YES) || PROPERTY.GAME.MODE==MODE.ACTIVE) {

				if ($mode==ROLE.GUEST) {
					PROPERTY.USER.NAME     = getText("guest");
					PROPERTY.USER.PASSWORD = "";
					PROPERTY.USER.ACTOR    = PROPERTY.GAME.LOGINACTOR;
					PROPERTY.USER.ROLE     = ROLE.GUEST;
					PROPERTY.GAME.MODE     = MODE.PASSIVE;
				};
		
				if (GAME.DATA==null) {
					debug(LOGGING.ALL, "No savegame found");
					// No savegame found - start new game

					_firstStart = 1;
					_nextAction = ACTION.NEW;
					transmit(_nextAction);
					
					DataManager.setupNewGame();
					SceneManager.goto(Scene_Map);					
					
					createParty();
					
					if (PROPERTY.GAME.AFTERNEWEVENT!=0) {
						debug(LOGGING.ALL,"reserveCommonEvent: "+PROPERTY.GAME.AFTERNEWEVENT);
						$gameTemp.reserveCommonEvent(PROPERTY.GAME.AFTERNEWEVENT);
					};										
					
				} else {
					debug(LOGGING.ALL, "Savegame loaded");
					
					_firstStart = 0;
					
					// Extract data from savegame
					debug(LOGGING.ALL, "Extract save contents");
					DataManager.createGameObjects();
					DataManager.extractSaveContents(JsonEx.parse(GAME.DATA));

					// Move player
					debug(LOGGING.ALL, "Reserve transfer player");
					$gamePlayer.reserveTransfer($gameMap.mapId(), $gamePlayer.x, $gamePlayer.y);
					$gamePlayer.requestMapReload();

					// Initialize map
					debug(LOGGING.ALL, "Goto Scene_Map");
					$gameSystem.onAfterLoad();
					Scene_Load.prototype.reloadMapIfUpdated.call(null);
					SceneManager.goto(Scene_Map);
					if (SceneManager._scene) { 
						SceneManager._scene.fadeOutAll(); 
					};
						
					createParty();
					
					if (PROPERTY.GAME.AFTERLOADEVENT!=0) {
						debug(LOGGING.ALL,"reserveCommonEvent: "+PROPERTY.GAME.AFTERLOADEVENT);
						$gameTemp.reserveCommonEvent(PROPERTY.GAME.AFTERLOADEVENT);
					};						
				};
			} else {
				_menuLevel = MENU.MAIN;
				_nextAction = ACTION.TITLE;			
				debug(LOGGING.WARNING, "Not allowed");
				SceneManager.push(Scene_Title);	
			};
		
		} catch(e) {
			console.error("[CBAM] "+e.stack);
			PROPERTY.GAME.MODE = MODE.UNKNOWN;
			alert("[CBAM] "+e.name+": "+e.message);
		};	
	};

	/******************
	  Organize actors
	******************/
	function createParty() {
		try {
			
			if (PROPERTY.GAME.PARTYMODE==PARTY.REGROUP || PROPERTY.GAME.PARTYMODE==PARTY.ACTIVE_ONLY) {
				var _index = 0;
				var _actors = $gameParty._actors.slice();

				debug(LOGGING.ALL,"Remove all actors from gameParty");
				for (_index = 0; _index < _actors.length; ++_index) {
					debug(LOGGING.ALL,"Remove actor #"+_actors[_index]+" from gameParty");
					$gameParty.removeActor(_actors[_index]);
				};

				debug(LOGGING.ALL,"Add actor #"+PROPERTY.USER.ACTOR+" to gameParty (leader)");
				$gameParty.addActor(PROPERTY.USER.ACTOR);

				if (PROPERTY.GAME.MODE==MODE.ACTIVE && PROPERTY.GAME.PARTYMODE==PARTY.REGROUP) {
					for (_index = 0; _index < _actors.length; ++_index) {
						if (_actors[_index]!=PROPERTY.USER.ACTOR && _actors[_index]!=PROPERTY.GAME.LOGINACTOR) {
							debug(LOGGING.ALL,"Add actor #"+_actors[_index]+" to gameParty");
							$gameParty.addActor(_actors[_index]);
						};
					};
				};			
			};
			
		} catch(e) {
			console.error("[CBAM] "+e.stack);
			alert("[CBAM] "+e.name+": "+e.message);
		};
	};
		
	/********************
	  Create title menu
	********************/	
	Window_TitleCommand.prototype.makeCommandList = function() {
		try {
			// Call up current game state
			if (PROPERTY.GAME.MODE==MODE.LOGGED_OUT || PROPERTY.GAME.MODE==MODE.PASSIVE || PROPERTY.GAME.MODE==MODE.ACTIVE) {

				switch (_menuLevel) {
					case MENU.MAIN: // Default
						if (PROPERTY.USER.ROLE==ROLE.GUEST) {
							this.addCommand(getText('login'), 'login');
							if (PROPERTY.ALLOW.GUESTS==YES && PROPERTY.ALLOW.PASSIVE==YES) {
								this.addCommand(getText('view'), 'view');
							};
						} else if (PROPERTY.USER.ROLE==ROLE.ADMIN) {
							this.addCommand(getText('logout'), 'logout');
							this.addCommand(getText('admin'), 'admin');
							this.addCommand(TextManager.continue_, 'continue');	
						} else {;
							this.addCommand(getText('logout'), 'logout');
							this.addCommand(TextManager.continue_, 'continue');
						};
						this.addCommand(TextManager.options, 'options');	
						if (PROPERTY.GAME.HOMEPAGE!="") {
							this.addCommand(getText('credits'), 'credits');
						};
						this.addCommand(getText('exit'), 'exit');
						break;
					case MENU.ADMIN: // Admin only
						this.addCommand(TextManager.newGame,  'newGame');
						if (PROPERTY.ALLOW.RESETUSER==YES) {
							this.addCommand(getText("resetUser"), 'resetUser');
						};
						if (PROPERTY.ALLOW.RESETGAME==YES) {
							this.addCommand(getText("resetGame"), 'resetGame');
						};
						this.addCommand(getText('back'), 'back');
						break;
				};

			} else {
				debug(LOGGING.WARNING,"Entering unknown game mode");
				this.addCommand(TextManager.options, 'options');				
				this.addCommand(getText('credits'), 'credits');
				this.addCommand(getText('exit'), 'exit');			
			};
		} catch(e) {
			console.error("[CBAM] "+e.stack);
			alert("[CBAM] "+e.name+": "+e.message);
		};
	};

	/**********************************
	  Link menu commands to functions
	**********************************/
	Scene_Title.prototype.createCommandWindow = function() {
		try {
			this._commandWindow = new Window_TitleCommand();
			this._commandWindow.setHandler('login',     this.commandLogin.bind(this));	    // Plugin
			this._commandWindow.setHandler('logout',    this.commandLogout.bind(this)); 	// Plugin
			this._commandWindow.setHandler('view',      this.commandView.bind(this));	    // Plugin
			this._commandWindow.setHandler('admin',     this.commandAdmin.bind(this));	    // Plugin
			this._commandWindow.setHandler('continue',  this.commandContinue.bind(this));
			this._commandWindow.setHandler('options',   this.commandOptions.bind(this));
			this._commandWindow.setHandler('credits',   this.commandCredits.bind(this));	// Plugin
			this._commandWindow.setHandler('exit',      this.commandExit.bind(this));	    // Plugin			
			this._commandWindow.setHandler('resetUser', this.commandResetUser.bind(this));	// Plugin
			this._commandWindow.setHandler('resetGame', this.commandResetGame.bind(this));	// Plugin			
			this._commandWindow.setHandler('newGame',   this.commandNewGame.bind(this));
			this._commandWindow.setHandler('back',      this.commandBack.bind(this));	    // Plugin
			this.addWindow(this._commandWindow);
		} catch(e) {
			console.error("[CBAM] "+e.stack);
			alert("[CBAM] "+e.name+": "+e.message);
		};
	};	
	
	/***********************
	  Title menu functions
	***********************/

	// Command: NEW GAME
	Scene_Title.prototype.commandNewGame = function() {
		try {
			debug(LOGGING.ALL,"commandNewGame");
			
			_menuLevel = MENU.ADMIN;
			_firstStart = -1;
			_nextAction = ACTION.NEW;
			transmit(_nextAction);
	
			if (PROPERTY.GAME.MODE==MODE.ACTIVE && PROPERTY.USER.ROLE==ROLE.ADMIN) {
				debug(LOGGING.ALL,"Start new game");
				_firstStart = 1;
				
				// Call original New Game command
				commandNewGame.call(this, arguments);
				
				createParty();
				
				if (PROPERTY.GAME.AFTERNEWEVENT!=0) {
					debug(LOGGING.ALL,"reserveCommonEvent: "+PROPERTY.GAME.AFTERNEWEVENT);
					$gameTemp.reserveCommonEvent(PROPERTY.GAME.AFTERNEWEVENT);
				};					
				
			} else {			
				// Back to title menu
				debug(LOGGING.WARNING,"Start new game failed");
				SceneManager.push(Scene_Title);			
			};
		} catch(e) {
			console.error("[CBAM] "+e.stack);
			alert("[CBAM] "+e.name+": "+e.message);
		};
	};

	// Command: CONTINUE GAME
	Scene_Title.prototype.commandContinue = function() {
		try {
			debug(LOGGING.ALL,"commandContinueGame");
			_menuLevel = MENU.MAIN;
		
			enterGame(ROLE.NORMAL);
			
		} catch(e) {
			console.error("[CBAM] "+e.stack);
			alert("[CBAM] "+e.name+": "+e.message);
		};
	};	
	
	// Command: GUEST VIEW
	Scene_Title.prototype.commandView = function() {
		try {
			debug(LOGGING.ALL,"commandView");
			_menuLevel = MENU.MAIN;
		
			enterGame(ROLE.GUEST);
			
		} catch(e) {
			console.error("[CBAM] "+e.stack);
			alert("[CBAM] "+e.name+": "+e.message);
		};	
	};

	// Command: OPTIONS
	Scene_Title.prototype.commandOptions = function() {
		try {
			debug(LOGGING.ALL,"commandOptions");
			_menuLevel = MENU.MAIN;
			_nextAction = ACTION.NONE;
		
			this._commandWindow.close();
			SceneManager.push(Scene_Options);
		} catch(e) {
			console.error("[CBAM] "+e.stack);
			alert("[CBAM] "+e.name+": "+e.message);
		};
	};
	
	// Command: LOGIN
	Scene_Title.prototype.commandLogin = function() {
		try {
			debug(LOGGING.ALL,"commandLogin");
			_menuLevel = MENU.MAIN;
			_nextAction = ACTION.USERNAME;
			this._commandWindow.close();
			
			PROPERTY.USER.NAME     = "";
			PROPERTY.USER.PASSWORD = "";			

			debug(LOGGING.ALL,"Open login input for actor #"+PROPERTY.GAME.LOGINACTOR);
			
			SceneManager.push(Scene_Login);
			SceneManager.prepareNextScene(PROPERTY.GAME.LOGINACTOR, PROPERTY.USER.MAXLENGTH);			
		} catch(e) {
			console.error("[CBAM] "+e.stack);
			alert("[CBAM] "+e.name+": "+e.message);
		};
	};

	// Command: LOGOUT
	Scene_Title.prototype.commandLogout = function() {
		try {
			debug(LOGGING.ALL, "commandLogout");
			_menuLevel = MENU.MAIN;
			_nextAction = ACTION.LOGOUT;
			SceneManager.push(Scene_Title);
		} catch(e) {
			console.error("[CBAM] "+e.stack);
			alert("[CBAM] "+e.name+": "+e.message);
		};
	};

	// Command: ADMIN (open admin sub-menu)
	Scene_Title.prototype.commandAdmin = function() {
		try {
			debug(LOGGING.ALL,"commandAdmin");
			_menuLevel = MENU.ADMIN;
			SceneManager.push(Scene_Title);
		} catch(e) {
			console.error("[CBAM] "+e.stack);
			alert("[CBAM] "+e.name+": "+e.message);
		};
	};

	// Command: CREDITS (open credits link)
	Scene_Title.prototype.commandCredits = function() {
		try {
			debug(LOGGING.ALL,"commandCredits");
			_nextAction = ACTION.TITLE;
			_menuLevel = MENU.MAIN;

			TouchInput.clear();
			Input.clear();
			
			debug(LOGGING.ALL, "Try to open "+PROPERTY.GAME.HOMEPAGE);
			var _homepage = window.open(PROPERTY.GAME.HOMEPAGE);
			SceneManager.push(Scene_Title);				
			if (_homepage) { 
				_homepage.focus();	
			};
		} catch(e) {
			console.error("[CBAM] "+e.stack);
			alert("[CBAM] "+e.name+": "+e.message);
		};
	};

	// Command: EXIT (quits the game)
	Scene_Title.prototype.commandExit = function() {
		try {
			debug(LOGGING.ALL, "commandExit");
			_menuLevel = MENU.MAIN;
			this._commandWindow.close();
			this.fadeOutAll();
			SceneManager.exit();
		} catch(e) {
			console.error("[CBAM] "+e.stack);
			alert("[CBAM] "+e.name+": "+e.message);
		};
	};

	// Command: CLEAR USER (delete active user from database)
	Scene_Title.prototype.commandResetUser = function() {
		try {
			debug(LOGGING.ALL, "commandResetUser");
			_menuLevel = MENU.MAIN;
		
			_nextAction = ACTION.RESETUSER;
			transmit(_nextAction);
			_nextAction = ACTION.TITLE;
			
			// Back to title menu
			SceneManager.push(Scene_Title);
		} catch(e) {
			console.error("[CBAM] "+e.stack);
			alert("[CBAM] "+e.name+": "+e.message);
		};
	};	

	// Command: CLEAR GAME (delete game from database)
	Scene_Title.prototype.commandResetGame = function() {
		try {
			debug(LOGGING.ALL, "commandResetGame");
			_menuLevel = MENU.MAIN;
		
			_nextAction = ACTION.RESETGAME;
			transmit(_nextAction);
			
			setProperties();
			
			_nextAction = ACTION.TITLE;
			
			// Back to title menu
			SceneManager.push(Scene_Title);
		} catch(e) {
			console.error("[CBAM] "+e.stack);
			alert("[CBAM] "+e.name+": "+e.message);
		};
	};	
	
	// Command: BACK (open main title menu)
	Scene_Title.prototype.commandBack = function() {
		try {
			debug(LOGGING.ALL, "commandBack");
		
			_menuLevel = MENU.MAIN;
			SceneManager.push(Scene_Title);
		} catch(e) {
			console.error("[CBAM] "+e.stack);
			alert("[CBAM] "+e.name+": "+e.message);
		};
	};	
	
	/*******************************************
	  Disable command menu items (inGame menu)
	*******************************************/
	Scene_Map.prototype.callMenu = function() {
		try {
			debug(LOGGING.ALL, "Call menu");
			if (PROPERTY.USER.ROLE==ROLE.GUEST) {
				SoundManager.playOk();
				SceneManager.push(Scene_GameEnd);
			} else {
				callMenu.call(this, arguments);
			};
		} catch(e) {
			console.error("[CBAM] "+e.stack);
			alert("[CBAM] "+e.name+": "+e.message);
		};		
	};

	Window_MenuCommand.prototype.makeCommandList = function() {
		try {
			debug(LOGGING.ALL, "makeCommandList");
		
			// Check session
			_nextAction = ACTION.MENU;
			transmit(_nextAction);
	
			if (PROPERTY.GAME.MODE==MODE.ACTIVE) {
			
				makeCommandList.call(this, arguments);
				
			} else {
			
				debug(LOGGING.WARNING, "PASSIE MODE: Reduced menu");

				// And commands for passive mode
				this.addOptionsCommand();
				if (PROPERTY.ALLOW.LOAD==YES) {
					// On passive mode, use save command for load game
					this.addSaveCommand();
				};
				this.addGameEndCommand();
			};
		} catch(e) {
			console.error("[CBAM] "+e.stack);
			alert("[CBAM] "+e.name+": "+e.message);
		};
	};
	
	Window_MenuCommand.prototype.addSaveCommand = function() {
		try {
			if (PROPERTY.GAME.MODE==MODE.ACTIVE) {
				addSaveCommand.call(this, arguments);
			} else if (PROPERTY.GAME.MODE!=MODE.ACTIVE && PROPERTY.USER.ROLE!=ROLE.GUEST) {
				this.addCommand(getText("load"), 'save', true);
			};
		} catch(e) {
			console.error("[CBAM] "+e.stack);
			alert("[CBAM] "+e.name+": "+e.message);
		};		
	};	

	/***********************************************
	  Overwrite "end game" on ingame option screen
	***********************************************/
	Scene_GameEnd.prototype.commandToTitle = function() {
		try {
			debug(LOGGING.ALL, "commandToTitle");
	
			// AutoSave on game end?
			if (PROPERTY.SAVEONEND==YES && PROPERTY.GAME.MODE==MODE.ACTIVE) {
				debug(LOGGING.ALL, "ACTIVE MODE: AutoSave");
				
				setActiveLocation();
				
				if (PROPERTY.GAME.BEFORESAVEEVENT!=0) {
					debug(LOGGING.ALL,"reserveCommonEvent: "+PROPERTY.GAME.BEFORESAVEEVENT);
					$gameTemp.reserveCommonEvent(PROPERTY.GAME.BEFORESAVEEVENT);
				};

				$gameSystem.onBeforeSave();
				_nextAction = ACTION.SAVE
				transmit(_nextAction);
			};
		
			_menuLevel = MENU.MAIN;
			_nextAction = ACTION.EXIT;
		
			// Call regular game end function
			commandToTitle.call(this, arguments);
		} catch(e) {
			console.error("[CBAM] "+e.stack);
			alert("[CBAM] "+e.name+": "+e.message);
		};
	};

	/*******************************************
	  Overwrite "save" on ingame option screen
	*******************************************/
	Scene_Menu.prototype.commandSave = function() {
		try {
			debug(LOGGING.ALL, "commandSave");
			
			if (PROPERTY.GAME.MODE==MODE.ACTIVE) {
				// Save the game, if you are on active mode
				debug(LOGGING.ALL, "ACTIVE MODE: Save");
				
				setActiveLocation();
				
				if (PROPERTY.GAME.BEFORESAVEEVENT!=0) {
					debug(LOGGING.ALL,"reserveCommonEvent: "+PROPERTY.GAME.BEFORESAVEEVENT);
					$gameTemp.reserveCommonEvent(PROPERTY.GAME.BEFORESAVEEVENT);
				};
				
				$gameSystem.onBeforeSave();
				
				if (PROPERTY.PASSIVEONSAVE==YES) {
					// Save game and switch to passive mode
					_nextAction = ACTION.SWITCHPASSIVE;
				} else {
					// Just save the game (and be active)
					_nextAction = ACTION.SAVE
				}
				transmit(_nextAction);

				if (PROPERTY.GAME.MODE!=MODE.ACTIVE && PROPERTY.GAME.AFTERPASSIVEEVENT!=0) {
					debug(LOGGING.ALL,"reserveCommonEvent: "+PROPERTY.GAME.AFTERPASSIVEEVENT);
					$gameTemp.reserveCommonEvent(PROPERTY.GAME.AFTERPASSIVEEVENT);
				};						
				
			} else if (PROPERTY.GAME.MODE!=MODE.ACTIVE && PROPERTY.USER.ROLE!=ROLE.GUEST && PROPERTY.ALLOW.LOAD==YES) {
				// Load the game on passive mode (and switch to active, if possible)
				debug(LOGGING.ALL,"Load game");
				_nextAction = ACTION.LOAD;
				transmit(_nextAction);					

				if (GAME.DATA!=null) {
					debug(LOGGING.ALL, "Savegame loaded");

					_firstStart = 2;
					
					TouchInput.clear();
					Input.clear();						
					
					// Extract data from savegame
					debug(LOGGING.ALL, "Extract save contents");
					DataManager.createGameObjects();
					DataManager.extractSaveContents(JsonEx.parse(GAME.DATA));

					// Move player
					debug(LOGGING.ALL, "Reserve transfer player");
					$gamePlayer.reserveTransfer($gameMap.mapId(), $gamePlayer.x, $gamePlayer.y);
					$gamePlayer.requestMapReload();

					// Initialize map
					debug(LOGGING.ALL, "Goto Scene_Map");
					$gameSystem.onAfterLoad();
					Scene_Load.prototype.reloadMapIfUpdated.call(null);
					SceneManager.goto(Scene_Map);
					//if (SceneManager._scene) { SceneManager._scene.fadeOutAll(); }

					createParty();
					
					if (PROPERTY.GAME.AFTERLOADEVENT!=0) {
						debug(LOGGING.ALL,"reserveCommonEvent: "+PROPERTY.GAME.AFTERLOADEVENT);
						$gameTemp.reserveCommonEvent(PROPERTY.GAME.AFTERLOADEVENT);
					};
				};

			} else {
				debug(LOGGING.WARNING, "PASSIVE MODE: Can't save");
			};

			// Close option window
			SceneManager.pop();		
		} catch(e) {
			console.error("[CBAM] "+e.stack);
			alert("[CBAM] "+e.name+": "+e.message);
		};
	};	
	
	/********************
	  Draw title screen
	********************/

	// SERVER CONNECTION - Create foreground (add some game infos)
	Scene_Title.prototype.createForeground = function() {
		try {
			// Call server on title menu!
			transmit(_nextAction);
			_nextAction=ACTION.TITLE;
		
			this._gameTitleSprite    = new Sprite(new Bitmap(Graphics.width, Graphics.height)); // Title
			this._gameNameSprite     = new Sprite(new Bitmap(Graphics.width, Graphics.height)); // Plugin name and version
			this._gameLoginSprite    = new Sprite(new Bitmap(Graphics.width, Graphics.height)); // Login name
			this._gameResponseSprite = new Sprite(new Bitmap(Graphics.width, Graphics.height)); // Server response
		
			this.addChild(this._gameTitleSprite);
			this.addChild(this._gameNameSprite);
			this.addChild(this._gameLoginSprite);
			this.addChild(this._gameResponseSprite);
		
			if ($dataSystem.optDrawTitle) {
				this.drawGameTitle();
			};
			if (PROPERTY.GAME.MODE==MODE.PASSIVE || PROPERTY.GAME.MODE==MODE.ACTIVE) {
				this.drawLoginSprite();
			};
			this.drawInfoSprite();
			if (PROPERTY.LOGGING>=LOGGING.RESPONSE) {
				this.drawResponseSprite();
			};
		} catch(e) {
			console.error("[CBAM] "+e.stack);
			alert("[CBAM] "+e.name+": "+e.message);
		};	
	};

	// Draw game title
	Scene_Title.prototype.drawGameTitle = function() {
		try {
			var x = 20;
			var y = Graphics.height / 5;
			var maxWidth = Graphics.width - x * 2;
			var text = $dataSystem.gameTitle;
			this._gameTitleSprite.bitmap.outlineColor = 'black';
			this._gameTitleSprite.bitmap.outlineWidth = 8;
			this._gameTitleSprite.bitmap.fontSize = 72;
			this._gameTitleSprite.bitmap.drawText(text, x, y, maxWidth, 48, 'center');
		} catch(e) {
			console.error("[CBAM] "+e.stack);
			alert("[CBAM] "+e.name+": "+e.message);
		};
	};

	// Draw server response
	Scene_Title.prototype.drawResponseSprite = function() {
		try {
			var x = Graphics.width - (Graphics.width / 2) - 20;
			var y = Graphics.height - 32;
			var maxWidth = Graphics.width / 2;
			var text = PROPERTY.GAME.RESPONSE;
			this._gameResponseSprite.bitmap.outlineColor = 'black';	
			this._gameResponseSprite.bitmap.outlineWidth = 1;
			this._gameResponseSprite.bitmap.fontSize = 14;
			this._gameResponseSprite.bitmap.drawText(text, x, y, maxWidth, 16, 'right');
			debug(LOGGING.ALL,"drawResponse: "+text);
		} catch(e) {
			console.error("[CBAM] "+e.stack);
			alert("[CBAM] "+e.name+": "+e.message);
		};		
	};	

	// Draw login information
	Scene_Title.prototype.drawLoginSprite = function() {
		try {
			var x = Graphics.width - (Graphics.width / 2) - 20;
			var y = Graphics.height - 48;
			var maxWidth = Graphics.width / 2;
			var text = getText("loggedin",PROPERTY.USER.NAME);
			this._gameLoginSprite.bitmap.outlineColor = 'black';
			this._gameLoginSprite.bitmap.outlineWidth = 2;
			this._gameLoginSprite.bitmap.fontSize = 14;
			this._gameLoginSprite.bitmap.drawText(text, x, y, maxWidth, 16, 'right');
			debug(LOGGING.ALL, "drawLoginName: "+text);
		} catch(e) {
			console.error("[CBAM] "+e.stack);
			alert("[CBAM] "+e.name+": "+e.message);
		};
	};

	// Draw game (plugin) name and version
	Scene_Title.prototype.drawInfoSprite = function() {
		try {
			var x = 20;
			var y = Graphics.height - 32;
			var maxWidth = Graphics.width - x * 2;
			var text = PROPERTY.GAME.NAME+" V"+PROPERTY.GAME.VERSION
			if (PROPERTY.LOGGING>=LOGGING.RESPONSE) {
				text += " : "+_transCount;
			};
			this._gameNameSprite.bitmap.outlineColor = 'black';
			this._gameNameSprite.bitmap.outlineWidth = 2;
			this._gameNameSprite.bitmap.fontSize = 14;
			this._gameNameSprite.bitmap.drawText(text, x, y, maxWidth, 16, 'left');
			debug(LOGGING.ALL, "drawGameName: "+text);
		} catch(e) {
			console.error("[CBAM] "+e.stack);
			alert("[CBAM] "+e.name+": "+e.message);
		};
	};

	/**********************************************************
	  Scene_Login - The scene class of the login input screen
	**********************************************************/
	function Scene_Login() {
		try {
			this.initialize.apply(this, arguments);
		} catch(e) {
			console.error("[CBAM] "+e.stack);
			alert("[CBAM] "+e.name+": "+e.message);
		};
	};

	Scene_Login.prototype = Object.create(Scene_MenuBase.prototype);
	Scene_Login.prototype.constructor = Scene_Login;

	Scene_Login.prototype.initialize = function() {
		try {
		Scene_MenuBase.prototype.initialize.call(this);
		} catch(e) {
			console.error("[CBAM] "+e.stack);
			alert("[CBAM] "+e.name+": "+e.message);
		};	
	};

	Scene_Login.prototype.prepare = function(actorId, maxLength) {
		try {
			this._actorId = actorId;
			this._maxLength = maxLength;
		} catch(e) {
			console.error("[CBAM] "+e.stack);
			alert("[CBAM] "+e.name+": "+e.message);
		};		
	};

	Scene_Login.prototype.create = function() {
		try {
		Scene_MenuBase.prototype.create.call(this);
		this._actor = $gameActors.actor(this._actorId);
		this.createEditWindow();
		this.createInputWindow();
		} catch(e) {
			console.error("[CBAM] "+e.stack);
			alert("[CBAM] "+e.name+": "+e.message);
		};	
	};

	Scene_Login.prototype.start = function() {
		try {
			Scene_MenuBase.prototype.start.call(this);
			this._editWindow.refresh();
		} catch(e) {
			console.error("[CBAM] "+e.stack);
			alert("[CBAM] "+e.name+": "+e.message);
		};		
	};

	Scene_Login.prototype.createEditWindow = function() {
		try {
			this._editWindow = new Window_LoginEdit(this._actor, this._maxLength);
			this.addWindow(this._editWindow);
		} catch(e) {
			console.error("[CBAM] "+e.stack);
			alert("[CBAM] "+e.name+": "+e.message);
		};
	};

	Scene_Login.prototype.createInputWindow = function() {
		try {
			this._inputWindow = new Window_NameInput(this._editWindow);
			this._inputWindow.setHandler('ok', this.onInputOk.bind(this));
			this.addWindow(this._inputWindow);
		} catch(e) {
			console.error("[CBAM] "+e.stack);
			alert("[CBAM] "+e.name+": "+e.message);
		};		
	};

	Scene_Login.prototype.onInputOk = function() {
		try {
			switch(_nextAction) {
				case ACTION.USERNAME: // Username was entered
					_nextAction = ACTION.PASSWORD;

					PROPERTY.USER.NAME = this._editWindow.name();
					debug(LOGGING.ALL, "Set username to "+PROPERTY.USER.NAME);
	
					this.popScene();
		
					debug(LOGGING.ALL, "Open password window");
					SceneManager.push(Scene_Login);
					SceneManager.prepareNextScene(PROPERTY.GAME.LOGINACTOR, PROPERTY.USER.MAXLENGTH);

					break;
				case ACTION.PASSWORD: // Password was entered

					_nextAction = ACTION.LOGIN;
			
					PROPERTY.USER.PASSWORD = this._editWindow.name();
					debug(LOGGING.ALL, "Set password to "+PROPERTY.USER.PASSWORD);
	
					this.popScene();
		
					debug(LOGGING.ALL, "Back to title menu");
					SceneManager.push(Scene_Title);		
					
					break;
				default:
					debug(LOGGING.WARNING, "There was something wrong with the login screen");
			};
		} catch(e) {
			console.error("[CBAM] "+e.stack);
			alert("[CBAM] "+e.name+": "+e.message);
		};
	};

	/**************************************************************
	  Window_LoginEdit - The window for editing an login/password
	**************************************************************/
	function Window_LoginEdit() {
		try {
			this.initialize.apply(this, arguments);
		} catch(e) {
			console.error("[CBAM] "+e.stack);
			alert("[CBAM] "+e.name+": "+e.message);
		};		
	};

	Window_LoginEdit.prototype = Object.create(Window_Base.prototype);
	Window_LoginEdit.prototype.constructor = Window_LoginEdit;

	Window_LoginEdit.prototype.initialize = function(actor, maxLength) {
		try {
			var width = this.windowWidth();
			var height = this.windowHeight();
			var x = (Graphics.boxWidth - width) / 2;
			var y = (Graphics.boxHeight - (height + this.fittingHeight(9) + 8)) / 2;
			Window_Base.prototype.initialize.call(this, x, y, width, height);
			this._actor = actor;
			switch(_nextAction) {
				case ACTION.USERNAME:
					this._name = PROPERTY.USER.NAME;
					break;
				case ACTION.PASSWORD:
					this._name = PROPERTY.USER.PASSWORD;
					break;
				default:
					this._name = ""
			};
			this._index = this._name.length;
			this._maxLength = maxLength;
			this._defaultName = this._name;
			this.deactivate();
			this.refresh();
			ImageManager.loadFace(actor.faceName());
		} catch(e) {
			console.error("[CBAM] "+e.stack);
			alert("[CBAM] "+e.name+": "+e.message);
		};		
	};

	Window_LoginEdit.prototype.windowWidth = function() {
		try {
			return 480;
		} catch(e) {
			console.error("[CBAM] "+e.stack);
			alert("[CBAM] "+e.name+": "+e.message);
		};			
	};

	Window_LoginEdit.prototype.windowHeight = function() {
		try {
			return this.fittingHeight(4);
		} catch(e) {
			console.error("[CBAM] "+e.stack);
			alert("[CBAM] "+e.name+": "+e.message);
		};			
	};

	Window_LoginEdit.prototype.name = function() {
		try {
			return this._name;
		} catch(e) {
			console.error("[CBAM] "+e.stack);
			alert("[CBAM] "+e.name+": "+e.message);
		};			
	};

	Window_LoginEdit.prototype.restoreDefault = function() {
		try {
			this._name = this._defaultName;
			this._index = this._name.length;
			this.refresh();
			return this._name.length > 0;
		} catch(e) {
			console.error("[CBAM] "+e.stack);
			alert("[CBAM] "+e.name+": "+e.message);
		};			
	};

	Window_LoginEdit.prototype.add = function(ch) {
		try {
			if (this._index < this._maxLength) {
				this._name += ch;
				this._index++;
				this.refresh();
				return true;
			} else {
				return false;
			}
		} catch(e) {
			console.error("[CBAM] "+e.stack);
			alert("[CBAM] "+e.name+": "+e.message);
		};			
	};

	Window_LoginEdit.prototype.back = function() {
		try {
			if (this._index > 0) {
				this._index--;
				this._name = this._name.slice(0, this._index);
				this.refresh();
				return true;
			} else {
				return false;
			};
		} catch(e) {
			console.error("[CBAM] "+e.stack);
			alert("[CBAM] "+e.name+": "+e.message);
		};			
	};

	Window_LoginEdit.prototype.faceWidth = function() {
		try {
			return 144;
		} catch(e) {
			console.error("[CBAM] "+e.stack);
			alert("[CBAM] "+e.name+": "+e.message);
		};			
	};

	Window_LoginEdit.prototype.charWidth = function() {
		try {
			var text = $gameSystem.isJapanese() ? '\uff21' : 'A';
			return this.textWidth(text);
		} catch(e) {
			console.error("[CBAM] "+e.stack);
			alert("[CBAM] "+e.name+": "+e.message);
		};			
	};

	Window_LoginEdit.prototype.left = function() {
		try {
			var nameCenter = (this.contentsWidth() + this.faceWidth()) / 2;
			var nameWidth = (this._maxLength + 1) * this.charWidth();
			return Math.min(nameCenter - nameWidth / 2, this.contentsWidth() - nameWidth);
		} catch(e) {
			console.error("[CBAM] "+e.stack);
			alert("[CBAM] "+e.name+": "+e.message);
		};			
	};

	Window_LoginEdit.prototype.itemRect = function(index) {
		try {
			return {
				x: this.left() + index * this.charWidth(),
				y: 54,
				width: this.charWidth(),
				height: this.lineHeight()
			};
		} catch(e) {
			console.error("[CBAM] "+e.stack);
			alert("[CBAM] "+e.name+": "+e.message);
		};		
	};

	Window_LoginEdit.prototype.underlineRect = function(index) {
		try {
			var rect = this.itemRect(index);
			rect.x++;
			rect.y += rect.height - 4;
			rect.width -= 2;
			rect.height = 2;
			return rect;
		} catch(e) {
			console.error("[CBAM] "+e.stack);
			alert("[CBAM] "+e.name+": "+e.message);
		};		
	};

	Window_LoginEdit.prototype.underlineColor = function() {
		try {
			return this.normalColor();
		} catch(e) {
			console.error("[CBAM] "+e.stack);
			alert("[CBAM] "+e.name+": "+e.message);
		};			
	};

	Window_LoginEdit.prototype.drawUnderline = function(index) {
		try {
			var rect = this.underlineRect(index);
			var color = this.underlineColor();
			this.contents.paintOpacity = 48;
			this.contents.fillRect(rect.x, rect.y, rect.width, rect.height, color);
			this.contents.paintOpacity = 255;
		} catch(e) {
			console.error("[CBAM] "+e.stack);
			alert("[CBAM] "+e.name+": "+e.message);
		};		
	};

	Window_LoginEdit.prototype.drawChar = function(index) {
		try {
			var rect = this.itemRect(index);
			this.resetTextColor();
			this.drawText(this._name[index] || '', rect.x, rect.y);
		} catch(e) {
			console.error("[CBAM] "+e.stack);
			alert("[CBAM] "+e.name+": "+e.message);
		};			
	};
	
	Window_LoginEdit.prototype.drawTitle = function() {
		try {
			var x = 175;
			var y = 0;
			var maxWidth = Graphics.width - x * 2;
			var text = "";
			switch(_nextAction) {
				case ACTION.USERNAME:
					text = getText("username")+":";
					break;
				case ACTION.PASSWORD:
					text = getText("password")+":";
					break;
			};			
			this.outlineColor = 'black';
			this.outlineWidth = 2;
			this.fontSize = 48;
			this.drawText(text, x, y, maxWidth, 48, 'left');
		} catch(e) {
			console.error("[CBAM] "+e.stack);
			alert("[CBAM] "+e.name+": "+e.message);
		};			
	};	

	Window_LoginEdit.prototype.refresh = function() {
		try {
			this.contents.clear();
			this.drawActorFace(this._actor, 0, 0);
			this.drawTitle();
			for (var i = 0; i < this._maxLength; i++) {
				this.drawUnderline(i);
			};
			for (var j = 0; j < this._name.length; j++) {
				this.drawChar(j);
			};
			var rect = this.itemRect(this._index);
			this.setCursorRect(rect.x, rect.y, rect.width, rect.height);
		} catch(e) {
			console.error("[CBAM] "+e.stack);
			alert("[CBAM] "+e.name+": "+e.message);
		};			
	};

	/****************************************************
	  Return values for script and use in other plugins
	****************************************************/
	$.welcomeMessageShow = function() {
		// Create a welcome message for the current player actor
		try {
			TouchInput.clear();
			Input.clear();		
		
			debug(LOGGING.ALL, "Create welcome message");
				
			if (PROPERTY.USER.ROLE==ROLE.GUEST) {
				$gameMessage.setBackground(1);
				$gameMessage.setPositionType(1);
			
				if (PROPERTY.MESSAGE.WELCOMEGUEST!="") {
					$gameMessage.add(PROPERTY.MESSAGE.WELCOMEGUEST.replace("{0}",getText("guest")));
				} else {
					$gameMessage.add(getText("wb_guest",getText("guest")));
				};
			} else { 			
				$gameMessage.setFaceImage($gameActors.actor(PROPERTY.USER.ACTOR)._faceName,$gameActors.actor(PROPERTY.USER.ACTOR)._faceIndex);
				$gameMessage.setBackground(1);
				$gameMessage.setPositionType(1);
			
				if (PROPERTY.GAME.MODE==MODE.ACTIVE) {
					if (PROPERTY.MESSAGE.WELCOMEACTIVE!="") {
						$gameMessage.add(PROPERTY.MESSAGE.WELCOMEACTIVE.replace("{0}",$gameActors.actor(PROPERTY.USER.ACTOR)._name));	
					} else {
						$gameMessage.add(getText("wb_active",$gameActors.actor(PROPERTY.USER.ACTOR)._name));	
					};
				} else {
					if (PROPERTY.MESSAGE.WELCOMEPASSIVE!="") {
						$gameMessage.add(PROPERTY.MESSAGE.WELCOMEPASSIVE.replace("{0}",$gameActors.actor(PROPERTY.USER.ACTOR)._name));
					} else {
						$gameMessage.add(getText("wb_passive",$gameActors.actor(PROPERTY.USER.ACTOR)._name));
					};
				};
			};
		} catch(e) {
			console.error("[CBAM] "+e.stack);
			alert("[CBAM] "+e.name+": "+e.message);
		};		
	};	
	$.gameMode = function() { return PROPERTY.GAME.MODE; };
	$.firstStart = function() { return _firstStart; };
	$.guestActor = function() { return PROPERTY.GAME.LOGINACTOR; };
	$.userActor = function() { return PROPERTY.USER.ACTOR; };
	$.userRole = function() { return PROPERTY.USER.ROLE; };
	$.userName = function() { return PROPERTY.USER.NAME; };
	$.userParty = function($actorId) { 
		// Looking for the (current) partyId where player's actor is
		try {
			var _party = 0;
			var _actor = 0;
			$actorId = $actorId || PROPERTY.USER.ACTOR;
			
			debug(LOGGING.ALL, "Looking for actor #"+$actorId);
			
			if (typeof $gameParties !== "undefined") {
				// Looking on HIME Party Manager
				for (_party in $gameParties._parties) {
					for (_actor = 0; _actor < $gameParties._parties[_party]._actors.length; ++_actor) {
						if ($actorId==$gameParties._parties[_party]._actors[_actor]) {
							debug(LOGGING.ALL, "Found actor #"+$actorId+" on party "+_party);
							return _party;
						};
					};
				};
			} else {
				// Looking on gameParty
				for (_actor = 0; _actor < $gameParty._actors.length; ++_actor) {
					if ($gameParty._actors[_actor]==$actorId) {
						debug(LOGGING.ALL, "Found actor #"+$actorId+" on gameParty");
						return 1;
					};							
				};				
			};
			debug(LOGGING.WARNING, "Actor #"+$actorId+" not found on a party");
			return 0;
		} catch(e) {
			console.error("[CBAM] "+e.stack);
			alert("[CBAM] "+e.name+": "+e.message);
		};
	};
	$.serverResponse = function() { return PROPERTY.GAME.RESPONSE; };
	$.setLeader = function($partyId, $actorId) {
		// Set the given actor as first char on the given party
		try {
			var _index = 0;
			
			if (typeof $gameParties !== "undefined") {
			
				var _actors = $gameParties._parties[$partyId]._actors.slice();
			
				for (_index = 0; _index < _actors.length; ++_index) {
					debug(LOGGING.ALL,"Remove actor #"+_actors[_index]+" from party #"+$partyId);
					$gameParties._parties[$partyId].removeActor(_actors[_index]);
				};

				debug(LOGGING.ALL,"Add actor #"+$actorId+" to party #"+$partyId+" (leader)");
				$gameParties._parties[$partyId].addActor($actorId);

				for (_index = 0; _index < _actors.length; ++_index) {
					if (_actors[_index]!=$actorId) {
						debug(LOGGING.ALL,"Add actor #"+_actors[_index]+" to party #"+$partyId+" (loader)");
						$gameParties._parties[$partyId].addActor(_actors[_index]);
					};			
				};
				
			} else {

				var _actors = $gameParty._actors.slice();
				
				for (_index = 0; _index < _actors.length; ++_index) {
					debug(LOGGING.ALL,"Remove actor #"+_actors[_index]+" from gameParty");
					$gameParty.removeActor(_actors[_index]);
				};
				
				debug(LOGGING.ALL,"Add actor #"+$actorId+" to gameParty (leader)");
				$gameParty.addActor($actorId);

				for (_index = 0; _index < _actors.length; ++_index) {
					if (_actors[_index]!=$actorId && _actors[_index]!=PROPERTY.GAME.LOGINACTOR) {
						debug(LOGGING.ALL,"Add actor #"+_actors[_index]+" to gameParty");
						$gameParty.addActor(_actors[_index]);
					};
				};
	
			};
		} catch(e) {
			console.error("[CBAM] "+e.stack);
			alert("[CBAM] "+e.name+": "+e.message);
		};
	};
	$.saveGame = function($quickSave) {
		try {
			debug(LOGGING.ALL, "commandSave");
		
			$quickSave = $quickSave || false;

			if (PROPERTY.GAME.MODE==MODE.ACTIVE) {
				debug(LOGGING.ALL, "ACTIVE MODE: Save");
			
				setActiveLocation();
			
				if (PROPERTY.GAME.BEFORESAVEEVENT!=0) {
					debug(LOGGING.ALL,"reserveCommonEvent: "+PROPERTY.GAME.BEFORESAVEEVENT);
					$gameTemp.reserveCommonEvent(PROPERTY.GAME.BEFORESAVEEVENT);
				};

				$gameSystem.onBeforeSave();
				if ($quickSave==true) {
					_nextAction = ACTION.QUICKSAVE;
				} else {
					_nextAction = ACTION.SAVE
				};
				transmit(_nextAction);
				return true;
			} else {
				debug(LOGGING.WARNING, "PASSIVE MODE: Can't save");
				return false;
			};
		} catch(e) {
			console.error("[CBAM] "+e.stack);
			alert("[CBAM] "+e.name+": "+e.message);
		};			
	};
	$.loadGame = function($quickLoad,$switch) {
		try {
			// Try to load a savegame
			debug(LOGGING.ALL, "commandLoad");
			
			$quickLoad = $quickLoad || false;
			$switch = parseInt($switch || 0);
			
			if ($quickLoad==true) {
				_nextAction = ACTION.QUICKLOAD;
			} else {
				_nextAction = ACTION.LOAD;
			};
			transmit(_nextAction);
			
			if (GAME.DATA!=null) {
				debug(LOGGING.ALL, "Savegame loaded");

				_firstStart = 2;
					
				// Extract data from savegame
				debug(LOGGING.ALL, "Extract save contents");
				DataManager.createGameObjects();
				DataManager.extractSaveContents(JsonEx.parse(GAME.DATA));

				// Move player
				debug(LOGGING.ALL, "Reserve transfer player");
				$gamePlayer.reserveTransfer($gameMap.mapId(), $gamePlayer.x, $gamePlayer.y);
				$gamePlayer.requestMapReload();

				// Initialize map
				debug(LOGGING.ALL, "Goto Scene_Map");
				$gameSystem.onAfterLoad();
				Scene_Load.prototype.reloadMapIfUpdated.call(null);
				SceneManager.goto(Scene_Map);
				//if (SceneManager._scene) { SceneManager._scene.fadeOutAll(); }

				createParty();

				if (PROPERTY.GAME.AFTERLOADEVENT!=0) {
					debug(LOGGING.ALL,"reserveCommonEvent: "+PROPERTY.GAME.AFTERLOADEVENT);
					$gameTemp.reserveCommonEvent(PROPERTY.GAME.AFTERLOADEVENT);
				};					
				
				if ($switch!=0) {
					debug(LOGGING.ALL,"Activate switch "+$switch);
					$gameSwitches.setValue($switch,true);
				};
				
				return true;
			} else {
				return false;
			};
		} catch(e) {
			console.error("[CBAM] "+e.stack);
			alert("[CBAM] "+e.name+": "+e.message);
		};				
	};
	$.timerTick = function($delta) {
		try {
			var _now = new Date().getTime();
			debug(LOGGING.RESPONSE, "[CBAM] Timer: "+String(Math.round((100*$delta)-((_now-_lastExecute)/10))/100)+" seconds left...");
			if ((_now-_lastExecute)/1000 > $delta) {
				debug(LOGGING.RESPONSE, "[CBAM] Timer: Execute!");
				_lastExecute = _now;
				return true;
			} else {
				return false;
			};
		} catch(e) {
			console.error("[CBAM] "+e.stack);
			alert("[CBAM] "+e.name+": "+e.message);
		};		
	};

})(CBAM);
// EOF