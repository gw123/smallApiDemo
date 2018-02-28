<?php
namespace api\model;


class ChartModel  extends \yii\db\ActiveRecord
{
    public static function getDb()
    {
        return parent::getDb(); // TODO: Change the autogenerated stub
    }

    public function rules()
    {
        $rules = [
            [['content','name'] , 'required'],
            [['user_id','id'],'integer'],
            [['created_time','updated_time'],'string'],
            ['name','string','length'=>[1,32]],
            ['content','string','length'=>[2,1024]]
        ];
        return $rules; // TODO: Change the autogenerated stub
    }

    public static function tableName()
    {
        return 'chart';
    }

    public function load($data, $formName = null)
    {
        $this->updated_time = date('Y-m-d H:i:s',time());
        return parent::load($data, $formName); // TODO: Change the autogenerated stub
    }



    public function save($runValidation = true, $attributeNames = null)
    {

        return parent::save($runValidation, $attributeNames); // TODO: Change the autogenerated stub
    }
}