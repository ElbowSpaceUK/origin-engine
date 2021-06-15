# Origin
> ElbowSpace UK Ltd (https://elbowspace.co.uk)

See the user guide [here](./user-notes.md)

See developer documentation [here](./developer-notes.md).

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
