<?php


namespace app\console\tool\controllers;


use app\providers\FacedServiceProvider;
use system\config\config;
use system\file;

class FaceToolController
{

    public function solveFaced()
    {
        $date = date('Y-m-d H:i:s');
        foreach ((new FacedServiceProvider())->facedRegister as $sourceClass => $descClass){
            $descClassNameList = explode('\\', $descClass);
            $descClassName = array_pop($descClassNameList);
            $nameSpace = str_replace('\\' . $descClassName, '', $descClass);
            $methods = ($this->resolveClass($sourceClass, $descClass));
            $methodsStr = '';
            foreach ($methods as $method){
                $methodsStr .= '* '. $method .PHP_EOL;
            }
            $template = <<<tem
<?php
/**
 * Created by awesome-cli-tool-faced.
 * Date: {$date}
 */

namespace $nameSpace;

use system\\kernel\\facede;

/**
{$methodsStr}
 */
class {$descClassName} extends facede {
    public function getFacadeAccessor()
    {
        return \\{$sourceClass}::class;
    }
}
tem;
            $writePath = config::env_path(). '\\' . $descClass . '.php';
            $writePath = str_replace('\\', '/', $writePath);
            $writePath = str_replace('//','/', $writePath);
            $file = new file();
            $file->mkdir(dirname($writePath), 0777);
            (new file())->write_file($writePath, $template);
        }
    }

    protected function resolveClass($className, $outPutClass)
    {
        $inputClassName = $className;
        $class = new \ReflectionClass($inputClassName);
        $methods = [];
        foreach ($class->getMethods() as $method){
            if ($method->isPublic()){
                $methodProperty = "@method static \\{$outPutClass} {$method->name}(";
                foreach ($method->getParameters() as $parameter){
                    $className = $parameter->getClass();
                    $parameterName = '$' . $parameter->name;
                    $isDefaultValue = $parameter->isDefaultValueAvailable();
                    if ($className){
                        $parameterName = '\\' . $class->getName() . ' ' . $parameterName;
                    }
                    if ($isDefaultValue){
                        $parameterName .= '=' . var_export($parameter->getDefaultValue(), true);
                    }
                    $methodProperty .= $parameterName . ',';
                }
                $methodProperty = rtrim($methodProperty, ',') . ')';
                $methods[] = $methodProperty;
            }
        }
        return $methods;
    }
}