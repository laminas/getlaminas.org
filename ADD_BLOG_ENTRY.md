## Adding a blog entry

Blog entries can be added by following these steps:

- If submitting for the first time an [author](#blog-author) YAML file must be added to the `data/blog/authors` directory.
- Add an MD file containing the [blog post's metadata and body](#blog-post-content).
  - each file must be added in the current year's directory, found in `data/blog/`.

> All submissions must follow the [contributing guidelines](https://github.com/laminas/.github/blob/main/CONTRIBUTING.md) as well as the [Code of Conduct](https://github.com/laminas/.github/blob/main/CODE_OF_CONDUCT.md)

### Blog post content

Each blog post **must** begin with the YAML section found in the [template](#template-of-blog-yaml-section).

The `<!--- EXTENDED -->` tag is used to mark a preview section of the blog post on the listing page, with all following content being hidden.

> **Note** that markdown and yaml files are linted as part of the repository's continuous integration pipeline.

### Blog author

The necessary author YAML file uses the following fields:

- #### `id` **required**

  **string** - used internally, file name **must** match the given id.

- #### `email` **required**

  **string** - to be displayed in RSS feeds.

- #### `uri` **required**

  **string** - to be added to the author's name on the blog post's page.

- #### `name`

  **string** - displayed name; if missing, a default value will be shown.

#### Template of blog YAML section

```yaml
---
id: 2024-10-22-example-id
author: example-author-id
title: 'Example post title'
draft: true
public: true
created: '2024-10-22T11:00:00-01:00'
updated: '2024-10-22T11:00:00-01:00'
tags:
  - example tag
  - second example tag
---
```

#### Blog entry metadata explained

All the following fields are **required**:

- `id` **string** - must be a unique identifier for the blog

  _By convention the date of submission is used as a prefix, using the `Y-m-d` format._

- `author` **(string)** - the `id` of the author, as set in the corresponding YAML file

- `title` **(string)** - non-empty string used as the title of the post

- `draft` **(boolean)** - the status of the blogpost, defaulting to `false`; if set to `true`, the post is hidden

- `public` **(boolean)** - the visibility of the post, defaulting to `false`; if set to `true`, the post is visible on the listing page and feed

- `created` **(string)** - must be a valid Date and Time format; used by default to sort the posts

- `updated` **(string)** - must be a valid Date and Time format

- `tags` **(array of strings)** - used to filter blog posts by tag 
