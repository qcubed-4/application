@ -2,20 +2,10 @@
This is the application part of the QCubed-4 framework, and includes forms, controls, actions, events and code to tie them all together.

## What is QCubed-4?
QCubed (pronounced 'Q' - cubed) is a PHP Model-View-Controller Rapid Application Development framework with support for PHP8 and above. The goal of the framework is to save development time around mundane, repetitive tasks - allowing you to concentrate on things that are useful AND fun. QCubed-4 excels in situations where you have a large database structure that you quickly want to make available to users.

## Additional Documentation and Knowledge Base

In addition to the official QCubed-4 documentation and examples, you may find the **DeepWiki knowledge base** useful for gaining a broader and more contextual overview of the QCubed-4 ecosystem.

DeepWiki aggregates community-driven knowledge, explanations, and references collected from open-source discussions, repositories, and real-world usage. It can help both new and experienced developers better understand QCubed’s architecture, design philosophy, and ongoing evolution as an open-source framework.

As with any open-source effort, community contributions and shared experiences help keep this knowledge relevant and growing.

https://deepwiki.com/qcubed-4
QCubed (pronounced 'Q' - cubed) is a PHP Model-View-Controller Rapid Application Development framework with support for PHP8 and above. The goal of the framework is to save development time around mundane, repetitive tasks - allowing you to concentrate on things that are useful AND fun. QCubed excels in situations where you have a large database structure that you quickly want to make available to users.

## Stateful architecture
With QCubed-4, you don't have to deal with POSTs and GETs coming from the browser. QCubed-4 automatically handles that for you and packages the information into object oriented forms and controls. Programming with QCubed-4 feels very much like programming a desktop application. If you are familiar with ASP, it is similar.
With QCubed-4, you don't have to deal with POSTs and GETs coming from the browser. QCubed automatically handles that for you and packages the information into object oriented forms and controls. Programming with QCubed feels very much like programming a desktop application. If you are familiar with ASP, it is similar.

## The Code Generator
The Code Generator automatically creates object classes with matching forms and controls based on your database schema. It uses the concept of ORM, [object-relational mapping](http://en.wikipedia.org/wiki/Object-relational_mapping), to practically create your whole model layer for you.
@ -98,5 +88,3 @@ Also, the JQuery UI framework has been put in its own directory to prepare for m

## Credits
QCubed was branched out of QCodo, a project by Michael Ho. QCubed-4v was branched out of QCubed project, which @spekary (Shannon Pekary) has been instrumental in redesigning.

[![Ask DeepWiki](https://deepwiki.com/badge.svg)](https://deepwiki.com/qcubed-4/application)