# Deleting Resources

{% hint style="warning" %}
This section is deprecated. However, as of now, the Sylius E-Commerce project is still resorting to this configuration so you might want to check it out.
{% endhint %}

Deleting a resource is simple.

{% code title="config/routes.yaml" lineNumbers="true" %}
```yaml
app_book_delete:
    path: /books/{id}
    methods: [DELETE]
    defaults:
        _controller: app.controller.book::deleteAction
```
{% endcode %}

## Calling an Action with DELETE method

Currently browsers do not support the "DELETE" http method. Fortunately, Symfony has a very useful feature.
You can make a POST call with parameter override, which will force the framework to treat the request as the specified method.

{% code %}
```html
<form method="post" action="{{ path('app_book_delete', {'id': book.id}) }}">
    <input type="hidden" name="_method" value="DELETE" />
    <button type="submit">
        Delete
    </button>
</form>
```
{% endcode %}

On submit, the delete action with the method DELETE, will remove and flush the resource.
Then, by default it redirects to ``app_book_index`` to display the books index, but just like for the other actions - it's customizable.

## Overriding the Criteria

By default, the **deleteAction** will look for the resource by id. However, you can easily change that.
For example, if you want to delete a book that belongs to a particular genre, not only by its id.

{% code title="config/routes.yaml" lineNumbers="true" %}
```yaml
app_book_delete:
    path: /genre/{genreId}/books/{id}
    methods: [DELETE]
    defaults:
        _controller: app.controller.book::deleteAction
        _sylius:
            criteria:
                id: $id
                genre: $genreId
```
{% endcode %}

There are no magic hacks behind that, it simply takes parameters from request and builds the criteria array for the ``findOneBy`` repository method.

## Custom Redirect After Success

By default the controller will redirect to the "index" route after successful action. To change that, use the following configuration.

{% code title="config/routes.yaml" lineNumbers="true" %}
```yaml
app_book_delete:
    path: /genre/{genreId}/books/{id}
    methods: [DELETE]
    defaults:
        _controller: app.controller.book::deleteAction
        _sylius:
            redirect:
                route: app_genre_show
                parameters: { id: $genreId }
```
{% endcode %}

## Custom Event Name

By default, there are two events dispatched during resource deletion, one before removing, the other after successful removal.
The pattern is always the same - ``{applicationName}.{resourceName}.pre/post_delete``.
However, you can customize the last part of the event, to provide your own action name.

{% code title="config/routes.yaml" lineNumbers="true" %}
```yaml
app_book_customer_delete:
    path: /customer/book-delete/{id}
    methods: [DELETE]
    defaults:
        _controller: app.controller.book::deleteAction
        _sylius:
            event: customer_delete
```
{% endcode %}

This way, you can listen to ``app.book.pre_customer_delete`` and ``app.book.post_customer_delete`` events. It's especially useful, when you use
``ResourceController:deleteAction`` in more than one route.

## Configuration Reference

{% code title="config/routes.yaml" lineNumbers="true" %}
```yaml
app_genre_book_remove:
    path: /{genreName}/books/{id}/remove
    methods: [DELETE]
    defaults:
        _controller: app.controller.book::deleteAction
        _sylius:
            event: book_delete
            repository:
                method: findByGenreNameAndId
                arguments: [$genreName, $id]
            criteria:
                genre.name: $genreName
                id: $id
            redirect:
                route: app_genre_show
                parameters: { genreName: $genreName }
```
{% endcode %}

Remember that you can use controller's Fully Qualified Class Name (``App\Controller\BookController``) instead of id ``app.controller.book`` 

**[Go back to the documentation's index](index.md)**
