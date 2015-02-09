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

yii2-usr does not prescribe a User model or user or profile tables. It respects that these belong
to your application and your design. But if you want to use the module's implementation fo a feature – user
account registration, for example – you have to implement the corresponding yii2-usr interfaces in your User model.

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



### Example

So see my example implementation in action, check out the `step-1` tag of this repo and try it out.