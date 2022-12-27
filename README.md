Description
----
This plugin provides waardepapieren functionality for WordPress. It has support in place for Gravity forms but can be used without it

## About Waardepapieren

The waardepapieren project aims at digitizing proof from the Dutch government for its citizens (e.g., birth certificates, marriage certificates, and proof of residence and residential history). The project is based on the [W3C claims structure](https://w3c.github.io/vc-data-model/#claims) for standardization.

At the core of the waardepapieren concept is that a “proof” should be applicable both digital and non-digital. Therefore proof is presented as a PDF containing a JTW-based claim. The claim itself, however, can also be used separately. For more information about the inner workings of waardepapieren, see the waardepapieren service at it [repro]( https://github.com/ConductionNL/waardepapieren-service).

## Online test environment
There are several online environments available for testing.

1. [Example user interface](https://waardepapieren-gemeentehoorn.commonground.nu)
2. [Example registration desk interface](https://waardepapieren-gemeentehoorn.commonground.nu/waardepapieren-balie)
3. [Example Wordpress implementation](https://dev.zuiddrecht.nl)
4. [Example Waardepapieren Service](https://waardepapieren-gemeentehoorn.commonground.nu/api/v1/waar)
5. [Example Waardepapieren Registration](https://waardepapieren-gemeentehoorn.commonground.nu/api/v1/wari )

## Dependencies

You will need an API key at a waardepapieren service for this repository. You can obtain a valid API key at [Dimpact](https://www.dimpact.nl/). Also, you can get a key for the test API from [Conduction](https://conduction.nl).

## Installation 
You can install this WordPress plugin from the [WordPress plugin store](https://wordpress.org/plugins/).

After installation and activation, you must configure the plugin before use.

## Other Repositories
*UI*
1. [Burger interface](https://github.com/ConductionNL/waardepapieren) 
2. [Ballie interface](https://github.com/ConductionNL/waardepapieren-ballie)

*Componenten*
1. [Motorblok](https://github.com/ConductionNL/waardepapieren-service) 
2. [Register](https://github.com/ConductionNL/waardepapieren-register) 

*Libraries*
1. [PHP](https://github.com/ConductionNL/waardepapieren-php)

*Plugins*
1. [WordPress](https://github.com/ConductionNL/waardepapieren_wordpress) 
2. [Typo3](https://github.com/ConductionNL/waardepapieren_typo3) 
3. [Drupal](https://github.com/ConductionNL/waardepapieren_drupal) 


Credits
----

Information about the authors of this component can be found [here](AUTHORS.md)

This component is based on the [ICTU disciple project](https://github.com/discipl/waardepapieren)

Copyright © [Dimpact](https://www.dimpact.nl/) 2020
