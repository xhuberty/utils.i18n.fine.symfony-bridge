Fine to Symfony translator bridge
=================================

This package is a bridge between [**Fine**, the translation package used in Mouf](http://mouf-php.com/packages/mouf/utils.i18n.fine/README.md)
and [**Symfony2's translation system**](symfony.com/doc/current/book/translation.html).

Using the `SymfonyFineBridge` class, Fine can be exposed as a service implementing [Symfony's `TranslatorInterface`](http://api.symfony.com/2.0/Symfony/Component/Translation/TranslatorInterface.html).
This means you can use Fine to translate components that expect a Symfony translator instead (usually, Symfony components).

The `SymfonyFineBridge` class implements the `TranslatorInterface` and expects 2 parameters in the constructor:

- a language translation instance (implementing Fine's `LanguageTranslationInterface`)
- a language detection instance (implementing Fine's `LanguageDetectionInterface`)

Install
-------

By default, this package comes with a Mouf install script that will create an instance named `symfonyTranslator` that you can use in your Symfony packages. It maps directly to the default
Fine instance created by Fine when installed.
