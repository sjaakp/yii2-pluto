yii2-pluto
==========
## User management module for Yii2 framework

**Pluto** is a complete user management module for the [Yii 2.0](https://www.yiiframework.com/ "Yii") PHP Framework.

It manages login and logout of users, signup, email-confirmation, blocking and assigning roles.
Users can change their email-address, forget their password. 
The site administrator can define roles and permissions and assign permissions to roles. 

A demonstration of **Pluto** is [here](https://demo.sjaakpriester.nl).

#### Prerequisites ####

**Pluto** relies on [Role-Based Access Control](https://www.yiiframework.com/doc/guide/2.0/en/security-authorization#rbac "Yii2")
 (RBAC). Therefore, the [`authManager`](https://www.yiiframework.com/doc/api/2.0/yii-base-application#$authManager-detail "Yii2")
 application component has to be configured. **Pluto** works with the **PhpManager** as well as 
 with the **DbManager**.
 
 Because **Pluto** sends emails, the `mailer` component of the application has to be up and running.
 Be sure that the `'adminEmail'` parameter of the application has a sensible value. If you prefer, you may set 
 the `'supportEmail'` parameter as well; if set, **Pluto** will use this.
 
 **Pluto** uses Yii2 [flash messages](https://www.yiiframework.com/wiki/21/how-to-work-with-flash-messages "Yii2"),
  so these have to be configured as well. If the site is set up
 using one of the [project templates](https://www.yiiframework.com/doc/guide/2.0/en/start-installation "Yii2"),
  this will be taking care of.
  
 Finally, it's important that the `identityClass` property of the application's `user`
  component is *not* set. **Pluto** sets this property. In fact, the `user` component doesn't need
  to be configured at all. However, you probably want to configure it just for the sake of
  setting [`enableAutoLogin`](https://www.yiiframework.com/doc/api/2.0/yii-web-user#$enableAutoLogin-detail "Yii2") to `true`.

## Installation ##

Install **yii2-pluto** in the usual way with [Composer](https://getcomposer.org/). 
Add the following to the require section of your `composer.json` file:

`"sjaakp/yii2-pluto": "*"` 

or run:

`composer require sjaakp/yii2-pluto` 

You can manually install **yii2-pluto** by [downloading the source in ZIP-format](https://github.com/sjaakp/yii2-iro/archive/master.zip).
 
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


The module has to be bootstrapped. Do this by adding the following to the
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
 has to be run. The first will create a database table for the users:
  
    yii migrate
    
The migration applied is called `sjaakp\pluto\migrations\m000000_000000_init`.
    
The second console command is:
 
    yii pluto
    
This will set up the basic roles and permissions.

#### Roles ####

After installation of **Pluto**, the site recognizes three user Roles and a few Permissions.
Read more about them in the [Authorization chapter of the Yii-guide](https://www.yiiframework.com/doc/guide/2.0/en/security-authorization#rbac "Yii2").
The default Roles are:

 - **'visitor'**: a user with no special rights,
 - **'support'**: a user who can manage user data (except those from 'admin'),
 - **'admin'**: a user with unlimited permissions.

**'admin'** is able to create more Roles and Permissions. 

#### Integrate in the user interface ####

Now that **Pluto** is installed, it has to be integrated in the user interface of the site. There are dozens of ways
to accomplish this, but these are some general guidelines:

 - A **guest user** should be offered an opportunity to *log in*. **Pluto**'s login screen
   has options to sign up (register) for new users, and to reset the password.
 - An **authenticated** user should be offered an opportunity to *log out*, as well as an opportunity
   to change her settings.
 - A user with special permissions should have options to access **Pluto**'s User Management
   Pages and the like.
   
Here is one common way to integrate **Pluto** in the site's main menu:

    <?php
    use ...
    
    $user = Yii::$app->user;
    
    $navItems = [
        ['label' => 'Home', 'url' => '/' ],
        // ...
        ['label' => 'About', 'url' => ['/site/about']],
        // ... more menu items ...,
    ];

    if ($user->isGuest) {
        $navItems[] = ['label' => 'Login', 'url' => ['/pluto/login']];
    } else {
        $navItems[] = [
            'label' => $user->identity->name,
            'items' => [
                [
                    'label' => 'Settings',
                    'url' => ['/pluto/settings'],
                ],
                [
                    'label' => 'Manage Users',
                    'url' => ['/pluto/user'],
                    'visible' => Yii::$app->user->can('manageUsers'),
                ],
                [
                    'label' => 'Manage Roles',
                    'url' => ['/pluto/role'],
                    'visible' => Yii::$app->user->can('manageRoles'),
                ],
                [
                    'label' => 'Logout',
                    'url' => ['/pluto/logout'],
                    'linkOptions' => ['data-method' => 'post']
                ]
            ],
        ];
    }
    
    NavBar::begin([
        // ... NavBar options ...
    ]);
    echo Nav::widget([
        'items' => $navItems,
    ]);
    NavBar::end();
  
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

 - **viewOptions** `array`
 - **views** `array` Default: `[]`.
 - **mailOptions** `array`
 - **passwordFlags** `array`
 - **passwordRegexp** `string` Default: `'/^\S*(?=\S{6,})\S*$/'`.
 - **passwordHint** `string` Default: `'At least 6 characters'`.
 - **standardRole** `string` Role assigned to new users. Default: `'visitor'`.
 - **ruleNamespace** `string` Namespace for Rule-classes (Conditions). Default: `'app\rbac'` 
 - **tokenStamina** `integer` Duration of the valid state of a sent email-token. Default: `21600` (six hours).
 - **loginStamina** `integer` Duration of 'Remember me'. Default: `2592000` (thirty days).
 - **formClass** `null|string` Yii2 class used for forms. If `null`, this ia set to `ActiveForm`
      in the 'bootstrap' namespace. Default: `null`
 - **multipleRoles** `boolean` Whether more than one role can be assigned to a user. In my 
      opinion this is generally a very bad idea. Therefore, default: `false`. 
 - **profileClass** `string` Name of the class used as profile. Default: `null`.
 - **identityClass** `string` Class name of the identity object associated with the current user.
      May be changed into a class extended from `sjaakp\pluto\models\User`. Default: `'sjaakp\pluto\models\User'`.           
   

## Profile ##

Apart from the `User` model, users can also have a Profile model. This can hold extra information that's
  intended to be public accessible, i.e. a short bio, profile photo or avatar, or location. **Pluto** doesn't 
  implement a Profile model itself, but it does support it.
  
A Profile is a standard [`ActiveRecord`](https://www.yiiframework.com/doc/api/2.0/yii-db-activerecord "Yii2")
  with one, very important, peculiarity: <em>it's `id` field is <strong>not</strong>
  auto-incrementing</em>.
  
Instead, the `id` field in the Profile's database table should be declared `UNIQUE`. The Profile takes the same `id`
  as the `User` it is associated with.
  
The Profile model should be configured as `profileClass` in **Pluto**'s configuration.
    
Instead of just the class name, the `'profile'` component can also be initialized with a configuration array,
  with the class name as value of the `'class'` element like on many other places in the
  Yii2 kingdom.

With this setup, **Pluto** will automatically create a Profile for each registered user. I a 
  `User` is deleted, her Profile will be deleted as well.
  
## How to signup the first admin? ##

## How to override identity-class? ##

**Pluto** sets `sjaakp\pluto\models\User` as `identityClass` of the application's `user`
  component. If you're ambitious, you might develop your own identity-class. This should
  extend from `sjaakp\pluto\models\User` and set as `identityClass` of the `user` component
  in the application configuration.
      
    
## Pluto ##

**Pluto** happens to be the name of a friendly guard dog I once knew.
