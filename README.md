
JuniWalk / Tessa
################

- Bundles can depend on other bundles
- Circular reference must be forbidden
- Move Nette stuff into Nette bridge? (make dependency on nette optional)


Structure
#########

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


Console commands
################

- Compile all bundles (warm up all)
- Compile specific bundle (warm up)
- List bundles


Neon configuration
##################

extension:
	tessa: JuniWalk\Tessa\DI\TessaExtention

tessa:
	naming: "{type}{bundle}-{hash}-{filename}"
	outputDir: "%wwwDir%/static/{bundle}/{type}"
	joinFiles: true

	filters:
		- @filterServiceName
		- App\Services\RelativeUrlFilter(%wwwDir%)
		- JuniWalk\Tessa\IFilter

	import:
		- bundles/bootstrap.neon


	bundle-default:
		- "https://fonts.googleapis.com/css?family=Open+Sans:400,300,700,600&subset=latin-ext"
		- "#boostrap"

	bundle-frontend:
		- %wwwDir%/css/frontend.css
		- %wwwDir%/js/frontend.js

	bundle-backend:
		- %wwwDir%/css/backend.css
		- %wwwDir%/js/backend.js
