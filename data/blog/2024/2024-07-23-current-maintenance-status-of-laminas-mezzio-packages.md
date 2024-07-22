---
id: 2024-07-23-current-maintenance-status-of-laminas-mezzio-packages
author: julian
title: 'Current Maintenance Status of Laminas & Mezzio Packages'
draft: false
public: true
created: '2024-07-23T11:00:00-01:00'
updated: '2024-07-23T11:00:00-01:00'
tags:
  - laminas api tools
  - custom proprieties
  - maintenance status
---

Laminas Project has created a large number of packages to serve the needs of the PHP community. Unfortunately, there is
no aggregated place to see the status of each package easily. You would need to visit each one of them on GitHub and
check out the *Custom Properties* page. And that is when you remember to do so...

<!--- EXTENDED -->

### Laminas and Mezzio packages maintenance status at a glance

The page below intends to provide a fast, accessible way to examine every package at a glance. It provides a column for
each of the three organizations: Laminas, Api Tools and Mezzio.
The page is automatically refreshed daily and is publicly available.

[**Current Maintenance Status of Laminas & Mezzio Packages**](https://getlaminas.org/packages-maintenance-status/)

There is also a JSON version [**here**](https://getlaminas.org/share/properties.json). It contains the full list of
properties for each package. Note that the properties differ between organizations.

Below is the meaning of each status. The non-active statuses may include the date when they were set and the Technical
Steering Committee minute file where they were discussed.

- **active** - The component is receiving regular updates and maintenance.
- **maintenance-only** - The component is receiving only maintenance-related updates, but no new features.
- **security-only** - The component is receiving only security-related updates, but no new features.
- **discontinued** - The component has been abandoned, so it will receive no new updates.

Also, you can use a *Dynamic JSON Badge* anywhere, using the Markdown sample below:

`![Dynamic JSON Badge](https://img.shields.io/badge/dynamic/json?url=https%3A%2F%2Fapi.github.com%2Frepos%2Flaminas%2Flaminas-authentication%2Fproperties%2Fvalues&query=%24%5B%3F(%40.property_name%3D%3D%22maintenance-mode%22)%5D.value&label=Maintenance%20Mode)`

where you can change the org name: *laminas* and the repo name *laminas-authentication* with your desired org and repo
name. The end result will look like:

![Dynamic JSON Badge](https://img.shields.io/badge/dynamic/json?url=https%3A%2F%2Fapi.github.com%2Frepos%2Flaminas%2Flaminas-authentication%2Fproperties%2Fvalues&query=%24%5B%3F(%40.property_name%3D%3D%22maintenance-mode%22)%5D.value&label=Maintenance%20Mode)