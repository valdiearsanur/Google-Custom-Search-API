Install Composer
====================
php -r "readfile('https://getcomposer.org/installer');" | php

install composer.json
php composer.phar install

Google Custom Search 
==================
Google's documentation for the Custom Search JSON API can be found here:
https://developers.google.com/custom-search/json-api/v1/overview

Credentials
-----

**Search Engine ID** - On the [Custom Search Engine Control Panel](http://www.google.com/cse/manage/all), create a new search engine for the site you would like to integrate. Once created, you can retrieve your Search Engine ID from the *Setup* tab.

**API Key** - If you're using the free option, visit the [Google Developers Console](https://console.developers.google.com) and create a project for your search engine. Within your project, you’ll need to enable the *Custom Search API* from the *APIs* tab. Finally, on the *Credentials* tab, you will need to create a Public API access key by selecting the *Create new Key* option and choosing *Server key*. The API Key will now be available.

Usage
-----

```(php)
include 'goog/GoogleCustomSearch.php';

define("SEARCH_ENGINE_ID", "[paste search engine ID here]");
define("API_KEY", "[paste api key here]");

$search = new goog\GoogleCustomSearch(SEARCH_ENGINE_ID, API_KEY);
$results = $search->search('komputer');

echo json_encode($results);
```