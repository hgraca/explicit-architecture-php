# Explicit Architecture 
[![Author][Author]](https://www.herbertograca.com)
[![Software License][License]](LICENSE)

[![Build Status][Build]](https://scrutinizer-ci.com/g/hgraca/explicit-architecture-php/build-status/master)
[![Scrutinizer Code Quality][Score]](https://scrutinizer-ci.com/g/hgraca/explicit-architecture-php/?branch=master)
[![Code Intelligence Status][CodeInt]](https://scrutinizer-ci.com/code-intelligence)
[![CodeCov][CodeCov]](https://codecov.io/gh/hgraca/explicit-architecture-php)

## Symfony Demo Application

The "Symfony Demo Application" is a reference application created to show how
to develop Symfony applications following the recommended best practices.

This repository is a demo of [Explicit Architecture][1], using the [Symfony Demo Application][2].

### Explicit Architecture

I explained [Explicit Architecture][1] in one of my blog posts, as a result of my understanding of several architectural
 styles such as (but not limited to) [EBI Architecture][11], [DDD][12], [Ports & Adapters Architecture][13], 
 [Onion Architecture][14] and [Clean Architecture][15].
 
[![Explicit Architecture](https://docs.google.com/drawings/d/e/2PACX-1vQ5ps72uaZcEJzwnJbPhzUfEeBbN6CJ04j7hl2i3K2HHatNcsoyG2tgX2vnrN5xxDKLp5Jm5bzzmZdv/pub?w=960&amp;h=657)][2]

#### Package by component

[![Package by component](https://docs.google.com/drawings/d/e/2PACX-1vQjEj4dKKUaQEUcNDq2UO58oIUu6pehqrE99q4gSRk0DY9KPIuhgG9Yg3qJGgW4ybrL5Ql8_Xo5z3yq/pub?w=960&h=720)][17]

#### Dependencies directions

[![Dependencies](https://docs.google.com/drawings/d/e/2PACX-1vQyv5xAx5hFJPhiK19AGl_2t256M0yKcDSliH8etojltE3tBlEnCndwfsUr1UsXvv5PKGVtrBHkQX3h/pub?w=913&amp;h=129)][16]

#### Folder structure

- **bin** (_application binaries, the dependencies binaries should go in vendor/bin_)
- **build** (_artifacts needed for building the application prior to running it_)
- **config** (_all the config needed to run the application_)
- **docs** (_application documentation_)
- **lib** (_libraries used by the application, which are specific to this application and/or not distributable_)
    - **std-lib**  (_functions and/or classes to be used as if they were part of the language itself_)
        - _src_
        - _tests_
- **src**
    - **[Core][10]** (_the application core_)
        - **[SharedKernel][6]** (_application and domain code shared among all components/bounded contexts_)
        - **[Component][5]** (_the application components/bounded contexts_)
            - **Component_A**
                - **[Application]** (_use cases, repositories, application services, ..., depends on the Ports and the Domain_)
                - **[Domain]** (_entities and their related value objects, enums, ..., depends on nothing_)
        - **[Port][8]** (_the ports, to be implemented by the infrastructure adapters_)
    - **[Infrastructure][9]** (_the port adapters for the infrastructure tools_)
    - **[Presentation][7]** (_the presentation layer with the several user facing applications, controllers, views and related code units_)
- **tests** (_unit, integration, functional, acceptance tests_)
    - **build** (_artifacts needed for running the application tests, like a test DB template_)
- **var** (_volatile artifacts like logs, cache, temporary test databases, generated code, ..._)
- **vendor** (_distributable libraries_)

## Usage

You need to have docker installed in your host OS. 

Then execute this command to run the app during development: 

```bash
make up
```

Then execute this command to run the app in production: 

```bash
make up-prd
```

Access the application in your browser at <http://localhost:8000>.

You can also test the error pages at `http://localhost:8000/index.php/_error/{statusCode}.{format}`,
 ie. <http://localhost:8000/index.php/_error/400.html>.

### Tests

Execute this command to run tests during development:

```bash
make test
```

Execute this command to run tests in the CI:

```bash
make test-ci
```

### Debug

The dev container has xDebug installed and you can use it out of the box, provided that you have your IDE configured
 appropriately.
 
Check the screenshots in the `docs/IDE/PHP_Storm/` folder, to check how to configure PHP Storm to work with xDebug, as
 it is set up in the dev docker image.

[1]: https://herbertograca.com/2017/11/16/explicit-architecture-01-ddd-hexagonal-onion-clean-cqrs-how-i-put-it-all-together/
[2]: https://docs.google.com/drawings/d/1E_hx5B4czRVFVhGJbrbPDlb_JFxJC8fYB86OMzZuAhg/edit?usp=sharing
[5]: http://ddd.fed.wiki.org/view/welcome-visitors/view/domain-driven-design/view/bounded-context
[6]: http://ddd.fed.wiki.org/view/welcome-visitors/view/domain-driven-design/view/shared-kernel
[7]: https://herbertograca.com/2017/11/16/explicit-architecture-01-ddd-hexagonal-onion-clean-cqrs-how-i-put-it-all-together/#primary-or-driving-adapters
[8]: https://herbertograca.com/2017/11/16/explicit-architecture-01-ddd-hexagonal-onion-clean-cqrs-how-i-put-it-all-together/#ports
[9]: https://herbertograca.com/2017/11/16/explicit-architecture-01-ddd-hexagonal-onion-clean-cqrs-how-i-put-it-all-together/#secondary-or-driving-adapters
[10]: https://herbertograca.com/2017/11/16/explicit-architecture-01-ddd-hexagonal-onion-clean-cqrs-how-i-put-it-all-together/#application-core-organisation
[11]: https://herbertograca.com/2017/08/24/ebi-architecture/
[12]: https://herbertograca.com/2017/09/07/domain-driven-design/
[13]: https://herbertograca.com/2017/09/14/ports-adapters-architecture/
[14]: https://herbertograca.com/2017/09/21/onion-architecture/
[15]: https://herbertograca.com/2017/09/28/clean-architecture-standing-on-the-shoulders-of-giants/
[16]: https://docs.google.com/drawings/d/1DGiP9qyBpRHPDPKRJoXdElw1DXwmJoR-88Qvtf6hBNA/edit?usp=sharing
[17]: https://docs.google.com/drawings/d/1QurViCcaZ4Eh1CgBvel9aK5RLBkw2TaOAnO-Lhu4pfw/edit?usp=sharing

[Author]: http://img.shields.io/badge/author-@hgraca-blue.svg?style=flat-square
[License]: https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square

[Build]: https://scrutinizer-ci.com/g/hgraca/explicit-architecture-php/badges/build.png?b=master
[Score]: https://scrutinizer-ci.com/g/hgraca/explicit-architecture-php/badges/quality-score.png?b=master
[CodeInt]: https://scrutinizer-ci.com/g/hgraca/explicit-architecture-php/badges/code-intelligence.svg?b=master
[CodeCov]: https://codecov.io/gh/hgraca/explicit-architecture-php/branch/master/graph/badge.svg
