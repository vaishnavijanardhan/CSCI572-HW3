<?php
include("MappingURLs.php");

header('Content-Type: text/html; charset=utf-8');

$limit = 10;
$query = isset($_REQUEST['q']) ? $_REQUEST['q'] : false;
$results = false;

if ($query)
{

require_once('./solr-php-client/Apache/Solr/Service.php');

$solr = new Apache_Solr_Service('localhost', 8983, '/solr/myexample/');

if (get_magic_quotes_gpc() == 1)
{
$query = stripslashes($query);
echo $query;
}

$othervars = array('fl' => 'id, title, author, stream_size','wt' => 'json');
if (isset($_GET['algorithm'])) {
  if($_GET['algorithm'] == "default") {
    $othervars = array('fl' => 'id, title, author, stream_size','wt' => 'json');
  }
else {
    $othervars = array('fl' => 'id, title, author, stream_size','sort' => 'pageRankFile desc', 'wt' => 'json');	
}
}
try
{
  $results = $solr->search($query, 0, $limit, $othervars);
}
catch (Exception $e)
{
die("<html><head><title>SEARCH EXCEPTION</title><body><pre>{$e->__toString()}</pre></body></html>");
}
}

?>
<html>
<head>
</head>
<body>
<form  accept-charset="utf-8" method="get">
  <label for="q">Search:</label>
  <input id="q" name="q" type="text" value="<?php echo htmlspecialchars($query, ENT_QUOTES, 'utf-8'); ?>"/>
<br>	
Search Algorithm to be used:
<br>

<input type="radio" name="algorithm" value="default" checked="checked"> Lucene(Solr Default)
<input type="radio" name="algorithm" value="pagerank"> PageRank
<input type="submit"/>
</form>
<?php


if ($results)
{
$total = (int) $results->response->numFound;
$begin = min(1, $total);
$end = min($limit, $total);
?>
<div>Results <?php echo $begin; ?> - <?php echo $end;?> of <?php echo $total; ?>:</div>
<ol>

<?php
foreach ($results->response->docs as $docu) {
  foreach ($docu as $col => $value) {
      $id_e = false;
      $author_e = false;
      $date_e = false;
      $title_e = false;
      $stream_e = false;

}

}
?>

<?php

foreach ($results->response->docs as $docu)
{
?>
<?php

foreach ($docu as $col => $value)
{
    if (!strcmp($col,"id")) {
        $id_e = true;
    }
    else $id_e = false;

    if (!strcmp($col,"title")) {
        $title_e = true;
    }
    else $title_e = false;

    if (!strcmp($col,"date")) {
        $date_e = true;
    }
    else $date_e = false;

    if (!strcmp($col,"author")) {
        $author_exists = true;
    }
    else $author_e = false;

    if (!strcmp($col,"stream_size")) {
        $stream_e = true;
    }
    else $stream_e = false;

}
?>
<?php
$au = 0;
$siz = 0;
$date = 0;
?>
<?php foreach ($docu as $col => $value) { ?>
        <!--<th><?php {echo htmlspecialchars($field, ENT_NOQUOTES, 'utf-8'); }?></th> -->
<?php if (!strcmp($col,"id")) { ?>
<a href="<?php echo $dict[($value)] ?>"> Click to go to the Link </a> <br><?php } ?>

<?php if (!strcmp($col,"title")) {?>
 Title: <?php echo $value . "<br>"; ?> 
<?php }?>

<?php if ($stream_e && !strcmp($col,"stream_size") && $siz == 0) {?>
 Size: <?php echo $value/1000; echo "KB <br>";?> 
<?php } else if (!($stream_e) && $siz == 0){

$stream_e = true;}?>
<?php if ($author_e && !strcmp($col,"author") && $au ==0) {?>
 Author: <?php echo $value . "br>"; $au = 1; ?> 
<?php } else if(!($author_e) & $au == 0) {
    echo "Author:N/A <br>";
    $au = 1;
 $author_e = true;}?>
<?php if ($date_e && !strcmp($col,"date") && $date == 0) {?>
 Date: <?php echo $value . "br>"; $date = 1?> 
<?php } else if(!($date_e) && $date == 0){echo "Date:N/A <br>"; $date_e = true; $date = 1;}?>
<?php
}
?>
<?php
echo "<br>";
}
?>
</tr>
</ol>
<?php
}
?>
</body>
</html>
