<?php

/**
 * @copyright Copyright (c) 2018 Carsten Brandt <mail@cebe.cc> and contributors
 * @license https://github.com/cebe/yii2-openapi/blob/master/LICENSE
 */

namespace cebe\yii2openapi\lib;

use cebe\openapi\spec\OpenApi;
use cebe\openapi\spec\Operation;
use cebe\yii2openapi\lib\items\FractalAction;
use cebe\yii2openapi\lib\items\RouteData;
use yii\helpers\Inflector;
use function in_array;

class FractalGenerator extends UrlGenerator
{
    /**
     * @var string
     */
    private $transformerNamespace;

    /**
     * @var bool
     */
    private $singularResourceKeys;

    public function __construct(
        OpenApi $openApi,
        string $modelNamespace,
        array $controllerMap,
        string $transformerNamespace,
        bool $singularResourceKeys = false
    ) {
        parent::__construct($openApi, $modelNamespace, $controllerMap);
        $this->transformerNamespace = $transformerNamespace;
        $this->singularResourceKeys = $singularResourceKeys;
    }

    /**
     * @param string                                $method
     * @param \cebe\openapi\spec\Operation          $operation
     * @param \cebe\yii2openapi\lib\items\RouteData $routeData
     * @return \cebe\yii2openapi\lib\items\FractalAction
     */
    protected function prepareAction(string $method, Operation $operation, RouteData $routeData)
    {
        $actionType = $this->resolveActionType($routeData, $method);
        $modelClass = SchemaResponseResolver::guessModelClass($operation, $actionType);
        $expectedRelations = in_array($actionType, ['list', 'view'])
            ?  SchemaResponseResolver::guessResponseRelations($operation)
            : [];
        // fallback to known model class on same URL
        if ($modelClass === null && isset($this->knownModelClasses[$routeData->path])) {
            $modelClass = $this->knownModelClasses[$routeData->path];
        } else {
            $this->knownModelClasses[$routeData->path] = $modelClass;
        }
        if ($routeData->isRelationship()) {
            $relatedClass = $modelClass;
            $transformerClass = $modelClass !== null
                ? $this->transformerNamespace . '\\' . Inflector::id2camel($modelClass, '_').'Transformer'
                : null;
            $controllerId = $routeData->controller;
            $modelClass = Inflector::id2camel(Inflector::singularize($controllerId));
            if (isset($this->controllerMap[$modelClass])) {
                $controllerId = Inflector::camel2id($this->controllerMap[$modelClass]);
            }
        } else {
            $relatedClass = null;
            if ($modelClass === null || !$routeData->isModelBasedType()) {
                $controllerId = $routeData->controller;
            } elseif (isset($this->controllerMap[$modelClass])) {
                $controllerId = Inflector::camel2id($this->controllerMap[$modelClass]);
            } else {
                $controllerId = Inflector::camel2id($modelClass, '-');
            }
            $transformerClass = $modelClass !== null
                ? $this->transformerNamespace . '\\' . Inflector::id2camel($modelClass, '_').'Transformer'
                : null;
        }

        if ($routeData->type === RouteData::TYPE_RESOURCE_OPERATION && !$modelClass) {
            $modelClass = Inflector::id2camel(Inflector::singularize($controllerId));
            if (isset($this->controllerMap[$modelClass])) {
                $controllerId = Inflector::camel2id($this->controllerMap[$modelClass]);
            }
        }

        return new FractalAction([
            'singularResourceKey'=> $this->singularResourceKeys,
            'type' => $routeData->type,
            'id' => $routeData->isNonCrudAction()?trim("{$actionType}-{$routeData->action}", '-'):"$actionType{$routeData->action}",
            'controllerId' => $controllerId,
            'urlPath' => $routeData->path,
            'requestMethod' => strtoupper($method),
            'urlPattern' => $routeData->pattern,
            'idParam' => $routeData->idParam ?? null,
            'parentIdParam' => $routeData->parentParam ?? null,
            'params' => $routeData->params,
            'modelName' => $modelClass,
            'relatedModel'=>$relatedClass,
            'modelFqn' => $modelClass !== null
                ? $this->modelNamespace . '\\' . Inflector::id2camel($modelClass, '_')
                : null,
            'transformerFqn'=> $transformerClass,
            'expectedRelations' => $expectedRelations
        ]);
    }
}
