# More DSpace Statistics

## Overview

This web application provides simple aggregated usage statistics for a [DSpace](http://www.dspace.org/) repository.  

Using the [DSpace Solr Statistics](https://wiki.duraspace.org/display/DSDOC5x/SOLR+Statistics) as the data source, the application provides the following statistics for items for the entire repository or a specified community, collection, or author, over a specified time period:

* item views
* file downloads
* number of items
* average downloads per item
* top ten items by file downloads

## Requirements

* DSpace 5.x (tested against 5.6-5.8).  We have not tested this against other major versions of DSpace. Field reports are welcome.
* PHP 5.6+ (tested in PHP 5.6 and 7.0)
* [Composer](https://getcomposer.org) for installing PHP dependencies.
* [DSpace REST API](https://wiki.duraspace.org/display/DSDOC5x/REST+API) enabled on your DSpace install
* (read-only) access to the DSpace Solr system, both search and statistics cores (see below)

## Accessing Solr

This application needs to access DSpace Solr, both the search and statistics cores.  It doesn't need to write anything. Options for providing Solr to this application include:

* Running this application on the same machine as Solr
* Configuring Solr to allow access to a remote client
* Port-forwarding / SSH tunneling to get access your Solr server
  * A good choice if you run *More DSpace Statistics* on one DSpace admin's personal computer.
* Installing a proxy that allows limited (read-only) access to Solr
  * We have been using the [*solr-proxy*](https://www.npmjs.com/package/solr-proxy) package from NPM.
  * Something similar could likely be written using mod_rewrite or Nginx rules.
  * Even a read-only proxy will expose visitor information, text of embargoed items, and more.  Consider access carefully.

## Installation

* Clone this repository
* Install with Composer to populate the `vendor/` directory and supply responses to configuration prompts:

```bash
composer install
```

This creates a `web/` directory that should be served by a PHP-enabled web server.

### Configuration options

* `google_analytics` - If you want Google Analytics tracking on these pages, provide a *UA-XXXXXX* code, otherwise leave null
* `site_name` - Name for your installation (Branding)
* `cacheTTL` - Certain things (lists of known communities, collections, and authors) are cached for performance.  Default is `12 hours`.
* `DSpaceBaseUrl` - This is the main path to your DSpace install.  **/rest** should be found under below this URL
* `solrSearchCore` and `solrStatisticsCore` - *URL* paths to your solr cores.
    * The URL could include ports, HTTP Basic credentials, etc (e.g. `https://user:password@solr.service.edu:8080/solr/search`)
* `secret`  - Just type something random here - this is a seed for CSRF tokens

After the initial install, those  options can modified in `app/config/parameters.yml`

## Docker version

The `docker/` directory contains an example of running the application in Docker

## License

[BSD 3-Clause](LICENSE)

Copyright 2017, University of Kansas

## Credits

Original (2015) - Matthew Copeland

Cleanup (2017) - Jeremy Keeler and Tom Shorock
