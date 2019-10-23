<?php


$datatime1 = new DateTime('2019/10/20 14:30:00');
$datatime2 = new DateTime(date('Y/m/d H:i:s'));

$data1  = $datatime1->format('Y-m-d H:i:s');
$data2  = $datatime2->format('Y-m-d H:i:s');

$diff = $datatime1->diff($datatime2);
$horas = $diff->h + ($diff->days * 24);


echo "A diferença de horas entre {$data1} e {$data2} é {$horas} horas";