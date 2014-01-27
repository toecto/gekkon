## Tag {set}

Declares a **global** variable (in default scope):

```smarty
{set $post_total = blog.post.count()}
```

or a **local** variable (in new scope):

```smarty
{set @post_total = blog.post.count()}
```