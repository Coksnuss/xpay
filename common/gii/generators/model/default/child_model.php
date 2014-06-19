<?php
/**
 * This is the template for generating the model class of a specified table.
 *
 * @var yii\web\View $this
 * @var yii\gii\generators\model\Generator $generator
 * @var string $tableName full table name
 * @var string $className class name
 */

echo "<?php\n";
?>
namespace <?= $generator->getChildNs() ?>;

use Yii;

/**
 * This is the model class for table "<?= $tableName ?>".
 *
 * Check the base class at <?= $generator->ns . '\\' . $className ?> in order to
 * see the column names and relations.
 */
class <?= $className ?> extends <?= '\\' . ltrim($generator->ns, '\\') . '\\' . $className . "\n" ?>
{

}
