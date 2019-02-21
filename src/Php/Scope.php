<?php

namespace Amz\Code\Php;

interface Scope
{
    /**
     * Public scope for a property or method
     */
    const PUBLIC = 'public';

    /**
     * Private scope for a property or method
     */
    const PRIVATE = 'private';

    /**
     * Protected scope for a property or method
     */
    const PROTECTED = 'protected';
}
