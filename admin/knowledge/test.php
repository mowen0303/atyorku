<?php
var_dump(1);
$image = imagecreatefromjpeg('img.jpg');
list($w,$h) = getimagesize("img.jpg");
$d = imagecreatetruecolor($w,$h);
var_dump($w);
var_dump($h);
imagecopyresampled($d,$image,0,0,0,0,$w,$h,$w,$h);
$g = array(
    array(1,2,1),
    array(2,4,2),
    array(1,2,1)
);
for ($i=0;$i<10;$i++)
    imageconvolution($d,$g,16,0);
imagepng($d,"output.jpg");
//http://www.jb51.net/article/131161.htm