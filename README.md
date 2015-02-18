# User Management in Yii 2

**Using the yii2-usr module in yii2-app-basic**

This git repository deomonstrates how you can implement user managament in a Yii 2.0 app with the nineinchnick/yii2-usr module. It starts by
installing the official yii2-app-basic and then proceeds in a series of steps to implement
user managament using the yii2-usr module. Each step is represented in the repo by a tag and is described below.


## About yii2-usr

yii2-usr is the only user management module I know that does not dictate your tables or models.
The module provides a number of features that you can attach to your application in whatever
combination meets your needs. Bits of functionality from the module can be included in your project
or not and the bits you use can be customized.

yii2-usr does not prescribe a user model or user or profile tables. It respects that these belong
to your application and your design. But if you want to use the module's implementation fo a feature – user
account registration, for example – you have to implement the corresponding yii2-usr interfaces in your user model.

Other optional features of yii2-usr are provided as widgets and actions.

The functionality provided by yii2-usr in its form models can be customized. For this purpose, the module provides
behavior classes you can extend and attach to the forms.

Finally, to change the views the module provides, you can change them one at a time by
putting override views in a theme in your app.


## Getting started

First, get your development environment set-up.

1. install Yii 2.0 and yii2-app-basic using the
[instructions in the Guide](http://www.yiiframework.com/doc-2.0/guide-start-installation.html).
1. Initialize it as a git repo and make your initial commit.
1. Get your web server and PHP running so that the basic app works as expected.
1. Get your relational database server set-up and running. The basic app doesn't use it but we will
need it in the next step. I use Maria 10 but any SQL RDBMS supported by Yii should work, perhaps even MongoDB.
1. Use a DB management tool suitable for your DB. I use SequelPro.


## Step 1 – Basic user registration and login


### Requirements

Requirements in this step are minimal because the goal is get something useful done with yii2-usr while keeping the scope of work as small as possible.

We need:

- A database table in which to keep all our registered users
- User registration feature in which users can create their own user accounts
- Login and logoff

For the moment we are not going to require:

- Activation and deactivation of accounts
- Verification of emails
- Password reset/recovery
- Usernames because user accounts are externally identified by email address only
- Any user profile information beyond email and password

### Implementation

In general I took guidance from the examples in yii2-usr. My requirements are simpler
so my user table and `User` model in this step are simplified and altered versions of those.

#### Module configuration

I installed the module using Composer and configured it as follows.

    'modules' => [
        'usr' => [
            'class' => 'nineinchnick\usr\Module',
            'dicewareEnabled' => false,
            'rememberMeDuration' => false,
            'requireVerifiedEmail' => false,
            'recoveryEnabled' => false,
        ],
    ],

Diceware is disabled because its method of password generation is now built into password cracker
software, so [it is not secure](https://www.schneier.com/blog/archives/2014/03/choosing_secure_1.html).

My app won't use cookie-based login so that is disabled. The other two properties are false because
I'm not implementing them in this step.

#### Schema

The schema is defined by the `table_user` migration. There is no username. `password` actually 
stores password hash values, not passwords. The datetime fields are useful for audit. And we
add one test user so we can test signin before regitration works.

#### Implementing the `IdentityInterface`

My user model class extends yii2-usr IdentityInterface which extends Yii's IdentityInterface, 
adding a password auth method. So I start by implementing
[Yii's IdentityInterface](http://www.yiiframework.com/doc-2.0/yii-web-identityinterface.html).

`User::findIdentity()` and `User::getId()` are too easy to need discussion. 

An access token is typical in REST services where user/pass auth doesn't work well. 
An authKey is used in cookie-based login, which the app does not allow. My app doesn't use
access token auth or cookie-based login so the thre methods in IdentityInterface related to 
those throw exceptions. I'd be happy if these methods were not cluttering my user class
but the interface demands something.

Finally I have to implement yii2-usr's `authenticate()` method. It uses my `verifyPassword()`
which chatches invalid parameter exceptions thrown by the `validatePassword()` in Yii's Security 
component. The Security component must be absolutely strict about checking inputs because 
proceeding with faulty input could lead to vulnerabilities in apps. In my case, I want to log
that this happened and hide the exception from the user.

#### Test it

Now I can www-browse to /index.php?r=usr which redirects to index.php?r=usr/default/login and
log in sucessfully.

The login/gout links in the top right don't look so good. So I changed these so they
use email instead of username and link to the module's login/out actions.

#### Implementing the `EditableIdentityInterface`

Thi interface allows the yii2-usr module to save and change my user model.

The `saveIdentity()` method comes with a `$requireVerifiedEmail` that I ignore until a later step.
We do not defeat validation in the call to `save()` but we have no validation rules.

> Note: yii2-usr uses a convention in which methods return false when they detect an error 
conditions. I dislike this. You have to add checks in callers and figure out what to do. If there's
more than a trivial call stack it get's complex. And sometimes methods need to return bool false
that doesn't represent an error. But I've chosen to follow this convention in this demo app rather 
than refactor so as to keep focused on how to use the module

The minimally documented method `identityAttributesMap()` is interesting. It returns the map
between attributes in my user model and attributes in the `ProfileForm` model in yii2-usr, which
is the model the module uses, together with `default/_form` view, for create and update actions
on users. The only attribute in `ProfileForm` that I use is email. The password attribule does not
map because in the form it is plaintext while in the user model it is a hash and saving it is
part of implementing `PasswordHistoryIdentityInterface`.

The `get/setIdentityAttributes()` methods allow the `ProfileForm` to get and set attributes in `User`.
While I only map the email attribute, these general implementations from the module's example user 
work with any map.

Finally, `beforeSave()`, which is not required by any yii2-usr interface, takes care of setting 
the basic audit records.

#### Test it

While logged on, I can www-browse to /index.php?r=usr/default/profile and view my proifile. There
is a  link to update it which I can test too. Changing email works but changing password does not
because that's not implemented yet.


#### Implementing the `PasswordHistoryIdentityInterface`

The `resetPassword()` is what saves a new or changed password so user registration and 
password change doesn't work until it is implemented. If you were keeping each users' 
password histories, you'd do that here too.

My `getPasswordDate()` method is also simple because I don't need to check password input
against a saved history.

#### Test it

Now I can go to the login page and test user registration.

Go to /index.php?r=usr/default/profile and click update. Attempting to change password appears to 
work but it doesn't actually update the user record with a hash of the new password.


### Example

So see my example implementation in action, check out the `step-1` tag of this repo and try it out.