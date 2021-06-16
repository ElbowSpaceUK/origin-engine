# User Guide

This guide will assume you use a binary called `origin` on your path. If this is not the case, you will need to adjust the command
accordingly.

You should always run `origin post-update` after any updates to the atlas command. The first time you run this, you need to give a project directory where all your local code will be saved.

## Main Concepts

### Sites
### Features
### Dependencies

## Common command options

## Sites
### Setting up a new local site
### Deleting a site
### List the sites
### Turn on and off a site
### Resetting a site
### Default site
### Prune

## Features
### Creating a Feature
### Listing features
### Deleting a feature
### Switching features
### Default feature

## Dependencies
### Local dependency
### Remote dependency
### See local dependencies

## Stubs
### See available stubs
### Use a stub

## Health Check
### Running the health check
### Fixing health check problems

## Command Reference

- Run after an update: `atlas post-update`

### Sites

- Create a site: `atlas site:new`
- Delete a site: `atlas site:delete`
- Bring a site up: `atlas site:up`
- Bring a site down: `atlas site:down`
- See all sites: `atlas site:list`
- Prune sites (if you've deleted one in the filesystem): `atlas site:prune`
- Set a site as the default site: `atlas site:use`
- Clear the current site, so always prompt for the site to use: `atlas site:clear`
- Reset the current site back to a fresh installed state: `atlas site:reset`

### Features

- Delete the selected feature: `feature:delete`
- List all features: `feature:list`
- Create a new feature in a site: `feature:new`
- Checkout the selected feature: `feature:use`

### Local dependencies

- Use a local dependencies: `atlas dep:local`
- Make a dependency remote again: `atlas dep:remote`
- List all local dependencies: `atlas dep:list`

### Stubs

- Create a new stub: `atlas stub:make`
- List all available stubs: `atlas stub:list`

### Health Check
- Check the health of origin: `atlas healthcheck`
- Fix origin: `atlas healthcheck:fix`
