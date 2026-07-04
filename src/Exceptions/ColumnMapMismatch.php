<?php

declare(strict_types=1);

namespace Yeevy\CentrisPasserelle\Exceptions;

use RuntimeException;

/**
 * The snapshot no longer lines up with the configured column map —
 * the feed structure has probably changed. Importing anyway would
 * silently write shifted, garbage data.
 */
final class ColumnMapMismatch extends RuntimeException {}
