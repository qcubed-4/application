# Coding Standards
QCubed generally follows PSR-1, PSR-2 and PSR-4 coding standards, with the following additions.

## File and Class Names
PSR-4 requires that files that contain a class end in ".php", with the base name
of the file corresponding to the name of the class inside that file.

Class names (and therefore file names), follow these additional guidelines:
* A class that is purposed to be a base class for other classes will end in "Base". Many of these classes are declared abstract, but they do not have to be
* If a class is a base class for the type of object that corresponds to its namespace, it will be named "ObjecttypeBase". For example, the base class for a node is named NodeBase, and is in the Node namespace. This is to avoid a proliferation of classes just called "Base".
* A class that just encapsulates an enumerated type will end in "Type".
* An interface will end in "Interface".
* A trait will end in "Trait".
* Class names that conflict with PHP reserved words or built-in classes will have a "Q" on the front of the name. For example QString and QDateTime. 

As much as possible, classes are chosen so that there names are unique across
the entire framework. This makes it easier to import them into other namespaces. This is not always practical, and there are some collissions,
but the use of the longer namespaced name can help it be explicit.

## Variable names
We like having variable names start with a small prefix indicating its
basic type. Arrays should start with an indicator of the type of each
item in the array, and end in a plural form, or the word "Array".

For example $strNames would be an array of strings containing names.