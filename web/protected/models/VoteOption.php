<?php

class VoteOption extends CActiveRecord
{
    /**
     * The followings are the available columns in table 'tbl_comment':
     * @var integer $id
     * @var integer $id_vote
     * @var string $name
     */


    public function relations()
    {
        return [
            'vote' => array(self::BELONGS_TO, 'Vote', 'id_vote'),
        ];
    }

    /**
     * Returns the static model of the specified AR class.
     * @return CActiveRecord the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'vote_option';
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'Id'),
            'name' => Yii::t('app', 'Name'),
        ];
    }

    public function load($id)
    {
        return $this->findByPk($id);
    }

    public function delete()
    {
        $this->is_deleted = 1;
        return $this->save();
    }


    public function getByVote($id)
    {
        return $this->findAll('id_vote=:id_vote', ['id_vote' => $id]);
    }

}