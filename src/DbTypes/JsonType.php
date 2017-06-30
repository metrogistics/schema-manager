<?php

namespace SchemaManager\DbTypes;

use Doctrine\DBAL\Platforms\MySqlPlatform;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class JsonType extends Type
{
    const JSON = 'json';

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        if($platform instanceof MySqlPlatform){
            return "JSON";
        }

        return "TEXT";
    }

    public function getName()
    {
        return self::JSON;
    }
}
