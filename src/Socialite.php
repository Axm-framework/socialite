<?php

namespace Axm\Socialite;


class Socialite
{
    /**
     * @var [type]
     */
    private static $instance;

    /**
     * @var array
     */
    protected static $tokens = [];

    /**
     * @var string
     */
    protected string $providerName = '';

    /**
     */
    private function __construct()
    {
    }

    /**
     * @return [type]
     */
    public static function make()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Returns a new instance of a provider's adapter by name
     *
     * @param string $name
     * @throws InvalidArgumentException
     * @return object
     */
    public function getProvider(string $name): object
    {
        $this->providerName = $name;
        $config = $this->getProviderConfig($name);
        $provider = sprintf('Axm\\Socialite\\Providers\\%sProvider', ucfirst($name));

        if (!class_exists($provider)) {
            throw new \InvalidArgumentException("Provider $provider not supported.");
        }

        return new $provider($config);
    }

    /**
     * @param string $provider
     * @return [type]
     */
    public static function driver(string $provider): object
    {
        $instance = self::make();
        return $instance->getProvider($provider);
    }

    /**
     * @return array
     */
    public function getProviderConfig(): array
    {
        $tokens = $this->openConfig();
        return $tokens;
    }

    /**
     * @return [type]
     */
    public function openConfig(): array
    {
        $file = __DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php';
        if (!is_file($file)) {
            throw new \InvalidArgumentException("The configuration file $file does not exist.");
        }

        $config = require($file);
        return $config[$this->providerName];
    }
}
