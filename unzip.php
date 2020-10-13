<?php
echo("descomprimiendo");
$zip = new ZipArchive;
$res = $zip->open('ts6.zip');
if ($res === TRUE) {
  $zip->extractTo('../ts6_comercial');
  $zip->close();
  echo 'woot!';
} else {
  echo 'doh!';
}
echo(" <br> descomprimido");