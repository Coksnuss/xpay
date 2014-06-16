<?php
namespace common\gii\generators\model;

/**
 * This generator will generate one or multiple ActiveRecord classes for the specified database table.
 *
 * This modified version adds an option which will add a timestamp behavior for the generated classes.
 *
 * @author Markus Schanz <coksnuss@googlemail.com>
 */
class Generator extends \yii\gii\generators\model\Generator
{
    public $ns = 'common\models';
    public $useTablePrefix = true;
    public $includeTimestampBehavior = true;
    public $createdColumnName = 'created_at';
    public $updatedColumnName = 'updated_at';


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['includeTimestampBehavior'], 'boolean'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function hints()
    {
        return array_merge(parent::hints(), [
            'includeTimestampBehavior' => 'Automatically includes a timestamp behavior to set the created_at and updated_at
                fields automatically. This option will also modify the rules accordingly.',
            'createdColumnName' => 'The column name of the field that is set to the current time when a new record is added.',
            'updatedColumnName' => 'The column name of the field that is set to the current time when a new record is added
                or an existing record is updated.',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function generateRules($table)
    {
        $rules = parent::generateRules($table);

        if ($this->includeTimestampBehavior)
        {
            foreach ($rules as $i => $rule) {
                list($ruleFields, $ruleName) = eval("return {$rule};");

                if ($ruleName === 'required' || $ruleName == 'safe') {
                    if (($key = array_search($this->createdColumnName, $ruleFields)) !== false) {
                        unset($ruleFields[$key]);
                    }

                    if (($key = array_search($this->updatedColumnName, $ruleFields)) !== false) {
                        unset($ruleFields[$key]);
                    }

                    if (empty($ruleFields)) {
                        unset($rules[$i]);
                    } else {
                        $newRuleFields = "['" . implode("', '", $ruleFields) . "']";
                        $rules[$i] = preg_replace('#^\[\[[^\]]+\]#', '[' . $newRuleFields, $rule);
                    }
                }
            }
        }

        return $rules;
    }
}
