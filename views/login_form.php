<?
if(!isset($page)) {
  header("Location: /");
  exit;
}
?>
<noscript>
<p>Javascript is required in the Client Area</p>
</noscript>
<div class="login">
  <h2>Login</h2>
  <form id="login" action="/" method="post">
  <div>
    <label for="system">System Name</label> 
    <input type="text" id="system" name="system" class="system"/>
  </div>
  <div>
    <label for="passwd">Password</label> 
    <input type="password" id="passwd" name="passwd" class="passwd"/>
  </div>
  <div>
    <label class="result"><span>&nbsp;</span></label>
    <input type="submit" value="Login" class='submit'/>
  </div>
  </form>
</div> <!-- login -->
