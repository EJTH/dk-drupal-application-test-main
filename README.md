
# Aller Application Test solution
To use the RESTful endpoint you must first enable the module 'aller_resful_api' with drush.

`drush pm:install aller_restful_api`

Optionally use drush to enable and use 'restui' module to configure the endpoint through backend configuration.

`drush pm:install restui`

# Aller Application Test 

The idea with this repo, is to give a starting point for applicants who want to
a code exercise test.

## Setup

Use whatever setup you want to run it locally, be it docker or something else.
You will need to

- `$ composer install` - to install composer dependencies.
- Setup database connection
- `$ drush install --existing-config` - to install the site with the provided config.

## Site

The site is set up with REST module install, a basic content type with uses
paragraphs for creating content. This is very basic with only two types of
paragraphs. The site is intended to be a Drupal headless site.

## Task

Create a custom module to expose the content create to a decoupled frontend.
The REST endpoint should output JSON. Ideally the JSON is clean and simple for
the frontend to use. It would also be nice if the implementation is made in a
way that would make it easier to add more paragraph types in the future.

The exercise is meant to be done in roughly 1 hour after setup. The goal is to
see the code style of applicants and give them the opportunity to show that
they master the core Drupal concepts and can work with a Drupal headless setup.

There are many approaches which could get the job done and there is no right or
wrong answer. The idea is to give a task which mimics the day-to-day work at
Aller web.
