<?php 
	ini_set('display_errors',1);//arata erorile
 	 	
 	include_once 'Automatizare/clase/login.php';//include o clasa
 	
 	$login  =  new Login();	// am creat un obiect de tip login din Automatizare/clase/login.php
 	
 	if($login->CheckLogin()){// daca metoda chekLogin returneaza TRUE se duce la metoda Redirect
		$login->RedirectToURL("Automatizare/page.php");
	
	}
	
 	if(isset($_POST['commit'])){// daca am apast butonul de trimitere 
 	
 		if($login->Log_in()){// daca corespunde user si parola
 			$login->RedirectToURL("Automatizare/page.php");// trimitere la Automatizare/page.php
 		}
 	} 	

?>
<!DOCTYPE html>
<head>
  <meta charset="utf-8"> <!-- indicatii pentru browser-->
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"> <!--limbajul pagini -->
  <title>LogIn Form</title>
  <link rel="stylesheet" href="Automatizare/css/style_login.css">
</head>
<body>
	  <div class="container"> <!-- eticheta pentru container  .... se foloseste pentru css java script etc-->
	    <div class="login"> 
	      <h1>LogIn to Home Automation</h1> <!--un tag se poate modifica in CSS;  heder H1(mare) pana la H6(cel mai mic) reprezinta dimensiunea caracterelor-->
	      <form method="post" action="index.php"><!-- bucata de cod unde introduc date-->
	        <p><input type="text" name="username" value="" placeholder="Username"></p> <!--imput type de tip text, name si ce sa apara in casuta pana introduc parola -->
	        <p><input type="password" name="password" value="" placeholder="Password"></p>
	      
	      
	        <p class="remember_me">
	          <label>
	            <input type="checkbox" name="remember_me" id="remember_me"><!-- checkbox e de tip bollean-->
	            Remember me on this computer
	          </label>
	        </p> 
	        
	        
	        <p class="submit">i<input type="submit" name="commit" ></p>
	      </form>
	    </div>
	  </div><!-- aici se termina eticheta container-->
</body>
</html>
