<?php

namespace app\behaviors;

use nineinchnick\usr\components;
use Yii;

/**
 * Class LoginFormBehavior
 *
 * The FormModelBehavior in yii2-usr allows some customization to the form models in the module.
 * This is class customizes the LoginForm.
 *
 * This class is specified in the 'loginFormBehaviors' of the module's configuration
 * in our application configuration.
 *
 * @package app\behaviors
 */
class LoginFormBehavior extends components\FormModelBehavior
{
    /**
     * Declares attribute labels.
     *
     * yii2-usr uses 'username' for identifying users. We use Email. Since our User::findByUsername()
     * is actually findign by 'email' attribute, it is enough to override the attribute label of the
     * module's login form.
     */
    public function attributeLabels()
    {
        return [
            'username' => Yii::t('usr', 'Email'),
        ];
    }
}