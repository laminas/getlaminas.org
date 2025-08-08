# Adding your entry to the Laminas Integrations Page

You can list your packages on the [Laminas Integrations](https://getlaminas.org/integrations) page by following the steps below:
> Note that all packages **must** be listed on [Packagist](https://packagist.org/)

- Add your entries as JSON objects to the `data/integration/integration-packages.json` file.
- Each package **must** be added as a single object.
- Entries **must** use the [template](#new-entry-template) for submission.
- Submit a PR against the default branch.

The JSON file can be validated using the provided JSON schema `data/integration/integration-packages-schema.json`.

Use the following command to make sure your submission will be correctly built:

```bash
./vendor/bin/laminas integration:create-db --github-token=<github_token> [--force-rebuild]
```

The optional "--force-rebuild" flag will regenerate the database completely, not only add and/or remove packages

## New entry template

```json
{
  "packagistUrl": "",
  "keywords": [],
  "homepage": null
}
```

### New entry fields description

- `packagistUrl` **(required)**
  **string** - the Packagist URL of the entry, with no query parameters

- `keywords`
  **array of strings** - optional user defined keywords used for filtering results

- `homepage`
  **string** - optional URL to package homepage, will overwrite "homepage" field from Packagist Api data

> Optional keys may be set to `null` or omitted altogether.
