<?php
namespace  api\model;
/***
 *
 * @property integer $is_init
 * */
class  Article extends BaseModel{

    public static function getDb()
    {
        return \All::$app->db_user;
    }

    public static function tableName()
    {
        return  'user_note';
    }

    public function rules()
    {
        return [
            ['title','string' ,'max'=>'128'],
            ['content','string' ]
        ];
    }


}