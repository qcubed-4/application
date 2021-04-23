#!/usr/bin/env bash
cd ../../common/tools
./gen_was.php ../src > common.regex.php

cd ../../orm/tools
./gen_was.php ../src > orm.regex.php

cd ../../application/tools
./gen_was.php ../src > app.regex.php
./gen_was.php ../install > project.regex.php
./gen_was.php ../codegen/generator > generator.regex.php

./combineRegex.php > all.regex.php