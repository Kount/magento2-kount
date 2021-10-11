Magento 2 Kount
=============================================

This is the official Magento 2 extension for Kount.

INSTALLATION

# Using Composer (recommended)
### Requirements
* Magento 2.3.x, Magento 2.4.x
* PHP 7.2.5 or above
### Instructions
* run `composer require kount/magento2-kount`
* run `php bin/magento setup:upgrade`

# Direct Download
### Requirements
* Magento 2.3.x, Magento 2.4.x
* PHP 7.2.5 or above
### Instructions
* Go to https://github.com/Kount/magento2-kount/releases/ and download source code of desired version
* Copy source code to your `<project>/app/code/Kount`. Ensure the registration.php in the Kount module is <project>/app/code/Kount/Kount/registration.php
* Go to https://github.com/Kount/kount-ris-php-sdk/wiki/Installation-Direct-Download and follow the direct download instructions.  The Kount module has a dependency on the Kount SDK and will not work without it.
* Ensure the sdk is placed in vendor/kount/kount-ris-php-sdk
