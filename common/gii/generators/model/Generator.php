<?php
namespace common\gii\generators\model;

use Yii;
use yii\gii\CodeFile;

/**
 * This generator will generate one or multiple ActiveRecord classes for the specified database table.
 *
 * This modified version adds an option which will add a timestamp behavior for the generated classes.
 *
 * @author Markus Schanz <coksnuss@googlemail.com>
 */
class Generator extends \yii\gii\generators\model\Generator
{
    public $ns = 'common\models\base';
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

    /**
     * @inheritdoc
     */
    protected function getTableNames()
    {
        $tableNames = parent::getTableNames();

        if (($key = array_search('migration', $tableNames)) !== false) {
            unset($tableNames[$key]);
            return array_values($tableNames);
        }

        return $tableNames;
    }

    /**
     * @return string The namespace for the child class which is used by the
     * developers for non-automatically generated code.
     */
    public function getChildNs()
    {
        return \yii\helpers\StringHelper::dirname($this->ns);
    }

    /**
     * @inheritdoc
     */
    public function generate()
    {
        $files = parent::generate();

        /* array_walk($files, function ($v, $k) {
            if ($v->operation == \yii\gii\CodeFile::OP_SKIP) {
                $v->operation = \yii\gii\CodeFile::OP_CREATE;
            }
        }); */

        $db = $this->getDbConnection();
        foreach ($this->getTableNames() as $tableName) {
            $className = $this->generateClassName($tableName);
            $params = [
                'tableName' => $tableName,
                'className' => $className,
            ];
            $files[] = new CodeFile(
                Yii::getAlias('@' . str_replace('\\', '/', $this->getChildNs())) . '/' . $className . '.php',
                $this->render('child_model.php', $params)
            );
        }

        return $files;
    }
}
