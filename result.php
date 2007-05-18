<?php
header("Content-Type: application/xhtml+xml");
$type = isset($_POST['image_type']) ? trim($_POST['image_type']) : 'svg';
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
?><!DOCTYPE html 
      PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
      "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:xlink="http://www.w3.org/1999/xlink">
<head>
  <title><?= $_POST['title'] ?></title>
</head>
<body>
<h1><?= $_POST['title'] ?></h1>
<h3>Octave code</h3>
<code style="font-size: 10pt;"><?php
$codelines = explode("\n", $_POST['code']);
$withgraph = false;
foreach ($codelines as $line) {
  $count = 0;
  $withgraph |= strpos($line, 'plot') !== false;
  $withgraph |= strpos($line, 'stem') !== false;
  echo str_replace("#", "<font color=\"green\">#", $line, $count);
  for ($i = 0; $i < $count; $i++) echo "</font>";
  echo "<br/>";
} 

$c = '';
if ($withgraph) {
    if ($type == 'png') {
        $c = "gset term png;\n";
        $c .= "gset output \"tempimg/".getmypid().".png\";\n";
    } else {
        $c = "gset term svg;\n";
    }
}
$c .= $_POST['code'];
file_put_contents('tempcode/'.getmypid().'.oct', $c);


?></code>
<h3>Output - <a href="#" onclick="document.getElementById('outputcode').style.display='none';this.innerHTML='Hidden';">Hide</a></h3><pre style="font-size: 8pt;" id="outputcode"><?php
$s = 'octave -q tempcode/'.getmypid().'.oct';
//echo $s;
$result = array();
exec($s, $result);
foreach ($result as $line) {
    if ($withgraph && $type == 'svg') {
        if (strpos($line, '<?xml') !== false) {
            echo "</pre><h3>Graph output</h3>\n";
            continue;
        }
        if (strpos($line, 'DOCTYPE') !== false) continue;
        if (strpos($line, '<svg ') !== false) {
            echo $line . " xmlns=\"http://www.w3.org/2000/svg\" ";
            continue;
        }
    }
    echo $line;
    echo "\n";
}
if ($withgraph && $type == 'png') : ?>
</pre><h3>Graph output</h3>
<img src="tempimg/<?= getmypid(); ?>.png" width="640" height="480"/>
<?php endif; ?>
</body>
</html>