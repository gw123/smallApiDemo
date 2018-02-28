<?php
namespace  api\model;
/***
 *
 * @property integer $is_init
 * */
class  User extends BaseModel{

    public static function getDb()
    {
        return \All::$app->db_user;
    }

    public static function tableName()
    {
        return  'user';
    }

    public function rules()
    {
        return [];
    }


}