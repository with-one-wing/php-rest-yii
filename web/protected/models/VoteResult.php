<?php

class VoteResult extends CActiveRecord
{
    /**
     * The followings are the available columns in table 'tbl_comment':
     * @var integer $id_option
     * @var integer $id_user
     */

    public function relations()
    {
        return [
            'option' => array(self::BELONGS_TO, 'VoteOption', 'id_option'),
            'user' => array(self::BELONGS_TO, 'User', 'id_user'),
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
        return 'vote_result';
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

    public function getResultByVote($voteId)
    {
        $res = Yii::app()->db->createCommand()
            ->select('vo.id, vo.name, COUNT(vr.id_user) AS `count`')
            ->from('vote_option vo')
            ->leftJoin('vote_result vr', 'vo.id = vr.id_option AND vo.id_vote='.$voteId)
            ->group('vo.name')
            ->queryAll();

        $count = $this->count();

        foreach ($res as &$item) {
            $item['percent'] = round($item['count'] * 100 / $count);
        }
        return $res;
    }

    public function isVoted($voteId, $userId)
    {
        return $this->count(
            'id_user = :id_user AND id_vote = :id_vote',
            [
                'id_vote' =>  $voteId,
                'id_user' =>  $userId,
            ]
        );
    }

    public function addVote($voteId, $userId, $optionId)
    {
        $this->id_vote = $voteId;
        $this->id_user = $userId;
        $this->id_option = $optionId;
        return $this->save();
    }

    public function getUserMessage()
    {

    }

}