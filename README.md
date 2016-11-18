Teknoo Software - East Foundation
=================================

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/6d14de07-2c9e-4070-a044-c9362fe2dc08/mini.png)](https://insight.sensiolabs.com/projects/6d14de07-2c9e-4070-a044-c9362fe2dc08) [![Build Status](https://travis-ci.org/TeknooSoftware/east-foundation.svg?branch=master)](https://travis-ci.org/TeknooSoftware/east-foundation)

East Foundation is a universal package to implement the [#east](http://blog.est.voyage/phpTour2015/) philosophy with 
any framework supporting PSR-11 or with Symfony 3+. :
All public method of objects must return $this or a new instance of $this.

This bundle uses PSR7 requests and responses and do automatically the conversion from Symfony's requests and responses.
So your controllers and services can be independent of Symfony. This bundle reuse internally Symfony's components
to manage routes and find controller to call.

Demo
----

Sorry, there are no simple example to show here, but you can watch a demo demo with 
Symfony 3.0 available [here](https://github.com/TeknooSoftware/east-foundation-demo)

Installation & Requirements
---------------------------
To install this library with composer, run this command :

    composer require teknoo/east-foundation

This library requires :

    * PHP 7+
    * Symfony 3+
    * Composer

Credits
-------
Richard Déloge - <richarddeloge@gmail.com> - Lead developer.
Teknoo Software - <http://teknoo.software>

About Teknoo Software
---------------------
**Teknoo Software** is a PHP software editor, founded by Richard Déloge. 
Teknoo Software's DNA is simple : Provide to our partners and to the community a set of high quality services or software,
 sharing knowledge and skills.

License
-------
East Foundation is licensed under the MIT License - see the licenses folder for details

Contribute :)
-------------

You are welcome to contribute to this project. [Fork it on Github](CONTRIBUTING.md)
