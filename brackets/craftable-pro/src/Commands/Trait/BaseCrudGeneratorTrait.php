<?php

namespace Brackets\CraftablePro\Commands\Trait;

use Carbon\Carbon;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Str;

trait BaseCrudGeneratorTrait
{
    /**
     * Append web route file with content.
     *
     * @param  string  $defaultContent
     * @return bool
     *
     * @throws FileNotFoundException
     */
    protected function appendIfNotAlreadyAppended($path, $content, $defaultContent = '<?php'.PHP_EOL.PHP_EOL)
    {
        if (! $this->files->exists($path)) {
            $this->makeDirectory($path);
            $this->files->put($path, $defaultContent.$content);
        } elseif (! $this->alreadyAppended($path, $content)) {
            $this->files->append($path, $content);
        } else {
            return false;
        }

        return true;
    }

    /**
     * Check if alredy appened content in web route file.
     *
     * @return bool
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function alreadyAppended($path, $content)
    {
        if (strpos($this->files->get($path), $content) !== false) {
            return true;
        }

        return false;
    }

    /**
     * Helper function check and create directory for path.
     */
    protected function makeDirectory($path): mixed
    {
        if (! $this->files->isDirectory(dirname($path))) {
            $this->files->makeDirectory(dirname($path), 0777, true, true);
        }

        return $path;
    }

    /**
     * Return vue template file for current view in param.
     *
     * @return string
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    private function getVueFile($view, $content = true)
    {
        $stub_path = __DIR__.'/../../../resources/views/generator/views/'."{$view}.vue";

        return $content ? $this->files->get($stub_path) : $stub_path;
    }

    /**
     * Return typescript file types.d.ts
     *
     * @return string
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    private function getTypeScriptFile()
    {
        $path = __DIR__.'/../../../resources/views/generator/views/types.d.ts';

        return $this->files->get($path);
    }

    /**
     * Return Controller path.
     */
    protected function getControllerPath($name): string
    {
        return $this->makeDirectory(app_path($this->getNamespacePath($this->controllerNamespace)."{$name}.php"));
    }

    /**
     * Return model path.
     */
    protected function getModelPath(string $name): string
    {
        return $this->makeDirectory(app_path($this->getNamespacePath($this->modelNamespace)."{$name}.php"));
    }

    /**
     * Return reqeuest path.
     */
    private function getRequestPath(string $name): string
    {
        return $this->makeDirectory(app_path($this->getNamespacePath($this->requestNamespace)."{$name}.php"));
    }

    /**
     * Return resource vue path.
     */
    private function getResourcesPath(string $name, string $fileName): string
    {
        return $this->makeDirectory(resource_path("js/craftable-pro/Pages/{$name}/{$fileName}.vue"));
    }

    /**
     * Return migration path and get migration file name.
     */
    private function getMigrationPath(string $name): string
    {
        $datetime = str_replace(['-', ' ', ':'], ['_', '_', ''], Carbon::now()->toDateTimeString());
        $this->migration_name = $datetime."_add_permissions_to_{$name}.php";

        return database_path('migrations/'.$this->migration_name);
    }

    private function getExportPath(string $name): string
    {
        return $this->makeDirectory(app_path($this->getNamespacePath($this->exportNamespace)."{$name}.php"));
    }

    /**
     * Get the path from namespace.
     */
    private function getNamespacePath($namespace): string
    {
        $str = Str::start(Str::finish(Str::after($namespace, 'App'), '\\'), '\\');

        return str_replace('\\', '/', $str);
    }
}
