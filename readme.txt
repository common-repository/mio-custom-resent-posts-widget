=== Mio Custom Recent Posts Widget ===
Contributors: miosee
Donate link: 
Tags: widget, recent posts, sidebar, recent posts widget, post type recent posts, thumbnail resent posts
Requires at least: 4.0
Tested up to: 4.4
Stable tag: 1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

It is a widget that can display recent posts in each thumbnail view and post type .

== Description ==

= That can be configured item =
* Change of title
* Display switching of thumbnail
* Setting the thumbnail size ( to abide to the size of the media settings )
* The setting of the display number
* Display settings Posted
* Setting of post type

= Languages =
* English
* Japanese

= HTML code that is output =
`<div id="mio_custom_recent_posts-{Num}" class="widget widget_custom_recent_entries">
  <h3 class="widgettitle heading-mark"><span class="f-um">{widget-title}</span></h3>
  <ul>
    <li>
      <a href="{post-url}">
        <span class="thumb">
          <img width="150" height="150" src="{Image File URL}" class="attachment-thumbnail wp-post-image" alt="{Media alt}"/>
        </span>
        <span class="title">{post-title}</span>
      </a>
      <span class="post-date on-thumb">{post-date}</span>
    </li>
  </ul>
</div>`

= CSS setting of the initial state =
`.widget_custom_recent_entries > ul {
  list-style: none;
}
.widget_custom_recent_entries > ul > li {
  position: relative;
}
.widget_custom_recent_entries > ul > li:before,
.widget_custom_recent_entries > ul > li:after {
  content: "";
  display: table;
}
.widget_custom_recent_entries > ul > li:after {
  clear: both;
}
.widget_custom_recent_entries > ul > li + li {
  margin-top: 10px;
}
.widget_custom_recent_entries .thumb {
  float: left;
  display: block;
  width: 80px;
  height: 80px;
}
.widget_custom_recent_entries .thumb img {
  max-width: 100%;
  height: auto;
}
.widget_custom_recent_entries .thumb + .title {
  margin-left: 88px;
}
.widget_custom_recent_entries .title {
  display: block;
}
.widget_custom_recent_entries .post-date {
  display: block;
}
.widget_custom_recent_entries .post-date.on-thumb {
  margin-left: 88px;
}`


Go to the widget screen , please set the " **Custom Recent Posts** " to your own widget area .


I'm sorry. We have created the English with Google Translate .

== Installation ==

1. Upload `mio-custom-recent-posts-widget` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Custom Recent Posts' from 'Plugins' menu in WordPress
3. Go to the widget screen, please set the 'Custom Recent Posts' to your own widget area.

== Frequently asked questions ==



== Screenshots ==



== Changelog ==

**1.0.0**
Initial release

== Upgrade notice ==

