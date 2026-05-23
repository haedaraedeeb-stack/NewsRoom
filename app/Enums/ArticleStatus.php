<?php

namespace APP\Enums;

enum ArticleStatus: string
{
    case Published = 'published';
    case Draft = 'draft';
    case Archived = 'archived';
}
