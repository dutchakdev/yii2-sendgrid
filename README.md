Sendgrid Yii2 integration
=========================

This extension allow the developper to use [Sendgrid](https://sendgrid.com/) as an email transport.


[![Latest Stable Version](https://poser.pugx.org/dutchakdev/yii2-sendgrid/v/stable)](https://packagist.org/packages/dutchakdev/yii2-sendgrid)
[![Build Status](https://api.travis-ci.org/pgaultier/yii2-sendgrid.svg?branch=master)](https://travis-ci.org/pgaultier/yii2-sendgrid)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/pgaultier/yii2-sendgrid/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/pgaultier/yii2-sendgrid/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/pgaultier/yii2-sendgrid/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/pgaultier/yii2-sendgrid/?branch=master)
[![License](https://poser.pugx.org/dutchakdev/yii2-sendgrid/license)](https://packagist.org/packages/dutchakdev/yii2-sendgrid)

[![Latest Development Version](https://img.shields.io/badge/unstable-devel-yellowgreen.svg)](https://packagist.org/packages/dutchakdev/yii2-sendgrid)
[![Build Status](https://travis-ci.org/pgaultier/yii2-sendgrid.svg?branch=devel)](https://travis-ci.org/pgaultier/yii2-sendgrid)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/pgaultier/yii2-sendgrid/badges/quality-score.png?b=devel)](https://scrutinizer-ci.com/g/pgaultier/yii2-sendgrid/?branch=devel)
[![Code Coverage](https://scrutinizer-ci.com/g/pgaultier/yii2-sendgrid/badges/coverage.png?b=devel)](https://scrutinizer-ci.com/g/pgaultier/yii2-sendgrid/?branch=devel)

Installation
------------

If you use Packagist for installing packages, then you can update your composer.json like this :

``` json
{
    "require": {
        "dutchakdev/yii2-sendgrid": "*"
    }
}
```

Howto use it
------------

Add extension to your configuration

``` php
return [
    //....
    'components' => [
        'mailer' => [
            'class' => 'dutchakdev\sendgrid\Mailer',
            'token' => '<your sendgrid token>',
        ],
    ],
];
```

You can send email as follow (using postmark templates)

``` php
Yii::$app->mailer->compose('contact/html')
     ->setFrom('from@domain.com')
     ->setTo($form->email)
     ->setSubject($form->subject)
     ->setTemplateId(12345)
     ->setTemplateModel([
         'firstname' => $form->firstname,
         'lastname' => $form->lastname,
     ->send();

```

For further instructions refer to the [related section in the Yii Definitive Guide](http://www.yiiframework.com/doc-2.0/guide-tutorial-mailing.html)


Running the tests
-----------------

Before running the tests, you should edit the file tests/_bootstrap.php and change the defines :

``` php
// ...
define('SENDGRID_FROM', '<sender>');
define('SENDGRID_TOKEN', '<token>');
define('SENDGRID_TO', '<target>');
define('SENDGRID_TEMPLATE', 575741);

define('SENDGRID_TEST_SEND', false);
// ...

```

to match your [Sendgrid](https://sendgrid.com/) configuration.

Contributing
------------

All code contributions - including those of people having commit access -
must go through a pull request and approved by a core developer before being
merged. This is to ensure proper review of all the code.

Fork the project, create a [feature branch ](http://nvie.com/posts/a-successful-git-branching-model/), and send us a pull request.
