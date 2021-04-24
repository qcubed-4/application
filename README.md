# Application Framework
This is the application part of the QCubed-4 framework, and includes forms, controls,
actions, events and code to tie them all together.

## Upgrade Notes
This version now uses namespacing. See the tools directory for tools to help you
convert your current code base to the new names. Specifically, run the following command line script
on your codebase, and it will convert about 99% of your code:

```php
cd (vendor_dir)/qcubed/application/tools/v4_converter
./run_was.php -R all.regex.php (your source dir)
```


The application framework moving forward will focus on supporting html5 tags in its 
control library only. There may be some other items in there to provide a way to support
common data relationships (like radio and checkbox lists), but for the most part, we
would like anything that isn't directly drawing a tag to be in a separate library.

As such, the following files are no longer supported in the core, and are currently dead
code. You will find them in the "dead" directory. 
However, if these old files are important to you, feel free to resurrect them
as a plugin. Much of the code is no longer applicable, as better ways to solve the problems
have been developed either built-in to PHP or in libraries available in github.

* QDialogBox.class.php (We currently use the JQuery UI dialog, but this may change)
* FileAssetDialog.php
* QArchive.class.php
* QEmailServer.class.php
* QFileAsset.class.php
* QFileAssetBase.class.php
* QImageBase.class.php
* QImageBrowser.class.php
* QImageControl.class.php
* QImageControlBase.class.php
* QImageFileAsset.class.php
* QImageLabel.class.php
* QImageLabelBase.class.php
* QImageRollover.class.php
* QLexer.class.php
* QMimeType.class.php
* QRegex.class.php
* QRssFeed.class.php
* QSoapService.class.php
* QStack.class.php
* QTreeNav.class.php
* QTreeNavItem.class.php
* QWriteBox.class.php

Also, the JQuery UI framework has been put in its own directory to prepare
for moving it to a separate library in a later version.

## Install
See the dev-master branch of the qcubed-4/app-starter repository for information on how to install v4 of QCubed quickly using Composer.
