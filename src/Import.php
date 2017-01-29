<?php
declare(strict_types = 1);

namespace Fluent;

/**
 * Import another configuration file.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class Import
{
    /**
     * @var string
     */
    private $resource;

    public function __construct(string $resource)
    {
        $this->resource = $resource;
    }

    public function getResource() : string
    {
        return $this->resource;
    }
}
