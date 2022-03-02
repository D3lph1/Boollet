<?php
declare(strict_types=1);

namespace D3lph1\Boollet\Label;

use D3lph1\Boollet\Label\DefaultSequentialExpressionLabelPool;
use D3lph1\Boollet\Structure\Expression\Expression;

abstract class ExpressionLabelPool
{
    private static ?ExpressionLabelPool $defaultPool = null;

    public static function getDefaultPool(): ExpressionLabelPool
    {
        if (self::$defaultPool === null) {
            self::$defaultPool = new DefaultSequentialExpressionLabelPool();
        }

        return self::$defaultPool;
    }

    public abstract function getLabelFor(?Expression $expression): string;
}
