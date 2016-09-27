<?php
file_put_contents(
    __DIR__.'/test2.php',
    sprintf("<?php\r\n\t//tab\n     // 5 spaces\n<<<TEST\n\t\r\n\r\nTEST;\n // heredoc test\n\$a = 1;")
);