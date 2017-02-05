#!/bin/bash

echo "RESET MSV"

rm -R src/include/custom/smarty/cache/*.tpl.php
mv src/include/module/-install/ src/include/module/install/

find . -name '*.DS_Store' -type f -delete

cp config-sample.php src/config.php