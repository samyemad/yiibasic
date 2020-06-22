<?php

namespace app\models;

use Yii;
use creocoder\nestedsets\NestedSetsBehavior;
use yii\db\ActiveRecord;
/**
 * 
 *
 * @property integer $id
 * @property string $name
 * @property integer $tree
 * @property integer $lft
 * @property integer $rgt
 * @property integer $depth
 * @property integer $position
 */
class Category extends ActiveRecord{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'category';
    }

    public function behaviors() {
        return [
            'tree' => [
                'class' => NestedSetsBehavior::className(),
                'treeAttribute' => 'tree',

            ],
        ];
    }
    
     public function transactions()
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_ALL,
        ];
    }

    public static function find()
    {
        return new CategoryQuery(get_called_class());
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['position'], 'default', 'value' => 0],
            [['tree', 'lft', 'rgt', 'depth', 'position'], 'integer'],
            [['name'], 'string', 'max' => 255],
        ];
    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'         => Yii::t('app', 'ID'),
            'name'       => Yii::t('app', 'Name'),
            'tree'       => Yii::t('app', 'Tree'),
            'lft'        => Yii::t('app', 'Lft'),
            'rgt'        => Yii::t('app', 'Rgt'),
            'depth'      => Yii::t('app', 'Depth'),
            'position'   => Yii::t('app', 'Position'),
        ];
    }
    
    
    public function getParentId()
    {
        $parent = $this->parent;
        return $parent ? $parent->id : null;
    }

    
    public function getParent()
    {
        return $this->parents(1)->one();
    }
    
    
    public static function getTree($node_id = 0)
    {
        // don't include children and the node
        $children = [];

        if ( ! empty($node_id))
            $children = array_merge(
                self::findOne($node_id)->children()->column(),
                [$node_id]
                );

        $rows = self::find()->
            select('id, name, depth')->
            where(['NOT IN', 'id', $children])->
            orderBy('tree, lft, position')->
            all();

        $return = [];
        foreach ($rows as $row)
            $return[$row->id] = str_repeat('-', $row->depth) . ' ' . $row->name;

        return $return;
    }
}
