<?php

namespace SchemaManager\DbTypes;

use Doctrine\DBAL\Platforms\MySqlPlatform;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class TinyIntType extends Type
{
    const TINYINT = 'tinyInt';

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        if($platform instanceof MySqlPlatform){
            return "TINYINT".($fieldDeclaration['unsigned'] ? ' UNSIGNED' : '');
        }

        return "INTEGER";
    }

    public function getName()
    {
        return self::TINYINT;
    }
}
