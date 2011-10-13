<div id="top">
  <div id="top_container">
    <div class="logo">
      <img src="http://frozenonline.com/assets/images/logo.png" alt="frozenonline" />
    </div>
    <div class="rightside">
      <div class="menu">
        <ul>
<?foreach($menu as $item){
  list($entry, $url) = explode(":", $item); 
  echo "<li><a href=\"$url\">$entry</a></li>";
}?>
        </ul>
      </div>
    </div>
  </div>
</div>
