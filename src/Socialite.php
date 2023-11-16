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
        $config = $this->getProviderConfig($name);
        $provider = sprintf('Axm\\Socialite\\Provider\\%sProvider', $name);

        if (!class_exists($provider)) {
            throw new \InvalidArgumentException("Provider $provider not supported.");
        }

        return new $provider($config);
    }

    /**
     * @param string $provider
     * @return [type]
     */
    public static function driver(string $provider)
    {
        $instance = self::make();
        $instance->getProvider($provider);
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
        $config = require config()->load(__DIR__ . DIRECTORY_SEPARATOR . 'config.php');
        return $config;
    }
}
