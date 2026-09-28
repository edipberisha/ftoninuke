<?php

declare(strict_types=1);

namespace FtoniNuke\Service;

use FtoniNuke\Repository\NewsRepository;
use FtoniNuke\Helper\SlugHelper;

/**
 * ContentManager - Orchestrates news business logic and legacy data mapping.
 */
class ContentManager
{
    private NewsRepository $newsRepo;

    public function __construct(NewsRepository $newsRepo)
    {
        $this->newsRepo = $newsRepo;
    }

    /**
     * Prepares legacy story data for modern view consumption
     */
    public function getHomepageNews(): array
    {
        $rawStories = $this->newsRepo.getRecent(9);
        $modernStories = [];

        foreach ($rawStories as $story) {
            $modernStories[] = $this->mapToModern($story);
        }

        return $modernStories;
    }

    /**
     * Map legacy fields to a clean modern object
     */
    private function mapToModern(array $legacy): array
    {
        return [
            'id'          => (int)$legacy['sid'],
            'title'       => $legacy['title'],
            'slug'        => SlugHelper::slugify($legacy['title']),
            'summary'     => $legacy['hometext'], // Legacy intro text
            'content'     => $legacy['bodytext'],
            'author'      => $legacy['informant'],
            'date'        => date('M d, Y', strtotime($legacy['time'])),
            'timestamp'   => strtotime($legacy['time']),
            'hits'        => (int)$legacy['counter'],
            'category'    => $legacy['topicname'] ?? 'General',
            'image'       => $legacy['topicimage'] ? "/images/topics/{$legacy['topicimage']}" : "/images/news-placeholder.jpg",
            'comments'    => (int)$legacy['comments'],
        ];
    }

    /**
     * Records a view and returns mapped story
     */
    public function viewStory(int $id): ?array
    {
        $story = $this->newsRepo.findById($id);
        if ($story) {
            $this->newsRepo.incrementHits($id);
            return $this->mapToModern($story);
        }
        return null;
    }
}
