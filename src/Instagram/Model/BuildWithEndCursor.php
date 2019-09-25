<?php

namespace Pvlima\MediaFeed\Instagram\Model;

use Pvlima\MediaFeed\Instagram\Model\Component\Feed;
use Pvlima\MediaFeed\Instagram\Model\Component\Media;
use Pvlima\MediaFeed\Instagram\Service\FeedServiceAbstract;

class BuildWithEndCursor
{
    /**
     * @var \stdClass
     */
    private $data;

    /**
     * @param array $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @return Feed
     *
     * @throws \Exception
     */
    public function getHydratedData()
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
            $media->setLikes($node->edge_media_preview_like->count);

            $media->setLink(FeedServiceAbstract::INSTAGRAM_ENDPOINT . "p/{$node->shortcode}/");

            $media->setThumbnails($node->thumbnail_resources);

            if(isset($node->location)){
                $media->setLocation($node->location);
            }

            $media->setVideo((bool)$node->is_video);

            if (property_exists($node, 'video_view_count')) {
                $media->setVideoViewCount((int)$node->video_view_count);
            }

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
        return $feed;
    }
}
