---
id: 2024-03-08-summary-of-the-meeting-in-march-2024
author: frank
title: 'Summary of the meeting in March 2024'
draft: false
public: true
created: '2024-03-08T11:00:00-01:00'
updated: '2024-03-08T11:00:00-01:00'
tags:
    - meeting summary
---

On Monday, 4 March 2024, the Technical Steering Committee for the Laminas Project held its monthly meeting.
The topics discussed included the visibility of maintenance decisions and the optional dependency of laminas-servicemanger in certain components.

<!--- EXTENDED -->

The Laminas Technical Steering Committee recently convened to address important matters concerning the communication of component status to users and the optional dependency of laminas-servicemanger in certain components.
One key issue discussed was the lack of visibility regarding maintenance decisions for users.
Suggestions were made to interlink commits/issues/pull requests with meeting minutes, utilize status badges, and leverage GitHub's custom properties to provide users with a comprehensive overview of package statuses.

Regarding the optional dependency of laminas-servicemanger, concerns were raised about potential conflicts and the impact on component usage.
Discussions centered on the implications of making laminas-servicemanger optional and its effects on dependency management.
Suggestions were made to reconsider the approach and explore alternative solutions, such as redefining plugin manager registration and investigating potential conflicts arising from optional dependencies.

Ultimately, decisions were made to implement visible README notices for components in security-only status and define custom properties for maintenance status, with the creation of a dashboard deferred until these measures are in place.
Additionally, further research and exploration of alternative approaches were agreed upon regarding the optional dependency of laminas-servicemanger in components providing plugin managers.

The committee's deliberations reflect a commitment to enhancing communication with users and ensuring robust dependency management within the Laminas ecosystem.

---

_We would like to thank [Eric Richer (visto9259)](https://github.com/visto9259) who brought in the topic of visibility regarding maintenance decisions._
_He is one of the members behind [LM Commons - Community developed packages for the Laminas MVC](https://lm-commons.github.io)._
