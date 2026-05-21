<?php

namespace APP\Enums;

enum ArticleStatus: string
{
    case Published = 'published';
    case Unpublished = 'unpublished';
    case Archived = 'archived';
    case Trashed = 'trashed';
}
