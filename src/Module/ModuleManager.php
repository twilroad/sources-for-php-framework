<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2016, notadd.com
 * @datetime 2016-12-13 21:05
 */
namespace Notadd\Foundation\Module;

use Illuminate\Container\Container;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Filesystem\Filesystem;
use Notadd\Foundation\Module\Repositories\AssetsRepository;
use Notadd\Foundation\Module\Repositories\MenuRepository;
use Notadd\Foundation\Module\Repositories\ModuleRepository;
use Notadd\Foundation\Module\Repositories\PageRepository;

/**
 * Class ModuleManager.
 */
class ModuleManager
{
    /**
     * Container instance.
     *
     * @var \Illuminate\Container\Container|\Notadd\Foundation\Application
     */
    protected $container;

    /**
     * @var \Illuminate\Support\Collection
     */
    protected $excepts;

    /**
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $file;

    /**
     * @var \Illuminate\Contracts\Config\Repository
     */
    private $configuration;

    /**
     * @var \Notadd\Foundation\Module\Repositories\ModuleRepository
     */
    protected $repository;

    /**
     * ModuleManager constructor.
     *
     * @param \Illuminate\Container\Container         $container
     * @param \Illuminate\Contracts\Config\Repository $configuration
     * @param \Illuminate\Filesystem\Filesystem       $files
     */
    public function __construct(Container $container, Repository $configuration, Filesystem $files)
    {
        $this->configuration = $configuration;
        $this->container = $container;
        $this->excepts = collect();
        $this->file = $files;
    }

    /**
     * @return \Notadd\Foundation\Module\Repositories\ModuleRepository
     */
    public function modules()
    {
        if (!$this->repository instanceof ModuleRepository) {
            $this->repository = new ModuleRepository($this->container, $this->file, collect($this->file->directories($this->getModulePath())));
        }

        return $this->repository;
    }

    /**
     * Get a module by name.
     *
     * @param $name
     *
     * @return \Notadd\Foundation\Module\Module
     */
    public function get($name)
    {
        return $this->repository->get($name);
    }

    /**
     * Module path.
     *
     * @return string
     */
    public function getModulePath(): string
    {
        return $this->container->basePath() . DIRECTORY_SEPARATOR . $this->configuration->get('module.directory');
    }

    /**
     * Check for module exist.
     *
     * @param $name
     *
     * @return bool
     */
    public function has($name)
    {
        return $this->repository->has($name);
    }

    /**
     * @return array
     */
    public function getExcepts()
    {
        return $this->excepts->toArray();
    }

    /**
     * @return \Notadd\Foundation\Module\Repositories\MenuRepository
     */
    public function menus()
    {
        $collection = collect();
        $this->repository->enabled()->each(function (Module $module) use ($collection) {
            $collection->put($module->identification(), $module->get('menus', []));
        });

        return new MenuRepository($collection);
    }

    /**
     * @return \Notadd\Foundation\Module\Repositories\PageRepository
     */
    public function pages()
    {
        $collection = collect();
        $this->repository->enabled()->each(function (Module $module) use ($collection) {
            $collection->put($module->identification(), $module->get('pages', []));
        });

        return new PageRepository($collection);
    }

    /**
     * @return \Notadd\Foundation\Module\Repositories\AssetsRepository
     */
    public function assets()
    {
        $collection = collect();
        $this->repository->enabled()->each(function (Module $module) use ($collection) {
            $collection->put($module->identification(), $module->get('assets', []));
        });

        return new AssetsRepository($collection);
    }

    /**
     * @param $excepts
     */
    public function registerExcept($excepts)
    {
        foreach ((array)$excepts as $except) {
            $this->excepts->push($except);
        }
    }

    /**
     * @param       $method
     * @param array $arguments
     *
     * @return mixed
     * @throws \Exception
     */
    public function __call($method, array $arguments)
    {
        try {
            return $this->repository->{$method}($arguments);
        } catch (\Exception $exception) {
            throw $exception;
        }
    }
}
