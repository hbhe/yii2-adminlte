<?php
namespace common\models;
use Yii;

/**
 * This is the ActiveQuery class for [[Member]].
 *
 * @see Member
 */
class MemberQuery extends \yii\db\ActiveQuery
{
    public function today()
    {
        $this->andWhere(['>=', 'created_at', date('Y-m-d H:i:s', strtotime('today midnight'))]);
        return $this;
    }

    public function yesterday()
    {
        $this->andWhere(['>=', 'created_at', date('Y-m-d H:i:s', strtotime('yesterday midnight'))]);
        $this->andWhere(['<', 'created_at', date('Y-m-d H:i:s', strtotime('today midnight'))]);
        return $this;
    }

    // 本月
    public function month()
    {
        $monthString = date('Y-m');
        $firstDay = date('Y-m-01', strtotime($monthString));
        $lastDay = date('Y-m-t', strtotime($monthString));
        $this->andWhere(['>=', 'created_at', $firstDay . " 00:00:00"]);
        $this->andWhere(['<=', 'created_at', $lastDay . " 23:59:59"]);
        return $this;
    }

    public function active()
    {
        $this->andWhere(['status' => Member::STATUS_ACTIVE]);
        return $this;
    }

    /**
     * @inheritdoc
     * @return Member[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Member|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
