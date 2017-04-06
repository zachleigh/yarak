<?php

namespace Yarak\tests\temp;

use Yarak\Console\Command;

class OptArray extends Command
{
    /**
     * The command signature.
     *
     * @var string
     */
    protected $signature = 'opt:array {--opt=*}';

    /**
     * The command description.
     *
     * @var string
     */
    protected $description = 'DESCRIPTION';

    /**
     * Handle the command.
     */
    protected function handle()
    {
        $data = json_encode([
            'arguments' => $this->argument(),
            'options'   => $this->option(),
        ]);

        $this->output->write($data);
    }
}
