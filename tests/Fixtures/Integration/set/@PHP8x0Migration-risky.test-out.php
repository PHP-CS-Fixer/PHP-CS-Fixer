<?php
declare(strict_types=1);

$pieces = [1, 2];
$f = implode('', $pieces);

echo time();
$exif = exif_read_data('tests/test1.jpg', 'IFD0');
