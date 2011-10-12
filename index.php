<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<meta http-equiv="pragma" content="no-cache" />
<meta http-equiv="cache-control" content="no-cache" />
<title>FrozenOnline - Client Area</title>
<link href="/assets/css/style.css" rel="stylesheet" type="text/css" />
<script src="/assets/js/jquery-1.6.2.min.js" type="text/javascript"> </script>
</head>
<body>

<div id="top">
<div class="inner">
<div class="logo">
<img src="http://frozenonline.com/assets/images/logo.png" alt="frozenonline" />
</div>
<div class="rightside">
<div class="menu">
<ul>
<li><a href="/">Home</a></li>
</ul>
</div>
</div>
</div>
</div>

<div id="middle">
  <div class="inner">
    <noscript>Javascript is required in the Client Area</noscript>
    <div class="login">
      <h2>Login</h2>
      <form id="login" action="/login.php">
      <div>
        <label for="system">System Name</label> 
        <input type="text" id="system" name="system" class="system"/>
      </div>
      <div>
        <label for="pwd">Password</label> 
        <input type="password" id="pwd" name="pwd" class="pwd"/>
      </div>
      <div>
        <label class="result"><span>&nbsp;</span></label>
        <input type="submit" value="Login" class='submit'/>
      </div>
      </form>
    </div> <!-- login -->
  </div> <!-- inner -->
</div> <!-- middle -->

</body>
</html>
