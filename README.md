## Installation

To install latest version of `juniwalk/tessa` use [Composer](https://getcomposer.org).

```bash
composer require juniwalk/tessa
```


### Usage

Check [config.neon](tests/config.neon) from tests for more details.

```neon
extensions:
	tessa: JuniWalk\Tessa\DI\TessaExtension

tessa:
	outputDir: %wwwDir%/static
	checkLastModified: true

	default:
		defer: true
		assets:
			- %wwwDir%/vendor/font-awesome/css/font-awesome.min.css
			- %wwwDir%/vendor/jquery/dist/jquery.min.js
			- %wwwDir%/vendor/bootstrap/dist/css/bootstrap.min.css
			- %wwwDir%/vendor/bootstrap/dist/js/bootstrap.min.js
			- %wwwDir%/vendor/nette-forms/src/assets/netteForms.min.js
			- %wwwDir%/assets/style.css
			- %wwwDir%/assets/index.js

	frontend:
		extend: default
		assets: []

	backend:
		extend: default
		assets:
			- %wwwDir%/assets/admin.js

	fullcalendar:
		defer: true
		assets:
			- %wwwDir%/vendor/fullcalendar/index.global.min.js
```

Include `AssetManager` trait to get access to Tessa component.

```php
use JuniWalk\Tessa\Attributes\AssetBundle;
use JuniWalk\Tessa\Traits\AssetManager;

#[AssetBundle('frontend')]
class TessaPresenter extends Presenter
{
	use AssetManager;

	#[AssetBundle('calendar')]
	public function actionCalendar(): void {}
}
```

Then render styles and scripts in template from Presenter attributes.

```latte
<!DOCTYPE html>
<html>
<head>

	<title>Tessa example</title>
	{control tessa:css}
	{control tessa:js}

</head>
<body>

	<!-- your page content -->

</body>
</html>
```

Alternatively you can render specific part of bundle.

```latte
<!DOCTYPE html>
<html>
<head>

	<title>Render just fullcalendar scripts</title>
	{control tessa:js 'fullcalendar'}

</head>
<body>

	<!-- your page content -->

</body>
</html>
```
