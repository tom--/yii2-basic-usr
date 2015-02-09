<?php

namespace app\behaviors;

use nineinchnick\usr\components;

/**
 * Class ProfileFormBehavior
 *
 * The FormModelBehavior in yii2-usr allows some customization to the form models in the module.
 * This is class customizes the ProfileForm for our needs.
 *
 * This class is specified in the 'profileFormBehaviors' of the module's configuration
 * in our application configuration.
 *
 * @package app\behaviors
 */
class ProfileFormBehavior extends components\FormModelBehavior
{
    /**
     * The profile form in yii2-usr has username, email and password as authentication credentials.
     * We don't use username in our app, only email. So this rather bizzare method removes the
     * validation rules defined by the ProfileFormModel yii2-usr for the 'username' attribute.
     *
     * @param array $rules Validation rules
     *
     * @return array
     */
    public function filterRules($rules = [])
    {
        $attributes = ['email', 'password'];
        foreach ($rules as $i => $rule) {
            if (is_array($rule[0])) {
                $rules[$i][0] = array_intersect($rule[0], $attributes);
            } else {
                if (!in_array($rule[0], $attributes)) {
                    unset($rules[$i]);
                }
            }
        }

        return $rules;
    }
}
