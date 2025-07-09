# Adding your entry to the Laminas Ecosystem

You can add packages **available via composer** to the `data/ecosystem/ecosystem-packages.json` file by following the steps below:

- Entries must use the [template](#new-entry-template) as a guide.
- Submit a PR.

> Use the following command to make sure your submission will be correctly built:

```bash
composer build
```

> The following command can be run individually for testing:

```bash
./vendor/bin/laminas ecosystem:create-db  --github-token=<github_token> [--force-rebuild]
```

> the optional "--force-rebuild" flag will regenerate the database completely, not only add and/or remove packages

*Used for creating the database.*

```bash
./vendor/bin/laminas ecosystem:seed-db  --github-token=<github_token>
```

*Used for updating the package data every X hours.*

## New entry template

```json
{
  "packagistUrl": "",
  "keywords": [],
  "homepage": "",
  "category": ""
}
```

### New entry fields description

- `packagistUrl`
  **string** - the packagist URL of the entry, with no query parameters

- `keywords`
  **array of strings** - user defined keywords used for filtering results

- `homepage`
  **string** - optional URL to package homepage, will overwrite "homepage" field from Packagist Api data

- `category`
  **string** - package category must be one of "skeleton", "integration", "tool"
