<?php

declare(strict_types=1);

namespace WordPressApi\Exceptions;

/**
 * Thrown when the WordPress API cannot be reached at the transport level
 * (connection refused, DNS failure, timeout — e.g. the VPN route is down).
 *
 * Distinct from {@see WordPressApiException} (HTTP-level/server errors) so
 * callers can react specifically to "backend unreachable" — e.g. serve a
 * cached or degraded response instead of crashing.
 */
class ConnectionException extends WordPressApiException
{
}
