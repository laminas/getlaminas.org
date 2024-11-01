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
./vendor/bin/laminas ecosystem:create-db
```

*Used for creating the database.*

```bash
./vendor/bin/laminas ecosystem:seed-db
```

*Used for updating the package data every X hours.*

## New entry template

```json
{
  "packagistUrl": "",
  "githubUrl": "",
  "categories": [],
  "homepage": ""
}
```

### New entry fields description

- `packagistUrl` **required**
  **string** - the packagist URL of the entry, with no query parameters

- `githubUrl`
  **string** - optional link to be displayed on the package card

- `categories`
  **array of strings** - user defined keywords used for filtering results

- `homepage`
  **string** - optional URL to package homepage, will overwrite "homepage" field from Packagist Api data
