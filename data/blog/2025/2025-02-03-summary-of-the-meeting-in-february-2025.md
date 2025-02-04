---
id: 2025-02-03-summary-of-the-meeting-in-february-2025
author: bidi
title: 'Summary of the meeting in February 2025'
draft: false
public: true
created: '2025-02-04T11:00:00-01:00'
updated: '2025-02-04T11:00:00-01:00'
tags:
    - meeting summary
---

On Monday, 3 February 2025, the Technical Steering Committee for the Laminas Project held its monthly meeting.
The topics discussed included:

- Creating a major release for laminas/laminas-session.
- Setting up email accounts for @getlaminas.org.
- Dropping Support for PHP 8.1 in future releases for Laminas packages.
- New design and layout for getlaminas.org.

<!--- EXTENDED -->
# Creating a major release for [laminas/laminas-session](https://github.com/laminas/laminas-session)

Backward compatibility among dependencies is desirable to maintain support for as many versions as is reasonable.
Removing dependencies or limiting them to more recent versions has direct impact on all applications that use the older version.
Releasing a backward incompatible version means some users may not be able to update to it without reworking the whole list of 3rd party packages.
Eventually, releases that break compatibility are inevitable to benefit from new features, bugfixes and security updates.

The TSC members agreed that a major release is needed for `laminas/laminas-session` because the update breaks backward compatibility.
The `laminas/laminas-session` package currently prevents other packages from being updated, because of its dependency for [laminas/laminas-servicemanager](https://github.com/laminas/laminas-servicemanager) version 3.
Updating the dependency to `laminas/laminas-servicemanager` version 4 would allow packages that depend on laminas-session to also benefit from the newer servicemanager.

# Setting up email accounts for @getlaminas.org

[Tuta](https://tuta.com/blog/tutanota-for-open-source-teams) offers a free plan for setting up secure emails for a custom domain.
There are multiple benefits to using Tuta, but the Laminas TSC members didn't find any use care that would justify setting it up for getlaminas.org.
An alternative is already in use within the Laminas organization for security and marketing concerns, so using Tuta was put on hold.

# Dropping Support for PHP 8.1 in future releases for Laminas packages

[Psalm](https://github.com/vimeo/psalm) version 6 has support for PHP from versions 8.1.17 to 8.4, so updating it in Laminas components is feasible.
On the other hand, [PHPUnit](https://github.com/sebastianbergmann/phpunit) version 11 has support only for PHP version >=8.2, so it needs to be kept at version 10 which requests PHP version >=8.1.
Updating the Laminas packages to use the newer versions of these development tools would be convenient.
It would also keep up to date with other frameworks that employ them.

Ultimately, the support for PHP version 8.1 will be kept in Laminas packages for now, because PHP 8.1 will still receive security fixes until January 1st 2026.
This has the added benefit of minimizing the impact on users of Laminas packages.
The only limitation right now is for PHPUnit which stays at version 10. 

# New design and layout for [getlaminas.org](getlaminas.org)

The design of a homepage is an important element in creating a positive first impression of a company's image.
It is the first thing most visitors see, so it is vital to include some essentials related to your company in an efficient and attractive format.

The homepage is where you:

- Introduce the company name, logo, activity and goals.
- Present critical information relevant to your company, like partners, examples of your products and/or services.
- Guide your visitors to other resources, like the contact us page and additional pages with more details on relevant topics.

The site getlaminas.org is in the process of receiving a face-lift to improve readability and user experience, while retaining the current information.
There is still [work being done](https://github.com/laminas/getlaminas.org/issues/242) on the fine details, but the update will definitely be released soon.
