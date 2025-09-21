# Software for WordPress czemp Theme

## Introduction
This is a repository for a WordPress block theme and possibly plugins facilitating a more efficient upload functionality 
for a custom made home site. The theme is currently based on the WordPress Twenty Twenty Five theme, and is as such a 
[Child Theme](https://developer.wordpress.org/themes/advanced-topics/child-themes/) currently. It is hosted here so that
the developer has a means of safely securing the code, but feel free to fork if you find it useful for your intents, provided
that the GPLv2 Licence is adhered to.

## Components

### Development Environment

Normally, the development setup for WordPress suggests to use some distribution
of a virtual machine containing the full stack of DB, WordPress installation and a Webserver.
because the author felt that it leads to more flexibility, he chose to use a dockerized setup and docker compose, as documented
in the `docker-compose.yml` file. 

#### Images
The current `docker-compose.yml` uses standard [Dockerhub](https://hub.docker.com/) images, specifically:

* [Mysql](https://hub.docker.com/_/mysql)
* [PhpMyAdmin](https://hub.docker.com/_/phpmyadmin)
* [WordPress](https://hub.docker.com/_/wordpress)

Note that you should configure images according to your target environment. It may be even worthwhile to create you own `Dockerfile`s. 
Any improvement and comment on this setup is greatly appreciated (as are corresponding PRs, BTW 😉).

#### Usage

Out of the box, this is easy:

    docker-compose up -d

and 

    docker-compose down

to get rid of it. You can access the site at `localhost:80` or whichever port you think is ok, the admin tool fo mysql is at port `8081`.

Note that there are some volumes linked to the local hard drive. This is so that changes in the current code
are directly linked into the docker container. Although this is very convenient, it does only provide some sort of poor man's
hot reload in that the browser needs to be refreshed manually.
Since the author sees that as a minor inconvenience, he did not further fine tune.

However, again, any PR or Email to the [author](mailto:thomas@rosser.ch) on how to improve is greatly appreciated.

Note that the `plugins` and `data`directories of the wordpress image have been mapped to local disk so they are persisted  
between restarts. This ensures you do not have to reinitiate every time you restart the Docker images.

## Theme Files

WIP

