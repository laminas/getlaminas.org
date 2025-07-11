# Adding your entry to the Laminas Ecosystem

You can list your packages on the Laminas third party packages page by following the steps below:
> Note that all packages **must** be listed on [Packagist](https://packagist.org/)

- Add your entries as JSON objects to the `data/ecosystem/ecosystem-packages.json` file.
- Each package **must** be added as a single object.
- Entries **must** use the [template](#new-entry-template) for submission.
- Submit a PR against the default branch.

Use the following command to make sure your submission will be correctly built:

```bash
./vendor/bin/laminas ecosystem:create-db --github-token=<github_token> [--force-rebuild]
```

The optional "--force-rebuild" flag will regenerate the database completely, not only add and/or remove packages

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
  **string** - must be one of "skeleton", "integration", "tool"
