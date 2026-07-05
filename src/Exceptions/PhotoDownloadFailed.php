<?php

declare(strict_types=1);

namespace Yeevy\CentrisPasserelle\Exceptions;

use RuntimeException;

/**
 * A photo could not be downloaded or written to disk.
 */
final class PhotoDownloadFailed extends RuntimeException {}
