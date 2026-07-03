<?php

declare(strict_types=1);

namespace DocPlatform\RuleConfiguration;

enum SchemaField: string
{
    case Type        = 'type';
    case Label       = 'label';
    case Description = 'description';
    case Parameters  = 'parameters';
    case Defaults    = 'defaults';
}
