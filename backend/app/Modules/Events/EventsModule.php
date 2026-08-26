<?php
declare(strict_types=1);

namespace App\Modules\Events;

use App\Core\BaseModule;

class EventsModule extends BaseModule
{
    protected string $id = 'events';

    protected array $name = [
        'ar' => 'الفعاليات والأنشطة',
        'en' => 'Events & Activities',
    ];

    protected array $description = [
        'ar' => 'إدارة الفعاليات والمؤتمرات الجامعية، وتنظيم تسجيل الحضور والمشاركين.',
        'en' => 'Management of academic events, conferences, and attendee registrations.',
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
        'events',
        'event_attendees',
    ];

    public function __construct()
    {
        $this->routesPath = __DIR__ . '/Routes/api.php';
    }
}
