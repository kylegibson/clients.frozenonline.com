<div id="top">
  <div class="inner">
    <div class="logo">
      <img src="http://frozenonline.com/assets/images/logo.png" alt="frozenonline" />
    </div>
    <div class="rightside">
      <div class="menu">
        <ul>
<?foreach($menu as $item){
  list($entry, $url) = explode(":", $item); 
  echo "<li><a href=\"$url\">$item</a></li>";
}?>
        </ul>
      </div>
    </div>
  </div>
</div>
