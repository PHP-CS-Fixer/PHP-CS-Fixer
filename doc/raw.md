Usage of the raw format
---

##### example 1:
```
echo -n "<?php \$test = array();" | php ./php-cs-fixer fix --rules='{"array_syntax": {"syntax": "short"}}' --format raw -
# <?php $test = [];
```

The result will be printed to stdout instead of write to file.

##### example 2:
```
echo -n "<?php \$test = [];" | php ./php-cs-fixer fix --rules='{"array_syntax": {"syntax": "short"}}' --format raw -
# <?php $test = [];
```

The source content will be printed if there are no changes.

### example 3:
```
echo -n "<?php \$test = array(;" | php ./php-cs-fixer fix --rules='{"array_syntax": {"syntax": "short"}}' --format raw -
# Files that were not fixed due to errors reported during linting before fixing:
#   1) stdin.php
echo $?
# 4
```

When errors has occurred, the command will fail with non-zero error code.

### example 4:
```
php ./php-cs-fixer fix --format raw .
# In ConfigurationResolver.php line 292:
#
#  "format"="raw" cannot be used with regular files, it can be used only combi
#  ning with stdin.
```

The command will fail with non-zero error code if it is not launched with combining stdin.
