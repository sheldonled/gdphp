<?php
	function autoload($className) {
	    //Manual entry
	    require_once("Dir.php");
	    require_once("File.php");
	    require_once("Utils.php");
		$classPath = explode('_', $className);
		for($i = 0; $i <= count($classPath); $i++) {
			if (@$classPath[0] != 'Google') {
				continue;
			}
			// Drop 'Google', and maximum class file path depth in this project is 3.
			$classPath = array_slice($classPath, 1, 2);

			$filePath = dirname(__FILE__) . '/Google-API/' . implode('/', $classPath) . '.php';
			if (file_exists($filePath)) {
				require_once($filePath);
			}
		}
	}
	spl_autoload_register('autoload');

	switch (@$_GET["p"]) {
		case 'settings':
			if(!$cfgfile = fopen("config.json","r+")) {
				echo "The config file doesn't exist";
				break;
			}
			//Clearing the file
			ftruncate($cfgfile, 0);

			//Writing the new settings
			if(fwrite($cfgfile, json_encode($_POST))) {
				echo "ok";
				fclose($cfgfile);
			}
			break;
		case 'settoken':
			echo Utils::setToken($_POST["authcode"]);
			break;//send break
		case 'send':

			break;//send break
		case 'list':
			$result = Dir::listFiles();
			if(is_string($result))
				echo $result;
			else
				json_encode($result);
			break;//list break
		case 'deletefile':
			break;//deletefile break
		
		case 'downloadfile':

			break;//downloadfile break
		default:
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>GD PHP</title>
	<style>
		* {
			padding: 0;
			margin: 0;
			border: 0;
			outline: 0;
			font-family: Arial, sans-serif;
			font-size: 1em;
			line-height: 1.2em;
			-webkit-box-sizing: border-box;
			-moz-box-sizing: border-box;
			box-sizing: border-box;
			color:#111;
		}
		html {
			height:100%;
			width:100%;
			margin: 0;
			padding: 0;
		}
		body {
			background: #ccc;
		}
		h1 {
			font-size: 2.4em;
			line-height: 2.8em;
			text-align: center;
		}
		h2 {
			font-size: 2em;
			line-height: 2em;
			text-align: center;
			color: #777;
		}
		h3 {
			font-size: 1.6em;
			line-height: 1.6em;
			color: #777;
		}
		ol li {
			margin-left: 20px;
		}
		p {
			margin-bottom: 15px;
		}
		a#goto-settings {
			display: block;
			margin: -40px -10px 0 0;
			width: 40px;
			height: 40px;
			background: url(img/gear.png) no-repeat;
			background-size: 100%;
			float:right;
			cursor: pointer;
		}
		.clear {
			clear:both;
		}
		.wrap {
			width: 80%;
			max-width: 1500px;
			height:auto;
			margin: 10px auto;
			background: #f1f1f1;
			padding: 50px 20px 100px;
			box-shadow: 0px 0px 15px 0px rgba(0,0,0,1);
			-webkit-box-shadow: 0px 0px 15px 0px rgba(0,0,0,1);
			-moz-box-shadow: 0px 0px 15px 0px rgba(0,0,0,1);
			-o-box-shadow: 0px 0px 15px 0px rgba(0,0,0,1);

			border-radius: 5px;
			-webkit-border-radius: 5px;
			-moz-border-radius: 5px;
			-o  -border-radius: 5px;
		}
		#message {
			width: 300px;
			height:auto;
			position:absolute;
			top: 25%;
			left:50%;
			margin-left:-150px;
			background: #f1f1f1;
			padding: 20px;
			box-shadow: 0px 0px 15px 0px rgba(0,0,0,1);
			-webkit-box-shadow: 0px 0px 15px 0px rgba(0,0,0,1);
			-moz-box-shadow: 0px 0px 15px 0px rgba(0,0,0,1);
			-o-box-shadow: 0px 0px 15px 0px rgba(0,0,0,1);

			border-radius: 5px;
			-webkit-border-radius: 5px;
			-moz-border-radius: 5px;
			-o  -border-radius: 5px;
		}
		#msgtitle,#msgcontent {
			text-align: center;
			margin-bottom: 15px;
		}
		#msgtitle {
			font-weight: bold;
		}
		label {
			display: inline-block;
			width: 100px;
		}
		input {
			width: 65%;
			height: auto;
			padding: 5px 10px;
			border: 1px solid #111;
		}
		.btnok, .btncancel {
			display: block;
			float: right;
			width: 150px;
			height: auto;
			margin: 5px;
			padding: 10px;
			cursor: pointer;
		}
		.btncancel {
			background-color: #FFB6B6;
			border: 1px solid red;
		}
		.btnok {
			background-color: #B6E2B6;
			border: 1px solid green;
		}
		#message .btnok, #message .btncancel {
			padding: 10px;
			width: 70px;
		}
		#message #btn {
			width: 160px;
			margin: 0 auto;
		}
		#mask {
			display:none;
			position:absolute;
			background: rgba(0,0,0,0.5);
			top:0;
			right:0;
			bottom:0;
			left:0;
		}
		#actions {
			width: 350px;
			margin: 0 auto;
		}
		#actions li {
			display: inline-block;
			width: 100px;
			text-align: center;
			height: auto;
			margin: 5px;
			padding: 10px;
			cursor: pointer;
			background-color: #CCC;
			border: 1px solid black;
		}
		#message, #msgok, #msgcancel, #settings,
		#vwupload, #vwdownload, #vwdelete, #tutorial, #vwtoken {
			display:none;
		}
	</style>
</head>
<body>
	<!-- HTML for the message window -->
	<div id="mask"></div>
	<div id="message">
		<div id="msgtitle">Error:</div>
		<div id="msgcontent">The server is out.</div>
		<div id="btn">
			<button id="msgok" class="btnok">OK</button>
			<button id="msgcancel" class="btncancel">Cancel</button>
			<div class="clear"></div>
		</div>
	</div>

	<!-- HTML for the main view -->
	<div id="main" class="wrap">
		<a id="goto-settings"></a>
		<div class="clear"></div>
		<h1>GD PHP</h1>
		<p>GD PHP (Google Drive PHP) is a draft of a project that uses <a href="https://developers.google.com/drive/v3/web/quickstart/php" target="_blank">Drive Rest API</a> to manage files in your Google Drive Account.</p>
		<p><a href="" id="gettingstarted">Getting Started? Click here.</a></p>
		<div id="tutorial">
			<h3>Step 1:</h3>
			<ol>
				<li>
					Use <a href="https://console.developers.google.com/start/api?id=drive" target="_blank">Google Developers Console's wizard</a> to create or select a project in the Google Developers Console and automatically turn on the API. Click <strong>Continue</strong>, then <strong>Go to credentials</strong>.
				</li>
				<li>
					At the top of the page, select the <strong>OAuth consent screen</strong> tab. Select an <strong>Email address</strong>, enter a <strong>Product name</strong> if not already set, and click the <strong>Save</strong> button. 
				</li>
				<li>
					Select the <strong>Credentials</strong> tab, click the <strong>Add credentials</strong> button and select <strong>OAuth 2.0 client ID</strong> .
				</li>
				<li>
					Select the application type <strong>Other</strong>, enter the name you want (Like "GD PHP"), and click the <strong>Create</strong> button.
				</li>
				<li>
					Click in the Gear at the top of this page and add the info generated there into the settings page.
				</li>
			</ol>
		</div>

		<h2>Choose the action</h2>
		<ul id="actions">
			<li id="slupload">Upload</li>
			<li id="sldownload">Download</li>
			<li id="sldelete">Delete</li>
		</ul>
		<div id="vwupload" class="view">
			<form method="post" action="index.php?p=upload" id="formupload">
				<p>
					<label for="file">Choose File:</label>
					<input id="file" name="file" type="file">
				</p>
				</p>
					<input  id="btnupload"  class="btnok" name="send" type="button" value="Send">
					<div class="clear"></div>
				</p>
			</form>
		</div>
		<div id="vwdownload" class="view">
			<ul id="folderfiles"></ul>
		</div>
		<div id="vwdelete" class="view">
			<ul id="folderfiles"></ul>
		</div>
		<div id="vwtoken" class="view">
			<?php if(substr(Utils::getTokenUrl(),0,4) != "http"): ?>
				<p><?php echo Utils::getTokenUrl() ?></p>
			<?php else: ?>
			<a href="<?php echo Utils::getTokenUrl() ?>" target="_blank">Click here to Get Token</a>
			<form method="post" action="index.php?p=settoken" id="formtoken">
				<p>
					<label for="authcode">Auth code:</label>
					<input id="authcode" name="authcode" type="text">
				</p>
				</p>
					<input  id="btnsettoken"  class="btnok" name="send" type="button" value="Send">
					<div class="clear"></div>
				</p>
			</form>
			<?php endif; ?>
		</div>
	</div>

	<!-- HTML for the settings view -->
	<div id="settings" class="wrap">
		<h2>Settings</h2>
		<form method="post" action="index.php?p=settings" id="formsettings">
			<p>
				<label for="email">Email:</label>
				<input id="email" name="email" type="email">
			</p>
			<p>
				<label for="client_id">Client ID:</label>
				<input id="client_id" name="client_id" type="text">
			</p>
			<p>
				<label for="client_secret">Client Secret:</label>
				<input id="client_secret" name="client_secret" type="text">
			</p>
			<p>
				<label for="folder">Folder ID:</label>
				<input id="folder" name="folder" type="text">
			</p>
			</p>
				<input  id="save"  class="btnok" name="save" type="button" value="Save">
				<input  id="cancel" class="btncancel" name="cancel" type="button" value="Cancel">
				<div class="clear"></div>
			</p>
		</form>
	</div>
</body>
<script>
	//The Getting Started's click function
	document.getElementById("gettingstarted").addEventListener("click", function(e){
		e.preventDefault();
		document.getElementById("tutorial").style.display = "block";
		this.style.display = "none";
	});
	//The Upload's click function
	document.getElementById("slupload").addEventListener("click", function(e){
		e.preventDefault();
		showVw("vwupload");
	});
	//The Download's click function
	document.getElementById("sldownload").addEventListener("click", function(e){
		e.preventDefault();
		xhr("get","index.php?p=list",null,function(result){
			console.log(result);
		});
		showVw("vwdownload");
	});
	//The Delete's click function
	document.getElementById("sldelete").addEventListener("click", function(e){
		e.preventDefault();
		showVw("vwdelete");
	});


	//The Gear's click function
	document.getElementById("goto-settings").addEventListener("click", function(e){
		e.preventDefault();
		xhr("get","config.json",null,function(result){
			result = JSON.parse(result);
			console.log(result);
			document.getElementById("email").value = result.email;
			document.getElementById("client_id").value = result.client_id;
			document.getElementById("client_secret").value = result.client_secret;
			document.getElementById("folder").value = result.folder;
		});
		document.getElementById("main").style.display = "none";
		document.getElementById("settings").style.display = "block";
	});
	//The Form's cancel click function
	document.getElementById("cancel").addEventListener("click", function(e){
		e.preventDefault();
		document.getElementById("main").style.display = "block";
		document.getElementById("settings").style.display = "none";
	});
	//The Gear's Save function
	document.getElementById("save").addEventListener("click", function(e){
		e.preventDefault();
		form = document.getElementById("formsettings");
		xhr(form.method, form.action, new FormData(form), function(result){
			if(result == "ok") {
				msg("OK","Settings successfully saved.");
				document.getElementById("cancel").click();
			} else {
				msg("Error",result+". Want to try again?",
					function(){return true},
					function(){document.getElementById("cancel").click();}
					);
			}
		});
	});
	//The SetToken's Send function
	if(document.getElementById("btnsettoken")) {
		document.getElementById("btnsettoken").addEventListener("click", function(e){
			e.preventDefault();
			form = document.getElementById("formtoken");
			xhr(form.method, form.action, new FormData(form), function(result){
				if(result == "ok") {
					msg("OK","Token saved.");
				} else {
					msg("Error",result);
				}
			});
		});
	}
	function showVw(view) {
		document.getElementById("vwupload").style.display = "none";
		document.getElementById("vwdownload").style.display = "none";
		document.getElementById("vwdelete").style.display = "none";
		document.getElementById("vwtoken").style.display = "none";

		document.getElementById(view).style.display = "block";
	}

	/**
	 * This function manages the communication with the server.
	 * @param  string	method		The form method
	 * @param  string	action		The form action
	 * @param  FormData	formData	The form inside a FormData object
	 * @param  Function	cb			Does the something with the server result
	 * @return 			void
	 */
	function xhr(method, action, formData, cb){
			try
			{
				var XHR = new XMLHttpRequest();
	        	XHR.upload.onprogress = function(evt)
				{
					var progress = parseInt((evt.loaded / evt.total)*100);
					if(progress < 100)
						msg("Sending","Sending File, ("+progress+"%)...");
					else
						msg("Processing","Please wait...");
				};

				XHR.open (method, action, true);
				XHR.send(formData);

				XHR.onreadystatechange = function()
				{
				 	if (this.readyState === 4)
				 	{
				    	if (this.status >= 200 && this.status < 400)
				    	{
					    	// Success
					    	if(cb)
					    		cb(this.responseText);
					    	else
					    		msg("Message", this.responseText);
					    }
					    else
					    {
					    	// Error
					    	msg("ERROR","The Server doesn't respond.");
				    	}
					}
				};

			}
			catch(e)
			{
				msg("Error","Your browser doesn't support XMLHttpRequest. Try use this in an up to date browser (Like Firefox or Chrome)");
			}
	}
	/**
	 * This method shows a message to the user
	 * @param  string	msg			the message to be shown
	 * @param  Function	cb			The function that reacts to the OK button.
	 * @param  Function	cancelcb	If you want a confirm dialog, this function is called when 
	 *                            	the user presses the Cancel button.
	 * @return			void
	 */
	function msg(title,msg,cb,cancelcb) {
		var mask		= document.getElementById("mask"),
			message		= document.getElementById("message"),
			msgtitle	= document.getElementById("msgtitle"),
			msgcontent	= document.getElementById("msgcontent"),
			btnok		= document.getElementById("msgok"),
			btncancel	= document.getElementById("msgcancel"),
			close		= function() {
				msgtitle.textContent	= "";
				msgcontent.textContent	= "";
				btnok.style.display		= "none";
				btncancel.style.display	= "none";
				message.style.display	= "none";
				mask.style.display		= "none";
			};

		msgtitle.textContent = title;
		msgcontent.textContent = msg;
		message.style.display = "block";
		mask.style.display = "block";

		btnok.style.display = "block";
		btnok.addEventListener("click",function(){
			if(cb)
				cb();

			close();
		});

		if(cancelcb) {
			btncancel.style.display = "block";
			btncancel.addEventListener("click",function(){
				cancelcb();
				close();
			});
		}
	}
</script>
</html>
<?php
	break;//default break;
	}