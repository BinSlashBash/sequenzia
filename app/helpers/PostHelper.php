<?php
class PostHelper extends Rails\ActionView\Helper
{
    public function source_link($source, $abbreviate = true)
    {
        if (!$source) {
            return 'none';
        } elseif (strpos($source, 'http') === 0) {
            $text = $source;
            if ($abbreviate)
                $text = substr($text, 7, 20) . '...';
            return $this->linkTo($text, $source, ['rel' => 'nofollow']);
        } else {
            return $this->h($source);
        }
    }
    
    public function print_preview($post, $options = array())
    {
        $is_post = $post instanceof Post;
        
        if ($is_post && !CONFIG()->can_see_post(current_user(), $post))
            return "";

        $image_class = "preview";
        
        !isset($options['url_params']) && $options['url_params'] = null;
        
        $image_id = isset($options['image_id']) ? 'id="'.$options['image_id'].'"' : null;
        
        $image_title = $is_post ? $this->h("Rating: ".$post->pretty_rating()." / Score: ".$post->score." / Tags: ".$this->h($post->cached_tags." / User: ".$post->author())) : null;
        
        $link_onclick = isset($options['onclick']) ? 'onclick="'.$options['onclick'].'"' : null;
        
        $link_onmouseover = isset($options['onmouseover']) ? ' onmouseover="'.$options['onmouseover'].'"' : null;
        $link_onmouseout = isset($options['onmouseout']) ? ' onmouseout="'.$options['onmouseout'].'"' : null;

        if (isset($options['display']) && $options['display'] == 'block') {
            # Show the thumbnail at its actual resolution, and crop it with northern orientation
            # to a smaller size.
            list($width, $height) = $post->raw_preview_dimensions();
            $block_size = array(200, 200);
            $visible_width = min(array($block_size[0], $width));
            $crop_left = ($width - $visible_width) / 2;
        } elseif (isset($options['display']) && $options['display'] == 'large') {
            list($width, $height) = $post->raw_preview_dimensions();
            $block_size = array($width, $height);
            $crop_left = 0;
        } else {
            # Scale it down to a smaller size.    This is exactly one half the actual size, to improve
            # resizing quality.
            list($width, $height) = $post->preview_dimensions();
            $block_size = array(150, 150);
            $crop_left = 0;
        }
        
        $image = '<img src="'.$post->preview_url().'" style="margin-left: '.$crop_left*(-1).'px;" alt="'.$image_title.'" class="'.$image_class.'" title="'.$image_title.'" '.$image_id.' width="'.$width.'" height="'.$height.'">';
        if ($is_post) {
            $plid = '<span class="plid">#pl http://'.CONFIG()->server_host.'/post/show/'.$post->id.'</span>';
            $target_url = '/post/show/' . $post->id . '/' . $post->tag_title() . $options['url_params'];
        } else {
            $plid = "";
            $target_url = $post->url;
        }

        $link_class = "thumb";
        !$is_post && $link_class .= " no-browser-link";
        $link = '<a class="'.$link_class.'" href="'.$target_url.'" '.$link_onclick.$link_onmouseover.$link_onmouseout.'>'.$image.$plid.'</a>';
        $div = '<div class="inner" style="width: '.$block_size[0].'px; height: '.$block_size[1].'px;">'.$link.'</div>';
        
        if ($post->use_jpeg(current_user()) && empty($options['disable_jpeg_direct_links'])) {
            $dl_width = $post->jpeg_width;
            $dl_height = $post->jpeg_height;
            $dl_url = $post->jpeg_url();
        } else {
            $dl_width = $post->width;
            $dl_height = $post->height;
            $dl_url = $post->file_url();
        }
        
        $directlink_info = '
        <span class="directlink-info">
			<img class="directlink-icon directlink-icon-uhd" src="/images/ddl_uhd.gif" alt="">
            <img class="directlink-icon directlink-icon-large" src="/images/ddl_large.gif" alt="">
            <img class="directlink-icon directlink-icon-small" src="/images/ddl.gif" alt="">
			<img class="directlink-icon file-type-jpg" src="/images/type-jpg.gif" alt="">
			<img class="directlink-icon file-type-png" src="/images/type-png.gif" alt="">
			<img class="directlink-icon file-type-gif" src="/images/type-gif.gif" alt="">
			<img class="directlink-icon file-type-mp4" src="/images/type-mp4.gif" alt="">
			<img class="directlink-icon file-type-webm" src="/images/type-webm.gif" alt="">
			<img class="directlink-icon file-type-flv" src="/images/type-flv.gif" alt="">
			<img class="directlink-icon image-rating-s" src="/images/rate-s.gif" alt="">
			<img class="directlink-icon image-rating-q" src="/images/rate-q.gif" alt="">
			<img class="directlink-icon image-rating-e" src="/images/rate-e.gif" alt="">
            <img class="parent-display" src="/images/post-star-parent.gif" alt="">
            <img class="child-display" src="/images/post-star-child.gif" alt="">
            <img class="flagged-display" src="/images/post-star-flagged.gif" alt="">
            <img class="pending-display" src="/images/post-star-pending.gif" alt="">
        </span>
        ';
        $li_class = "";

        $ddl_class = "directlink";
		
		if ($post->width >= 3500 || $post->height >= 2600 )
			$ddl_class .= " extremeimg";
		else 
			$ddl_class .= ($post->width > 1920 || $post->height > 1500 )?    " largeimg":" smallimg";
		
		if ($post->file_ext == "jpg" || $post->file_ext == "jpeg")
			$ddl_class .= " file-jpg";
		if ($post->file_ext == "png")
			$ddl_class .= " file-png";
		if ($post->file_ext == "gif")
			$ddl_class .= " file-gif";
		if ($post->file_ext == "flv" || $post->file_ext == "swf")
			$ddl_class .= " file-flv";
		if ($post->file_ext == "mp4")
			$ddl_class .= " file-mp4";
		if ($post->file_ext == "webm")
			$ddl_class .= " file-webm";
		
		if ($post->rating == "s")
			$ddl_class .= " rating-s";
		
		if ($post->rating == "q")
			$ddl_class .= " rating-q";
		
		if ($post->rating == "e")
			$ddl_class .= " rating-e";
		
        if (!empty($options['similarity'])) {
            $icon = '<img src="'.$post->service_icon().'" alt="'.$post->service().'" class="service-icon" id="source">';
            $ddl_class .= " similar similar-directlink";
            is_numeric($options['similarity']) && $options['similarity'] >= 90 && $li_class .= " similar-match";
            is_string($options['similarity']) && $options['similarity'] == 'Original' && $li_class .= " similar-original";
            $directlink_info = '<span class="similar-text">'.$icon.'</span>'.$directlink_info;
        }

        if (!empty($options['hide_directlink']))
            $directlink = "";
        else {
            $directlink_res = '<span class="directlink-res">'.$dl_width.' x '.$dl_height.'</span>';
            if (current_user()->can_see_posts())
                $directlink = '<a class="'.$ddl_class.'" href="'.$dl_url.'">'.$directlink_info.$directlink_res.'</a>';
            else
                $directlink = '<a class="'.$ddl_class.'" href="#" onclick="return false;">'.$directlink_info.$directlink_res.'</a>';
        }
        
        if ($is_post) {
            # Hide regular posts by default.    They'll be unhidden by the scripts once the
            # blacklists are loaded.    Don't do this for ExternalPost, which don't support
            # blacklists.
            !empty($options['blacklisting']) && $li_class .= " javascript-hide";
            $li_class .= " creator-id-".$post->user_id;
        }
        $post->is_flagged() && $li_class .= " flagged";
        $post->has_children && $li_class .= " has-children";
        $post->parent_id    && $li_class .= " has-parent";
        $post->is_pending() && $li_class .= " pending";
        # We need to specify a width on the <li>, since IE7 won't figure it out on its own.
        $item = '<li style="width: '.($block_size[0]+10).'px;" id="p'.$post->id.'" class="'.$li_class.'">'.$div.$directlink.'</li>';
        
        return $item;
    }
    
    public function auto_discovery_link_tag_with_id($type = 'rss', $url_options = array(), $tag_options = array())
    {
        if (is_array($url_options)) {
            $url = array_shift($url_options);
            $url_options['only_path'] = false;
            $href = $this->urlFor($url, $url_options);
        } else {
            $href = $url_options;
        }
        return $this->tag(
            "link", array(
                "rel"   => isset($tag_options['rel']) ? $tag_options['rel'] : "alternate",
                "type"  => isset($tag_options['type']) ? $tag_options['type'] : "application/".$type."+xml",
                "title" => isset($tag_options['title']) ? $tag_options['title'] : strtoupper($type),
                "id"    => $tag_options['id'],
                "href"  => $href
        ));
    }
    
    public function vote_tooltip_widget()
    {
        return '<span class="vote-desc"></span>';
    }
    
    public function vote_widget($user = null, $className = "standard-vote-widget")
    {
        if (!$user)
            $user = current_user();
        
        $html = '<span class="stars '.$className.'">';
        
        if (!$user->is_anonymous()) {
            foreach(range(0, 3) as $vote)
                $html .= '<a href="#" class="star star-'.$vote.' star-off"></a>';
            $html .= '<span class="vote-up-block"><a class="star vote-up" href="#"></a></span>';
        }
        
        $html .= '</span>';
        return $html;
    }
    
    public function get_service_icon($service)
    {
        return ExternalPost::get_service_icon($service);
    }
    
    /* Import uses this */
    public function import_file_detail_name($name)
    {
        if (is_int(strpos($name, '/'))) {
            $parts = explode('/', $name);
            $last_part = array_pop($parts);
            $name = '<span class="dir">'.implode('/', $parts).'/</span>'.$last_part;
        } else {
            $name = substr(stripslashes($name), 0, 100);
            strlen($name) > 100 && $name .= '...';
        }
        return $name;
    }
}