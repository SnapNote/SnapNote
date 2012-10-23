SnapNote
==============

SnapNote is a web-based note taking program, written in PHP, and based on Kohana framework with several additional open source modules.

SnapNote has a REST API at its core. The website generally acts as a client of the web service, which should make developing additional clients easy. The one exception is that the user login and management features are only available through the website. API access is then granted by use of an API key.

This project is still under development. Currently, users can log in, manage and view notes and labels, and manage users (admin permission).

Version 1 plans include:
Star notes
Templates
Trash
Log
Pagination
Search
Bulk actions in list view

Version 2 plans include:
AJAX Frontend
Rich Editing
Versioning/Version Management
Sharing
Document API

Credits:
Restful was forked from michal-m:
https://github.com/michal-m/kohana-modules-restful

Restify and Dispatch were forked from Morgan:
https://github.com/morgan/kohana-restify
https://github.com/morgan/kohana-dispatch

Useradmin was originally forked from Mixu:
https://github.com/mixu/useradmin

Pagination and OAuth were forked from ShadowHand:
https://github.com/shadowhand/pagination
https://github.com/shadowhand/oauth

Email was forked from DigitalJohn:
https://github.com/digitaljohn/kohana-email

Bootstrap is available from Twitter:
http://twitter.github.com/bootstrap/index.html
