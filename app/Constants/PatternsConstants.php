<?php

namespace App\Constants;

class PatternsConstants
{
    const FILTER = '/^(nul|nnu)|^(eql|neq|gt|lt|gte|lte|lik)[,]([0-9a-zA-Z-:.@+-_\s]+)$/';
    const SUFFIX = '/^[a-zA-Z0-9_-]*$/';
    const ULID = '/[0-9A-Z]{26}/';
}
