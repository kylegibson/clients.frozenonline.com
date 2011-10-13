<?
if(!isset($page)) {
  header("Location: /");
  exit;
}
?>
<noscript>
<p>Javascript is required in the Client Area</p>
</noscript>
<div class="box login">
  <div class="inner">
    <h2>Login</h2>
    <? if(isset($error)) { echo "<p class='error'>$error</p>"; } ?>
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
  </div>
</div> <!-- login -->
