<?php

namespace SchemaManager\DbTypes;

use Doctrine\DBAL\Platforms\MySqlPlatform;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class TimestampType extends Type
{
    const TIMESTAMP = 'timestamp';

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        // dump($fieldDeclaration);
        if($platform instanceof MySqlPlatform){
            $nullable = !$fieldDeclaration['notnull'];

            return "TIMESTAMP".($nullable ? ' NULL' : '');
        }

        return "VARCHAR(11)";
    }

    public function getName()
    {
        return self::TIMESTAMP;
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform)
    {
        return true;
    }
}
