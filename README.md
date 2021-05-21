yii2-pluto x
==========
## User management module for Yii2 framework

[![Latest Stable Version](https://poser.pugx.org/sjaakp/yii2-pluto/v/stable)](https://packagist.org/packages/sjaakp/yii2-pluto)
[![Total Downloads](https://poser.pugx.org/sjaakp/yii2-pluto/downloads)](https://packagist.org/packages/sjaakp/yii2-pluto)
[![License](https://poser.pugx.org/sjaakp/yii2-pluto/license)](https://packagist.org/packages/sjaakp/yii2-pluto)

**Pluto** is a complete user management module for the [Yii 2.0](https://www.yiiframework.com/ "Yii") PHP Framework.

It manages log in and log out of users, sign up, email-confirmation, blocking and assigning roles.
Users can change their email-address, ask for a reset of their password. 
The site administrator can define roles and permissions and assign permissions to roles. 

A demonstration of **Pluto** is [here](https://demo.sjaakpriester.nl).

## Prerequisites ##

[**Pluto**](#pluto) relies on [Role-Based Access Control](https://www.yiiframework.com/doc/guide/2.0/en/security-authorization#rbac "Yii2")
 (RBAC). Therefore, the [`authManager`](https://www.yiiframework.com/doc/api/2.0/yii-base-application#$authManager-detail "Yii2")
 application component has to be configured. **Pluto** works with Yii's PhpManager as well as 
 with the DbManager.
 
 Because **Pluto** sends emails, the `mailer` component of the application has to be up and running.
 Be sure that the `'adminEmail'` parameter of the application has a sensible value. If you prefer, you may set 
 the `'supportEmail'` parameter as well; if set, **Pluto** will use this.
 
 **Pluto** uses Yii2 [flash messages](https://www.yiiframework.com/wiki/21/how-to-work-with-flash-messages "Yii2"),
  so these have to be configured as well. If the site is set up
 using one of Yii's [project templates](https://www.yiiframework.com/doc/guide/2.0/en/start-installation "Yii2"),
  this will be taken care of.

## Installation ##

Install **yii2-pluto** in the usual way with [Composer](https://getcomposer.org/). 
Add the following to the require section of your `composer.json` file:

`"sjaakp/yii2-pluto": "*"` 

or run:

`composer require sjaakp/yii2-pluto` 

You can manually install **yii2-pluto** by [downloading the source in ZIP-format](https://github.com/sjaakp/yii2-pluto/archive/master.zip).
 
#### Module ####

**Pluto** is a [module](https://www.yiiframework.com/doc/guide/2.0/en/structure-modules#using-modules "Yii2")
 in the Yii2 framework. It has to be configured 
in the main configuration file, usually called `web.php` or `main.php` in the `config`
directory. Add the following to the configuration array:

    <?php
    // ...
    'modules' => [
        'pluto' => [
            'class' => 'sjaakp\pluto\Module',
            // several options
        ],
    ],
    // ...


The module has to be *bootstrapped*. Do this by adding the following to the
application configuration array:

    <php
    // ...
    'bootstrap' => [
        'pluto',
    ]
    // ...

There probably already is a `bootstrap` property in your configuration file; just
add `'pluto'` to it.

**Important**: the module should also be set up in the same way in the console configuration (usually
called `console.php`).

#### Console commands ####

To complete the installation, two [console commands](https://www.yiiframework.com/doc/guide/2.0/en/tutorial-console#usage "Yii2")
 have to be run. The first will create a database table for the users:
  
    yii migrate
    
The migration applied is called `sjaakp\pluto\migrations\m000000_000000_init`.
    
The second console command is:
 
    yii pluto
    
This will set up the basic roles and permissions.

#### Actions ####

**Pluto** adds a bunch of [actions](https://www.yiiframework.com/doc/guide/2.0/en/structure-controllers#actions "Yii2")
 to the application. The most important are:
 
 |Route|Description|
 |---|---|
 |<example.com>**/pluto/login**|to log in|
 |<example.com>**/pluto/logout**|to log out|
 |<example.com>**/pluto/signup**|to sign up (register)|
 |<example.com>**/pluto/forgot**|if the user forgot her password|
 |<example.com>**/pluto/settings**|to change name or email-address|
 |<example.com>**/pluto/download**|to download user data in human and machine readable form (requirement of European legislation)|
 |<example.com>**/pluto/delete**|to be completely forgotten by the site (also a requirement of the EU)|
 |<example.com>**/pluto/user**|User management (only for 'support' and 'admin' Roles)|
 |<example.com>**/pluto/role**|Role management (only for 'admin')|

#### Roles ####

After installation of **Pluto**, the site recognizes two user Roles and a few Permissions.
Read more about them in the [Authorization chapter of the Yii-guide](https://www.yiiframework.com/doc/guide/2.0/en/security-authorization#rbac "Yii2").
The installed Roles are:

|Role|Description|
|---|---|
|**'support'**|user who can manage user data (except those from 'admin')|
|**'admin'**|user with unlimited Permissions, like creating more Roles and Permissions|

#### Integrate in the user interface ####

Now that **Pluto** is installed, it has to be integrated in the user interface of the site. There are dozens of ways
to accomplish this, but here are some general guidelines:

 - A **guest user** should be offered an opportunity to *log in*. **Pluto**'s login screen
   has options to sign up (register) for new users, and to reset the password.
 - An **authenticated** user should be offered an opportunities to *log out*, as well as
   to change her settings, etc.
 - Users with special permissions should have options to access **Pluto**'s User Management
   Pages and the like.
   
**LoginMenu** is a widget to integrate **Pluto** in the site's main menu:

    <?php
    use ...
    use sjaakp\pluto\widgets\LoginMenu
    
    $user = Yii::$app->user;
    
    $navItems = [
        ['label' => 'Home', 'url' => '/' ],
        // ...
        ['label' => 'About', 'url' => ['/site/about']],
        // ... more menu items ...,
        LoginMenu::widget([
            'options' => [
               'class' => 'bg-primary'
            ]
        ]),
    ];

    NavBar::begin([
        'options' => [
            'class' => '... navbar-dark bg-primary',
        ],
        // ... more NavBar options ...
    ]);
    echo Nav::widget([
        'items' => $navItems,
    ]);
    NavBar::end();
    
Take care to give **LoginMenu** the same background defining CSS class as NavBar, for
instance `bg-primary`.    
  
## Options ##

The **Pluto** module has an extensive range of options. They are set in the application 
 configuration like so:
 
     <?php
     // ...
     'modules' => [
         'pluto' => [
             'class' => 'sjaakp\pluto\Module',
             'passwordHint' => 'At least eight characters, one uppercase, one digit',
             // ...
             // ... more options ...
         ],
     ],
     // ...
     
The options (all are optional) are:

 - **viewOptions** `array` CSS options for certain aspects of **Pluto**'s views, with the following
    key-value pairs. Optimized for Bootstrap4.
    - `'row'` Options for the outer 'row'-div. Default value: `[ 'class' => 'row justify-content-center' ]`.
    - `'col'` Likewise for the inner 'col'-div. Default: `[ 'class' => 'col-md-6 col-lg-5' ]`.
    - `'button'` Options for the view's main button. Default: `[ 'class' => 'btn btn-success' ]`.
    - `'link'` Options for the secondary links. Default: `[ 'class' => 'btn btn-sm btn-secondary' ]`.            
 - **views** `array` See [below](#override-view-files). Default: `[]`.
 - **mailOptions** `array` Options for the [app mailer](https://www.yiiframework.com/doc/api/2.0/yii-mail-basemailer "Yii2").
    Default: see source.
 - **passwordFlags** `array` Options for the password input. Keys: any of the action id's (like
    'login' or 'forgot'), or `'all'` (meaning, well, all of the actions). Values: `string` or
     `array` of the following flags. Default: `[ 'all' => 'reveal' ]`.
    - `'reveal'` Password input has a small 'reveal'-button.
    - `'double'` User must fill in password twice (doesn't affect `'forgot'`, `'resend'`).
    - `'captcha'` Dialog has [captcha field](#captcha).
 - **passwordRegexp** `string` [Regular expression](https://www.php.net/manual/en/reference.pcre.pattern.syntax.php "PHP")
   against which the password is matched. Complex example:
  `'/^\S*(?=\S{8,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])\S*$/'` (meaning: at least 8 characters,
  of which at least one lower case, one upper case, and one digit. To experiment with this,
   use a site like [Live Regex](https://www.phpliveregex.com/). Default: `'/^\S*(?=\S{6,})\S*$/'`.
 - **passwordHint** `string` Textual representation of the above. Default: `'At least 6 characters'`.
 - **defaultRole** `null|string|array` Role(s) assigned to new users. Default: `null`.
 - **firstDefaultRole** `null|string|array` Role(s) assigned to the [*first*](#sign-up-the-first-admin) new user. Default: `'admin'`.
 - **ruleNamespace** `string` Namespace for [Rule-classes](#roles-permissions-and-all-that) (Conditions). Default: `'app\rbac'` 
 - **tokenStamina** `integer` Duration of the valid state of a sent email-token. Default: `21600` (six hours).
 - **loginStamina** `integer` Duration of 'Remember me'. Default: `2592000` (thirty days).
 - **formClass** `null|string` Yii2 class used for forms. If `null`, this is set to `ActiveForm`
      in the 'bootstrap' namespace. Default: `null`
 - **multipleRoles** `boolean` Whether more than one role can be assigned to a user. In my 
      opinion this is generally a very bad idea. Therefore, default: `false`.
 - **fenceMode** `boolean|string` Whether the site is 'behind a fence', i.e. completely unaccessible
      for guest users. Every page leads to the login screen. Great for development stages. Can
      also be a Permission name, in which case only users with this Permission are granted access.
      Suitable for a site with a separate 'admin' subdomain.
      Default: `false`.       
 - **profileClass** `null|string|array` Name of the class used as [profile](#profile). Can also be a configuration array.
      Default: `null`.
 - **identityClass** `string` Class name of the identity object associated with the current user.
      May be changed into a class extended from `sjaakp\pluto\models\User`. 
      Default: `'sjaakp\pluto\models\User'`.           
   

## Profile ##

Apart from the `User` model, users can also have a Profile model. This can hold extra information that's
  intended to be public: a short bio, profile photo, avatar, location and the like. **Pluto** doesn't 
  implement a Profile model itself, but it does support it.
  
A Profile is a standard [`ActiveRecord`](https://www.yiiframework.com/doc/api/2.0/yii-db-activerecord "Yii2")
  with one, very important, peculiarity: <em>it's `id` field is <strong>not</strong>
  auto-incrementing</em>.
  
Instead, the `id` field in the Profile's database table should be declared `UNIQUE`. The Profile takes the same `id`
  as the `User` it is associated with.
  
The Profile model should be configured as `profileClass` in **Pluto**'s configuration.
    
Instead of just the class name, the `'profile'` component can also be initialized with a configuration array,
  with the class name as value of the `'class'` element, like on many other places in the
  Yii2 kingdom.

With this setup, **Pluto** will automatically create a Profile for each registered user. If a 
  `User` is deleted, her Profile will be deleted as well.
  
## Roles, Permissions and all that ##

For the uninitiated, Roles and Permissions can be daunting. The
 [Authorization chapter of the Yii-guide](https://www.yiiframework.com/doc/guide/2.0/en/security-authorization#rbac "Yii2")
 offers some help. Basically, **Roles** are assigned to **Users**, and **Permissions** are used
 to structure the site. One or more **Permissions** are assigned to each **Role**.
 
Both **Roles** and **Permissions** can be subject to **Conditions** (Yii2 calls these 
 ['Rules'](https://www.yiiframework.com/doc/guide/2.0/en/security-authorization#using-rules "Yii2")).
 They are implemented as PHP classes, extending from 
 [`yii\rbac\Rule`](https://www.yiiframework.com/doc/api/2.0/yii-rbac-rule "Yii2"). **Pluto** looks 
 under namespace `'app\rbac'` (settable by option `ruleNamespace`) for unregistered
 **Condition**'s and offers 'admin' an opportunity to register them.
 
**Pluto** avoids the somewhat misleading term 'children' with respect to Roles and
  Permissions. Instead it uses 'Included Roles' or 'Included Permissions'. 
  
## Sign up the first 'admin' ##

With the default set-up, **Pluto** automatically assigns the 'admin' Role to the *first* user
who signs up. If afterwards you come into a situation without a registered 'admin', there is 
no possibility to manage Roles. There are several solutions to this puzzle. One is: *temporarely*
set **Pluto**'s `defaultRole` to `'admin'`. The importance of *temporarely* can't be stretched
enough. You'll *never* want this setting in a live site.

## Captcha ##

**Pluto** supports Yii2's standard [Captcha](https://www.yiiframework.com/doc/api/2.0/yii-captcha-captcha "Yii2"), as well as
  Google's [reCaptcha](https://developers.google.com/recaptcha/ "Google") v2 ('I am not a robot'). A captcha-challenge
  will show up in the dialog when one of the **passwordFlags** is `'captcha'`. 
  If [himiklab/yii2-recaptcha-widget](https://github.com/himiklab/yii2-recaptcha-widget "GitHub")
  is installed on the site, it will be Google's reCaptcha v2, otherwise Yii2's standard captcha.

## Internationalization ##

All of **Pluto**'s utterances are translatable. The translations are in the `'sjaakp\pluto\messages'`
 directory.
 
You can override **Pluto**'s translations by setting the application's 
 [message source](https://www.yiiframework.com/doc/guide/2.0/en/tutorial-i18n#2-configure-one-or-multiple-message-sources "Yii2")
 in the main configuration, like so: 

    <?php
    // ...
    'components' => [
        // ... other components ...     
        'i18n' => [
             'translations' => [
                  // ... other translations ...
                 'pluto' => [    // override pluto's standard messages
                     'class' => 'yii\i18n\PhpMessageSource',
                     'basePath' => '@app/messages',  // this is a default
                     'sourceLanguage' => 'en-US',    // this as well
                 ],
             ],
        ],
        // ... still more components ...
    ]

The translations should be in a file called `'pluto.php'`.

If you want a single or only a few messages translated and use **Pluto**'s translations for 
 the main part, the trick is to set up `'i18n'` like above and write your translation file
 something like:
 
     <?php
     // app/messages/nl/pluto.php
     
     $plutoMessages = Yii::getAlias('@sjaakp/pluto/messages/nl/pluto.php');
     
     return array_merge (require($plutoMessages), [
        'Settings' => 'Instellingen',   // your preferred translation
     ]);


At the moment, only two languages are implemented: Italian and Dutch. Agreed, Dutch is only the world's
 [52th language](https://en.wikipedia.org/wiki/List_of_languages_by_number_of_native_speakers "Wikipedia"),
 but it happens to be my native tongue. Please, feel invited to translate **Pluto** in 
 other languages. I'll be more than glad to include them into **Pluto**'s next release.
 
## Override view-files ##

Any of the **Pluto**'s view files can be overridden, perhaps to add a logo or 
 change the structure. Just set the **views** setting of the module to something like:
 
     <?php
     // ...
     'modules' => [
         'pluto' => [
             'class' => 'sjaakp\pluto\Module',
             'views' => [
                  'default' => [    // Pluto controller id
                      'login' => <view file>    // action => view
                  ]
             ],
             // ...
             // ... more options ...
         ],
     ],
     // ...

`<view file>` can be of any form
  [`yii\web\controller::render()`](https://www.yiiframework.com/doc/api/2.0/yii-base-controller#render()-detail "Yii2")
  accepts.  

## Override identity-class ##

**Pluto** sets `sjaakp\pluto\models\User` as `identityClass` of the application's `yii\web\User`
  component. If you're ambitious, you might develop your own identity-class. This should
  extend from `sjaakp\pluto\models\User` and be set as **Pluto**'s `identityClass` option.
      
    
## Pluto ##

**Pluto** happens to be the name of a friendly guard dog I once knew. If you really hate that name,
just set up the module like this:

    <?php
    // ...
    'modules' => [
        'saturnus' => [
            'class' => 'sjaakp\pluto\Module',
            // several options
        ],
    ],
    // ...
    'bootstrap' => [
        'saturnus',
    ]
    // ...

Your users will never be confronted with the name 'pluto'.

## Thanks ##

 - **rossaddison**: English grammar.
 - **paskuale75**: better hints.
 - **ettolo**: Italian translation.
