<?php

namespace SchemaManager\DbTypes;

use Doctrine\DBAL\Platforms\MySqlPlatform;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class CharType extends Type
{
    const CHAR = 'char';

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        if($platform instanceof MySqlPlatform){
            return "CHAR({$fieldDeclaration['length']})";
        }

        return "VARCHAR({$fieldDeclaration['length']})";
    }

    public function getName()
    {
        return self::CHAR;
    }
}
