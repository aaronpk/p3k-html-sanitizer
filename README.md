# p3k-html-sanitizer

An HTML sanitizer with good defaults usable for displaying untrusted HTML in applications.

Allows only a basic set of formatting tags, removing all `<script>` tags. Removes all attributes of allowed tags except leaves in Microformats 2 classes.


## Installation

```
composer require p3k/html-sanitizer
```


## Usage

```
$output = p3k\HTML::sanitize($input);
```

### Options

There are a minimal number of options you can pass to the sanitize function:

```
$options = [
  'baseURL' => 'https://example.com/'
];

$output = p3k\HTML::sanitize($input, $options);
```

* `baseURL` - (default `false`)
* `allowImg` - (`true`/`false`, default `true`) - whether to allow `img` tags in the output
* `allowMf2` - (`true`/`false`, default `true`) - whether to allow Microformats 2 classes on elements
* `allowTables` - (`true`/`false`, default `false`) - whether to allow table elements (`table`, `thead`, `tbody`, `tr`, `td`)


## Allowed Tags

The following HTML tags are the only tags allowed in the input. Everything else will be removed.

* `a`
* `abbr`
* `b`
* `br`
* `code`
* `del`
* `em`
* `i`
* `q`
* `strike`
* `strong`
* `time`
* `blockquote`
* `pre`
* `p`
* `h1`
* `h2`
* `h3`
* `h4`
* `h5`
* `h6`
* `ul`
* `li`
* `ol`
* `span`
* `hr`
* `img` - only if `$options['allowImg']` is `true`
* `table`, `thead`, `tbody`, `tfoot`, `tr`, `th`, `td` - only if `$options['allowTables']` is `true`

All attributes other than those below will be removed.

* `<a>` - `href`
* `<img>` - `src width height alt`
* `<time>` - `datetime`

If `$options['allowMf2']` is `true`, class attributes will be removed, except for Microformats 2 class values.

For example:

`<h2 class="p-name name">Hello</h2>`

will become

`<h2 class="p-name">Hello</h2>`





