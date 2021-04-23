These regex files were used as an aid to convert v3 qcubed source code files to v4. They try to identify common patterns
for functions and classes and then convert them to namespaced versions. For constants it can identify, it will
try to convert them to UPPER_CASE versions, including their declaration and usages. Its not perfect, and doesn't quite
catch everything, but gets the vast majority, making the move from v3 to v4 much easier.

The tools are breifly described below. See the file itself for more explanation:

gen_was.php - Reads php files looking for the @was annotation to know what a class's previous name was, and then outputs
regex.php files that contain patterns to use in a regex substitution. The run_was.php file uses these files.

run_was.php - Runs the regex patterns in gen_was output to create a v4 file from a v3 file.

regen.sh - Runs gen_was against all the known directories in the entire framework, then combines the files into one all.regex.php file

combineRegex.php - Used by regen.sh to combine regex files into one master file.

manual.regex.php -  A file that contains manually created regex files that can be used in run_was. For those outlier
situations that we could not find an automated way to create.


