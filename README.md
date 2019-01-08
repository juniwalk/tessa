
### JuniWalk / Tessa TODO

- Bundles can depend on other bundles
- Circular reference must be forbidden
- Move Nette stuff into Nette bridge? (make dependency on nette optional)


### Structure

- AssetManager
	- Compiler
		- $namingStyle
		- $outputDir
		- $joinFiles
	- Bundles
		- BundleA
			- FileAsset
		- BundleB
			- FileAsset
- AssetControl


### Console commands

- Compile all bundles (warm up all)
- Compile specific bundle (warm up)
- List bundles


### Neon configuration
```yaml
extensions:
    tessa: JuniWalk\Tessa\DI\TessaExtension

tessa:
    outputDir: %wwwDir%/static
    checkLastModified: FALSE
    filters:
        - JuniWalk\Tessa\Filters\UrlFixerFilter(%wwwDir%)

    default:
        joinFiles: false
        assets:
            - %wwwDir%/vendor/font-awesome/css/font-awesome.min.css
            - %wwwDir%/vendor/jquery/dist/jquery.min.js
            - %wwwDir%/vendor/bootstrap/dist/css/bootstrap.min.css
            - %wwwDir%/vendor/bootstrap/dist/js/bootstrap.min.js
            - %wwwDir%/vendor/nette-forms/src/assets/netteForms.min.js
            - %wwwDir%/assets/style.css
            - %wwwDir%/assets/main.js

    frontpage:
        extend: default
        assets: []
```
