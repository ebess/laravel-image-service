<?php

namespace Ebess\ImageService\Console;

use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\Filesystem;

class TruncateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'image-service:truncate {--cached}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete all saved images.';

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem) {
        parent::__construct();
        $this->filesystem = $filesystem;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if ($this->option('cached')) {
            foreach ($this->filesystem->allDirectories(config('image-service.path')) as $dir) {
                $this->filesystem->deleteDirectory($dir);
            }
            $this->info('Deleted all cached images');
        } else {
            $this->filesystem->deleteDirectory(config('image-service.path'));
            $this->info('All images has been deleted.');
        }
    }
}