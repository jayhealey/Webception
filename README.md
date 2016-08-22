# Webception

[![Join the chat at https://gitter.im/jayhealey/Webception](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/jayhealey/Webception?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

#### Web Interface for running [Codeception](http://codeception.com) tests.

Built with [Slim PHP framework](http://www.slimframework.com/) and [Foundation CSS framework](http://foundation.zurb.com/).

------------

**What does it do?**

Webception is a deployable web-application that allows you to run all your Codeception tests in the browser.

You can access multiple test suites and decide which tests to include in a run. It allows you start, stop and restart the process whilst watching the test results in the Console.

**What does it look like?**

I'm glad you asked...

<img src="http://i.imgur.com/nSsMFIS.gif">

**What's the ideal usage?**

If you're a web developer, it's likely you run a staging server that hosts work in progress for your clients.

Webception's ideal setup would be on a sub-domain on your staging server (`webception.your-staging-domain.com`) so it could have access to all of your test suites.

**What it will it do?**

Webception is a work in progress. Check out the [roadmap](#roadmap) for short-term goals.

Check out how to [contribute](#contribute) if you want to get involved.

------------

## Requirements

A web-server running PHP 5.3.0+ and [Composer](http://getcomposer.org/download/). Codeception will be installed via Composer.

------------

## Installation

Out of the box, Webception is configured to run it's own Codeception tests.

You'll need [Composer](http://getcomposer.org/download/) to be installed and the Codeception executable and logs directory need full read/write permissions.

The *only* configuration file you need to update is `App/Config/codeception.php`. It's here where you add references to the `codeception.yml` configurations.

Also note Webception's `codeception.yml` is setup to use `http://webception:80` as it's host. Change this to be whatever host and port you decide to run Webception on.

### 1. Deploy Webception

You can either install Webception using Composer:

`composer create-project jayhealey/webception --stability=dev`

Or [downloaded Webception](https://github.com/jayhealey/Webception/archive/master.zip) and unzip it.  Once you've unzipped it, you need to install the Composer dependancies with:

`composer install`

Now you can do the following:

1. Ensure Codeception has permissions:

   `sudo chmod a+x vendor/bin/codecept`

2. Set permissions so Codeception can write out the log files:

   `sudo chmod -R 777 App/Tests/_log`

3. Set permissions so Slim PHP can write to the template cache:

   `sudo chmod -R 777 App/Templates/_cache`

4. Point your new server to the `public` path of where you unzipped Webception.

You'll now be able to load Webception in your browser.

If there are any issues Webception will do it's best to tell what you need to do.

### 2. Customise the Webception configuration

There are a few configuration files you can play with in  `/App/Config/codeception.php`.

#### Adding your own tests to Webception

You can add as many Codeception test suites as you need by adding to the `sites` array:

```
'sites' => array(
   'Webception' => dirname(__FILE__) .'/../../codeception.yml',
),
```
Put them in order you want to see in the dropdown. And if you only have one entry, you won't see the dropdown.

Feel free to remove/replace the `Webception` entry with one of your own suites.

If you have more than one site in the configuration, you can use the site picker on the top-left of Webception to swap between test suites.

**And remember**: it's important you set `sudo chmod -R 777 /path/to/logs` on the log directories declared in your `codeception.yml` configurations. If you don't, Webception will fail to run the tests.

*Note*: You may experience issues using `$_SERVER['DOCUMENT_ROOT']` to define the configuration path. It may be best to put the absolute path to your application root or a relative path using `dirname(__FILE__)`.

### 3. Run your tests!

If you've configured everything correctly, Webception will show all your available tests. Just click **START** to run everything!

That's it! **Happy Testing!**

------------

<a name='contribute'></a>
## Want to Contribute?
There's a few ways you can get in touch:

* **Chat on Twitter**. Follow [@WebceptionApp](https://www.twitter.com/WebceptionApp) for release updates or follow [@JayHealey](https://www.twitter.com/JayHealey) for everything else.

* **Post bugs, issues, feature requests** via [GitHub Issues](https://github.com/jayhealey/webception/issues).

* **Pull & Fork** on [GitHub](https://github.com/jayhealey/Webception/pulls) if you want to get your hands dirty. Please ensure that any new features you add have assosciated tests with them, and where possible point yuor feature request at the appropriate version (as per the [Roadmap](https://github.com/jayhealey/Webception/wiki))

And **please let me know** if you use Webception. I'm keen to understand how you'd *like* to use it and if there's anything you'd like to see in future releases.

I'm open to any feedback on how to improve Webception. From tips on SlimPHP, to how best to improve the Codeception handling to improving the UI. I'd be happy to hear it!

------------

## Infrequently Asked Questions (IAQs)

**Why would I use Webception?**

The aim of Webception is to open the test suites up to anyone involved in a web-development project. This could be a team leader, another developer (who might not be a PHP developer), client manager or even the client.

The plan is to grow the tool to be a worthwhile part of your process. Potentially integrating CI tools or part of a bug reporting process.

And selfishly, I couldn't find anything else that acted as a web interface for Codeception, so it was a problem worth solving.

**Is Webception made by the same people as Codeception?**

No. It's completely un-official. It's not affiliated or sponsored in anyway by the people who built Codeception.

So, raise all issues about Webception on the Webception [GitHub Issues](https://github.com/jayhealey/Webception/issues) page.

------------

<a name='roadmap'></a>
## Roadmap

* **Automated/Interactive Setup**: I'd like to replace the manual setup with an interactive installation that asks for the Codeception test suites whilst verifying the details as you enter them. You'll should also be able to add/remove test suites via the app instead of modifying configuration files. It's possible to find all available `codeception.yml` files which would help automate installation.

* **Logs and Screenshots**: When Codeception runs, it creates HTML snapshots and screenshots of where a test fails. It'd be useful for Webception to copy those files across and make them accessible via the console.

* **Security**: At the moment, you can just secure the installation with .htaccess - but it might be worth adding built-in security via a Slim module.

* **Exposed Unit Tests**: Unit tests contain multiple tests in a single file, so it'd be nice to scan Unit tests to expose them - and then allow the ability to run each of these tests individually (is that even possible in Codeception?).

* **More Webception Tests**: It feels fitting that an application that runs tests should be drowning in tests. So, there'll be more of them in future.

There's also the [TODO](TODO.md) list which contains a list of things I'd like to improve.

If you have any ideas or issues, jump on [GitHub Issues](https://github.com/jayhealey/Webception/issues) or [@WebceptionApp](https://www.twitter.com/WebceptionApp) on Twitter.
