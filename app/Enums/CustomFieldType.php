<?php

namespace App\Enums;

enum CustomFieldType: string
{
    case TEXT = 'text';
    case TEXTAREA = 'textarea';
    case SELECT = 'select';
    case CHECKBOX = 'checkbox';
    case RADIO = 'radio';
    case NUMBER = 'number';
    case DATE = 'date';
}
