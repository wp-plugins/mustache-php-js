# Mustache PHP JS - For WordPress
*Easy sharing of logic-less mustache templates between php and js.*

Adds mustache as PHP and JavaScript template engine to WordPress.

More info about mustache on [mustache.github.io](http://mustache.github.io) and [sitepoint.com](http://www.sitepoint.com/sharing-templates-between-php-and-javascript)

##PHP
This is an example of how it can look in your html template file.

```
<div class="panel-primary">
    <div class="panel-heading">
        <h3 class="panel-title">Mustache Example</h3>
    </div>
    <div class="panel-body feed">
        <?php mustache()->capture(); ?>
            <div class="col-sm-3">
                <a href="{{permalink}}">
                    <img src="{{image}}">
                    <h3 class="title">{{title}}</h3>
                    <small>{{date}}</small>
                    <div class="excerpt">{{excerpt}}</div>
                </a>
            </div>
        <?php mustache()->render('postTmpl', getTmplData()); ?>
    </div>
    <?php mustache()->getScript(); ?>
    <a class="load" href="#">Load more posts</a>
</div>
```
You can call the mustache() function as soon as the plugin is active. This will return the mustache object for you. Call the ```capture()``` function at the top of the code that you want to be a part of your template. This will start the PHP output buffer and save all code within your template to a string. At the end of your template you call ```render()```. This will stop the output buffer as well as output the template with the passed template data.

You probably want to get your template data from a function if your going to use mustache.js since you will be loading your data with ajax. If you're not going to use mustache.js there's not much more to it.
```
function getTmplData()
{
    // Load your data and return a nicely formatted array
    $tmplData = [
        'title' => 'First post',
        'permalink' => 'http://your-site.com/first-post',
        'image' => 'image-1.jpg',
        'date' => '2025-01-01',
        'excerpt' => 'This is a future post',
    ];

    return $tmplData;
}
add_action('wp_ajax_get_tmpl_data', 'getTmplData');
add_action('wp_ajax_nopriv_get_tmpl_data', 'getTmplData');
```

## Javascript
Outside of your wrapper div you have to call the ```getScript()``` function. This will output the template code wrapped in script tags for mustache.js to use. Use the ID of the script to get the html in it and pass that to ```Mustache.render``` together with your template data. Last step is to append (or maybe replace) the new content.

```
$('a#load').click(function(e) {
    e.preventDefault();

    // Get this data from your php function
    var tmplData = {
        'title' => 'Second post',
        'permalink' => 'http://your-site.com/second-post',
        'image' => 'image-2.jpg',
        'date' => '2225-01-01',
        'excerpt' => 'This... is not going to happen',
    };

    var output = Mustache.render($('#postTmpl').html(), tmplData);
    $('.wrapper').append(output);
});
```

## Partials
Define your partials like this...
```
<?php mustache()->capture(); ?>
    {{#posts}}
        <div class="col-sm-3">
            <a href="{{permalink}}">
                {{{post_thumbnail}}}
                <h3>{{post_title}}</h3>
                <small>{{date}}</small>
                <div>{{excerpt}}</div>
            </a>
        </div>
    {{/post}}
<?php mustache()->setPartial('postPartial')->getScript(); ?>
```
... and use it like this
```
<?php mustache()->capture(); ?>
    {{>post_partial}}
<?php mustache()->render('postTmpl', getTmplData()); ?>
```
This will generate the same output as the first example, but now you can reuse your *postPartial*. An example using partials in javascript could look something like this.
```
var template = $('#postTmpl').html();
var partials = {postPartial: $('#postPartial').html()};
$.post(ajax.url, data, function(response) {
    var output = Mustache.render(template, response, partials);
    $('.wrapper').append(output);
});
```
