<?php
declare(strict_types=1);

namespace App\Modules\Cms;

use App\Core\BaseModule;

class CmsModule extends BaseModule
{
    protected string $id = 'cms';

    protected array $name = [
        'ar' => 'إدارة المحتوى والأخبار',
        'en' => 'Content & News Management',
    ];

    protected array $description = [
        'ar' => 'إدارة الأخبار الصحفية، وتصنيف المقالات، والإعلانات والتنبيهات الجامعية الهامة.',
        'en' => 'Management of news articles, categories, and university announcements.',
    ];

    protected string $version = '1.0.0';

    /**
     * @var string[]
     */
    protected array $dependencies = [];

    /**
     * @var string[]
     */
    protected array $ownedTables = [
        'news_categories',
        'news_articles',
        'announcements',
    ];

    public function __construct()
    {
        $this->routesPath = __DIR__ . '/Routes/api.php';
    }
}
