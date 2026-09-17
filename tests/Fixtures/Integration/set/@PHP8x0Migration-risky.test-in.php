<?php
declare(strict_types=1);

$pieces = [1, 2];
$f = implode($pieces, '');

echo mktime();
$exif = read_exif_data('tests/test1.jpg', 'IFD0');
