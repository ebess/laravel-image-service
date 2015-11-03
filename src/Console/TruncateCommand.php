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
    protected $signature = 'image-service:truncate {--cached} {--filter=}';

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
        if ($this->option('filter')) {
            $this->filesystem->deleteDirectory(config('image-service.path') . '/' . $this->option('filter'));
            $this->info('Deleted all cached images for "'.$this->option('filter').'" filter.');
        } elseif ($this->option('cached')) {
            foreach ($this->filesystem->directories(config('image-service.path')) as $dir) {

                // don't delete the original source image
                if (!preg_match("/original$/mi", $dir)) {
                    $this->filesystem->deleteDirectory($dir);
                }

            }
            $this->info('Deleted all cached images.');
        } else {
            $this->filesystem->deleteDirectory(config('image-service.path'));
            $this->info('All images has been deleted.');
        }
    }
}
