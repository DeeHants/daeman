<?php
function print_header($title) {
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
 <head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title><?php print $title; ?></title>
  <style type="text/css">
   body { font-family: arial; }
   h1 { font-size: x-large; }
   h2 { font-size: large; }
   h3 { font-size: medium; }
   th { text-align: left; }
   li { margin-left: -15px; }
   .footer { font-size: x-small; }
   .action { font-size: x-small; }
   .help { font-size: x-small; }
   .error { color: red; }
   .status {}
  </style>
 </head>
 <body>
  <h1><?php print $title; ?></h1>
<?php
}

function print_footer() {
?>
  <p class=footer><a href="http://sourceforge.net/projects/daeman/" target="_blank">Daeman 0.13</a> written by <a href="http://www.earlsoft.co.uk/" target="_blank">Earl Software</a> 2005</p>
 </body>
</html>
<?php
}
?>
