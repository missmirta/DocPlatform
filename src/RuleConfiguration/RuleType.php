<?php

declare(strict_types=1);

namespace DocPlatform\RuleConfiguration;

enum RuleType: string
{
    case MaxSizeRule             = 'MaxSizeRule';
    case ProhibitedWordsRule     = 'ProhibitedWordsRule';
    case RequiredMetadataRule    = 'RequiredMetadataRule';
    case AllowedContentTypeRule  = 'AllowedContentTypeRule';
    case MetadataValueFormatRule = 'MetadataValueFormatRule';
}
