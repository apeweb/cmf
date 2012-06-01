The Framework
=============

The framework is designed to be flexible, extendable and easy to use. Therefore
a few contraints are in effect to prevent the framework from becoming a
playground full of hacky code and are explained in the following sections of
readme document.


Coding Standards
================

As an outline, the following standards apply:
* Do not write functions, use methods in classes only
* Each method should have one purpose only, abstraction is the key to a KISS
  framework
* Comment on code, but don't document it, you should document your code
  elsewhere
* Use mutators and accessors as opposed to public properties
* If you can set something, for security you should also be able to get it too
* Don't use __get or __set, instead write methods such as getFooName,
  setFooName
* Use __construct to create something new, like a new menu or a new user,
  passing an array as the only argument with all required settings

So for a new user use:
  $user = new User(
    'username' => 'foo'
  );
The reason for this is that you are creating a new object, hence something new

To get an existing user use:
  $user = User::getUser('foo');
The reason for this is you are getting something that already exists, so you
don't need to use the new keyword

To set the user's nickname use:
  $user->setNickname('John');
The reason for this is if the nickname compromises of options too,
$user->nickname = 'John' would not suffice

To get the user's nickname use:
  $user->getNickname();
The reason for this is because public properties should not be accessible from
outside the class

See the Cmf_Menu_Link for an example of how a class should be strucutred.


Naming Conventions
==================

General
* Avoid abbreviations, use full words instead

OOP Classes
* Classes should use underscores to separate words
* At all times, there should be no two classes with the same name

OOP Properties
* Properties should be named using camel-cased named

OOP Methods
* "remove" is to end the relationship between two objects, "delete" is to
  delete an actual object
* "add" should be used to add something such as a relationship, new ClassName
  should be used to create a new object
* "save" should be used where a change was previously made, "update" should be
  not be used to save changes to an objec

CSS Classes
* CSS classes should use underscores opposed to dashes or camel-cased naming

Paths
* URL's should use dashes opposed to underscores or camel-cased naming, all
  words should be lowercase
* All filenames should use underscores to separate words


DATABASE PRACTISES
==================

* All tables should have a prefix to help identify what group of modules or
  what feature of the site they relate to
* All tables should have their own unique code to which all fields that belong
  to that table (excluding foreign keys) should be prefixed with the unique
  code for that table
* All tables except those using the archive data storage type should have a
  primary key which preferably is a numeric ID
* All tables should have a s_id column (which is used to specify the site the
  row belongs to)
* All tables should have an active and delete field, active is used to specify
  whether the field is in use or shows on the public facing website and the
  deleted field is used to mark the row as deleted (allowing the delete action
  to be undone if required)


Conventions Coming Soon
=======================

* Standardised URL patterns such as whether to use /user/edit/1 or /user/1/edit