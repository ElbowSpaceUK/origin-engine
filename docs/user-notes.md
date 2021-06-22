# User Guide

This guide will assume you use a binary called `origin` on your path. If this is not the case, you will need to adjust the command accordingly.

You should always run `origin post-update` after any updates to the origin command. The first time you run this, you need to give a project directory where all your local code will be saved.

## Introduction

### What does Origin do?

- Normally a step by step on how to run a site locally. That is the same whenever ran on the same environment, therefore
can be automated.
- Origin allows you to do just that. Once it knows how to set up any site locally, it can do it with 1 command, meaning it takes
the hastle out of managing multiple instances or features.
- As well as that, has additional features for managing development environments, such as local dependency management, workflows for managing your development process, quick scaffolding of boilerplate code with stubs and extensive plugin and pipeline systems which allows you to customise as much as is needed.

### Sites

- A site is an instance of a web application, such as a website built with Laravel. Origin is able to set up and run
new instances locally, both letting you access them through the browser and work on the code in realtime.
- Can have many different types of sites for different projects.
- Can quickly see all the URLs to interact with the sites

### Features

- A feature is an addition or change to code being made. By using features, you can keep a smooth development workflow
by using git branches to control and version your work.
- Can have many features per site and easily switch between them
- Will be able to share features with one another

### Dependencies

- In modern PHP applications, there are many dependencies managed with a package manager like `composer`. It can be hard 
to seamlessly work on a dependency that has its own git repository, without changing your development process too much.
- Origin gets around that by letting you work on any dependency, locally. Will clone the repository and link it to composer, letting
you focus on work rather than setting up environments.

## Common command options

- Debug -v -vv -vvv/--verbose
- --quiet to not output any message
- --help to display the help for the given command
- --version to display application version

- Never have to give any flags to the command, will always work with just the command
- But all options can be given through the command if needed.
- Use --config to pass to pipelines. These will be shown on the --help section of the command.

## Sites

This section pertains to setting up, managing and removing local sites.

### Setting up a new local site

Once you have installed the command, you may run `origin site:new` to create a new site. You will be asked the following questions
- Name this site: This name will be used to refer to the site.
- Description for the site: This can be used to further identify the site if needed.
- Type of site: The options here will depend on how your version of origin is set up.

The site will now install, and let you know once it's ready to be used!

You may give all the information about the site to the command up front. Run `origin site:new --help` to see which flags to use

### Deleting a site

You can delete any site using `origin site:delete`. You will be asked for the site to delete. Ensure you have saved
and pushed any work before running this operation. Origin will take care of cleaning up the site files and your local
environment.

### List the sites

You can quickly see an overview of the sites you have installed, as well as if they're ready for use or not. Run `origin site:list`.

### Turn on and off a site

If you want to save on resources but don't want to delete a site, you may be able to temporarily turn the site off. To do so,
run `origin site:down`. You can choose from any site that's currently ready.

To turn a site back on, the `origin site:up` command will do it.

### Resetting a site

To bring a site back to how it started, by removing all local dependencies and resetting the code to the base branch, run
`origin site:reset`.

### Default site

If you're working on the same site for a period of time, you may want to bypass always being asked for the site 
to use for things like dependencies. To do this, run `origin site:default`.

You can see which site is the default site by seeing which has the `*` to the side of it when running `origin site:list`

To stop using a site, you can run `origin site:clear`.

## Features

Features let you swap between different work on the same site. They capture all the changes you made and reset the site, allowing you to add new features without mixing work. When you want to go back to an earlier feature, just check it out and the cli will bring all your changes back.

### Creating a Feature

To create a new feature, you will need to have created a site. Run `origin feature:new` to create a new feature.

These can also be passed through as arguments origin feature:new --name="Blog dependency styling" --description="Change the background colour of the blog dependency" --type=changed

The type will be used for the changelog eventually.




You will have to give the following information, either through the command or using the interactive input.
- Name of the feature: To help you quickly reference it
- Description of the feature: This may be put into your sites changelog, so make sure it's clear what the change will do
- The kind of change: What kind of change are you making? One if
    - added: Adding a new feature
    - changed: Change how a current feature works
    - deprecated: Introduce deprecation
    - removed: Removed a feature
    - fixed: Fixed a bug
    - security: Fixed a security vulnerability
    - The branch name: This will give you a sensible default based on the branch name, but you may change this if necessary.

### Listing features

- See all the features in your current local environment, across all sites.
- Run `origin feature:list`

### Deleting a feature

- Run `origin feature:delete`. If you have a site set as the default, it will run on the feature of that site. You may
opt to choose the feature to delete instead.

### Switching features

For switching between features, or to a feature from a new/reset site, run `origin feature:use`. This will
let you choose the feature to checkout.

### Default feature

If you have set a default site, then the feature this site is using is the default feature. You can clear
this by clearing the feature.

## Dependencies

Origin makes handling composer dependencies a breeze. When developing on a dependency, the workflow is designed to be as follows:
- Create a feature
- Pull in the dependency as a local dependency
- Do the work on the dependency.
- Commit. This can be done by `cd`-ing into the dependency and running the normal git commands.
- Push it and merge the work into the dependency develop branch
- Make the dependency remote again, push if you made other changes.

With the workflow plugin this will be more customisable, but you may use dependencies in any way you like. This is just a suggestion.

To make this easier, we supply a command to make any dependency work locally. Dependencies are tied to features, so
if you use a feature which had dependencies these will be downloaded and set up.

### Local dependency

Just run `origin dep:local` to make a package local. This will ask you for
- The feature to use (defaults to the feature of the current site)
- Package - the exact name of the composer package. For example, `elbowspaceuk/origin-engine`.
- Repository URL - The URl of the repository. E.g. `git@github.com:ElbowSpaceUK/origin-engine`.
- Branch - the branch to check out in the dependency. Defaults to a branch name made from the feature name.

As before, these can all be passed directly to the command.

You will see a folder appear in the site directory called `repos`. In here, you will see all the local dependencies. Making
a change here will instantly change it on the site.

### Remote dependencies

To stop a dependency being a local dependency, and instead use the code released on github, you just need to run `origin dep:remote`. It needs the package name, which it will ask for or can
be passed in the `--package` parameter.

This will remove the repository in `repos`, so make sure you save and push your work.

### See local dependencies

You can see all the local dependencies for a site by running `origin dep:list`.

## Stubs

Stubs allow you to quickly scaffold part of your site.

### See available stubs

Run `origin stub:list` to see the stubs you are able to use.

### Use a stub

To use a stub, run `origin stub:make`. This will prompt you for the stub to make, then ask you questions
to help create the scaffolded code.

You may pass the name of the stub  to `--stub`. You may also pass a location (relative to the site root directory) to save
the stub in a different location to its default. Add `--overwrite` to overwrite any files already saved. If
not included, if the file already exists it will not be modified.  Use `--use-default` to always use the default values for a
stub, and `--dry-run` to just output the stubs to the terminal instead of saving them.

`origin stub:make --stub=route-stub --location="Routes" --overwrite --use-default --dry-run`
or
`origin stub:make -S route-stub -L "Routes" -O -U -R`

You'll also be able to pass in a dependency that's already local to use the dependency for the stub.

`origin stub:make --dep=elbowspaceuk/core-module`


## Health Check

The health check can detect any problems with your setup. This will usually occur because of an unexpected failure in the running of a pipeline, or through sites being changed manually.

The health check is both capable of reporting the status of sites, and fixing any problems that are found.

### Running the health check

Run `origin healthcheck` to run the healthcheck for your command. You can add the `--quick` flag to only run checks
that won't take long to complete.

### Fixing health check problems

Run `origin healthcheck:fix` to fix any issues that the healthcheck found.

## Command Reference

- Run after an update: `origin post-update`

### Sites

- Create a site: `origin site:new`
- Delete a site: `origin site:delete`
- Bring a site up: `origin site:up`
- Bring a site down: `origin site:down`
- See all sites: `origin site:list`
- Prune sites (if you've deleted one in the filesystem): `origin site:prune`
- Set a site as the default site: `origin site:use`
- Clear the current site, so always prompt for the site to use: `origin site:clear`
- Reset the current site back to a fresh installed state: `origin site:reset`

### Features

- Delete the selected feature: `feature:delete`
- List all features: `feature:list`
- Create a new feature in a site: `feature:new`
- Checkout the selected feature: `feature:use`

### Local dependencies

- Use a local dependencies: `origin dep:local`
- Make a dependency remote again: `origin dep:remote`
- List all local dependencies: `origin dep:list`

### Stubs

- Create a new stub: `origin stub:make`
- List all available stubs: `origin stub:list`

### Health Check
- Check the health of origin: `origin healthcheck`
- Fix origin: `origin healthcheck:fix`
