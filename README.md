<!-- markdownlint-disable no-inline-html -->
<p align="center">
	<a href="https://packagist.org/packages/axm/Socialite"
		><img
			src="https://poser.pugx.org/axm/Socialite/v/stable"
			alt="Latest Stable Version"
	/></a>
	<a href="https://packagist.org/packages/axm/Socialite"
		><img
			src="https://poser.pugx.org/axm/Socialite/downloads"
			alt="Total Downloads"
	/></a>
	<a href="https://packagist.org/packages/axm/Socialite"
		><img
			src="https://poser.pugx.org/axm/Socialite/license"
			alt="License"
	/></a>
</p>
<br />
<br />


## 📦 Installation

You can also use [Composer](https://getcomposer.org/) to install Axm in your project quickly.

```bash
composer require axm/Socialite
```

## Socialite PHP Library

Socialite is a PHP library that provides Socialite localization support. It includes an interface `SocialiteInterface` defining the methods required for a Socialite translator and a class `Socialite` implementing this interface for handling Socialite localization.

### SocialiteInterface

 `getLocale(): string`

Returns the current locale.

`trans(string $key, array $params = []): string`

Translates a key with optional parameters.

- `$key`: The translation key.
- `$params`: Optional parameters for string interpolation.

Returns the translated message.

### Socialite Class

### Singleton Pattern

The `Socialite` class follows the singleton pattern, ensuring only one instance is created throughout the application.

### Methods
`make(): SocialiteInterface`

Static method to get an instance of the `Socialite` class.

`setLocale(): void`

Sets the current locale and reloads translations.

`getLocale(): string`

Gets the current locale.

`trans(string $key, array $params = []): string`

Translates a key with optional parameters.

- `$key`: The translation key.
- `$params`: Optional parameters for string interpolation.

Returns the translated message.

`loadTranslationsFromFile(): void`

Loads translations from Socialite files. This method throws an `AxmException` if an error occurs while loading Socialite files.

### Configuration

- `DEFAULT_SocialiteUAGE`: The default Socialite if no locale is set.

### Usage

```php
// Get an instance of Socialite
$Socialite = Socialite::make();

$Socialite->trans('file.message');

//or

// Translate a key
$Socialite->trans('file.message', ['param1', 'param2']);
```
