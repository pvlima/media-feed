<?php

namespace Pvlima\MediaFeed\Instagram;

use Pvlima\MediaFeed\Instagram\Model\Feed;
use Pvlima\MediaFeed\Instagram\Model\Media;
use Pvlima\MediaFeed\Instagram\Service\FeedServiceAbstract;

class ResultBuilder
{
    /**
     * @var \stdClass
     */
    private $data;

    /**
     * @var string|null
     */
    private $endCursor;

    public function __construct($endCursor)
    {
        $this->endCursor = $endCursor;
    }

    /**
     * @param array $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @return Feed
     */
    public function getDataBuild()
    {
        $feed = $this->generateFeed();

        foreach ($this->data->edge_owner_to_timeline_media->edges as $edge) {

            /** @var \stdClass $node */
            $node = $edge->node;

            $media = new Media();

            $media->setId($node->id);
            $media->setTypeName($node->__typename);

            if ($node->edge_media_to_caption->edges) {
                $media->setCaption($node->edge_media_to_caption->edges[0]->node->text);
            }

            $media->setHeight($node->dimensions->height);
            $media->setWidth($node->dimensions->width);

            $media->setThumbnailSrc($node->thumbnail_src);
            $media->setDisplaySrc($node->display_url);

            $date = new \DateTime();
            $date->setTimestamp($node->taken_at_timestamp);

            $media->setDate($date);

            $media->setComments($node->edge_media_to_comment->count);
            
            if($this->endCursor == null)
                $media->setLikes($node->edge_liked_by->count);
            else 
                $media->setLikes($node->edge_media_preview_like->count);

            $media->setLink(FeedServiceAbstract::INSTAGRAM_ENDPOINT . "p/{$node->shortcode}/");

            $media->setThumbnails($node->thumbnail_resources);

            $feed->addMedia($media);
        }

        return $feed;
    }

    /**
     * @return Feed
     */
    private function generateFeed()
    {
        $feed = new Feed();

        $feed->setEndCursor($this->data->edge_owner_to_timeline_media->page_info->end_cursor);

        $feed->setId($this->data->id);
        $feed->setUserName($this->data->username);
        $feed->setBiography($this->data->biography);
        $feed->setFullName($this->data->full_name);
        $feed->setProfilePicture($this->data->profile_pic_url_hd);
        $feed->setMediaCount($this->data->edge_owner_to_timeline_media->count);
        $feed->setFollowers($this->data->edge_followed_by->count);
        $feed->setFollowing($this->data->edge_follow->count);
        $feed->setExternalUrl($this->data->external_url);

        return $feed;
    }
}
