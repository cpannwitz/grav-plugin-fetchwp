<?php
namespace Grav\Plugin;

use \Grav\Common\Plugin;
use Grav\Common\Data\Data;
use Grav\Common\Page\Page;
use Grav\Common\GPM\Response;;

class fetchWPPlugin extends Plugin
{
    private $template_post_html = 'partials/wordpress.post.html.twig';
    private $template_post_vars = [];
    private $template_event_vars = [];
    private $feeds = array();
    private $blog_image = '';

    public static function getSubscribedEvents()
    {
        return [
            'onPluginsInitialized' => ['onPluginsInitialized', 0]
        ];
    }

    public function onPluginsInitialized() {
        $this->enable([
            'onTwigTemplatePaths' => ['onTwigTemplatePaths', 0],
            'onTwigInitialized' => ['onTwigInitialized', 0]]
        );
    }

    public function onTwigInitialized() {
        $this->grav['twig']->twig->addFunction(new \Twig_SimpleFunction(
            'wordpress_posts',
            [$this, 'getWordpressPosts']
        ));
    }

    /**
    * Add current directory to twig lookup paths.
    **/
    public function onTwigTemplatePaths() {
        $this->grav['twig']->twig_paths[] = __DIR__ . '/templates';
    }

    public function getWordpressPosts() {
        /** @var Page $page */
        $page = $this->grav['page'];
        /** @var Twig $twig */
        $twig = $this->grav['twig'];
        /** @var Data $config */
        $config = $this->mergeConfig($page, TRUE);

        $blogurl = $config->get('wordpress_fetch_settings.blogurl');
        $enableFMedia = $config->get('wordpress_fetch_settings.enableFMedia');
        $enableTranslation = $config->get('wordpress_fetch_settings.enableTranslation');
        $translation = $this->grav['language']->getActive();

        $url = $blogurl . '/wp-json/wp/v2/posts?' . ($enableFMedia ? '_embed' : '') . ($enableTranslation ? '&lang='.$translation : '');

        try {
            $results = Response::get($url);
        } catch (\Exception $e) {
            return;
        }

        if ($results != null) {
            $this->parsePostResponse($results, $config);
            $this->template_post_vars = [
                    'feed' => $this->feeds,
                    'postcount' => empty($config->get('wordpress_fetch_settings.count')) ? 3 : $config->get('wordpress_fetch_settings.count'),
                    'sectiontitle' => $config->get('wordpress_fetch_settings.sectiontitle'),
                    'altimage' => $config->get('wordpress_fetch_settings.altImage'),
                    ];
            $isitimage = $config->get('wordpress_fetch_settings.altImage');
            $output = $this->grav['twig']->twig()->render($this->template_post_html, $this->template_post_vars);
            return $output;
        } else {
            return;
        }
    }

    private function parsePostResponse($json, $config) {
        $r = array();
        $content = json_decode($json);

        $count = empty($config->get('wordpress_fetch_settings.count')) ? 3 : $config->get('wordpress_fetch_settings.count');

        foreach ($content as $blogitem) {

            if (property_exists($blogitem, 'excerpt') && isset($blogitem->excerpt)) {
                $blog_image = '';
                $has_blogimage = '';
                $blog_image_quality = '';
                $blog_title = '';
                $blog_link = '';
                $blog_excerpt = '';

                if($config->get('wordpress_fetch_settings.enableFMedia')) {
                    $has_blogimage = $blogitem->featured_media;
                    $blog_image_quality = $config->get('wordpress_fetch_settings.qualityFMedia');

                    if ($has_blogimage !== 0) {
                        $blog_image = $blogitem->_embedded->{'wp:featuredmedia'}[0]->media_details->sizes->$blog_image_quality->source_url;
                        $r[$count]['hasimg'] = $has_blogimage;
                        $r[$count]['imgsrc'] = $blog_image;
                    }
                    $blog_author_name = $blogitem->_embedded->author[0]->name;
                    $blog_author_link = $blogitem->_embedded->author[0]->link;
                    $blog_author_description = $blogitem->_embedded->author[0]->description;

                    $r[$count]['authorname'] = $blog_author_name;
                    $r[$count]['authorlink'] = $blog_author_link;
                    $r[$count]['authordescription'] = $blog_author_description;
                }

                $blog_title = $blogitem->title->rendered;
                $blog_link = $blogitem->link;
                $blog_date = $blogitem->date;
                $blog_modified = $blogitem->modified;
                $blog_excerpt = $blogitem->excerpt->rendered;
                $blog_content = $blogitem->content->rendered;

                $r[$count]['blogtitle'] = $blog_title;
                $r[$count]['bloglink'] = $blog_link;
                $r[$count]['blogdate'] = $blog_date;
                $r[$count]['blogmodified'] = $blog_modified;
                $r[$count]['blogexcerpt'] = $blog_excerpt;
                $r[$count]['blogcontent'] = $blog_content;

                $this->addFeed($r);

                $count -= 1;

            }
        }
    }

    private function addFeed($result) {
        foreach ($result as $key => $blogitem) {
            if (!isset($this->feeds[$key])) {
                $this->feeds[$key] = $blogitem;
            }
        }
        krsort($this->feeds);
    }
}