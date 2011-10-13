<? 
session_start();
require_once($_SERVER["DOCUMENT_ROOT"]."/backend.php"); 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<meta http-equiv="pragma" content="no-cache" />
<meta http-equiv="cache-control" content="no-cache" />
<title>FrozenOnline - Client Area - <?=$title?></title>
<link href="/assets/css/style.css" rel="stylesheet" type="text/css" />
<script src="/assets/js/jquery-1.6.2.min.js" type="text/javascript"> </script>
</head>
<body>

<?include_once($_SERVER["DOCUMENT_ROOT"]."/".$header);?>

<div id="middle">
<div class="inner">
<?include_once($_SERVER["DOCUMENT_ROOT"]."/".$page);?>
</div>
</div>

</body>
</html>
