## Application Framework
This is the application part of the QCubed-4 framework, and includes forms, controls, actions, events and code to tie them all together.

## What is QCubed-4?
QCubed (pronounced 'Q' - cubed) is a PHP Model-View-Controller Rapid Application Development framework with support for PHP8 and above. The goal of the framework is to save development time around mundane, repetitive tasks - allowing you to concentrate on things that are useful AND fun. QCubed excels in situations where you have a large database structure that you quickly want to make available to users.

## Additional Documentation and Knowledge Base

In addition to the official QCubed documentation and examples, you may find the **DeepWiki knowledge base** useful for gaining a broader and more contextual overview of the QCubed-4 ecosystem.

DeepWiki aggregates community-driven knowledge, explanations, and references collected from open-source discussions, repositories, and real-world usage. It can help both new and experienced developers better understand QCubed’s architecture, design philosophy, and ongoing evolution as an open-source framework.

As with any open-source effort, community contributions and shared experiences help keep this knowledge relevant and growing.

https://deepwiki.com/qcubed-4

## Stateful architecture
With QCubed-4, you don't have to deal with POSTs and GETs coming from the browser. QCubed automatically handles that for you and packages the information into object oriented forms and controls. Programming with QCubed feels very much like programming a desktop application. If you are familiar with ASP, it is similar.

## The Code Generator
The Code Generator automatically creates object classes with matching forms and controls based on your database schema. It uses the concept of ORM, [object-relational mapping](http://en.wikipedia.org/wiki/Object-relational_mapping), to practically create your whole model layer for you.

Codegen can take advantage of foreign key relationships and field constraints to generate ready-to-use data models complete with validation routines and powerful CRUD methods, allowing you to manipulate objects instead of constantly issuing SQL queries.

More info as well as examples are available online at <https://qcubed.eu/>

### Object-oriented querying

Using QQueries allows for simple yet powerful loading of models, all generated ORM classes have Query methods and QQNodes. By using these methods, getting a complex subset of data is pretty straightforward - and can be used on almost any relational database.

## User Interface Library

QCubed-4 uses the concept of a Qform to keep form state between POST transactions. A QForm serves as the controller and can contain Controls which are UI components.

All Controls (including Qform itself) can use a template which is the view layer, completing the MVC structure.

Controls can take advantage of the Qform's FormState to update themselves through Ajax callbacks as easily as synchronous server POSTs. All jQuery UI core widgets are available as Controls.

Some Controls include:
- Dialog
- TextBox
- ListBox
- Tabs
- Accordion

The easiest way to learn QCubed-4 is to see the examples tutorial at https://qcubed.eu/

### Plugins
Through its plugin system, QCubed-4 makes it easy to package and deliver enhancements and additions to the core codebase. Some plugins are created by other users, try creating them according to these examples.

## System Requirements
* A development computer that you can set up so that the browser can write to a directory in your file system.
* v4.0.x, requires PHP 8.3 and above. HHVM are supported as well.
* All html code is html5 compliant.
* QCubed-4 relies on jQuery for some of its ajax interactions. Also, many of the built-in controls beyond basic html controls require JQuery UI.
* A SQL database engine. MySQL, SqlServer, Postgres, Oracle, PDO, SqlLite, Informix adapters are included. Creating another adapter is not hard if you have a different SQL.

## Installation
The installation procedure is described in detail here: [Installation instructions](https://github.com/qcubed-4/application/blob/master/INSTALL.md "Installation instructions").


## Upgrade Notes
This version now uses namespacing. See the tools directory for tools to help you convert your current code base to the new names. Specifically, run the following command line script on your codebase, and it will convert about 99% of your code:

```php
cd (vendor_dir)/qcubed/application/tools/v4_converter
./run_was.php -R all.regex.php (your source dir)
```
The application framework moving forward will focus on supporting html5 tags in its control library only. There may be some other items in there to provide a way to support common data relationships (like radio and checkbox lists), but for the most part, we would like anything that isn't directly drawing a tag to be in a separate library.

As such, the following files are no longer supported in the core, and are currently dead code. You will find them in the "dead" directory. However, if these old files are important to you, feel free to resurrect them as a plugin. Much of the code is no longer applicable, as better ways to solve the problems have been developed either built-in to PHP or in libraries available in github.

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

Also, the JQuery UI framework has been put in its own directory to prepare for moving it to a separate library in a later version.

## Credits
QCubed was branched out of QCodo, a project by Michael Ho. QCubed-4v was branched out of QCubed project, which @spekary (Shannon Pekary) has been instrumental in redesigning.
