<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Foundation\Console\ServeCommand as BaseServeCommand;

use function Laravel\Prompts\info;

final class ServeCommand extends BaseServeCommand
{
    public function handle(): int
    {
        $base = sprintf('http://%s:%s', $this->host(), $this->port());

        info(sprintf('Swagger UI: %s/docs/api', $base));
        info(sprintf('OpenAPI JSON: %s/docs/api.json', $base));

        return parent::handle();
    }
}
