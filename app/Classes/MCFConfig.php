<?php
/*
 * This file is part of the MyChiaFarm project.
 *
 *   (c) Lozovoy Vyacheslav <lozovoyv@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace App\Classes;

use Illuminate\Support\Arr;
use Symfony\Component\Yaml\Yaml;

class MCFConfig
{
    /** @var string Filename to store config and load from */
    protected string $filename;

    /** @var array MCF config cache */
    protected array $config = [];

    /**
     * MCF config constructor.
     *
     * @param string $filename
     *
     * @return  void
     */
    public function __construct(string $filename)
    {
        $this->filename = $filename;

        if (file_exists($this->filename)) {
            $content = file_get_contents($this->filename);
            if (!empty($content)) {
                $this->config = Yaml::parse($content);
            }
        }
    }

    /**
     * Get MCF config value or whole config set.
     *
     * @param string|null $key
     *
     * @return  mixed
     */
    public function get(?string $key = null): mixed
    {
        if ($key === null) {
            return $this->config;
        }

        return Arr::get($this->config, $key);
    }

    /**
     * Set MCF config values.
     *
     * @param array $values
     *
     * @return  void
     */
    public function set(array $values): void
    {
        $values = Arr::dot($values);

        foreach ($values as $key => $value) {
            Arr::set($this->config, $key, $value);
        }

        $this->saveConfig();
    }

    /**
     * Replace MCF config values.
     *
     * @param array $values
     *
     * @return  void
     */
    public function replaceAll(array $values): void
    {
        $values = Arr::dot($values);
        $config = [];

        foreach ($values as $key => $value) {
            Arr::set($config, $key, $value);
        }

        $this->config = $config;
        $this->saveConfig();
    }

    /**
     * Save MCF config to file.
     *
     * @return  void
     *
     * @internal
     */
    protected function saveConfig(): void
    {
        file_put_contents($this->filename, Yaml::dump($this->config));
    }
}
