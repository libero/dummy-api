Libero dummy API
================

[![Build Status](https://travis-ci.com/libero/dummy-api.svg?branch=master)](https://travis-ci.com/libero/dummy-api)

This is a dummy implementation of a Libero API for development and testing purposes.

Getting started
---------------

To run an API with two content services (`blog-articles` and `research-articles`):

1. Create `config/packages/content_api.yaml`. This contains:

    ```yaml
    # Libero content APIs
    content_api:
        services:
            blog-articles:
                items: libero.dummy_api.content_api.blog_articles
            research-articles:
                items: libero.dummy_api.content_api.research_articles
    
    # Symfony services to read the content, one per content API
    services:
        _defaults:
            autowire: true
            autoconfigure: true
            public: false
    
        libero.dummy_api.content_api.blog_articles:
            class: Libero\ContentApiBundle\Adapter\FilesystemItems
            arguments:
                - '%kernel.data_dir%/blog-articles'
    
        libero.dummy_api.content_api.research_articles:
            class: Libero\ContentApiBundle\Adapter\FilesystemItems
            arguments:
                - '%kernel.data_dir%/research-articles'
    ```

2. Create the `data/blog-articles` and `data/research-articles` directories.

3. Place content inside these two directories. Each item must have its own directory (when its ID as the name), and one or more version files inside (named `{number}.xml`). The root element of these files is `{http://libero.pub}item`.

   For example:
   
   ```
   data
   ├── blog-articles
   │   ├── post1
   │   │   ├── 1.xml
   │   │   └── 2.xml
   │   └── post2
   │       └── 1.xml
   └── research-articles
       └── article1
           └── 1.xml
   ```

4. Run `docker-compose down -v && docker-compose up --build`.

5. Open http://localhost:8080/blog-articles/items to see the list of two posts.

Getting help
------------

- Report a bug or request a feature on [GitHub](https://github.com/libero/libero/issues/new/choose).
- Ask a question on the [Libero Community Slack](https://libero-community.slack.com/).
- Read the [code of conduct](https://libero.pub/code-of-conduct).
