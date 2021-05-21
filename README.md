# Origin

See developer documentation [here](./DOCNOTES.md).

## Installation

Download the following file: https://github.com/ElbowSpaceUK/atlas-cli/raw/develop/builds/atlas

Save it in your path. This is one of the folders listed when you run `echo $PATH`. I recommend `/usr/local/bin` or a folder in your home directory.
You may also have to make it executable with `sudo chmod +x /usr/local/bin/atlas`

You can now run commands using `atlas`

## Usage

You should always run `atlas setup` after any updates to the atlas command. The first time you run this, you need to give a project directory where all the code will be saved.

### Sites

#### Setting up a new local site

You can create a new site at any time by running `atlas site:new`. You may have any number of sites running at the same time.

You will be asked for a name and description for the site (the description is optional). These can also be passed through
as arguments
`atlas site:new --name="Name for the site" --description="Description for the site"`

By default, the Atlas CMS will be installed. You can instead install the frontend site by passing `--repository=frontend`.

#### Deleting a site

To remove a site, just run `atlas site:delete` and choose the site to delete.

#### List the sites

You can list the sites you have created by running `atlas site:list`. From here, you can see their status and their URL.

#### Working with a site

When you create a new site, this will be the default site for future commands. This saves you having to always choose the site
to run commands against.

To use a different site as the default, run `atlas site:use`. To clear the default, and always be prompted for the site, run `atlas site:clear`

You can see which site is being used as the default by which site has a `*` next to it when running `atlas site:list`.

#### Turn on and off a site

You can turn a site on and off by running `atlas site:up` or `atlas site:down`. These will use the default site.

#### Resetting a site

This will take the site back to a fresh version. Make sure you save any work before running this.

### Features

Features let you swap between different work on the same site. They capture all the changes you made and reest the site,
allowing you to add new features without mixing work. When you want to go back to an earlier feature, just check it out
and the cli will bring all your changes back.

#### Creating a Feature

You can create a feature as soon as you've created a site. Run `atlas feature:new` to create a new feature. You will be asked for a name, description and type for the feature.

These can also be passed through as arguments `atlas feature:new --name="Blog dependency styling" --description="Change the background colour of the blog dependency" --type=changed`

The type will be used for the changelog eventually.

- added: Adding a new feature
- changed: Change how a current feature works
- deprecated: Introduce deprecation
- removed: Removed a feature
- fixed: Fixed a bug
- security: Fixed a security vulnerability

#### Listing features
See all features saved by running `atlas feature:list`

#### Working within a feature

This will change soon as the cli gets the functionality to handle this, but for now a good workflow is
- Create a new site/reset a current site
- Create or use the feature to work on
- Pull in any dependencies you want to work on (covered later)
- Regularly commit to the branch that's checked out (name will be the name of the feature) for both the root repository and any dependencies you're working on.
- If you need to switch features, commit beforehand.
- Do the work, commit and push.
- Once everything is committed and pushed, reset the site so its ready for the next feature.

#### Deleting a feature

Once you're done with a feature, you can delete it with `atlas feature:delete`.

#### Switching features

You don't always have to reset your site before using a new feature. Just running `feature:use` is enough to switch to any feature.

This command also sets up a site with the selected feature even when you don't currently have a feature checked out.

### Dependencies

The CLI make handling composer dependencies a breeze. When developing on a dependency, the workflow is as follows:
- Create a feature
- Pull in the dependency as a local dependency
- Do the work on the dependency.
- Commit. This can be done by `cd`-ing into the dependency and running the normal git commands.
- Push it and merge the work into the dependency develop branch
- Make the dependency remote again, push if you made other changes.

To make this easier, we supply a command to make any dependency work locally. Dependencies are tied to features, so
if you use a feature which had dependencies these will be downloaded and set up.

#### Local dependency

Just run `atlas dep:local` to make a package local. This will ask you for
- The feature to use (defaults to the feature of the current site)
- Package - the exact name of the composer package. For example, `elbowspaceuk/blog-dependency`.
- Repository URL - The URl of the repository. E.g. `git@github.com:ElbowSpaceUK/Blog-dependency`. Casing does matter!
- Branch - the branch to check out in the dependency. Defaults to a branch name made from the feature name.

As before, these can all be passed directly to the command.

You will see a folder appear in the site directory called `repos`. In here, you will see all the local dependencies. Making
a change here will instantly change it on the site.

#### Remote dependencies

To stop a dependency being a local dependency, and instead use the code released on github, you just need to run `atlas dep:remote`. It needs the package name, which it will ask for or can
be passed in the `--package` parameter.

This will remove the repository in `repos`, so make sure you save and push your work.

#### See local dependencies

You can see all the local dependencies for a site by running `atlas dep:list`.

### Stubs

Stubs allow you to quickly scaffold part of your site.

#### See available stubs

#### Use a stub

To use a stub, run `atlas stub:make`. This will prompt you for the stub to make, then ask you questions
to help create the scaffolded code.

You may pass the name of the stub  to `--stub`. You may also pass a location (relative to the site root directory) to save
the stub in a different location to its default. Add `--overwrite` to overwrite any files already saved. If
not included, if the file already exists it will not be modified.  Use `--use-default` to always use the default values for a
stub, and `--dry-run` to just output the stubs to the terminal instead of saving them.

`atlas stub:make --stub=route-stub --location="Routes" --overwrite --use-default --dry-run`
or
`atlas stub:make -S route-stub -L "Routes" -O -U -R`

You'll also be able to pass in a dependency that's already local to use the dependency for the stub.

`atlas stub:make --dep=elbowspaceuk/core-module`

### Sharing containers over a local network

To share containers between sites, for example having a single mysql database shared between two apps, you can manually edit your `docker-compose.yml` file. For this to work, you must be using Laravel with Laravel Sail, using the `docker-compose.yml` struture which doesn't deviate too far from the default Sail environment, and must be using the default database credential `.env` variables.

Ensure you NEVER commit the `docker-compose.yml` changes, as these are specific to your setup and will break the site for everyone else.

- Create or decide on the FE and CMS instance to use
- Bring the FE instance down with `./vendor/bin/sail down -v`
- On the FE `docker-compose.yml`, add the following to the `networks` key. `cms-name` is the name of your CMS site sluggified, which is also the folder the CMS is  in.
```
    cms-name_sail:
    driver: bridge
    external: true
```
- Within the `atlas.su.test` service, in the `networks` key, add `-cms-name_sail`.
- Update the `.env.local` variables to do with the database to share the same credentials as the `cms`.
- Bring the FE back up.
- Run `./vendor/bin/sail artisan migrate --env=local` to migrate the FE changes

## Command Reference

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
