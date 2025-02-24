# Adding a blog entry

Blog entries can be added by following these steps:

- If submitting for the first time, an [author](#blog-author) YAML file must be added to the `data/blog/authors` directory.
- Add an MD file containing the [blog post's metadata and body](#blog-post-content).
  - blog post files are named following a `yyyy-mm-dd-title` convention, e.g., `2024-10-24-blog-title.md`
  - each file must be added to the appropriate year's directory, found in `data/blog/`
  - use the following command to make sure your submission will be correctly built:

```bash
$ composer build-blog
```

- When ready, submit the blog post for review via pull request to the default branch.

> You can use the [file examples](#file-examples) as starting point
>
> All submissions must follow the [contributing guidelines](https://github.com/laminas/.github/blob/main/CONTRIBUTING.md) as well as the [Code of Conduct](https://github.com/laminas/.github/blob/main/CODE_OF_CONDUCT.md)

## Blog post content

Each blog post **must** begin with the YAML section found in the [template](#template-of-blog-yaml-section).

The `<!--- EXTENDED -->` tag is used to mark a preview section of the blog post on the listing page, with all following content being hidden.

> **Note** that markdown and yaml files should be linted.

## Blog author

The necessary author YAML file uses the following fields:

- ### `id` **required**

  **string** - used internally, file name **must** match the given id.

- ### `email` **required**

  **string** - to be displayed in RSS feeds.

- ### `uri` **required**

  **string** - to be added to the author's name on the blog post's page.

- ### `name`

  **string** - displayed name; if missing, a default value will be shown.

### Template of blog YAML section

```yaml
---
id: 2024-10-22-example-id
author: example-author-id
title: 'Example post title'
draft: true
public: true
created: '2024-10-22T11:00:00-01:00'
updated: '2024-10-22T11:00:00-01:00'
openGraphImage: '2024-10-22-custom-image.png'
openGraphDescription: 'Custom description'
tags:
  - example tag
  - second example tag
---
```

### Blog entry metadata explained

All the following fields are **required**:

- `id` **string** - must be a unique identifier for the blog

  _By convention the date of submission is used as a prefix, using the `yyyy-mm-dd` format._

- `author` **(string)** - the `id` of the author, as set in the corresponding YAML file

- `title` **(string)** - non-empty string used as the title of the post

- `draft` **(boolean)** - the status of the blogpost, defaulting to `false`; if set to `true`, the post is hidden

- `public` **(boolean)** - the visibility of the post, defaulting to `false`; if set to `true`, the post is visible on the listing page and feed

- `created` **(string)** - must be a valid Date and Time format; used by default to sort the posts

- `updated` **(string)** - must be a valid Date and Time format

- `tags` **(array of strings)** - used to filter blog posts by tag

The Open Graph preview card has a default image and description which can be **optionally** overwritten using the following fields:

- `openGraphImage` **string** - custom image to replace the default in the Open Graph preview
  - given value **must** be the same as the file name and extension
  - the corresponding image file **must** be added in the `public\images\opengraph\blog` directory
  - to maintain cohesion, the image file **should** be named after the blog post itself (`yyyy-mm-dd-title.extension`)

- `openGraphDescription` **string** - custom text to be displayed in the Open Graph preview, replacing the default.

### File examples

#### author.yml

```yaml
id: author
name: Sample Author
email: sampleAuthor@example.com
uri: 'https://example.com'

```

#### 2024-10-24-blog-post-example.md

```markdown
---
id: 2024-10-24-example-post
author: author
title: 'Example post'
draft: false
public: true
created: '2024-10-24'
updated: '2024-10-24'
openGraphImage: '2024-10-24-custom-image.png'
openGraphDescription: 'Custom description'
tags:
  - example tag
---

Section above extended tag, which will be visible in the post list.

<!--- EXTENDED -->

## Sample heading

Section below extended tag will be hidden in the listing, being visible only on the post's own page.

```
