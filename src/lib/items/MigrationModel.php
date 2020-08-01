<?php

namespace cebe\yii2openapi\lib\items;

use yii\base\BaseObject;
use yii\helpers\Inflector;
use function array_push;
use function array_unshift;
use function implode;
use function is_string;
use const PHP_EOL;

/**
 * @property-read string $tableAlias
 * @property-read string $upCodeString
 * @property-read string $downCodeString
 * @property-read string $fileClassName
 */
class MigrationModel extends BaseObject
{
    /**
     * @var string
     **/
    public $fileName;

    /**
     * @var array
     **/
    public $upCodes = [];

    /**
     * @var array
     **/
    public $downCodes = [];

    /**
     * @var array
     **/
    public $dependencies = [];

    /**
     * @var string
     **/
    private $fileClassName = '';

    /**
     * @var \cebe\yii2openapi\lib\items\DbModel
     */
    private $model;

    public function __construct(DbModel $model, bool $isFresh = true, $config = [])
    {
        parent::__construct($config);
        $this->model = $model;
        $this->fileName = $isFresh
            ? 'create_table_' . $model->tableName
            : 'change_table_' . $model->tableName;
    }

    public function getUpCodeString():string
    {
        return !empty($this->upCodes) ? implode(PHP_EOL, $this->upCodes) : '';
    }

    public function getDownCodeString():string
    {
        return !empty($this->downCodes) ? implode(PHP_EOL, $this->downCodes) : '';
    }

    public function notEmpty():bool
    {
        return !empty($this->upCodes) && !empty($this->downCodes);
    }

    public function getTableAlias():string
    {
        return $this->model->tableAlias;
    }

    public function getFileClassName():string
    {
        return $this->fileClassName;
    }

    public function getDescription():string
    {
        return 'Table for '.$this->model->name;
    }

    public function makeClassNameByTime(int $index, ?string $nameSpace = null, ?string $date = null):string
    {
        if ($nameSpace) {
            $m = sprintf('%s%04d', ($date ?: date('ymdH')), $index);
            $this->fileClassName = "M{$m}" . Inflector::id2camel($this->fileName, '_');
        } else {
            $m = sprintf('%s%04d', ($date ?: date('ymd_H')), $index);
            $this->fileClassName = "m{$m}_" . $this->fileName;
        }
        return $this->fileClassName;
    }

    /**Add up code, by default at bottom
     * @param array|string $code
     * @param bool         $toTop
     * @return $this
     */
    public function addUpCode($code, bool $toTop = false):MigrationModel
    {
        $code = is_string($code) ? [$code] : $code;
        if ($toTop === true) {
            array_unshift($this->upCodes, ...$code);
        } else {
            array_push($this->upCodes, ...$code);
        }
        return $this;
    }

    /**add down code, by default to top
     * @param array|string $code
     * @param bool         $toBottom
     * @return $this
     */
    public function addDownCode($code, bool $toBottom = false):MigrationModel
    {
        $code = is_string($code) ? [$code] : $code;
        if ($toBottom === true) {
            array_push($this->downCodes, ...$code);
        } else {
            array_unshift($this->downCodes, ...$code);
        }
        return $this;
    }
}