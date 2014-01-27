## Tag {cache}

This tag caches the contents for a given amount of time. It takes at least two arguments: the cache `timeout`, in seconds, and the `id` to give the cache fragment.

```smarty
	{cache id="sidebar" timeout=500}
	    ... sidebar ...
	{/foreach}
```