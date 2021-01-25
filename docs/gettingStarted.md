---
id: gettingStarted
title: Getting Started
sidebar_label: Getting Started
slug: /
---

You can write content using [GitHub-flavored Markdown syntax](https://github.github.com/gfm/).

## Purpose of this site

The purpose of this docs site is to document the progress we are making and any issues we resolve. Many times I have fixed something and find it again down the road and it takes me hours to fix again because I didn't document the fix properly.

## Tools we are using

### Laravel

Laravel is a web application framework with expressive, elegant syntax. A web framework provides a structure and starting point for creating your application, allowing you to focus on creating something amazing while we sweat the details.

More information: https://laravel.com/docs/8.x/installation

### Vue.js

Vue (pronounced /vjuː/, like view) is a progressive framework for building user interfaces. Unlike other monolithic frameworks, Vue is designed from the ground up to be incrementally adoptable. The core library is focused on the view layer only, and is easy to pick up and integrate with other libraries or existing projects. On the other hand, Vue is also perfectly capable of powering sophisticated Single-Page Applications when used in combination with modern tooling and supporting libraries.

More information: https://vuejs.org/v2/guide/

### MySQL database

We have an instance of ClearDB running on Heroku. Contact Gabe for credentials. Heroku uses PostgreSQL by default, but most likely the client will be using MySQL so it is better to desing the database on it from the start.

More information: https://www.cleardb.com/#features


### Heroku 

Used to deploy project during development, useful to show to client.

More information: https://www.heroku.com/

### Mailgun

SMTP service to send out emails.

More information: https://www.mailgun.com/


### Asana

For project managament


## Creating your local project

```
git clone https://github.com/GaboxFH/tennis-court-system.git
```

After git pull (or git clone [repourl] the repo if you hadn't created a copy yet), run the following commands:

:::important

You only have to do this the first time you set up the project

:::
```
composer install
```

```
npm install && npm run dev 
```

```
cp .env.example .env
```

```
php artisan key:generate
```

```
php artisan config:cache
```

```
php artisan serve
```

Once you have done this the first time, all you have to run is the following:

:::important

Run these in separate tabs in your terminal

:::

```
npm run watch
```

```
php artisan serve
```
