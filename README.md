Inline Editor for Laravel based projects
================
[![Latest Stable Version](https://poser.pugx.org/swatty007/laravel-inline-editor/v/stable)](https://packagist.org/packages/swatty007/laravel-inline-editor)
[![Latest Unstable Version](https://poser.pugx.org/swatty007/laravel-inline-editor/v/unstable)](https://packagist.org/packages/swatty007/laravel-inline-editor)
[![Total Downloads](https://poser.pugx.org/swatty007/laravel-inline-editor/downloads)](https://packagist.org/packages/swatty007/laravel-inline-editor)
[![License](https://poser.pugx.org/swatty007/laravel-inline-editor/license)](https://packagist.org/packages/swatty007/laravel-inline-editor)

Simple inline editor toolbar, to update the content of any HTML block, 
or specific DB table.

This package is an updated version of [dyusha/laravel-html-editor](https://github.com/dyusha/laravel-html-editor) 
with some additional functionality and updated dependencies.

## Installation

Download this git repo, or install it via composer:

`composer require swatty007/laravel-inline-editor`

Also make sure to get our npm dependencies:

`npm install vue vue-resource medium-editor vue-sweetalert2 --save`

## Configuration

> Note, Optional in `Laravel 5.5 or +`

Add the following to your config file:
```php
// config/app.php
'providers' => [
    ...
    swatty007\LaravelInlineEditor\InlineEditorServiceProvider::class,
],
```

### Initial Setup
Run:
`php artisan vendor:publish`

* Include the following code snippet into your applications layout file, ***eg: "view/layouts/app.blade.php"***. 
This will render the required controls on your page.
```blade
{{-- Start Laravel Inline Editor Components --}}
    @include('laravel-inline-editor::html-manager')
{{-- End Laravel Inline Editor Components --}}`
```

* Afterwards apply our default table with:

`php artisan migrate`

* By default editing is allowed only for users who have `laravel-inline-editor` ability, 
so you should add it in your `AuthServiceProvider`
    
    ```php
    // app/Providers/AuthServiceProvider.php
    use Illuminate\Contracts\Auth\Access\Gate as GateContract;
    
    public function boot(GateContract $gate)
    {
       //...
        $gate->define('laravel-inline-editor', function ($user) {
            // Add your logic here
            return true;
        });
    }
    ```

* Include provided .scss and .js files on the page using your preferred build tools. For laravel mix:
```js
// resources/assets/js/app.js
Vue.component('inlineManager', require('./components/laravel-inline-editor/manager'));
Vue.component('inlineContentBlock', require('./components/laravel-inline-editor/contentBlock.vue'));
```

```scss
// resources/assets/sass/app.scss
// Inline Editor
@import "./plugins/_medium-editor.scss";
```


### Customization

You can publish the configuration files of the package, to tweak its behaviour or appearance:

`php artisan vendor:publish`

To change the default behaviour of the medium editor simply override the options setting in the config file:
```php
// config/laravel-inline-editor.php
'options' => '{ \'anchor\': { \'targetCheckbox\': true }, \'toolbar\': { \'buttons\': [\'bold\', \'italic\', \'underline\'] } }',
```

By default js and sass assets will be published to `/resources/assets/js/components` and 
`/resources/assets/sass/plugins` directories respectively. 
To change this, just update the following paths in your config file:
```php
// config/laravel-inline-editor.php
'paths' => [
    'js' => base_path('/resources/assets/js/components'),
    'sass' => base_path('/resources/assets/sass/plugins'),
],
```


## Usage

This package provides custom Blade directives `@inlineEditor` and `@endInlineEditor` which can be used to wrap blocks of HTML that should be editable. 
For example if somewhere in your template you will have the following code:

**Simple**  
Saves the containing HTML content in the `laravel-inline-editor` table under the key `item01`.
```html
{{-- Simple Content Block --}}
@inlineEditor( 'item01' )
    <p>Lorem text for our text block</p>
@endInlineEditor
```

**Custom Table**    
To save the containing HTML in a custom table, just specify, the table name, source & target keys.
If no source, or target keys are defined the defaults `key` and `content` will be used.
```php
{{-- Custom Database Content Block --}}
{{-- $source_value, $table, $source_key, $target_key, $options --}}
@inlineEditor('example_item', 'example_objects', 'title', 'content' )
    {{ \Illuminate\Support\Facades\DB::table('example_objects')->where('title', 'example_item')->first() }}
@endInlineEditor
```


**Custom Options**

You can  override the default options for the medium editor by overriding the default options:
```php
{{-- Shows the usage of the options property --}}
@inlineEditor( 'item01', null, null, null, "{ 'anchor': { 'targetCheckbox': true }, 'toolbar': { 'buttons': ['bold', 'italic', 'underline'] } }" )
    <p>Lorem text for our text block</p>
@endInlineEditor
``` 
For details check the medium js documentation: https://github.com/yabwe/medium-editor#mediumeditor-options

> Note Look at the configuration settings to override the default options


**Properties**
1. **Source Value** - DB row, where we are looking for our source key.
2. **Table** - will override at which table we are looking for the above defined value.
3. **Source Key** - allow overwriting the DB row at which we are looking for our source value.
4. **Target Key** - allow overwriting the DB row at which will be updated.
5. **Editor Options** - allows you to specify additional options for the medium editor.
6. **Validation Rules** - Allows you to define a set of custom validation rules.
7. **Strip HTML** - Strips all HTML elements from the given input string, before saving it into the DB.

The first time it's being rendered directive will try to find the content of your element 
by its defined key in the default database, if not specified otherwise.
If it is present then its content will be rendered on the page. 

Otherwise new HTML block will be created with the given parameters. 
You can put any HTML markup between `@inlineEditor` and `@endInlineEditor` directives.


### Workflow

When you press `Accept changes` button `<html-manager>` component will send `POST` request to `/inline-content-block` with `blocks` param that will contain all changed HTML blocks.

#### License
This library is licensed under the MIT license. Please see [LICENSE](LICENSE.md) for more details.
