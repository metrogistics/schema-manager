<?php

namespace SchemaManager\DbTypes;

use Doctrine\DBAL\Platforms\MySQL57Platform;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class JsonType extends Type
{
    const JSON = 'json';

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        if($platform instanceof MySQL57Platform){
            return "JSON";
        }

        return "TEXT";
    }

    public function getName()
    {
        return self::JSON;
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform)
    {
        return true;
    }
}
