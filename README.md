# README

<h2 id="Introduction">Introduction</h2>
Provides a very simple REST API for Symfony2 entities, the bundle is configured by adding a new entry to your config.yml like the following:


``` yaml
pogotc_rest_in_a_box:
    user:	## Name of your entity as it will appear in the request
		class: Path\To\My\Entity		## Where your class lives
		updateableFields: ['name', 'description'] ## Which fields the user can update
		displayableFields: ['id', 'name', 'description'] ## Which fields will be returned by get requests
```