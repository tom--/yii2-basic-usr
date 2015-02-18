<?php

namespace app\models;

use Yii;
use nineinchnick\usr\components;
use yii\base\NotSupportedException;

/**
 * This is the model class for table "{{users}}".
 *
 * Inherits from:
 *  - nineinchnick\usr\components\IdentityInterface
 *      - yii\web\IdentityInterface
 *
 *          Based in the Yii Identity with one additional method.
 *
 *  - nineinchnick\usr\components\EditableIdentityInterface
 *
 *      The methods that yii2-usr needs to save an instance of our Identity
 *      to DB and to get and set attributes of an instance.
 *
 *  - nineinchnick\usr\components\PasswordHistoryIdentityInterface
 *
 *      Methods yii2-usr needs to access and reset the date when the user's password
 *      was last changed.
 *
 * @property integer $id User id and primary key
 * @property string $email
 * @property string $password
 * @property string $created UTC datetime
 * @property string $updated UTC datetime
 * @property string $last_visit UTC datetime
 * @property string $password_set UTC datetime
 */
class User extends \yii\db\ActiveRecord implements
    components\IdentityInterface
    , components\EditableIdentityInterface
    , components\PasswordHistoryIdentityInterface
{
    /**
     * Required by IdentityInterface
     *
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    /**
     * Required by IdentityInterface
     *
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Required by IdentityInterface
     *
     * Except that we are using 'email' as the external user identifier, not 'username'.
     *
     * @inheritdoc
     */
    public static function findByUsername($email)
    {
        return self::find()->where(['email' => $email])->one();
    }

    /**
     * Required by IdentityInterface
     *
     * We are not using access token-based authentication, we are only using user/pass.
     *
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('Token-based authentication is not supported.');
    }

    /**
     * Required by IdentityInterface
     *
     * Our app does not allow a "remember me" cookie that encodes the user's ID. Our app is
     * used mostly on shared computers.
     *
     * @inheritdoc
     */
    public function getAuthKey()
    {
        throw new NotSupportedException('Cookie-based login is not supported.');
    }

    /**
     * Required by IdentityInterface
     *
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        throw new NotSupportedException('Cookie-based login is not supported.');
    }

    /**
     * Required by IdentityInterface
     *
     * @inheritdoc
     */
    public function authenticate($password)
    {
        if (!$this->verifyPassword($password)) {
            return [self::ERROR_INVALID, Yii::t('usr', 'Invalid email or password.')];
        }

        $this->last_visit = gmdate('Y-m-d H:i:s');
        $this->save(false);
        return true;
    }

    /**
     * Verifies a password against a saved hash.
     *
     * Wraps yii\base\Security::validatePassword() in a try/catch so parameter errors
     * are logged but otherwise treated as a login failure.
     *
     * @param string $password password to validate
     *
     * @return bool if password provided matches password
     */
    public function verifyPassword($password)
    {
        try {
            return Yii::$app->security->validatePassword($password, $this->password);
        } catch (\yii\base\InvalidParamException $e) {
            Yii::warning($e->getMessage(), __METHOD__);
            return false;
        }
    }

    /**
     * Required by EditableIdentityInterface
     *
     * @inheritdoc
     */
    public function saveIdentity($requireVerifiedEmail = false)
    {
        if (!$this->save()) {
            Yii::warning('Failed to save user: ' . print_r($this->getErrors(), true), __METHOD__);
            return false;
        }

        return true;
    }

    /**
     * Required by EditableIdentityInterface
     *
     * Maps from \nineinchnick\usr\models\ProfileForm attributes to attributes of this model.
     *
     * @see \nineinchnick\usr\models\ProfileForm::attributes()
     *
     * @inheritdoc
     */
    public function identityAttributesMap()
    {
        return [
            'email' => 'email',
        ];
    }

    /**
     * Required by EditableIdentityInterface
     *
     * Allows the profile form to set our attributes.
     *
     * @inheritdoc
     */
    public function setIdentityAttributes(array $profileAttributes)
    {
        $allowedAttributes = $this->identityAttributesMap();
        foreach ($profileAttributes as $name => $value) {
            if (isset($allowedAttributes[$name])) {
                $key = $allowedAttributes[$name];
                $this->$key = $value;
            }
        }

        return true;
    }

    /**
     * Required by EditableIdentityInterface
     *
     * Allows the profile form to get our attributes.
     *
     * @inheritdoc
     */
    public function getIdentityAttributes()
    {
        $allowedAttributes = array_flip($this->identityAttributesMap());
        $result = [];
        foreach ($this->getAttributes() as $name => $value) {
            if (isset($allowedAttributes[$name])) {
                $result[$allowedAttributes[$name]] = $value;
            }
        }

        return $result;
    }

    /**
     * Sets some history dates on the user record before saving.
     *
     * @param bool $insert
     *
     * @return bool
     */
    public function beforeSave($insert)
    {
        if ($insert) {
            $this->created = gmdate('Y-m-d H:i:s');
        } else {
            $this->updated = gmdate('Y-m-d H:i:s');
        }

        return parent::beforeSave($insert);
    }

    /**
     * Required by PasswordHistoryIdentityInterface
     *
     * @inheritdoc
     */
    public function getPasswordDate($password = null)
    {
        // We aren't implementing password histories so...
        if ($password === null) {
            return $this->password_set;
        }

        return null;
    }

    /**
     * Required by PasswordHistoryIdentityInterface
     *
     * @inheritdoc
     */
    public function resetPassword($password)
    {
        $this->password = Yii::$app->security->generatePasswordHash($password);
        $this->password_set = gmdate('Y-m-d H:i:s');
        return $this->save();
    }
}
