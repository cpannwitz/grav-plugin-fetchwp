# FetchWP Plugin for Grav

`FetchWP` is a [Grav](http://github.com/getgrav/grav) Plugin and allows to fetch Wordpress posts by using the Wordpress REST Api.

Inspired by [Grav Plugin Facebook](https://github.com/mikahanninen/grav-plugin-facebook) .

Data from the Wordpress REST Api is fetched, processed through different settings the user can change and available via Twig function.

# Features

# Installation

Installing the Error plugin can be done in one of two ways. Our GPM (Grav Package Manager) installation method enables you to quickly and easily install the plugin with a simple terminal command, while the manual method enables you to do so via a zip file.

## GPM Installation

### Coming soon.

The simplest way to install this plugin is via the [Grav Package Manager (GPM)](http://learn.getgrav.org/advanced/grav-gpm) through your system's Terminal (also called the command line).  From the root of your Grav install type:

    bin/gpm install error

This will install the Error plugin into your `/user/plugins` directory within Grav. Its files can be found under `/your/site/grav/user/plugins/error`.

## Manual Installation

To install this plugin, just download the zip version of this repository and unzip it under `/your/site/grav/user/plugins`. Then, rename the folder to `error`. You can find these files either on [GitHub](https://github.com/cpannwitz/grav-plugin-fetchwp) or via [GetGrav.org](http://getgrav.org/downloads/plugins) (coming soon).

You should now have all the plugin files under

    /your/site/grav/user/plugins/fetchwp

# Usage

After installing the FetchWP-Plugin, you are required to visit the page of the plugin.

1) Enter your blog URL with activated WP REST API to fetch posts. Be aware of some language specific folders in the URL, than can prevent the plugin from fetching posts (eg. `https://blog.com/en/`). If your blog URL includes `/index.php`, do the same.

2) Implement the Twig function into your template, to display the fetched posts.
```
{{ wordpress_posts() }}
```

3) Adjust settings on the plugin page to your needs.

    - Number of blog posts to display
    - language support on/off
    - Featured Media embed on/off (also affects author)
    - Featured Media quality/size
    - Alternative image placeholder
    - Section title

## Template

Copy the template file [wordpress.posts.html.twig](templates/partials/wordpress.posts.html.twig) into the `templates` folder of your custom theme and that is it.

```
/your/site/grav/user/themes/custom-theme/templates/partials/wordpress.posts.html.twig
```

There are several variables available in the template.

- **sectiontitle**: text / opt. title for a heading
    - usage: `{{ sectiontitle }}`
- **postcount**: int / number of posts possible to render
- **altimage**: url / alternative image
    - usage: `{{ altimg|first.path }}`
- **feed**: array / holds all fetched posts
    - usage: ```{% for post in feed|slice(0, postcount) %}
    ...
    {% endfor %}```
- *inside feed :*
    - post.hasimg: boolean / if post has a featured image
    - post.imgsrc: url / source of the post image
    - post.authorname: text
    - post.authorlink: url
    - post.authordescription: text
    - post.blogtitle: text
    - post.bloglink: url
    - post.blogdate: text
    - post.blogmodified: text
    - post.blogexcerpt: text, limited, with HTML markup + "read more" link
    - post.blogcontent: text with HTML markup

Please refer to the example template for different ways of use.

# Updating


As development for the Error plugin continues, new versions may become available that add additional features and functionality, improve compatibility with newer Grav releases, and generally provide a better user experience. Updating Error is easy, and can be done through Grav's GPM system, as well as manually.

## GPM Update
### Coming soon.

The simplest way to update this plugin is via the [Grav Package Manager (GPM)](http://learn.getgrav.org/advanced/grav-gpm). You can do this with this by navigating to the root directory of your Grav install using your system's Terminal (also called command line) and typing the following:

    bin/gpm update error

This command will check your Grav install to see if your Error plugin is due for an update. If a newer release is found, you will be asked whether or not you wish to update. To continue, type `y` and hit enter. The plugin will automatically update and clear Grav's cache.

## Manual Update

Manually updating Error is pretty simple. Here is what you will need to do to get this done:

* Delete the `your/site/user/plugins/error` directory.
* Download the new version of the Error plugin from either [GitHub](https://github.com/cpannwitz/grav-plugin-fetchwp) or [GetGrav.org](http://getgrav.org/downloads/plugins#extras) (coming soon).
* Unzip the zip file in `your/site/user/plugins` and rename the resulting folder to `fetchwp`.
* Clear the Grav cache. The simplest way to do this is by going to the root Grav directory in terminal and typing `bin/grav clear-cache`.

> Note: Any changes you have made to any of the files listed under this directory will also be removed and replaced by the new set. Any files located elsewhere (for example a YAML settings file placed in `user/config/plugins`) will remain intact.
