# Getting a Collection of Resources

{% hint style="warning" %}
This section is deprecated. However, as of now, the Sylius E-Commerce project is still resorting to this configuration so you might want to check it out.
{% endhint %}

{% hint style="warning" %}
This section is deprecated. However, as of now, the Sylius E-Commerce project is still resorting to this configuration so you might want to check it out.
{% endhint %}

To get a paginated list of Books, we will use **indexAction** of our controller.
In the default scenario, it will return an instance of paginator, with a list of Books.

{% code title="config/routes.yaml" lineNumbers="true" %}
```yaml
app_book_index:
    path: /books
    methods: [GET]
    defaults:
        _controller: app.controller.book::indexAction
```
{% endcode %}

When you go to ``/books``, the ResourceController will use the repository (``app.repository.book``) to create a paginator.
The default template will be rendered - ``App:Book:index.html.twig`` with the paginator as the ``books`` variable.

A paginator can be a simple array, if you disable the pagination, otherwise it is an instance of ``Pagerfanta\Pagerfanta``
which is a [Library](https://github.com/BabDev/Pagerfanta) used to manage the pagination.

## Overriding the Template and Criteria

Just like for the **showAction**, you can override the default template and criteria.

{% code title="config/routes.yaml" lineNumbers="true" %}
```yaml
app_book_index_inactive:
    path: /books/disabled
    methods: [GET]
    defaults:
        _controller: app.controller.book::indexAction
        _sylius:
            filterable: true
            criteria:
                enabled: false
            template: Book/disabled.html.twig
```
{% endcode %}

This action will render a custom template with a paginator only for disabled Books.

## Sorting

Except filtering, you can also sort Books.

{% code title="config/routes.yaml" lineNumbers="true" %}
```yaml
app_book_index_top:
    path: /books/top
    methods: [GET]
    defaults:
        _controller: app.controller.book::indexAction
        _sylius:
            sortable: true
            sorting:
                score: desc
            template: Book/top.html.twig
```
{% endcode %}

Under that route, you can paginate over the Books by their score.

## Using a Custom Repository Method

You can define your own repository method too, you can use the same way explained
in [show_resource](http://docs.sylius.com/en/latest/components_and_bundles/bundles/SyliusResourceBundle/show_resource.html#using-custom-repository-methods).

### **Note**
If you want to paginate your resources you need to use ``EntityRepository::getPaginator($queryBuilder)``.
It will transform your doctrine query builder into ``Pagerfanta\Pagerfanta`` object.

## Changing the "Max Per Page" Option of Paginator

You can also control the "max per page" for paginator, using ``paginate`` parameter.

{% code title="config/routes.yaml" lineNumbers="true" %}
```yaml
app_book_index_top:
    path: /books/top
    methods: [GET]
    defaults:
        _controller: app.controller.book::indexAction
        _sylius:
            paginate: 5
            sortable: true
            sorting:
                score: desc
            template: Book/top.html.twig
```
{% endcode %}

This will paginate 5 books per page, where 10 is the default.

## Disabling Pagination - Getting a Simple Collection

Pagination is handy, but you do not always want to do it, you can disable pagination and simply request a collection of resources.

{% code title="config/routes.yaml" lineNumbers="true" %}
```yaml
app_book_index_top3:
    path: /books/top
    methods: [GET]
    defaults:
        _controller: app.controller.book::indexAction
        _sylius:
            paginate: false
            limit: 3
            sortable: true
            sorting:
                score: desc
            template: Book/top3.html.twig
```
{% endcode %}

That action will return the top 3 books by score, as the ``books`` variable.

## Changing the serialization groups of the elements in a paginated response

{% code title="config/routes.yaml" lineNumbers="true" %}
```yaml
app_book_index:
    path: /{author}/books
    methods: [GET]
    defaults:
        _controller: app.controller.book::indexAction
        _sylius:
            serialization_groups: { 0: Default, items: [ Custom ] }
```
{% endcode %}

Read more about [nested serialization groups](https://jmsyst.com/libs/serializer/master/cookbook/exclusion_strategies#overriding-groups-of-deeper-branches-of-the-graph).

## Configuration Reference

{% code title="config/routes.yaml" lineNumbers="true" %}
```yaml
app_book_index:
    path: /{author}/books
    methods: [GET]
    defaults:
        _controller: app.controller.book::indexAction
        _sylius:
            template: Author/books.html.twig
            repository:
                method: createPaginatorByAuthor
                arguments: [$author]
            criteria:
                enabled: true
                author.name: $author
            paginate: false # Or: 50
            limit: 100 # Or: false
            serialization_groups: [Custom, Details]
            serialization_version: 1.0.2
```
{% endcode %}

Remember that you can use controller's Fully Qualified Class Name (``App\Controller\BookController``) instead of id ``app.controller.book`` 

**[Go back to the documentation's index](index.md)**
