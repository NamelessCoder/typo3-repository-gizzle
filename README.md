<img src="https://release.namelesscoder.net/typo3-repository-gizzle-logo.svg"
     alt="TYPO3 GitHub Repository Releaser" style="width: 100%;" />

[![Build Status](https://img.shields.io/jenkins/s/https/jenkins.fluidtypo3.org/typo3-repository-gizzle.svg?style=flat-square)](https://jenkins.fluidtypo3.org/job/typo3-repository-gizzle/) [![Coverage Status](https://img.shields.io/coveralls/NamelessCoder/typo3-repository-gizzle.svg?style=flat-square)](https://coveralls.io/r/NamelessCoder/typo3-repository-gizzle)


This project allows any GitHub-hosted repository containing a TYPO3 CMS
extension to be released to the official TYPO3 Extension Repository
(hereafter referred to as TER) by adding a very simple GitHub web hook.

The official endpoint of this service is `https://release.namelesscoder.net`
but you are welcome to install this package on your own and use that
installation location as endpoint.

The project uses Gizzle to listen for GitHub events and uses Gizzle TYPO3
plugins to do the actual uploading. Internally, the Gizzle TYPO3 plugins
use the TYPO3 Repository Client.
 
* https://github.com/NamelessCoder/gizzle
* https://github.com/NamelessCoder/gizzle-typo3-plugins
* https://github.com/NamelessCoder/typo3-repository-client

Requirements
------------

* A **public** GitHub repository containing your extension's source code.
* For custom endpoints only: access to the `git` and `php` CLI commands as
  well as some way to serve the `web/index.php` file through HTTP/HTTPS.

Installation
------------

1. Edit the settings of your repository and locate "Webhooks & services".
2. Click "Add webhook" to create a new hook.
3. In "Payload URL", fill in the endpoint URL you wish to use. The default
   URL is `https://release.namelesscoder.net`
4. Add your unique credentials and information to the URL. There are two
   possible URL formats:
   * If your GitHub repository name is **not the same** as your TER extension
     key, a URL like `https://release.namelesscoder.net/my_extension/user:password`.
     must be used - which will release the repository as key `my_extension`.
   * If your GitHub repository is already named the same as your extension
     key you can leave out that part of the URL and use a shorter URL like
     `https://release.namelesscoder.net/user:password`.
   * Alternatively, if you feel more comfortable with it and the endpoint
     supports it, use `https://username:password@release.namelesscoder.net/my_extension`.
     This method only works if the server supports the `authnz_external_module`
     or corresponding external authentications - see below.
5. Enter a "Secret". We use a fixed secret for now - enter the text `typo3rocks`.
6. Leave the "Which events..." selectors as-is. We only need the `push` event.

Unfortunately there is no way to isolate an event that only gets dispatched
when you create new tags - which is why we have to listen to all `push`es.
We simply ignore those that do not refer to a tag.

Security
--------

Because your credentials are included in the URL, we are doing the following
on the default endpoint and you should definitely do the same if you create one:

* The URL is protected by SSL.
* The full URL of requests is never logged.

Please note that this credentials-in-URL approach is considered *temporary* and
is only implemented because there currently are no other ways. The end goal is
to use a token solution both for the "Secret" that is currently fixed, as well
as for the credentials that must be passed to TER. The former will be solved
by creating an official GitHub Service but the latter will depend on work that
has to be done on TER or even TYPO3 SSO itself.

If your web server supports it (Apache does via `authnz_external_module`) then
you can register an external authenticator (change/move the path if needed):

```
<IfModule authnz_external_module>
    DefineExternalAuth fake environment /var/www/release.namelesscoder.net/fake-auth.sh
</IfModule>
```

...which goes in your virtual host or root server configuration. The `fake-auth.sh`
script is a dummy that allows any username and password to be authenticated -
and if done this way, this project will instead read those (fake) credentials.
This means you can specify the credentials to use when uploading to TER, as
`https://username:password@release.namelesscoder.net/my_extension`.

Usage
-----

To create a new TER release from your GitHub repository simply create a new
tag and push it. The uploader will then use the message of the commit you tag
as the upload comment on TER. To create and push a new tag:

```
git tag 1.2.3
git push origin 1.2.3
```

Which creates a new tag called `1.2.3` and pushes it to the remote called `origin`.

Viewing results
---------------

The results of uploads triggered this way can be read in two different places:

* The **publicly available** message that the release was created gets added
  as a comment to the commit that was HEAD of the repository when tagging.
* The **privately available** debugging information can be viewed by locating
  the "Webhooks & services" panel as described in the installation section, and
  clicking the URL that was added. A short list of most recent transactions is
  displayed and clicking each one will allow you to read any technical errors.

Behavior
--------

The behavior of this plugin is very easily described:

1. Whenever you push to GitHub, we receive an event.
2. We analyse the Payload HEAD:
   * If it is not a new tag we exit.
   * If it is a new tag we upload a new release.
3. We set a "success" status flag on the commit you tagged.
4. We add a comment to the commit you tagged, saying it was released to TER.
5. We assign a bit of debugging information and send our response to GitHub.

The whole process should only take a couple of seconds, depending on how large
your extension is. If at any point during the process an error occurs, it is
caught and can be inspected in the web hook response as described above.
